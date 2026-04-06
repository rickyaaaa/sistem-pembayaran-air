<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'published_at' => 'nullable|date',
        ]);

        Announcement::query()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'budget' => $request->input('budget'),
            'published_at' => $request->filled('published_at') ? $request->input('published_at') : now(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'published_at' => 'nullable|date',
        ]);

        $announcement->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'budget' => $request->input('budget'),
            'published_at' => $request->filled('published_at') ? $request->input('published_at') : ($announcement->published_at ?? now()),
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Berita berhasil dihapus.');
    }
}
