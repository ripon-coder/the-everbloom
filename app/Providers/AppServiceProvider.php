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
use App\Repositories\Eloquent\ProductRespository;
use App\Repositories\Eloquent\CategoryRespository;
use App\Repositories\Eloquent\AttributeRespository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Eloquent\AttributeValueRespository;
use App\Repositories\Contracts\BrandRespositoryInterface;
use App\Repositories\Contracts\ProductRespositoryInterface;
use App\Repositories\Contracts\CategoryRespositoryInterface;
use App\Repositories\Contracts\AttributeRespositoryInterface;
use App\Repositories\Contracts\AttributeValueRespositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class,AdminRepository::class);
        $this->app->bind(BrandRespositoryInterface::class,BrandRespository::class);
        $this->app->bind(CategoryRespositoryInterface::class,CategoryRespository::class);
        $this->app->bind(AttributeRespositoryInterface::class,AttributeRespository::class);
        $this->app->bind(AttributeValueRespositoryInterface::class,AttributeValueRespository::class);
        $this->app->bind(ProductRespositoryInterface::class,ProductRespository::class);

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

        //Brand::observe(BrandObserver::class);
    }
}
