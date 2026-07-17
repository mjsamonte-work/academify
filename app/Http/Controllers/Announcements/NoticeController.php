<?php

namespace App\Http\Controllers\Announcements;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        return view('notices.index', [
            'notifications' => request()->user()
                ->noticeNotifications()
                ->with('announcement')
                ->whereHas('announcement', fn ($query) => $query->where('status', Announcement::STATUS_PUBLISHED))
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(Notification $notification): View
    {
        abort_unless($notification->user_id === request()->user()?->id, 403);

        $notification->load('announcement.creator');

        return view('notices.show', compact('notification'));
    }

    public function read(Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === request()->user()?->id, 403);

        $notification->update(['read_at' => now()]);

        return redirect()->route('notices.show', $notification)->with('status', 'Notice marked as read.');
    }
}
