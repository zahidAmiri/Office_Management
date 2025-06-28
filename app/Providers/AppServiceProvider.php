<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Routing\Router;
use App\Http\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {
        // Share user with Inertia (already working)
        Inertia::share([
            'auth' => fn () => [
                'user' => Auth::check() ? Auth::user() : null,
            ],
        ]);

        // ✅ Manually register the role middleware alias
        $router->aliasMiddleware('role', RoleMiddleware::class);
    }
}
