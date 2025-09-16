<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;

// Public route for getting token
Route::post('/auth/token', [AuthController::class, 'getToken']);

// Protected Articles API
Route::middleware(['jwt.verify'])->prefix('v1')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
});
