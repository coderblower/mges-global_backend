<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {

        // Get all notifications for the authenticated user
        $notifications = $request->user()->unreadNotifications;
        return response()->json($notifications);
    }

    public function markAsSeen(Request $request)
    {



        // Mark all notifications as seen for the authenticated user
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Notifications marked as seen.']);
    }
}
