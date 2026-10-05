<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\SubmissionRequirement;
use App\Models\TemplateDocument;

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
        // PhpWord writes text into the .docx XML unescaped by default, so a topic or
        // question containing "&" or "<" produced a file Word refuses to open.
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

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

                // Only faculty submit against requirements, and only faculty
                // receive distributed templates — Program Head/Admin/Secretary
                // handle those from the upload/forward/distribute side, so
                // these counts wouldn't mean anything for those roles.
                if ($role === 'faculty') {
                    $view->with('urgentSubmissionsCount', SubmissionRequirement::urgentCountFor(Auth::user()));
                    $view->with('newTemplatesCount', TemplateDocument::newCountFor(Auth::user()));
                }
            }
        });
    }
}
