<?php

use App\Http\Controllers\CommissionCalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.commission_calculator.index');
});

Route::post('/upload', [CommissionCalculatorController::class, 'upload']);
