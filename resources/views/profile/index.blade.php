@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page-title', 'Profil & Akun Pengguna')
@section('page-subtitle', 'Kelola informasi identitas dan keamanan akun login Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white table-card border border-slate-200/80 shadow-sm p-6 sm:p-8">
        
        <div class="flex items-center gap-4 pb-6 mb-6 border-b border-slate-100">
            <div class="w-14 h-14 rounded-lg bg-brand-700 text-white flex items-center justify-center font-black text-xl shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-900">{{ $user->name }}</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">Username: {{ $user->username }} &bull; Role: {{ strtoupper($user->level) }}</p>
                <p class="text-xs text-slate-400 mt-1">Terakhir login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '-' }} (IP: {{ $user->ip_address ?? '-' }})</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="form-input">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="username" class="form-label">Username (Tetap)</label>
                <input type="text" id="username" value="{{ $user->username }}" disabled
                       class="form-input bg-slate-100 text-slate-500 cursor-not-allowed">
            </div>

            <div>
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                       class="form-input" placeholder="contoh@med.unhas.ac.id">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-sm font-bold text-slate-900 mb-1">Ubah Kata Sandi (Opsional)</h4>
                <p class="text-xs text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password akun Anda saat ini.</p>

                <div class="space-y-3">
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-600 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password"
                               class="form-input" placeholder="Masukkan password lama">
                        @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-slate-600 mb-1">Password Baru</label>
                        <input type="password" name="new_password" id="new_password"
                               class="form-input" placeholder="Minimal 8 karakter">
                        @error('new_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                               class="form-input" placeholder="Ketik ulang password baru">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-sm font-bold shadow-md shadow-brand-900/20 transition-colors">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
