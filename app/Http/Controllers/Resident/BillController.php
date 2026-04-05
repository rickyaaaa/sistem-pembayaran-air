<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Resident;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $bills = collect();
        $availableYears = collect();
        $resident = null;
        $searched = false;

        $houseNumber = $request->input('house_number');

        if ($houseNumber) {
            $searched = true;

            // Find resident by house_number (case-insensitive trim)
            $resident = Resident::where('house_number', trim($houseNumber))
                ->where('is_active', true)
                ->first();

            if ($resident) {
                $query = Bill::where('resident_id', $resident->id);

                if ($year = $request->input('year')) {
                    $query->where('year', $year);
                }

                if ($status = $request->input('status')) {
                    $query->where('status', $status);
                }

                $bills = $query->orderByDesc('year')->orderByDesc('month')->paginate(12)->withQueryString();

                $availableYears = Bill::where('resident_id', $resident->id)
                    ->selectRaw('DISTINCT year')
                    ->orderByDesc('year')
                    ->pluck('year');
            }
        }

        return view('resident.bills.index', compact('bills', 'availableYears', 'resident', 'houseNumber', 'searched'));
    }

    public function show(Bill $bill)
    {
        $bill->load('payments', 'resident');

        return view('resident.bills.show', compact('bill'));
    }
}
