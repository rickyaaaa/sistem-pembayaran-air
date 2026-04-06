<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RegistrationCategory;
use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Resident;
use App\Http\Requests\Admin\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DatabaseHelper;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with(['resident', 'creator']);

        if ($year = $request->input('year')) {
            $query->whereYear('payment_date', $year);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $registrations = $query->orderByDesc('payment_date')->paginate(20);

        $yearFn = DatabaseHelper::getYearFunction('payment_date');

        $availableYears = Registration::selectRaw("{$yearFn} as year")
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
            rsort($availableYears);
        }

        $categories = RegistrationCategory::cases();

        return view('admin.registrations.index', compact('registrations', 'availableYears', 'categories'));
    }

    public function create()
    {
        $residents = Resident::where('is_active', true)->orderBy('block')->get();
        $categories = RegistrationCategory::cases();
        return view('admin.registrations.create', compact('residents', 'categories'));
    }

    public function store(RegistrationRequest $request)
    {
        $validated = $request->validated();

        $categoryEnum = RegistrationCategory::from($validated['category']);

        if ($categoryEnum->requiresResident() && empty($validated['resident_id'])) {
            return back()->withErrors(['resident_id' => 'Kategori ini wajib memilih warga.'])->withInput();
        }

        $notesStr = $validated['notes'] ?? '';
        if ($categoryEnum === RegistrationCategory::Iuran && !empty($validated['months'])) {
            $monthNames = implode(', ', $validated['months']);
            $year = $validated['iuran_year'] ?? date('Y');
            $periodeText = "Periode Iuran: {$monthNames} {$year}";
            $notesStr = $notesStr ? "{$notesStr} - {$periodeText}" : $periodeText;
        }

        Registration::create([
            'category'     => $categoryEnum,
            'resident_id'  => $validated['resident_id'] ?? null,
            'payment_date' => $validated['payment_date'],
            'amount'       => $validated['amount'],
            'notes'        => $notesStr ?: null,
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Data pemasukan berhasil dihapus.');
    }
}
