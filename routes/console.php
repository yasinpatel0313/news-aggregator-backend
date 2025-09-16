<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// NewsAPI - every hour at the top of the hour
Schedule::call(function () {
    $controller = new \App\Http\Controllers\CronJobs\NewsApiController(
        new \App\Services\NewsApiService()
    );
    $controller->fetchArticles();
})->hourly()->name('fetch-newsapi')->withoutOverlapping();

// Guardian - every hour at 15 minutes past
Schedule::call(function () {
    $controller = new \App\Http\Controllers\CronJobs\GuardianController(
        new \App\Services\GuardianService()
    );
    $controller->fetchArticles();
})->cron('15 * * * *')->name('fetch-guardian')->withoutOverlapping();

// NYT - every hour at 30 minutes past
Schedule::call(function () {
    $controller = new \App\Http\Controllers\CronJobs\NytController(
        new \App\Services\NytService()
    );
    $controller->fetchArticles();
})->cron('30 * * * *')->name('fetch-nyt')->withoutOverlapping();
