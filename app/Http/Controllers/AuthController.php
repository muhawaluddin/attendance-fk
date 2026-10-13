<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Masukkan username Anda.',
            'password.required' => 'Masukkan password Anda.',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->withInput()->withErrors([
                'login' => 'Username atau password salah.',
            ]);
        }

        // Dukungan legacy MD5 & bcrypt
        $passwordMatches = false;
        if (Hash::check($credentials['password'], $user->password)) {
            $passwordMatches = true;
        } elseif (strlen($user->password) === 32 && md5($credentials['password']) === $user->password) {
            // Password lama berupa MD5 plain, upgrade otomatis ke bcrypt
            $user->password = Hash::make($credentials['password']);
            $user->save();
            $passwordMatches = true;
        }

        if (!$passwordMatches) {
            return back()->withInput()->withErrors([
                'login' => 'Username atau password salah.',
            ]);
        }

        if (!$user->status) {
            return back()->withInput()->withErrors([
                'login' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin.',
            ]);
        }

        // Login sukses
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->update([
            'ip_address' => $request->ip(),
            'last_login_at' => now(),
        ]);

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil logout.');
    }

    public function profile()
    {
        return view('profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'current_password.required_with' => 'Masukkan password saat ini untuk mengganti password.',
        ]);

        if (!empty($validated['current_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = $validated['name'];
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        $user->save();

        return back()->with('success', 'Profil dan akun Anda berhasil diperbarui!');
    }
}
