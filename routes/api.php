<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas de autenticación
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});

// Rutas protegidas por el middleware de autenticación
Route::middleware('custom-sanctum-auth')->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
    });

    Route::controller(WeatherController::class)->group(function () {
        Route::post('get-weather-by-city', 'getWeatherByCity');
        Route::get('recent-searches', 'getRecentSearches');
        Route::get('favorites', 'getFavorites');
        Route::post('toggle-favorite', 'toggleFavorite');
    });
});
