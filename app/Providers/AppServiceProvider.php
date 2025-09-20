<?php

namespace App\Providers;

use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Eloquent\ProductEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(ProductRepository::class,ProductEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useTailwind();
    }
}
