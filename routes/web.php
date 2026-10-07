<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/teams', [PredictionController::class, 'getTeams']);
Route::get('/api/standings', [PredictionController::class, 'getStandings']);
Route::post('/api/predict', [PredictionController::class, 'predict']);
