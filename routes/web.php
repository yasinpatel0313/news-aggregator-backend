<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CronJobs\NewsApiController;
use App\Http\Controllers\CronJobs\GuardianController;
use App\Http\Controllers\CronJobs\NytController;

Route::get('/', function () {
    return view('welcome');
});

// Cron job routes
Route::prefix('cron')->group(function () {
    Route::get('/fetch-newsapi', [NewsApiController::class, 'fetchArticles']);
    Route::get('/fetch-guardian', [GuardianController::class, 'fetchArticles']);
    Route::get('/fetch-nyt', [NytController::class, 'fetchArticles']);
});
