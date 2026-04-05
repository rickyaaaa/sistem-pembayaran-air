<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $resident = Auth::user()->resident;

        $query = Bill::where('resident_id', $resident->id);

        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $bills = $query->orderByDesc('year')->orderByDesc('month')->paginate(12);

        $availableYears = Bill::where('resident_id', $resident->id)
            ->selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year');

        return view('resident.bills.index', compact('bills', 'availableYears'));
    }

    public function show(Bill $bill)
    {
        $resident = Auth::user()->resident;

        if ($bill->resident_id !== $resident->id) {
            abort(403);
        }

        $bill->load('payments');

        return view('resident.bills.show', compact('bill'));
    }
}
