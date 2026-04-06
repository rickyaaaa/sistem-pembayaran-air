<?php

namespace App\Http\Controllers\Resident;

use App\Enums\BillStatus;
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

            // Fix 3: case-insensitive search by block_number
            $resident = Resident::whereRaw('LOWER(block_number) = ?', [strtolower(trim($houseNumber))])
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

    // Fix 4: Validate ownership via house_number query param
    public function show(Request $request, Bill $bill)
    {
        $houseNumber = $request->query('house_number');

        if (!$houseNumber) {
            abort(403, 'Anda wajib memasukkan nomor rumah untuk validasi akses.');
        }

        $ownerBlockNumber = $bill->resident?->block_number;
        if (!$ownerBlockNumber || strtolower($ownerBlockNumber) !== strtolower(trim($houseNumber))) {
            abort(403, 'Akses ditolak: Data tagihan tidak sesuai dengan nomor rumah yang Anda masukkan.');
        }

        $bill->load('payments', 'resident');

        return view('resident.bills.show', compact('bill', 'houseNumber'));
    }
}
