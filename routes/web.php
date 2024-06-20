<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrapeModelController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/models', [ScrapeModelController::class, 'fetchModels']);
Route::get('/generations', [ScrapeModelController::class, 'fetchGeneration']);