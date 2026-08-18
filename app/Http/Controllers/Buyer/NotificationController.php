<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->paginate(20);

        return view('buyer.notifications', compact('notifications'));
    }

    public function markRead($id)
    {
        Auth::user()->notifications()->findOrFail($id)->update(['is_read' => true]);
        return back();
    }

    public function markAllRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
