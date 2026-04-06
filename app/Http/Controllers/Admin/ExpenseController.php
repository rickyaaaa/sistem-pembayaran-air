<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ExpenseRequest;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('creator');

        if ($year = $request->input('year')) {
            $query->whereYear('date', $year);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $expenses = $query->orderByDesc('date')->paginate(20);

        $categories = Expense::select('category')->distinct()->pluck('category');

        return view('admin.expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        return view('admin.expenses.create');
    }

    public function store(ExpenseRequest $request)
    {
        $validated = $request->validated();

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            // Fix 7: store to private disk
            $proofPath = $request->file('proof_file')->store('expenses', 'private');
        }

        Expense::create([
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'proof_file' => $proofPath,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit(Expense $expense)
    {
        return view('admin.expenses.edit', compact('expense'));
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $validated = $request->validated();

        if ($request->hasFile('proof_file')) {
            // Fix 7: delete old file from private disk
            if ($expense->proof_file) {
                Storage::disk('private')->delete($expense->proof_file);
            }
            $validated['proof_file'] = $request->file('proof_file')->store('expenses', 'private');
        }

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil diubah.');
    }

    public function destroy(Expense $expense)
    {
        // Fix 7: delete from private disk
        if ($expense->proof_file) {
            Storage::disk('private')->delete($expense->proof_file);
        }

        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    // Fix 7: Serve expense proof files from private storage
    public function viewProof(Expense $expense)
    {
        if (!$expense->proof_file || !Storage::disk('private')->exists($expense->proof_file)) {
            abort(404, 'File bukti tidak ditemukan.');
        }

        return Storage::disk('private')->response($expense->proof_file);
    }
}
