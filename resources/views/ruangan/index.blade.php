@extends('layouts.app')

@section('title', 'Ruang Kuliah')
@section('page-title', 'Ruang Perkuliahan')
@section('page-subtitle', 'Daftar ruang kuliah, laboratorium, amphitheater, dan mesin fingerprint terpasang')

@section('content')
<div class="space-y-5" x-data="{ modalAdd: false, modalEdit: false, editData: {} }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('ruangan.index') }}" method="GET" class="relative max-w-sm w-full">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode ruangan, lokasi..."
                   class="form-input pl-9 py-2 shadow-sm" style="border-radius:8px;">
        </form>
        <button @click="modalAdd = true"
                class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold text-sm shadow-md shadow-brand-900/20 flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Tambah Ruangan</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Ruang Kuliah</th>
                        <th class="py-3 px-4">Kode & Lokasi</th>
                        <th class="py-3 px-4 text-center">Kapasitas</th>
                        <th class="py-3 px-4">Mesin Finger Terpasang</th>
                        <th class="py-3 px-4 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ruangans as $index => $r)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">{{ $ruangans->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-door-open text-brand-700"></i>
                                    <span>{{ $r->nama_ruangan }}</span>
                                </div>
                                @if($r->keterangan)
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $r->keterangan }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono text-xs font-bold text-brand-700">{{ $r->kode_ruangan ?: '-' }}</div>
                                <span class="text-xs text-slate-500">{{ $r->lokasi ?: '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    {{ $r->kapasitas ? $r->kapasitas . ' Kursi' : '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($r->mesins->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($r->mesins as $m)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <i class="fa-solid fa-fingerprint text-[10px]"></i>
                                                <span>{{ $m->serial }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum dipetakan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button @click="editData = {
                                            id: {{ $r->id }},
                                            nama: '{{ addslashes($r->nama_ruangan) }}',
                                            kode: '{{ $r->kode_ruangan }}',
                                            kapasitas: '{{ $r->kapasitas }}',
                                            lokasi: '{{ addslashes($r->lokasi) }}',
                                            ket: '{{ addslashes($r->keterangan) }}',
                                            mesinIds: {{ json_encode($r->mesins->pluck('id')) }}
                                        }; modalEdit = true"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </button>
                                <form action="{{ route('ruangan.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Hapus ruangan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-14 text-slate-400"><i class="fa-solid fa-door-open text-3xl text-slate-300 mb-2 block"></i>Tidak ada data ruang kuliah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ruangans->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $ruangans->links() }}</div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-10 p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-xl mb-8">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center"><i class="fa-solid fa-plus-circle text-brand-700"></i></div>
                    <div><h3 class="text-base font-bold text-slate-900">Tambah Ruang Kuliah</h3><p class="text-xs text-slate-500">Daftarkan ruangan baru</p></div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>
            <form action="{{ route('ruangan.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Ruang Kuliah <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_ruangan" required placeholder="Contoh: Ruang Kuliah 1 LT.2" class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Kode Ruang</label>
                            <input type="text" name="kode_ruangan" placeholder="RK-201" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Kapasitas (Kursi)</label>
                            <input type="number" name="kapasitas" placeholder="120" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Lokasi / Gedung</label>
                        <input type="text" name="lokasi" placeholder="Gedung Pendidikan LT.2" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Hubungkan Mesin Fingerprint</label>
                        <div class="max-h-36 overflow-y-auto p-2 bg-slate-50 border border-slate-200 rounded-lg space-y-1">
                            @foreach($availableMesins as $m)
                                <label class="flex items-center gap-2 p-1.5 hover:bg-white rounded-lg text-xs cursor-pointer">
                                    <input type="checkbox" name="mesin_ids[]" value="{{ $m->id }}" class="rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                                    <span class="font-mono font-semibold">{{ $m->serial }}</span>
                                    <span class="text-slate-400">({{ $m->ip_address ?: 'No IP' }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" placeholder="Catatan fasilitas ruangan..." class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 transition-colors flex items-center gap-2"><i class="fa-solid fa-floppy-disk"></i> Simpan Ruangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-10 p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalEdit = false" class="modal-box bg-white w-full max-w-xl mb-8">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-pen-to-square text-blue-600"></i></div>
                    <div><h3 class="text-base font-bold text-slate-900">Edit Ruang Kuliah</h3><p class="text-xs text-slate-500">Perbarui data ruangan</p></div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>
            <form :action="'{{ url('ruangan') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Ruang Kuliah <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_ruangan" x-model="editData.nama" required class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Kode Ruang</label>
                            <input type="text" name="kode_ruangan" x-model="editData.kode" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Kapasitas (Kursi)</label>
                            <input type="number" name="kapasitas" x-model="editData.kapasitas" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Lokasi / Gedung</label>
                        <input type="text" name="lokasi" x-model="editData.lokasi" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Hubungkan Mesin Fingerprint</label>
                        <div class="max-h-36 overflow-y-auto p-2 bg-slate-50 border border-slate-200 rounded-lg space-y-1">
                            @foreach($availableMesins as $m)
                                <label class="flex items-center gap-2 p-1.5 hover:bg-white rounded-lg text-xs cursor-pointer">
                                    <input type="checkbox" name="mesin_ids[]" value="{{ $m->id }}"
                                           :checked="editData.mesinIds && editData.mesinIds.includes({{ $m->id }})"
                                           class="rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                                    <span class="font-mono font-semibold">{{ $m->serial }}</span>
                                    <span class="text-slate-400">({{ $m->ip_address ?: 'No IP' }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" x-model="editData.ket" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2"><i class="fa-solid fa-floppy-disk"></i> Perbarui Ruangan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
