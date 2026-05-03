<?php

namespace App\Providers;

use App\Models\Wishlist;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Eloquent\UserEloquent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\BrandEloquent;
use App\Repositories\Eloquent\OrderEloquent;
use App\Repositories\Eloquent\CouponEloquent;
use App\Repositories\Eloquent\ProductEloquent;
use App\Repositories\Contracts\BrandRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Eloquent\CategoryEloquent;
use App\Repositories\Eloquent\DistrictEloquent;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Eloquent\AttributeEloquent;
use App\Repositories\Eloquent\FlashSaleEloquent;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Repositories\Contracts\DistrictRepository;
use App\Repositories\Eloquent\SaveAddressEloquent;
use App\Repositories\Contracts\AttributeRepository;
use App\Repositories\Contracts\FlashSaleRepository;
use App\Repositories\Contracts\SaveAddressRepository;
use App\Repositories\Eloquent\AttributeValueEloquent;
use App\Repositories\Contracts\AttributeValueRepository;
use App\Repositories\Contracts\WishlistRepository;
use App\Repositories\Eloquent\WishlistEloquent;
use App\Repositories\Contracts\CheckoutCalculationRepository;
use App\Repositories\Eloquent\CheckoutCalculationEloquent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(AttributeRepository::class, AttributeEloquent::class);
        app()->bind(AttributeValueRepository::class, AttributeValueEloquent::class);
        app()->bind(BrandRepository::class, BrandEloquent::class);
        app()->bind(CategoryRepository::class, CategoryEloquent::class);
        app()->bind(CouponRepository::class, CouponEloquent::class);
        app()->bind(FlashSaleRepository::class, FlashSaleEloquent::class);
        app()->bind(OrderRepository::class, OrderEloquent::class);
        app()->bind(ProductRepository::class, ProductEloquent::class);
        app()->bind(DistrictRepository::class, DistrictEloquent::class);
        app()->bind(SaveAddressRepository::class, SaveAddressEloquent::class);
        app()->bind(UserRepository::class, UserEloquent::class);
        app()->bind(WishlistRepository::class, WishlistEloquent::class);
        app()->bind(CheckoutCalculationRepository::class, CheckoutCalculationEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(!app()->isProduction());

        // Money Sign Pass
        View::share('currency_sign', config('eccomerce.currency_sign'));

        // Create Blade directive for active state checking
        Blade::directive('active', function ($expression) {
            return "<?php echo request()->is($expression) ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?>";
        });

        // Create Blade directive for active route checking
        Blade::directive('activeRoute', function ($expression) {
            return "<?php echo request()->routeIs($expression) ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?>";
        });

        // Create Blade directive for active state with wildcard
        Blade::directive('activeWildcard', function ($expression) {
            return "<?php echo request()->is($expression) || request()->is($expression . '/*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?>";
        });
    }
}
