<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Http\Controllers\CommissionCalculatorController;

class CommissionCalculatorControllerTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function testUploadWithValidCSV()
    {
        Storage::fake('local');

        $csvContent = $this->getValidCSVContent();

        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $response = $this->postJson('/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    /**
     *
     * @return void
     */
    public function testUploadWithIncompleteCSV()
    {
        Storage::fake('local');

        // Missing currency
        $csvContent = "2014-12-31,4,private,withdraw,1200.00\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $response = $this->postJson('/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(200);

        // No valid records in the response
        $response->assertJsonCount(0);
    }

    /**
     *
     * @return void
     */
    public function testUploadWithNonCSVFile()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.png');

        $response = $this->postJson('/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(422);
    }

    /**
     *
     * @return void
     */
    public function testUploadProcessingError()
    {
        Storage::fake('local');

        $this->partialMock(CommissionCalculatorController::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('processCSV')->andThrow(new \Exception('Processing error'));
        });

        $csvContent = $this->getValidCSVContent();

        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $response = $this->postJson('/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Processing error']);
    }

    /**
     *
     * @return string
     */
    private function getValidCSVContent(): string
    {
        $csvContent = "2014-12-31,4,private,withdraw,1200.00,EUR\n";
        $csvContent .= "2015-01-01,4,private,withdraw,1000.00,EUR\n";
        return $csvContent;
    }
}
