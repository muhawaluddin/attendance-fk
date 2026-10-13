<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(15);
        return view('akun.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'level' => ['required', 'in:admin,operator,pimpinan,dosen'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'level' => $validated['level'],
            'status' => true,
        ]);

        return redirect()->route('akun.index')->with('success', 'Akun pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, User $akun)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $akun->id],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email,' . $akun->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'level' => ['required', 'in:admin,operator,pimpinan,dosen'],
            'status' => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'level' => $validated['level'],
            'status' => $request->boolean('status'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $akun->update($updateData);
        return redirect()->route('akun.index')->with('success', 'Data akun pengguna berhasil diperbarui!');
    }

    public function destroy(User $akun)
    {
        if ($akun->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $akun->delete();
        return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus!');
    }
}
