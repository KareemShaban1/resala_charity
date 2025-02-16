<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function getNotificationsByDate(Request $request)
    {
        $notifications = Notification::whereDate('date', '<=', Carbon::today())
        ->whereHasMorph('notifiable', [Event::class], function ($query) {
            $query->where('status', 'ongoing');
        })
        ->where('status', 'unread')
        ->orderBy('date', 'desc')
        ->get();

        return response()->json($notifications);
    }
}
