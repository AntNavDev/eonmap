<?php

declare(strict_types=1);

use App\Http\Controllers\Api\OccurrenceApiController;
use App\Http\Controllers\Api\OccurrenceExportController;
use Illuminate\Support\Facades\Route;

Route::get('/occurrences', [OccurrenceApiController::class, 'index']);
Route::get('/occurrences/{id}', [OccurrenceApiController::class, 'show']);
Route::get('/export/occurrences', [OccurrenceExportController::class, 'csv']);
