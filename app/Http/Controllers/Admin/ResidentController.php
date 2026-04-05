<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Resident::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('block_number', 'like', "%{$search}%")
                  ->orWhere('block', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $residents = $query->orderBy('block')->orderBy('house_number')->paginate(15);

        return view('admin.residents.index', compact('residents'));
    }

    public function create()
    {
        return view('admin.residents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'block' => 'required|string|max:10',
            'house_number' => 'required|string|max:10',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $blockNumber = strtolower($validated['block'] . $validated['house_number']);

        // Check if block_number already exists
        if (Resident::where('block_number', $blockNumber)->exists()) {
            return back()->withErrors(['block_number' => 'Nomor blok sudah terdaftar.'])->withInput();
        }

        DB::transaction(function () use ($validated, $blockNumber) {
            $user = User::create([
                'username' => $blockNumber,
                'name' => $validated['name'],
                'password' => Hash::make($blockNumber), // default password = block number
                'role' => 'resident',
            ]);

            Resident::create([
                'user_id' => $user->id,
                'block_number' => $blockNumber,
                'block' => strtoupper($validated['block']),
                'house_number' => $validated['house_number'],
                'phone_number' => $validated['phone_number'] ?? null,
                'is_active' => true,
            ]);
        });

        return redirect()->route('admin.residents.index')
            ->with('success', 'Warga berhasil ditambahkan. Password default: ' . $blockNumber);
    }

    public function edit(Resident $resident)
    {
        $resident->load('user');
        return view('admin.residents.edit', compact('resident'));
    }

    public function update(Request $request, Resident $resident)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        DB::transaction(function () use ($validated, $resident) {
            $resident->user->update(['name' => $validated['name']]);
            $resident->update([
                'phone_number' => $validated['phone_number'] ?? null,
                'is_active' => $validated['is_active'],
            ]);
        });

        return redirect()->route('admin.residents.index')
            ->with('success', 'Data warga berhasil diperbarui.');
    }

    public function resetPassword(Resident $resident)
    {
        $resident->user->update([
            'password' => Hash::make($resident->block_number),
        ]);

        return back()->with('success', "Password warga {$resident->block_number} berhasil direset ke default.");
    }

    public function destroy(Resident $resident)
    {
        DB::transaction(function () use ($resident) {
            $resident->user->delete();
        });

        return redirect()->route('admin.residents.index')
            ->with('success', 'Warga berhasil dihapus.');
    }
}
