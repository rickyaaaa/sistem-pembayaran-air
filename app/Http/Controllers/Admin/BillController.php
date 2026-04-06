<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillStatus;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Resident;
use App\Http\Requests\Admin\BillRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with('resident');

        if ($month = $request->input('month')) {
            $query->where('month', $month);
        }

        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($block = $request->input('block')) {
            $query->whereHas('resident', fn($q) => $q->where('block', strtoupper($block)));
        }

        if ($search = $request->input('search')) {
            $query->whereHas('resident', function ($q) use ($search) {
                $q->where('block_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $bills = $query
            ->join('residents', 'residents.id', '=', 'bills.resident_id')
            ->select('bills.*')
            ->orderBy('residents.block_number')
            ->orderByDesc('bills.year')
            ->orderByDesc('bills.month')
            ->paginate(20);

        $yearFn = DatabaseHelper::getYearFunction('year');
        $availableYears = Bill::selectRaw("DISTINCT {$yearFn} as year")
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
            rsort($availableYears);
        }

        $availableBlocks = \App\Models\Resident::where('is_active', true)
            ->selectRaw('DISTINCT block')
            ->orderBy('block')
            ->pluck('block');

        return view('admin.bills.index', compact('bills', 'availableYears', 'availableBlocks'));
    }

    public function create()
    {
        $residents = Resident::where('is_active', true)->orderBy('block')->orderBy('house_number')->get();
        return view('admin.bills.create', compact('residents'));
    }

    public function store(BillRequest $request)
    {
        $validated = $request->validated();

        $created = 0;
        $skipped = 0;

        if ($validated['type'] === 'bulk') {
            // Fix 12: Single insertOrIgnore instead of N+1 loop
            $activeResidents = Resident::where('is_active', true)->pluck('id');

            $records = $activeResidents->map(fn($id) => [
                'resident_id' => $id,
                'month'       => $validated['month'],
                'year'        => $validated['year'],
                'amount'      => $validated['amount'],
                'status'      => BillStatus::Unpaid,
                'created_at'  => now(),
                'updated_at'  => now(),
            ])->toArray();

            // insertOrIgnore: skip if already exists (unique constraint resident_id+month+year)
            $created = Bill::insertOrIgnore($records);
            $skipped = count($records) - $created;

        } else {
            $exists = Bill::where('resident_id', $validated['resident_id'])
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->exists();

            if (!$exists) {
                Bill::create([
                    'resident_id' => $validated['resident_id'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'amount' => $validated['amount'],
                    'status' => BillStatus::Unpaid,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Berhasil membuat {$created} tagihan.";
        if ($skipped > 0) {
            $message .= " {$skipped} tagihan sudah ada dan dilewati.";
        }

        return redirect()->route('admin.bills.index')->with('success', $message);
    }

    public function edit(Bill $bill)
    {
        $bill->load('resident');
        return view('admin.bills.edit', compact('bill'));
    }

    public function update(BillRequest $request, Bill $bill)
    {
        $validated = $request->validated();

        // Nominal 0 → auto lunas
        if ((float) $validated['amount'] === 0.0) {
            $validated['status'] = BillStatus::Paid->value;
        }

        // Pengurus: submit change request instead of editing directly
        if (auth()->user()->isPengurus()) {
            \App\Models\ChangeRequest::create([
                'model_type'     => Bill::class,
                'model_id'       => $bill->id,
                'original_data'  => $bill->only(['amount', 'status']),
                'requested_data' => [
                    'amount' => $validated['amount'],
                    'status' => $validated['status'],
                ],
                'reason'         => $request->input('reason', 'Diajukan oleh pengurus'),
                'status'         => 'pending',
                'requested_by'   => auth()->id(),
            ]);

            return redirect()->route('admin.bills.index')
                ->with('info', 'Permintaan perubahan tagihan berhasil dikirim dan menunggu persetujuan Admin.');
        }

        // Admin: apply changes directly
        DB::transaction(function () use ($validated, $bill) {
            if ($validated['status'] === 'paid' && $bill->status !== BillStatus::Paid) {
                if ((float) $validated['amount'] > 0) {
                    \App\Models\Payment::create([
                        'bill_id'      => $bill->id,
                        'resident_id'  => $bill->resident_id,
                        'payment_date' => now()->toDateString(),
                        'amount_paid'  => $validated['amount'],
                        'proof_file'   => \App\Models\Payment::MANUAL_PROOF,
                        'payer_name'   => 'Admin (' . auth()->user()->name . ')',
                        'payer_phone'  => '-',
                        'status'       => \App\Enums\PaymentStatus::Confirmed,
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => now(),
                        'notes'        => 'Dicatat manual oleh admin',
                    ]);
                }
            }

            if ($validated['status'] === 'unpaid' && $bill->status === BillStatus::Paid) {
                $bill->payments()
                    ->whereIn('status', [\App\Enums\PaymentStatus::Pending, \App\Enums\PaymentStatus::Confirmed])
                    ->delete();
            }

            $bill->update([
                'amount' => $validated['amount'],
                'status' => BillStatus::from($validated['status']),
            ]);
        });

        return redirect()->route('admin.bills.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Bill $bill)
    {
        if (auth()->user()->isPengurus()) {
            return back()->withErrors(['error' => 'Pengurus tidak bisa menghapus tagihan. Hubungi Admin.']);
        }

        if ($bill->status === BillStatus::Paid) {
            return back()->withErrors(['error' => 'Tagihan yang telah lunas tidak bisa dihapus.']);
        }

        $bill->delete();

        return redirect()->route('admin.bills.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    public function markPaid(Bill $bill)
    {
        if ($bill->status === BillStatus::Paid) {
            return back()->with('info', 'Tagihan ini sudah lunas.');
        }

        DB::transaction(function () use ($bill) {
            Payment::create([
                'bill_id'      => $bill->id,
                'resident_id'  => $bill->resident_id,
                'payment_date' => now()->toDateString(),
                'amount_paid'  => $bill->amount,
                'proof_file'   => Payment::MANUAL_PROOF,
                'payer_name'   => 'Admin (' . auth()->user()->name . ')',
                'payer_phone'  => '-',
                'status'       => PaymentStatus::Confirmed,
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
                'notes'        => 'Dicatat manual oleh admin (bayar via WA/cash)',
            ]);

            $bill->update(['status' => BillStatus::Paid]);
        });

        return back()->with('success', "Tagihan {$bill->resident->block_number} — {$bill->period} berhasil ditandai lunas.");
    }
}
