<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PengurusController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', User::ROLE_PENGURUS)->orderBy('name')->get();
        return view('admin.pengurus.index', compact('staffs'));
    }

    public function create()
    {
        return view('admin.pengurus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name'     => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'username' => $validated['username'],
            'name'     => $validated['name'],
            'password' => Hash::make($validated['password']),
            'role'     => User::ROLE_PENGURUS,
        ]);

        return redirect()->route('admin.pengurus.index')->with('success', 'Pengurus berhasil ditambahkan.');
    }

    public function edit(User $staff)
    {
        if ($staff->role !== User::ROLE_PENGURUS) {
            abort(404);
        }
        return view('admin.pengurus.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        if ($staff->role !== User::ROLE_PENGURUS) {
            abort(404);
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'name'     => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'username' => $validated['username'],
            'name'     => $validated['name'],
        ];

        if ($validated['password']) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff->update($data);

        return redirect()->route('admin.pengurus.index')->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy(User $staff)
    {
        if ($staff->role !== User::ROLE_PENGURUS) {
            abort(404);
        }

        $staff->delete();

        return redirect()->route('admin.pengurus.index')->with('success', 'Pengurus berhasil dihapus.');
    }
}
