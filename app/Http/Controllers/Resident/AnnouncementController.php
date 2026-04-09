<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::whereNotNull('published_at');

        if ($request->filled('start_date')) {
            $query->whereDate('published_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('published_at', '<=', $request->end_date);
        }

        $announcements = $query->orderBy('published_at', 'desc')->paginate(10)->withQueryString();

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
