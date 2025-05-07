<?php

namespace App\Providers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Observers\BrandObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Repositories\Eloquent\AdminRepository;
use App\Repositories\Eloquent\BrandRespository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\BrandRespositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class,AdminRepository::class);
        $this->app->bind(BrandRespositoryInterface::class,BrandRespository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60);
        });
        
        Blade::directive('active', function ($expression) {
            return "<?php echo request()->is({$expression}) ? 'active' : ''; ?>";
        });

        Brand::observe(BrandObserver::class);
    }
}
