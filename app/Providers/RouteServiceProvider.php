<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Route::middleware('auth.api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/auth.php'))
            ->group(base_path('routes/user/guest.php'));

        Route::middleware('auth')->group($this->userRoute());
        Route::middleware('auth')->group($this->adminRoute());
    }

    private function adminRoute()
    {
        Route::prefix('admin')
            ->namespace('Admin')
            ->name('admin.')
            ->middleware(['web', 'auth', 'role:Admin'])
            ->group(base_path('routes/admin/system.php'))
            ->group(base_path('routes/admin/board.php'))
            ->group(base_path('routes/admin/project.php'));
    }

    private function userRoute()
    {
        Route::middleware(['web', 'auth'])
            ->group(base_path('routes/user/project.php'))
            ->group(base_path('routes/user/board.php'));
    }
}