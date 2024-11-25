<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\CsvController;

// Rutas para HelloWorldController
Route::apiResource('hello', HelloWorldController::class);

// Rutas para JsonController (utilizando apiResource)
Route::apiResource('json', JsonController::class);

// Rutas para CsvController
Route::prefix('csv')->group(function () {
    Route::get('/', [CsvController::class, 'index']);
    Route::post('/', [CsvController::class, 'store']);
    Route::get('/{id}', [CsvController::class, 'show']);
    Route::put('/{id}', [CsvController::class, 'update']);
    Route::delete('/{id}', [CsvController::class, 'destroy']);
});
