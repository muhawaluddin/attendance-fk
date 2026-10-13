@extends('layouts.app')

@section('title', 'Mata Kuliah & Blok')
@section('page-title', 'Mata Kuliah / Blok Perkuliahan')
@section('page-subtitle', 'Manajemen daftar blok kurikulum kedokteran dan mata kuliah FK UNHAS')

@section('content')
<div class="space-y-5" x-data="{ modalAdd: false, modalEdit: false, editData: {} }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('mata-kuliah.index') }}" method="GET" class="relative max-w-sm w-full">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode blok..."
                   class="form-input pl-9 py-2 shadow-sm" style="border-radius:8px;">
        </form>
        <button @click="modalAdd = true"
                class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold text-sm shadow-md shadow-brand-900/20 flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Tambah Blok / MK</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Blok / Mata Kuliah</th>
                        <th class="py-3 px-4 text-center">Bobot SKS</th>
                        <th class="py-3 px-4 text-center">Semester</th>
                        <th class="py-3 px-4 text-center">Total Jadwal</th>
                        <th class="py-3 px-4 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mataKuliahs as $index => $mk)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">{{ $mataKuliahs->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono text-xs font-bold text-brand-700">{{ $mk->kode_mk ?: '-' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $mk->nama_mk }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">{{ $mk->sks }} SKS</span>
                            </td>
                            <td class="py-3 px-4 text-center text-xs font-medium text-slate-600">{{ $mk->semester ?: '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700">{{ $mk->jadwals_count }} Jadwal</span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button @click="editData = {
                                            id: {{ $mk->id }},
                                            nama: '{{ addslashes($mk->nama_mk) }}',
                                            kode: '{{ $mk->kode_mk }}',
                                            sks: '{{ $mk->sks }}',
                                            semester: '{{ $mk->semester }}'
                                        }; modalEdit = true"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </button>
                                <form action="{{ route('mata-kuliah.destroy', $mk) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-14 text-slate-400"><i class="fa-solid fa-book-medical text-3xl text-slate-300 mb-2 block"></i>Tidak ada data mata kuliah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mataKuliahs->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $mataKuliahs->links() }}</div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center"><i class="fa-solid fa-plus-circle text-brand-700"></i></div>
                    <div><h3 class="text-base font-bold text-slate-900">Tambah Blok / Mata Kuliah</h3><p class="text-xs text-slate-500">Daftarkan blok atau mata kuliah baru</p></div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>
            <form action="{{ route('mata-kuliah.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Blok / Mata Kuliah <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mk" required placeholder="Contoh: Kardiovaskular" class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Kode Blok (Opsional)</label>
                            <input type="text" name="kode_mk" placeholder="BKD-201" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Bobot SKS</label>
                            <input type="number" name="sks" value="4" min="1" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-input">
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                        </select>
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
                    <div><h3 class="text-base font-bold text-slate-900">Edit Blok / Mata Kuliah</h3><p class="text-xs text-slate-500">Perbarui data blok</p></div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>
            <form :action="'{{ url('mata-kuliah') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Blok / Mata Kuliah <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mk" x-model="editData.nama" required class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Kode Blok</label>
                            <input type="text" name="kode_mk" x-model="editData.kode" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Bobot SKS</label>
                            <input type="number" name="sks" x-model="editData.sks" min="1" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Semester</label>
                        <select name="semester" x-model="editData.semester" class="form-input">
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                        </select>
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
