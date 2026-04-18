<?php

declare(strict_types=1);

use App\Http\Controllers\BrowseController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\OccurrenceController;
use App\Http\Controllers\TaxonController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/map');

Route::get('/map', [MapController::class, 'index'])->name('map');
Route::get('/browse', [BrowseController::class, 'index'])->name('browse');
Route::get('/occurrences/{id}', [OccurrenceController::class, 'show']);
Route::get('/taxa/{name}', [TaxonController::class, 'show']);
