<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['resident', 'bill', 'confirmedBy']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('resident', function ($q) use ($search) {
                $q->where('block_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'confirmed' THEN 2 WHEN 'rejected' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['resident', 'bill', 'confirmedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    public function confirm(Payment $payment)
    {
        if ($payment->status !== PaymentStatus::Pending) {
            return back()->withErrors(['error' => 'Pembayaran ini sudah diproses.']);
        }

        $payment->update([
            'status' => PaymentStatus::Confirmed,
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        // Update bill status to paid
        $payment->bill->update(['status' => BillStatus::Paid]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        if ($payment->status !== PaymentStatus::Pending) {
            return back()->withErrors(['error' => 'Pembayaran ini sudah diproses.']);
        }

        $payment->update([
            'status' => PaymentStatus::Rejected,
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'notes' => $validated['notes'],
        ]);

        // Revert bill status to unpaid
        $payment->bill->update(['status' => BillStatus::Unpaid]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }

    // Fix 2: Serve proof files from private disk
    public function viewProof(Payment $payment)
    {
        if (!Storage::disk('private')->exists($payment->proof_file)) {
            abort(404, 'File bukti tidak ditemukan.');
        }

        return Storage::disk('private')->response($payment->proof_file);
    }
}
