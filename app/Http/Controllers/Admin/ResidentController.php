<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ResidentRequest;
use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Resident::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('block_number', 'like', "%{$search}%")
                  ->orWhere('block', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        if ($unpaidMonth = $request->input('unpaid_month')) {
            $year = $request->input('year', now()->year);
            $monthFn = \App\Helpers\DatabaseHelper::getMonthFunction('payment_date');
            $yearFn = \App\Helpers\DatabaseHelper::getYearFunction('payment_date');
            
            // Warga deemed unpaid if they have NO confirmed payment in that month and year
            $query->whereDoesntHave('payments', function ($q) use ($unpaidMonth, $year, $monthFn, $yearFn) {
                $q->where('status', \App\Enums\PaymentStatus::Confirmed)
                  ->whereRaw("{$monthFn} = ?", [(int)$unpaidMonth])
                  ->whereRaw("{$yearFn} = ?", [(int)$year]);
            });
        }

        $residents = $query->orderBy('block')->orderBy('house_number')->paginate(15)->withQueryString();

        return view('admin.residents.index', compact('residents'));
    }

    public function create()
    {
        return view('admin.residents.create');
    }

    public function store(ResidentRequest $request)
    {
        if (auth()->user()->isPengurus()) {
            return back()->withErrors(['error' => 'Pengurus tidak bisa menambah warga. Hubungi Admin.']);
        }
        $validated = $request->validated();

        $blockNumber = strtolower($validated['block'] . $validated['house_number']);

        // Check if block_number already exists
        if (Resident::where('block_number', $blockNumber)->exists()) {
            return back()->withErrors(['block_number' => 'Nomor blok sudah terdaftar.'])->withInput();
        }

        Resident::create([
            'name' => $validated['name'],
            'block_number' => $blockNumber,
            'block' => strtoupper($validated['block']),
            'house_number' => $validated['house_number'],
            'phone_number' => $validated['phone_number'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.residents.index')
            ->with('success', 'Warga berhasil ditambahkan.');
    }

    public function edit(Resident $resident)
    {
        return view('admin.residents.edit', compact('resident'));
    }

    public function update(ResidentRequest $request, Resident $resident)
    {
        $validated = $request->validated();

        $resident->update([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.residents.index')
            ->with('success', 'Data warga berhasil diperbarui.');
    }

    // Fix 8: Soft delete — checks for unpaid bills first
    public function destroy(Resident $resident)
    {
        if (auth()->user()->isPengurus()) {
            return back()->withErrors(['error' => 'Pengurus tidak bisa menghapus warga. Hubungi Admin.']);
        }
        $unpaidCount = $resident->bills()
            ->whereIn('status', [\App\Enums\BillStatus::Unpaid, \App\Enums\BillStatus::Pending])
            ->count();

        if ($unpaidCount > 0) {
            return back()->withErrors(['error' => "Warga masih memiliki {$unpaidCount} tagihan yang belum lunas. Selesaikan dulu sebelum menghapus."]);
        }

        $resident->delete(); // Soft delete — data tetap ada di DB

        return redirect()->route('admin.residents.index')
            ->with('success', 'Warga berhasil dinonaktifkan.');
    }
}
