<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    public function boot(): void
    {
        // I-share ang permissions sa lahat ng views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $role = Auth::user()->role;

                $permissions = DB::table('role_permissions')
                    ->where('role', $role)
                    ->pluck('is_enabled', 'module')
                    ->toArray();

                $view->with('navPermissions', $permissions);
            }
        });
    }
}
