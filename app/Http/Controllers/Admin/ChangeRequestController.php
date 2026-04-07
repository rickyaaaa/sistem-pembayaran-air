<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangeRequestController extends Controller
{
    public function index()
    {
        // Only admin can see this page
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $changeRequests = ChangeRequest::with(['requester', 'reviewer'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 WHEN 'rejected' THEN 3 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.change_requests.index', compact('changeRequests'));
    }

    public function approve(ChangeRequest $changeRequest)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$changeRequest->isPending()) {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses.']);
        }

        $allowed = [
            \App\Models\Bill::class,
            \App\Models\Expense::class,
        ];
        if (!in_array($changeRequest->model_type, $allowed)) {
            abort(422, 'Tipe model tidak diizinkan.');
        }

        DB::transaction(function () use ($changeRequest) {
            // Apply the requested changes to the actual model
            $model = app($changeRequest->model_type)::find($changeRequest->model_id);

            if ($model) {
                // If the model is a Bill, we need to run business logic (payment record etc)
                if ($model instanceof \App\Models\Bill) {
                    // Logic uses auth()->user() (ChangeRequest requester)
                    $model->update($changeRequest->requested_data);
                    
                    // Trigger business logic for payment record
                    if (($changeRequest->requested_data['status'] ?? null) === 'paid' && 
                        ($changeRequest->original_data['status'] ?? null) !== 'paid') {
                        if ((float) ($changeRequest->requested_data['amount'] ?? 0) > 0) {
                            \App\Models\Payment::create([
                                'bill_id'      => $model->id,
                                'resident_id'  => $model->resident_id,
                                'payment_date' => now()->toDateString(),
                                'amount_paid'  => $changeRequest->requested_data['amount'],
                                'proof_file'   => \App\Models\Payment::MANUAL_PROOF,
                                'payer_name'   => 'Admin (Via Approval)',
                                'payer_phone'  => '-',
                                'status'       => \App\Enums\PaymentStatus::Confirmed,
                                'confirmed_by' => Auth::id(),
                                'confirmed_at' => now(),
                                'notes'        => 'Otomatis dibuat dari persetujuan perubahan: ' . $changeRequest->reason,
                            ]);
                        }
                    }
                } else {
                    $model->update($changeRequest->requested_data);
                }
            }

            $changeRequest->update([
                'status'       => 'approved',
                'reviewed_by'  => Auth::id(),
                'reviewed_at'  => now(),
            ]);
        });

        return back()->with('success', 'Perubahan berhasil disetujui dan diterapkan.');
    }

    public function reject(Request $request, ChangeRequest $changeRequest)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'review_notes' => 'required|string|max:500',
        ]);

        if (!$changeRequest->isPending()) {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses.']);
        }

        $changeRequest->update([
            'status'       => 'rejected',
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
            'review_notes' => $request->review_notes,
        ]);

        return back()->with('success', 'Permintaan perubahan ditolak.');
    }
}
