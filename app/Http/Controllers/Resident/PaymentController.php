<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function create(Bill $bill)
    {
        if ($bill->status === 'paid') {
            return back()->with('info', 'Tagihan ini sudah lunas.');
        }

        if ($bill->status === 'pending') {
            return back()->with('info', 'Pembayaran Anda sedang menunggu konfirmasi.');
        }

        $bill->load('resident');

        return view('resident.payments.create', compact('bill'));
    }

    public function store(Request $request, Bill $bill)
    {
        if ($bill->status !== 'unpaid') {
            return back()->withErrors(['error' => 'Tagihan tidak bisa dibayar saat ini.']);
        }

        $validated = $request->validate([
            'payment_date' => 'required|date|before_or_equal:today',
            'amount_paid'  => 'required|numeric|min:1',
            'proof_file'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = $request->file('proof_file')->store('payments', 'public');

        // Get resident_id directly from the bill relation
        $bill->load('resident');
        $residentId = $bill->resident_id;

        Payment::create([
            'bill_id'      => $bill->id,
            'resident_id'  => $residentId,
            'payment_date' => $validated['payment_date'],
            'amount_paid'  => $validated['amount_paid'],
            'proof_file'   => $proofPath,
            'status'       => 'pending',
        ]);

        $bill->update(['status' => 'pending']);

        return redirect()->route('resident.bills.index', ['house_number' => $bill->resident->house_number])
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi admin.');
    }

    public function history(Request $request)
    {
        $payments = collect();
        $houseNumber = $request->input('house_number');
        $resident = null;

        if ($houseNumber) {
            $resident = \App\Models\Resident::where('house_number', trim($houseNumber))
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
