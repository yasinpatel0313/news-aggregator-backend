<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Fix for MySQL index key length issue
        Schema::defaultStringLength(191);


        // Repository Binding
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
