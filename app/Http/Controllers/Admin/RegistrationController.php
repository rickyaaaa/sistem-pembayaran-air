<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with(['resident.user', 'creator']);

        if ($year = $request->input('year')) {
            $query->whereYear('payment_date', $year);
        }

        $registrations = $query->orderByDesc('payment_date')->paginate(20);

        return view('admin.registrations.index', compact('registrations'));
    }

    public function create()
    {
        $residents = Resident::with('user')->where('is_active', true)->orderBy('block')->get();
        return view('admin.registrations.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        Registration::create([
            'resident_id' => $validated['resident_id'],
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Biaya pendaftaran berhasil dicatat.');
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
