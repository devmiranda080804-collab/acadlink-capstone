<?php

namespace App\Http\Controllers;

// The bell at the top of every page.
class NotificationController extends Controller
{
    // Marks it read and goes to the page it is about.
    public function open(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $path = (string) ($notification->data['path'] ?? '');

        return redirect(str_starts_with($path, '/') ? url($path) : url('/'));
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return request()->expectsJson() ? response()->json(['ok' => true]) : back();
    }
}
