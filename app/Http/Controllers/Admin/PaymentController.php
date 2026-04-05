<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['resident.user', 'bill', 'confirmedBy']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('resident', function ($q) use ($search) {
                $q->where('block_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'confirmed' THEN 2 WHEN 'rejected' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['resident.user', 'bill', 'confirmedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    public function confirm(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->withErrors(['error' => 'Pembayaran ini sudah diproses.']);
        }

        $payment->update([
            'status' => 'confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        // Update bill status to paid
        $payment->bill->update(['status' => 'paid']);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        if ($payment->status !== 'pending') {
            return back()->withErrors(['error' => 'Pembayaran ini sudah diproses.']);
        }

        $payment->update([
            'status' => 'rejected',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'notes' => $validated['notes'],
        ]);

        // Revert bill status to unpaid
        $payment->bill->update(['status' => 'unpaid']);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }

    public function viewProof(Payment $payment)
    {
        if (!Storage::disk('public')->exists($payment->proof_file)) {
            abort(404, 'File bukti tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($payment->proof_file));
    }
}
