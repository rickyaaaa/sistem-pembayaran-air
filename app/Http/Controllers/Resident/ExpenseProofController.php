<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseProofController extends Controller
{
    public function show(Request $request, Expense $expense)
    {
        $token = $request->query('token');
        $expectedToken = sha1($expense->id . config('app.key'));

        if (!$token || $token !== $expectedToken) {
            abort(403, 'Token akses tidak valid atau sudah kadaluarsa.');
        }

        if (!$expense->proof_file || $expense->proof_file === \App\Models\Payment::MANUAL_PROOF) {
            abort(404, 'Bukti tidak tersedia.');
        }

        // Try private disk first
        if (Storage::disk('private')->exists($expense->proof_file)) {
            return Storage::disk('private')->response($expense->proof_file);
        }

        if (Storage::disk('public')->exists($expense->proof_file)) {
            return Storage::disk('public')->response($expense->proof_file);
        }

        abort(404, 'File bukti tidak ditemukan.');
    }
}
