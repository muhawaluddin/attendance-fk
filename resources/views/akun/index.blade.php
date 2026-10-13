@extends('layouts.app')

@section('title', 'Kelola Akun')
@section('page-title', 'Manajemen Akun Pengguna')
@section('page-subtitle', 'Kelola hak akses administrator, operator, dan pengelola sistem')

@section('content')
<div class="space-y-5" x-data="{ modalAdd: false, modalEdit: false, editData: {} }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('akun.index') }}" method="GET" class="relative max-w-sm w-full">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, email..."
                   class="form-input pl-9 py-2 shadow-sm" style="border-radius:8px;">
        </form>
        <button @click="modalAdd = true"
                class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold text-sm shadow-md shadow-brand-900/20 flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-user-plus text-sm"></i>
            <span>Tambah Akun</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Pengguna</th>
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">Level / Hak Akses</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Terakhir Login</th>
                        <th class="py-3 px-4 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">{{ $users->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900 text-sm flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-brand-700 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div>{{ $u->name }}</div>
                                        <span class="text-xs text-slate-400 font-normal">{{ $u->email ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs font-bold text-brand-700">{{ $u->username }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-xs font-bold capitalize
                                    {{ $u->level === 'admin' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $u->level }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($u->status)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check text-[10px]"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500">
                                        <i class="fa-solid fa-ban text-[10px]"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button @click="editData = {
                                            id: {{ $u->id }},
                                            name: '{{ addslashes($u->name) }}',
                                            username: '{{ $u->username }}',
                                            email: '{{ $u->email }}',
                                            level: '{{ $u->level }}',
                                            status: {{ $u->status ? 'true' : 'false' }}
                                        }; modalEdit = true"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </button>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('akun.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-14 text-slate-400">
                                <i class="fa-solid fa-users text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada akun pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>

    <!-- =========================================== -->
    <!-- Modal Tambah Akun                           -->
    <!-- =========================================== -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Akun Pengguna</h3>
                        <p class="text-xs text-slate-500">Buat akun operator atau administrator baru</p>
                    </div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <form action="{{ route('akun.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Operator ICT 1" class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" required placeholder="username" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Level / Hak Akses</label>
                            <select name="level" class="form-input">
                                <option value="operator">Operator</option>
                                <option value="admin">Administrator</option>
                                <option value="pimpinan">Pimpinan</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Email (Opsional)</label>
                        <input type="email" name="email" placeholder="user@med.unhas.ac.id" class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required placeholder="Min. 8 karakter" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi password" class="form-input">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Edit Akun                             -->
    <!-- =========================================== -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalEdit = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Edit Akun Pengguna</h3>
                        <p class="text-xs text-slate-500">Perbarui data dan hak akses pengguna</p>
                    </div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <form :action="'{{ url('akun') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editData.name" required class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" x-model="editData.username" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Level / Role</label>
                            <select name="level" x-model="editData.level" class="form-input">
                                <option value="operator">Operator</option>
                                <option value="admin">Administrator</option>
                                <option value="pimpinan">Pimpinan</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" x-model="editData.email" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Password Baru <span class="text-xs text-slate-400 normal-case font-normal">(kosongkan jika tidak ganti)</span></label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" class="form-input">
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <input type="checkbox" name="status" value="1" x-model="editData.status" id="edit-status"
                               class="rounded border-slate-300 text-brand-700 focus:ring-brand-600">
                        <label for="edit-status" class="text-xs font-bold text-slate-700 cursor-pointer">Akun Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Perbarui Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
