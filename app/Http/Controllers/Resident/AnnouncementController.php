<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);
            
        return view('resident.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        if (!$announcement->published_at) {
            abort(404);
        }
        return view('resident.announcements.show', compact('announcement'));
    }
}
