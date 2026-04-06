<?php

namespace App\Http\Controllers\Resident;

use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Bill $bill)
    {
        if ($bill->status === BillStatus::Paid) {
            return back()->with('info', 'Tagihan ini sudah lunas.');
        }

        if ($bill->status === BillStatus::Pending) {
            return back()->with('info', 'Pembayaran Anda sedang menunggu konfirmasi.');
        }

        $bill->load('resident');

        return view('resident.payments.create', compact('bill'));
    }

    public function store(Request $request, Bill $bill)
    {
        if ($bill->status !== BillStatus::Unpaid) {
            return back()->with('info', 'Tagihan ini sudah dalam proses atau sudah lunas.');
        }

        // Block if pending payment already exists
        if ($bill->payments()->where('status', PaymentStatus::Pending)->exists()) {
            return back()->with('info', 'Bukti pembayaran sudah dikirim dan sedang menunggu konfirmasi admin.');
        }

        $validated = $request->validate([
            'payment_date' => 'required|date|before_or_equal:today',
            'proof_file'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'payer_name'   => 'required|string|max:100',
            'payer_phone'  => 'required|string|max:20',
        ]);

        // Amount selalu dari bill, bukan dari request
        $amountPaid = $bill->amount;

        // Store to private disk
        $proofPath = $request->file('proof_file')->store('payments', 'private');

        Payment::create([
            'bill_id'      => $bill->id,
            'resident_id'  => $bill->resident_id,
            'payment_date' => $validated['payment_date'],
            'amount_paid'  => $amountPaid,
            'proof_file'   => $proofPath,
            'payer_name'   => $validated['payer_name'],
            'payer_phone'  => $validated['payer_phone'],
            'status'       => PaymentStatus::Pending,
        ]);

        $bill->update(['status' => BillStatus::Pending]);

        return redirect()->route('resident.bills.index', ['house_number' => $bill->resident->block_number])
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi admin.');
    }

    public function history(Request $request)
    {
        $payments = collect();
        $houseNumber = $request->input('house_number');
        $resident = null;

        if ($houseNumber) {
            // Fix 3: case-insensitive search
            $resident = \App\Models\Resident::whereRaw('LOWER(block_number) = ?', [strtolower(trim($houseNumber))])
                ->where('is_active', true)
                ->first();

            if ($resident) {
                $payments = Payment::with('bill')
                    ->where('resident_id', $resident->id)
                    ->orderByDesc('created_at')
                    ->paginate(15)
                    ->withQueryString();
            }
        }

        return view('resident.payments.history', compact('payments', 'houseNumber', 'resident'));
    }
}
