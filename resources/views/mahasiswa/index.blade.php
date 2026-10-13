@extends('layouts.app')

@section('title', 'Data Mahasiswa')
@section('page-title', 'Manajemen Mahasiswa')
@section('page-subtitle', 'Daftar data mahasiswa Fakultas Kedokteran UNHAS, PIN fingerprint, dan kelas')

@section('content')
<div class="space-y-5" x-data="{
    modalAdd: false,
    modalEdit: false,
    modalImport: false,
    editData: {},
    selectedIds: [],
    selectAll: false,
    toggleAll() {
        if (this.selectAll) {
            this.selectedIds = Array.from(document.querySelectorAll('.row-checkbox')).map(el => el.value);
        } else {
            this.selectedIds = [];
        }
    }
}">

    <!-- Filter and Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        
        <!-- Filters -->
        <form action="{{ route('mahasiswa.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative max-w-xs w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIM, PIN..."
                       class="form-input pl-9 py-2 shadow-sm" style="border-radius:8px;">
            </div>

            <select name="kelas_id" onchange="this.form.submit()"
                    class="form-input py-2 w-auto shadow-sm" style="border-radius:8px;">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>

            <select name="angkatan" onchange="this.form.submit()"
                    class="form-input py-2 w-auto shadow-sm" style="border-radius:8px;">
                <option value="">Semua Angkatan</option>
                @foreach($angkatanList as $akt)
                    <option value="{{ $akt }}" {{ request('angkatan') == $akt ? 'selected' : '' }}>
                        Angkatan {{ $akt }}
                    </option>
                @endforeach
            </select>

            @if(request()->anyFilled(['q', 'kelas_id', 'angkatan']))
                <a href="{{ route('mahasiswa.index') }}" class="text-xs text-red-600 font-semibold hover:underline">
                    Reset Filter
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Multi Delete Trigger -->
            <button x-show="selectedIds.length > 0" x-cloak
                    @click="if(confirm('Hapus ' + selectedIds.length + ' mahasiswa terpilih?')) { $refs.manyForm.submit(); }"
                    class="px-3.5 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold text-sm shadow transition-all flex items-center gap-2">
                <i class="fa-solid fa-trash-can text-sm"></i>
                <span x-text="'Hapus (' + selectedIds.length + ')'"></span>
            </button>

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
                <span>Tambah</span>
            </button>
        </div>
    </div>

    <!-- Hidden Form for Multi-Delete -->
    <form x-ref="manyForm" action="{{ route('mahasiswa.destroy-many') }}" method="POST" class="hidden">
        @csrf
        <template x-for="id in selectedIds" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    <!-- Table Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()"
                                   class="w-4 h-4 rounded text-brand-700 focus:ring-brand-600 border-slate-300 cursor-pointer">
                        </th>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">NIM &amp; Mahasiswa</th>
                        <th class="py-3 px-4">ID Finger</th>
                        <th class="py-3 px-4">Kelas &amp; Angkatan</th>
                        <th class="py-3 px-4">No. HP</th>
                        <th class="py-3 px-4 text-right w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mahasiswas as $index => $m)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox" value="{{ $m->id }}" x-model="selectedIds"
                                       class="row-checkbox w-4 h-4 rounded text-brand-700 focus:ring-brand-600 border-slate-300 cursor-pointer">
                            </td>
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">
                                {{ $mahasiswas->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900 text-sm">{{ $m->nama_mahasiswa }}</div>
                                <span class="text-xs font-mono font-bold text-brand-700">{{ $m->nim }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-mono font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-fingerprint text-[10px]"></i>
                                    {{ $m->id_finger ?: '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-xs text-slate-800">{{ $m->kelas->nama_kelas ?? '-' }}</div>
                                <span class="text-[11px] text-slate-400">Angkatan {{ $m->angkatan ?: '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">
                                {{ $m->no_hp ?: '-' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <!-- Reset Password Button -->
                                <form action="{{ route('mahasiswa.reset-password', $m) }}" method="POST" class="inline" onsubmit="return confirm('Reset password mahasiswa ke NIM: {{ $m->nim }}?')">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-600 rounded hover:bg-amber-50 transition-colors" title="Reset Password ke NIM">
                                        <i class="fa-solid fa-key text-xs"></i>
                                    </button>
                                </form>

                                <!-- Edit Button -->
                                <button @click="editData = {
                                            id: {{ $m->id }},
                                            nim: '{{ $m->nim }}',
                                            nama: '{{ addslashes($m->nama_mahasiswa) }}',
                                            finger: '{{ $m->id_finger }}',
                                            kelas_id: '{{ $m->kelas_id }}',
                                            angkatan: '{{ $m->angkatan }}',
                                            hp: '{{ $m->no_hp }}',
                                            jk: '{{ $m->jenis_kelamin }}'
                                        }; modalEdit = true"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('mahasiswa.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini?')">
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
                            <td colspan="7" class="text-center py-14 text-slate-400">
                                <i class="fa-solid fa-users-slash text-3xl mb-2 block text-slate-300"></i>
                                Tidak ada data mahasiswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mahasiswas->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $mahasiswas->links() }}
            </div>
        @endif
    </div>

    <!-- =========================================== -->
    <!-- Modal Tambah Mahasiswa                      -->
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
                        <h3 class="text-base font-bold text-slate-900">Tambah Mahasiswa Baru</h3>
                        <p class="text-xs text-slate-500">Isi data mahasiswa lengkap di bawah ini</p>
                    </div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <form action="{{ route('mahasiswa.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">NIM <span class="text-red-500">*</span></label>
                            <input type="text" name="nim" required placeholder="Contoh: C011231001" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" class="form-input">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Nama Mahasiswa <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mahasiswa" required placeholder="Nama lengkap mahasiswa" class="form-input">
                    </div>

                    <!-- ID Finger — highlighted -->
                    <div class="p-3.5 rounded-lg bg-amber-50 border border-amber-200">
                        <label class="form-label" style="color:#92400e;"><i class="fa-solid fa-fingerprint mr-1"></i> PIN Fingerprint (ID Finger)</label>
                        <input type="text" name="id_finger" placeholder="Masukkan nomor PIN mesin fingerprint" class="form-input font-mono" style="border-color:#d97706;font-size:1rem;padding:0.6rem 0.75rem;">
                        <p class="text-[11px] text-amber-700 mt-1.5">Nomor PIN ini digunakan mesin biometrik untuk mengenali mahasiswa. Bisa diisi belakangan.</p>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-input">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Tahun Angkatan</label>
                            <input type="text" name="angkatan" placeholder="2025" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" class="form-input">
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Mahasiswa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Edit Mahasiswa                        -->
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
                        <h3 class="text-base font-bold text-slate-900">Edit Data Mahasiswa</h3>
                        <p class="text-xs text-slate-500">Perbarui informasi mahasiswa</p>
                    </div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <form :action="'{{ url('mahasiswa') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">NIM <span class="text-red-500">*</span></label>
                            <input type="text" name="nim" x-model="editData.nim" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" x-model="editData.jk" class="form-input">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Nama Mahasiswa <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mahasiswa" x-model="editData.nama" required class="form-input">
                    </div>

                    <!-- ID Finger — highlighted -->
                    <div class="p-3.5 rounded-lg bg-amber-50 border border-amber-200">
                        <label class="form-label" style="color:#92400e;"><i class="fa-solid fa-fingerprint mr-1"></i> PIN Fingerprint (ID Finger)</label>
                        <input type="text" name="id_finger" x-model="editData.finger" placeholder="Nomor PIN mesin fingerprint" class="form-input font-mono" style="border-color:#d97706;font-size:1rem;padding:0.6rem 0.75rem;">
                        <p class="text-[11px] text-amber-700 mt-1.5">Nomor PIN yang terdaftar di mesin biometrik. Kosongkan jika belum ada.</p>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" x-model="editData.kelas_id" class="form-input">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Tahun Angkatan</label>
                            <input type="text" name="angkatan" x-model="editData.angkatan" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="no_hp" x-model="editData.hp" class="form-input">
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Perbarui Mahasiswa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Import XLSX Mahasiswa                 -->
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
                        <h3 class="text-base font-bold text-slate-900">Import Data Mahasiswa</h3>
                        <p class="text-xs text-slate-500">Upload file Excel (.xlsx / .xls)</p>
                    </div>
                </div>
                <button @click="modalImport = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body space-y-4">

                    <!-- Template info -->
                    <div class="p-3 rounded-lg bg-blue-50 border border-blue-100 text-xs text-blue-700">
                        <p class="font-bold mb-1"><i class="fa-solid fa-circle-info mr-1"></i> Format Kolom yang Diperlukan:</p>
                        <div class="font-mono bg-white border border-blue-100 rounded px-2 py-1.5 text-[11px] text-slate-600 overflow-x-auto whitespace-nowrap">
                            nim | nama_mahasiswa | id_finger | kelas | angkatan | jenis_kelamin | no_hp
                        </div>
                        <p class="mt-1.5 text-blue-600">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>
                            Kolom <strong>nim</strong> dan <strong>nama_mahasiswa</strong> wajib diisi. NIM yang sudah ada akan dilewati.
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
