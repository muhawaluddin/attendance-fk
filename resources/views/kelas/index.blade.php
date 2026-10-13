@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page-title', 'Manajemen Kelas')
@section('page-subtitle', 'Daftar pembagian kelas akademik mahasiswa Fakultas Kedokteran UNHAS')

@section('content')
<div class="space-y-5" x-data="{ modalAdd: false, modalEdit: false, editData: {} }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('kelas.index') }}" method="GET" class="relative max-w-sm w-full">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kelas..."
                   class="form-input pl-9 py-2 shadow-sm" style="border-radius:8px;">
        </form>
        <button @click="modalAdd = true"
                class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold text-sm shadow-md shadow-brand-900/20 flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Tambah Kelas</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Kelas</th>
                        <th class="py-3 px-4">Tahun / Angkatan</th>
                        <th class="py-3 px-4 text-center">Jumlah Mahasiswa</th>
                        <th class="py-3 px-4 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kelas as $index => $k)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">{{ $kelas->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-brand-700 flex items-center justify-center font-bold text-xs">
                                    <i class="fa-solid fa-chalkboard"></i>
                                </div>
                                <span>{{ $k->nama_kelas }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-xs font-semibold">{{ $k->angkatan ? 'Angkatan ' . $k->angkatan : '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700">{{ $k->mahasiswas_count }} Mahasiswa</span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button @click="editData = { id: {{ $k->id }}, nama: '{{ addslashes($k->nama_kelas) }}', angkatan: '{{ $k->angkatan }}' }; modalEdit = true"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </button>
                                <form action="{{ route('kelas.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-14 text-slate-400"><i class="fa-solid fa-chalkboard text-3xl text-slate-300 mb-2 block"></i>Tidak ada data kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kelas->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $kelas->links() }}</div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center"><i class="fa-solid fa-plus-circle text-brand-700"></i></div>
                    <div><h3 class="text-base font-bold text-slate-900">Tambah Kelas Baru</h3><p class="text-xs text-slate-500">Buat pembagian kelas mahasiswa</p></div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" required placeholder="Contoh: Kelas A, Kelas B, Internasional" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Tahun Angkatan (Opsional)</label>
                        <input type="text" name="angkatan" placeholder="Contoh: 2025" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 transition-colors flex items-center gap-2"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalEdit = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-pen-to-square text-blue-600"></i></div>
                    <div><h3 class="text-base font-bold text-slate-900">Edit Kelas</h3><p class="text-xs text-slate-500">Perbarui data kelas</p></div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>
            <form :action="'{{ url('kelas') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" x-model="editData.nama" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Tahun Angkatan</label>
                        <input type="text" name="angkatan" x-model="editData.angkatan" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2"><i class="fa-solid fa-floppy-disk"></i> Perbarui</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
