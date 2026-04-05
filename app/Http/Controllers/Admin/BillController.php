<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Resident;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with('resident.user');

        if ($month = $request->input('month')) {
            $query->where('month', $month);
        }

        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }

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

        $bills = $query->orderByDesc('year')->orderByDesc('month')->paginate(20);

        $availableYears = Bill::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
            rsort($availableYears);
        }

        return view('admin.bills.index', compact('bills', 'availableYears'));
    }

    public function create()
    {
        $residents = Resident::with('user')->where('is_active', true)->orderBy('block')->orderBy('house_number')->get();
        return view('admin.bills.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2099',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:bulk,single',
            'resident_id' => 'required_if:type,single|nullable|exists:residents,id',
        ]);

        $created = 0;
        $skipped = 0;

        if ($validated['type'] === 'bulk') {
            $activeResidents = Resident::where('is_active', true)->get();

            foreach ($activeResidents as $resident) {
                $exists = Bill::where('resident_id', $resident->id)
                    ->where('month', $validated['month'])
                    ->where('year', $validated['year'])
                    ->exists();

                if (!$exists) {
                    Bill::create([
                        'resident_id' => $resident->id,
                        'month' => $validated['month'],
                        'year' => $validated['year'],
                        'amount' => $validated['amount'],
                        'status' => 'unpaid',
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }
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
                    'status' => 'unpaid',
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
        $bill->load('resident.user');
        return view('admin.bills.edit', compact('bill'));
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        if ($bill->status === 'paid') {
            return back()->withErrors(['amount' => 'Tagihan yang telah lunas tidak bisa diubah.']);
        }

        $bill->update(['amount' => $validated['amount']]);

        return redirect()->route('admin.bills.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Bill $bill)
    {
        if ($bill->status === 'paid') {
            return back()->withErrors(['error' => 'Tagihan yang telah lunas tidak bisa dihapus.']);
        }

        $bill->delete();

        return redirect()->route('admin.bills.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }
}
