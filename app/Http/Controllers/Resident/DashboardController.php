<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $resident = Auth::user()->resident;

        if (!$resident) {
            abort(403, 'Data warga tidak ditemukan.');
        }

        // Current month bill
        $currentBill = Bill::where('resident_id', $resident->id)
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();

        // Recent bills (last 6 months)
        $recentBills = Bill::where('resident_id', $resident->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(6)
            ->get();

        // Stats
        $totalUnpaid = Bill::where('resident_id', $resident->id)
            ->where('status', 'unpaid')
            ->sum('amount');

        $totalPending = Bill::where('resident_id', $resident->id)
            ->where('status', 'pending')
            ->count();

        return view('resident.dashboard', compact(
            'resident',
            'currentBill',
            'recentBills',
            'totalUnpaid',
            'totalPending',
        ));
    }
}
