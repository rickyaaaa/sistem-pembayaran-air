<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Document::with('creator');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $documents = $query->latest()->paginate(15)->withQueryString();
        return view('resident.documents.index', compact('documents'));
    }

    public function download(Document $document)
    {
        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $document->title . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION)
        );
    }
}
