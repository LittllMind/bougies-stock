<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

use App\Services\CartService;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Models\Vente;
use App\Observers\VenteObserver;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);

        Gate::define('admin', function ($user) {
            return in_array($user->role, ['admin', 'employe']);
        });

        Gate::define('reports', function ($user) {
            return $user->role === 'admin';
        });

        if (env('APP_ENV') === 'local' && str_contains(config('app.url'), 'ngrok')) {
            URL::forceRootUrl(config('app.url'));
        }
    }
}
