<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function ($router) {
    Route::post('login', 'login');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('get-weather-by-city', [WeatherController::class, 'getWeatherByCity']);

Route::middleware('custom-sanctum-auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
});


