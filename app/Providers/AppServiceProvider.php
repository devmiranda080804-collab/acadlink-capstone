<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\SubmissionRequirement;

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
        // Share permissions with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $role = Auth::user()->role;

                $permissions = DB::table('role_permissions')
                    ->where('role', $role)
                    ->pluck('is_enabled', 'module')
                    ->toArray();

                $view->with('navPermissions', $permissions);
                $view->with('unreadAnnouncementsCount', Announcement::unreadCountFor(Auth::user()));

                // Only faculty submit against requirements — Program Head/Admin
                // create them, so this count wouldn't mean anything for those roles.
                if ($role === 'faculty') {
                    $view->with('urgentSubmissionsCount', SubmissionRequirement::urgentCountFor(Auth::user()));
                }
            }
        });
    }
}
