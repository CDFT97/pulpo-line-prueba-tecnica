<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function ($router) {
    Route::post('login', 'login');
    Route::post('/register', [AuthController::class, 'register']);
});


Route::middleware('custom-sanctum-auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});


