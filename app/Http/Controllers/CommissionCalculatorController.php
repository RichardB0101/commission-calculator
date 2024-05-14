<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

class CommissionCalculatorController extends Controller
{
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');

        try {
            $results = $this->processCSV($file);
        } catch (\Exception $e) {
            Log::error('Error processing CSV: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($results);
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     */
    protected function processCSV($file): array
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(null);

        $records = $csv->getRecords();

        $results = [];
        foreach ($records as $index => $record) {
            if (!is_array($record) || count($record) < 6) {
                Log::warning('Incomplete record at index ' . $index, ['record' => $record]);
                continue;
            }

            $operationDate = $record[0] ?? null;
            $userId = $record[1] ?? null;
            $userType = $record[2] ?? null;
            $operationType = $record[3] ?? null;
            $amount = isset($record[4]) ? (float)$record[4] : 0;
            $currency = $record[5] ?? 'EUR';

            if (!$operationDate || !$userId || !$userType || !$operationType || !$currency) {
                Log::warning('Missing essential data at index ' . $index, ['record' => $record]);
                continue;
            }

            $results[] = $this->calculateCommission($userType, $operationType, $amount, $currency);
        }

        return $results;
    }

    private function calculateCommission($userType, $operationType, $amount, $currency): float|int
    {
        $commission = 0;

        if ($operationType === 'withdraw') {
            if ($userType === 'private') {
                $commission = $this->calculatePrivateWithdrawCommission($amount, $currency);
            } elseif ($userType === 'business') {
                $commission = 0.005 * $amount;
            }
        } elseif ($operationType === 'deposit') {
            $commission = 0.0003 * $amount;
        }

        return $this->roundUp($commission, $currency);
    }

    private function calculatePrivateWithdrawCommission($amount, $currency): float
    {
        return 0.003 * $amount;
    }

    private function roundUp($value, $currency): float|int
    {
        $precision = 2;

        if ($currency === 'JPY') {
            $precision = 0;
        }

        return ceil($value * pow(10, $precision)) / pow(10, $precision);
    }
}
