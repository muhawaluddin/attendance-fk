@extends('layouts.app')

@section('title', 'Data Dosen')
@section('page-title', 'Manajemen Dosen')
@section('page-subtitle', 'Daftar dosen pengajar dan staf akademik Fakultas Kedokteran UNHAS')

@section('content')
<div class="space-y-5" x-data="{ modalAdd: false, modalEdit: false, modalImport: false, editData: {} }">

    <!-- Filter and Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        
        <!-- Filters -->
        <form action="{{ route('dosen.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative max-w-xs w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIP, PIN finger..."
                       class="form-input pl-9 py-2 shadow-sm" style="border-radius:8px;">
            </div>

            <select name="departemen_id" onchange="this.form.submit()"
                    class="form-input py-2 w-auto shadow-sm" style="border-radius:8px;">
                <option value="">Semua Departemen</option>
                @foreach($departemens as $dept)
                    <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nama_departemen }}
                    </option>
                @endforeach
            </select>

            @if(request()->anyFilled(['q', 'departemen_id']))
                <a href="{{ route('dosen.index') }}" class="text-xs text-red-600 font-semibold hover:underline">
                    Reset Filter
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Import Button -->
            <button @click="modalImport = true"
                    class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow flex items-center gap-2">
                <i class="fa-solid fa-file-arrow-up text-sm"></i>
                <span>Import XLSX</span>
            </button>

            <!-- Add Button -->
            <button @click="modalAdd = true" 
                    class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold text-sm shadow shadow-brand-900/20 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-sm"></i>
                <span>Tambah Dosen</span>
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Dosen</th>
                        <th class="py-3 px-4">NIP &amp; PIN Finger</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dosens as $index => $d)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">
                                {{ $dosens->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900 text-sm">{{ $d->nama_lengkap }}</div>
                                <span class="text-xs text-slate-400">Dosen Pengajar FK UNHAS</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono text-xs font-semibold text-slate-700">{{ $d->nip }}</div>
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-fingerprint text-[10px]"></i>
                                        {{ $d->id_finger ?: 'Belum Ada' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $d->departemen->nama_departemen ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600 space-y-0.5">
                                @if($d->email)
                                    <div><i class="fa-regular fa-envelope text-slate-400 w-3.5"></i> {{ $d->email }}</div>
                                @endif
                                @if($d->no_hp)
                                    <div><i class="fa-solid fa-phone text-slate-400 w-3.5"></i> {{ $d->no_hp }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button @click="editData = {
                                            id: {{ $d->id }},
                                            nip: '{{ $d->nip }}',
                                            nama: '{{ addslashes($d->nama_dosen) }}',
                                            depan: '{{ addslashes($d->gelar_depan) }}',
                                            belakang: '{{ addslashes($d->gelar_belakang) }}',
                                            finger: '{{ $d->id_finger }}',
                                            dept: '{{ $d->departemen_id }}',
                                            hp: '{{ $d->no_hp }}',
                                            email: '{{ $d->email }}'
                                        }; modalEdit = true"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </button>
                                <form action="{{ route('dosen.destroy', $d) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dosen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-14 text-slate-400">
                                <i class="fa-solid fa-user-slash text-3xl mb-2 block text-slate-300"></i>
                                Tidak ada data dosen ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dosens->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $dosens->links() }}
            </div>
        @endif
    </div>

    <!-- =========================================== -->
    <!-- Modal Tambah Dosen                          -->
    <!-- =========================================== -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-10 p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-2xl">
            <!-- Header -->
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Data Dosen</h3>
                        <p class="text-xs text-slate-500">Isi data dosen pengajar lengkap</p>
                    </div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <form action="{{ route('dosen.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">NIP <span class="text-red-500">*</span></label>
                        <input type="text" name="nip" required placeholder="Nomor Induk Pegawai" class="form-input">
                    </div>

                    <!-- ID Finger — highlighted -->
                    <div class="p-3.5 rounded-lg bg-amber-50 border border-amber-200">
                        <label class="form-label" style="color:#92400e;"><i class="fa-solid fa-fingerprint mr-1"></i> PIN Fingerprint (ID Finger)</label>
                        <input type="text" name="id_finger" placeholder="Masukkan nomor PIN mesin fingerprint" class="form-input font-mono" style="border-color:#d97706;font-size:1rem;padding:0.6rem 0.75rem;">
                        <p class="text-[11px] text-amber-700 mt-1.5">Nomor PIN yang digunakan mesin biometrik untuk mengenali dosen. Bisa diisi belakangan.</p>
                    </div>

                    <div>
                        <label class="form-label">Nama &amp; Gelar <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-5 gap-2">
                            <div class="col-span-1">
                                <input type="text" name="gelar_depan" placeholder="Dr. dr." class="form-input">
                                <p class="text-[10px] text-slate-400 mt-0.5">Gelar Depan</p>
                            </div>
                            <div class="col-span-3">
                                <input type="text" name="nama_dosen" required placeholder="Nama tanpa gelar" class="form-input">
                                <p class="text-[10px] text-slate-400 mt-0.5">Nama Lengkap</p>
                            </div>
                            <div class="col-span-1">
                                <input type="text" name="gelar_belakang" placeholder="Sp.PD" class="form-input">
                                <p class="text-[10px] text-slate-400 mt-0.5">Gelar Belakang</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Departemen / Bagian</label>
                        <select name="departemen_id" class="form-input">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nomor HP / WA</label>
                            <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" placeholder="dosen@med.unhas.ac.id" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Dosen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Edit Dosen                            -->
    <!-- =========================================== -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-10 p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalEdit = false" class="modal-box bg-white w-full max-w-2xl">
            <!-- Header -->
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Edit Data Dosen</h3>
                        <p class="text-xs text-slate-500">Perbarui informasi dosen pengajar</p>
                    </div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <form :action="'{{ url('dosen') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">NIP <span class="text-red-500">*</span></label>
                        <input type="text" name="nip" x-model="editData.nip" required class="form-input">
                    </div>

                    <!-- ID Finger — highlighted -->
                    <div class="p-3.5 rounded-lg bg-amber-50 border border-amber-200">
                        <label class="form-label" style="color:#92400e;"><i class="fa-solid fa-fingerprint mr-1"></i> PIN Fingerprint (ID Finger)</label>
                        <input type="text" name="id_finger" x-model="editData.finger" placeholder="Nomor PIN mesin fingerprint" class="form-input font-mono" style="border-color:#d97706;font-size:1rem;padding:0.6rem 0.75rem;">
                        <p class="text-[11px] text-amber-700 mt-1.5">Nomor PIN yang terdaftar di mesin biometrik. Kosongkan jika belum ada.</p>
                    </div>

                    <div>
                        <label class="form-label">Nama &amp; Gelar <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-5 gap-2">
                            <div class="col-span-1">
                                <input type="text" name="gelar_depan" x-model="editData.depan" placeholder="Dr. dr." class="form-input">
                                <p class="text-[10px] text-slate-400 mt-0.5">Gelar Depan</p>
                            </div>
                            <div class="col-span-3">
                                <input type="text" name="nama_dosen" x-model="editData.nama" required placeholder="Nama tanpa gelar" class="form-input">
                                <p class="text-[10px] text-slate-400 mt-0.5">Nama Lengkap</p>
                            </div>
                            <div class="col-span-1">
                                <input type="text" name="gelar_belakang" x-model="editData.belakang" placeholder="Sp.PD" class="form-input">
                                <p class="text-[10px] text-slate-400 mt-0.5">Gelar Belakang</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Departemen / Bagian</label>
                        <select name="departemen_id" x-model="editData.dept" class="form-input">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nomor HP / WA</label>
                            <input type="text" name="no_hp" x-model="editData.hp" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" x-model="editData.email" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Perbarui Dosen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Import XLSX Dosen                     -->
    <!-- =========================================== -->
    <div x-show="modalImport" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalImport = false" class="modal-box bg-white w-full max-w-xl" x-data="{ fileName: '', dragging: false }">
            <!-- Header -->
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fa-solid fa-file-arrow-up text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Import Data Dosen</h3>
                        <p class="text-xs text-slate-500">Upload file Excel (.xlsx / .xls)</p>
                    </div>
                </div>
                <button @click="modalImport = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('dosen.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body space-y-4">

                    <!-- Template info -->
                    <div class="p-3 rounded-lg bg-blue-50 border border-blue-100 text-xs text-blue-700">
                        <p class="font-bold mb-1"><i class="fa-solid fa-circle-info mr-1"></i> Format Kolom yang Diperlukan:</p>
                        <div class="font-mono bg-white border border-blue-100 rounded px-2 py-1.5 text-[11px] text-slate-600 overflow-x-auto whitespace-nowrap">
                            nip | nama_dosen | gelar_depan | gelar_belakang | id_finger | departemen | no_hp | email
                        </div>
                        <p class="mt-1.5 text-blue-600">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>
                            Kolom <strong>nip</strong> dan <strong>nama_dosen</strong> wajib diisi. NIP yang sudah ada akan dilewati.
                        </p>
                    </div>

                    <!-- File upload dropzone -->
                    <div class="import-dropzone p-6 text-center cursor-pointer relative"
                         :class="{ 'dragover': dragging }"
                         @dragover.prevent="dragging = true"
                         @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; const f = $event.dataTransfer.files[0]; if(f){ fileName = f.name; $refs.fileInput.files = $event.dataTransfer.files; }"
                         @click="$refs.fileInput.click()">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                               x-ref="fileInput" class="hidden"
                               @change="fileName = $event.target.files[0]?.name || ''">
                        <template x-if="!fileName">
                            <div>
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-300 mb-2"></i>
                                <p class="text-sm font-semibold text-slate-600">Klik atau seret file ke sini</p>
                                <p class="text-xs text-slate-400 mt-1">Format: .xlsx, .xls, .csv — Maks. 5 MB</p>
                            </div>
                        </template>
                        <template x-if="fileName">
                            <div>
                                <i class="fa-solid fa-file-excel text-3xl text-emerald-500 mb-2"></i>
                                <p class="text-sm font-semibold text-emerald-700" x-text="fileName"></p>
                                <p class="text-xs text-slate-400 mt-1">Klik untuk ganti file</p>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" @click="modalImport = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" :disabled="!fileName"
                            class="px-5 py-2 rounded-lg text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-upload"></i> Upload &amp; Import
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
