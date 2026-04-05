<?php

namespace App\Http\Controllers\Admin;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'category' => 'required|string|max:100',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('expenses', 'public');
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

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'category' => 'required|string|max:100',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('proof_file')) {
            // Delete old file
            if ($expense->proof_file) {
                Storage::disk('public')->delete($expense->proof_file);
            }
            $validated['proof_file'] = $request->file('proof_file')->store('expenses', 'public');
        }

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil diubah.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->proof_file) {
            Storage::disk('public')->delete($expense->proof_file);
        }

        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
