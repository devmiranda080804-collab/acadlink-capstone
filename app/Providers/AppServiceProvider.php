<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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

        // Plain-language validation messages (lang/en/validation.php): a file limit is
        // shown in MB instead of kilobytes, and allowed types as "PDF, DOC or DOCX".
        Validator::replacer('max', function ($message, $attribute, $rule, $parameters) {
            $kb = (float) $parameters[0];

            return str_replace([':max', ':mb'], [$parameters[0], rtrim(rtrim(number_format($kb / 1024, 1), '0'), '.')], $message);
        });
        $types = function ($message, $attribute, $rule, $parameters) {
            $list = array_map(fn($t) => strtoupper(str_contains($t, '/') ? substr($t, strpos($t, '/') + 1) : $t), $parameters);
            $last = array_pop($list);

            return str_replace([':types', ':values'], $list ? implode(', ', $list) . ' or ' . $last : $last, $message);
        };
        Validator::replacer('mimes', $types);
        Validator::replacer('mimetypes', $types);

        // Share permissions with all views
        View::composer('*', function ($view) {
            // Error pages must render even when the database is the problem.
            if (str_starts_with($view->getName(), 'errors')) {
                return;
            }

            if (Auth::check()) {
                $role = Auth::user()->role;

                $permissions = DB::table('role_permissions')
                    ->where('role', $role)
                    ->pluck('is_enabled', 'module')
                    ->toArray();

                $view->with('navPermissions', $permissions);
                $view->with('unreadAnnouncementsCount', Announcement::unreadCountFor(Auth::user()));

                // Only faculty submit against requirements and receive the
                // templates the Dean sends, so these counts only apply to them.
                if ($role === 'faculty') {
                    $view->with('urgentSubmissionsCount', SubmissionRequirement::urgentCountFor(Auth::user()));
                    $view->with('newTemplatesCount', TemplateDocument::newCountFor(Auth::user()));
                }
            }
        });
    }
}
