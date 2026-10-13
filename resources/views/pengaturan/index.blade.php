@extends('layouts.app')

@section('title', 'Pengaturan Sistem & Branding')
@section('page-title', 'Pengaturan Sistem & Branding')
@section('page-subtitle', 'Kustomisasi identitas institusi, tema warna kampus, dan durasi perkuliahan')

@section('content')
<div class="space-y-6" x-data="{
    tab: 'branding',
    modalAdd: false,
    modalEdit: false,
    editData: {},
    selectedTheme: '{{ $activeSetting->theme_color ?? 'unhas-red' }}',
    customPrimary: '{{ $activeSetting->primary_color_hex ?? '#1d4ed8' }}',
    institutionName: '{{ old('institution_name', $activeSetting->institution_name ?? 'Universitas Hasanuddin') }}',
    shortName: '{{ old('short_name', $activeSetting->short_name ?? 'FK UNHAS') }}',
    facultyName: '{{ old('faculty_name', $activeSetting->faculty_name ?? 'Fakultas Kedokteran') }}',
    appName: '{{ old('app_name', $activeSetting->app_name ?? 'ABSENSI') }}',
    websiteUrl: '{{ old('website_url', $activeSetting->website_url ?? 'www.med.unhas.ac.id') }}',
    emailContact: '{{ old('email_contact', $activeSetting->email_contact ?? 'prodis1_fkuh@gmail.com') }}',
    runningText: '{{ old('running_text', $activeSetting->running_text ?? 'Disampaikan kepada seluruh mahasiswa untuk selalu melakukan tap absensi sidik jari pada mesin fingerprint di depan ruang kuliah masing-masing sebelum perkuliahan dimulai.') }}',
    applyPreset(type) {
        if (type === 'umb') {
            this.institutionName = 'Universitas Mega Buana Palopo';
            this.shortName = 'UMB PALOPO';
            this.facultyName = 'Fakultas Kesehatan';
            this.appName = 'SIM PRESENSI';
            this.websiteUrl = 'www.umbpalopo.ac.id';
            this.emailContact = 'rektorat@umbpalopo.ac.id';
            this.runningText = 'Selamat datang di Sistem Absensi Universitas Mega Buana Palopo (UMB Palopo). Silakan tap sidik jari Anda sebelum perkuliahan dimulai.';
            this.selectedTheme = 'umb-blue';
        } else if (type === 'unhas') {
            this.institutionName = 'Universitas Hasanuddin';
            this.shortName = 'FK UNHAS';
            this.facultyName = 'Fakultas Kedokteran';
            this.appName = 'ABSENSI';
            this.websiteUrl = 'www.med.unhas.ac.id';
            this.emailContact = 'prodis1_fkuh@gmail.com';
            this.runningText = 'Disampaikan kepada seluruh mahasiswa Fakultas Kedokteran UNHAS untuk selalu melakukan tap absensi sidik jari pada mesin fingerprint sebelum perkuliahan dimulai.';
            this.selectedTheme = 'unhas-red';
        }
    }
}">

    <!-- Quick Brand Preset Shortcuts -->
    <div class="p-4 sm:p-5 table-card bg-gradient-to-r from-blue-900/10 via-brand-50/20 to-slate-100 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-brand-700 text-white flex items-center justify-center text-lg font-bold shadow-md">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-slate-900 text-sm">Preset Instan Sekali Klik</h4>
                <p class="text-xs text-slate-500">Pilih template konfigurasi brand kampus dan palet warna dengan 1 klik:</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="applyPreset('umb')" 
                    class="px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-900/20 flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Terapkan Preset UMB Palopo (Biru)</span>
            </button>
            <button type="button" @click="applyPreset('unhas')" 
                    class="px-3.5 py-2 rounded-lg bg-red-700 hover:bg-red-800 text-white font-bold text-xs shadow-md shadow-red-900/20 flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-building-columns"></i>
                <span>Terapkan Preset UNHAS (Merah)</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 p-1.5 bg-white rounded-lg border border-slate-200/80 shadow-sm w-fit">
        <button @click="tab = 'branding'" 
                :class="tab === 'branding' ? 'bg-brand-700 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="px-4 py-2 rounded-md text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-solid fa-paintbrush"></i>
            <span>Identitas Kampus & Tema Warna</span>
        </button>
        <button @click="tab = 'perkuliahan'" 
                :class="tab === 'perkuliahan' ? 'bg-brand-700 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="px-4 py-2 rounded-md text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-solid fa-clock"></i>
            <span>Waktu Perkuliahan & Durasi Jam</span>
        </button>
    </div>

    <!-- TAB 1: IDENTITAS KAMPUS & TEMA WARNA -->
    <div x-show="tab === 'branding'" x-cloak class="space-y-6">
        
        <form action="{{ route('pengaturan.branding') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Card 1: Pengaturan Brand Institusi -->
            <div class="bg-white table-card border border-slate-200/80 shadow-sm p-5 sm:p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Identitas Institusi / Universitas</h3>
                        <p class="text-xs text-slate-500">Ubah nama kampus, fakultas, logo, dan identitas resmi untuk white-label sistem</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Institusi / Universitas <span class="text-red-500">*</span></label>
                        <input type="text" name="institution_name" x-model="institutionName" required
                               placeholder="Contoh: UMB Palopo / Universitas Mega Buana Palopo"
                               class="form-input">
                        <p class="text-[11px] text-slate-400 mt-1">Contoh: <strong>UMB Palopo</strong> atau <strong>Universitas Mega Buana Palopo</strong></p>
                    </div>

                    <div>
                        <label class="form-label">Singkatan / Kode Brand <span class="text-red-500">*</span></label>
                        <input type="text" name="short_name" x-model="shortName" required
                               placeholder="Contoh: UMB PALOPO"
                               class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Nama Fakultas / Program Studi (Opsional)</label>
                        <input type="text" name="faculty_name" x-model="facultyName"
                               placeholder="Contoh: Fakultas Kesehatan / Prodi Kebidanan"
                               class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Nama Aplikasi <span class="text-red-500">*</span></label>
                        <input type="text" name="app_name" x-model="appName" required
                               placeholder="Contoh: ABSENSI / SIM PRESENSI"
                               class="form-input font-bold text-brand-700">
                    </div>

                    <div>
                        <label class="form-label">Website Resmi Kampus</label>
                        <input type="text" name="website_url" x-model="websiteUrl"
                               placeholder="Contoh: www.umbpalopo.ac.id"
                               class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Email Kontak Akademik</label>
                        <input type="email" name="email_contact" x-model="emailContact"
                               placeholder="info@umbpalopo.ac.id"
                               class="form-input">
                    </div>
                </div>

                <!-- Logo Upload -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="w-16 h-16 rounded-lg bg-slate-100 border border-slate-200 p-2 flex items-center justify-center shrink-0">
                        <img src="{{ $activeSetting->institution_logo ? asset($activeSetting->institution_logo) : asset('assets/img/unhas.png') }}" 
                             alt="Logo" class="max-h-full max-w-full object-contain"
                             onerror="this.src='{{ asset('assets/img/unhas.png') }}'">
                    </div>
                    <div class="flex-1">
                        <label class="form-label">Unggah Logo Institusi Baru (Opsional)</label>
                        <input type="file" name="logo" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                        <p class="text-[11px] text-slate-400 mt-1">Format: PNG, JPG, JPEG, SVG. Sistem otomatis menghapus latar belakang (background removal) menjadi transparan.</p>
                    </div>
                </div>

                <!-- Running Text Marquee -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="form-label">Running Text / Pengumuman Layar TV Monitoring</label>
                    <textarea name="running_text" x-model="runningText" rows="2" placeholder="Tuliskan teks pengumuman berjalan untuk layar TV display lobby..."
                              class="form-input"></textarea>
                </div>
            </div>

            <!-- Card 2: Pengaturan Tema Warna Visual -->
            <div class="bg-white table-card border border-slate-200/80 shadow-sm p-5 sm:p-6 space-y-6 mt-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Pilihan Tema Warna Visual Sistem</h3>
                        <p class="text-xs text-slate-500">Pilih palet warna yang sesuai dengan identitas kampus Anda</p>
                    </div>
                </div>

                <!-- Grid Pilihan Warna -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($themePresets as $key => $preset)
                        <label @click="selectedTheme = '{{ $key }}'"
                               :class="selectedTheme === '{{ $key }}' ? 'ring-2 ring-brand-600 border-transparent shadow-md' : 'border-slate-200/80 hover:border-slate-300'"
                               class="relative p-4 rounded-lg border bg-white flex flex-col justify-between cursor-pointer transition-all">
                            <input type="radio" name="theme_color" value="{{ $key }}" class="sr-only" x-model="selectedTheme">
                            
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full shadow-inner border border-white" style="background-color: {{ $preset['primary'] }};"></span>
                                        <span class="w-5 h-5 rounded-full shadow-inner border border-white" style="background-color: {{ $preset['secondary'] }};"></span>
                                    </div>
                                    <span x-show="selectedTheme === '{{ $key }}'" class="text-brand-600 text-sm font-bold">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $preset['name'] }}</h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $preset['desc'] }}</p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-mono text-slate-400">
                                Primary: <span class="font-bold text-slate-700">{{ $preset['primary'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Custom Color Picker -->
                <div x-show="selectedTheme === 'custom'" x-cloak class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Pilih Kode Warna HEX:</label>
                    <input type="color" name="primary_color_hex" x-model="customPrimary" class="w-10 h-8 rounded cursor-pointer border border-slate-300">
                    <input type="text" name="primary_color_hex" x-model="customPrimary" class="w-32 px-3 py-1.5 bg-white border border-slate-200 rounded text-xs font-mono font-bold">
                </div>

                <!-- Action Button -->
                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-sm shadow-md shadow-brand-900/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan Branding & Terapkan Tema</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <!-- TAB 2: WAKTU PERKULIAHAN & DURASI JAM -->
    <div x-show="tab === 'perkuliahan'" x-cloak class="space-y-6">

        <!-- Active Profile Highlight Card -->
        @if($activeSetting)
        <div class="p-5 sm:p-6 table-card bg-gradient-to-r from-navy-950 via-slate-900 to-navy-900 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-xs font-bold mb-3 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Profil Pengaturan Aktif Saat Ini</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black">{{ $activeSetting->nama_pengaturan }}</h2>
                <p class="text-xs text-slate-400 mt-1">Digunakan untuk menghitung estimasi waktu jam keluar dan durasi absensi di seluruh sistem.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-lg bg-white/5 border border-white/10 text-center">
                <div class="p-2">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Menit / Jam</span>
                    <span class="text-lg sm:text-xl font-black text-brand-400">{{ $activeSetting->menit_perjam }} mnt</span>
                </div>
                <div class="p-2">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Pergantian</span>
                    <span class="text-lg sm:text-xl font-black text-amber-400">{{ $activeSetting->menit_pergantian }} mnt</span>
                </div>
                <div class="p-2">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Jam Mulai</span>
                    <span class="text-lg sm:text-xl font-black text-emerald-400">{{ $activeSetting->jam_kuliah }}</span>
                </div>
                <div class="p-2">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Istirahat</span>
                    <span class="text-lg sm:text-xl font-black text-blue-400">{{ $activeSetting->jam_mulai_istirahat }}-{{ $activeSetting->jam_selesai_istirahat }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Bar -->
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-base">Daftar Profil Waktu Perkuliahan</h3>
            <button @click="modalAdd = true" 
                    class="px-4 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-sm shadow-md shadow-brand-900/20 flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Profil Baru</span>
            </button>
        </div>

        <!-- Table Card -->
        <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/70">
                            <th class="py-3.5 px-6">Nama Profil</th>
                            <th class="py-3.5 px-6 text-center">Menit / Jam</th>
                            <th class="py-3.5 px-6 text-center">Menit Pergantian</th>
                            <th class="py-3.5 px-6 text-center">Jam Kuliah</th>
                            <th class="py-3.5 px-6 text-center">Jam Istirahat</th>
                            <th class="py-3.5 px-6 text-center">Jam / Hari</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pengaturans as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors {{ $p->is_active ? 'bg-emerald-50/20' : '' }}">
                                <td class="py-3.5 px-6 font-bold text-slate-800">
                                    <div>{{ $p->nama_pengaturan }}</div>
                                    <span class="text-[11px] text-slate-400 font-normal">Dibuat: {{ $p->created_at ? $p->created_at->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-700">
                                    {{ $p->menit_perjam }} Menit
                                </td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-700">
                                    {{ $p->menit_pergantian }} Menit
                                </td>
                                <td class="py-3.5 px-6 text-center font-mono text-xs">
                                    {{ $p->jam_kuliah }}
                                </td>
                                <td class="py-3.5 px-6 text-center font-mono text-xs">
                                    {{ $p->jam_mulai_istirahat }} - {{ $p->jam_selesai_istirahat }}
                                </td>
                                <td class="py-3.5 px-6 text-center text-xs">
                                    {{ $p->jam_perhari }} Jam
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($p->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <form action="{{ route('pengaturan.set-active', $p) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded text-xs font-semibold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors">
                                                Jadikan Aktif
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right space-x-1 whitespace-nowrap">
                                    <button @click="editData = {
                                                id: {{ $p->id }},
                                                nama: '{{ addslashes($p->nama_pengaturan) }}',
                                                perjam: {{ $p->menit_perjam }},
                                                pergantian: {{ $p->menit_pergantian }},
                                                jam_kuliah: '{{ $p->jam_kuliah }}',
                                                jam_mulai: '{{ $p->jam_mulai_istirahat }}',
                                                jam_selesai: '{{ $p->jam_selesai_istirahat }}',
                                                perhari: {{ $p->jam_perhari }},
                                                is_active: {{ $p->is_active ? 'true' : 'false' }}
                                            }; modalEdit = true"
                                            class="p-1.5 text-slate-500 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    Belum ada profil pengaturan perkuliahan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Tambah Profil Perkuliahan -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-plus-circle text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Profil Waktu Perkuliahan</h3>
                        <p class="text-xs text-slate-500">Atur durasi per jam, jeda pergantian, dan jam istirahat</p>
                    </div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <form action="{{ route('pengaturan.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Profil Pengaturan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pengaturan" required placeholder="Contoh: Semester Ganjil 2025/2026" class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Menit per Jam Kuliah <span class="text-red-500">*</span></label>
                            <input type="number" name="menit_perjam" value="50" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Menit Pergantian Jam <span class="text-red-500">*</span></label>
                            <input type="number" name="menit_pergantian" value="10" required class="form-input">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="text" name="jam_kuliah" value="08:00" required class="form-input text-center font-mono">
                        </div>
                        <div>
                            <label class="form-label">Istirahat Mulai <span class="text-red-500">*</span></label>
                            <input type="text" name="jam_mulai_istirahat" value="12:00" required class="form-input text-center font-mono">
                        </div>
                        <div>
                            <label class="form-label">Istirahat Selesai <span class="text-red-500">*</span></label>
                            <input type="text" name="jam_selesai_istirahat" value="13:00" required class="form-input text-center font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 items-center pt-1">
                        <div>
                            <label class="form-label">Total Jam per Hari <span class="text-red-500">*</span></label>
                            <input type="number" name="jam_perhari" value="8" required class="form-input">
                        </div>
                        <div class="pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked
                                       class="rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                                <span class="text-xs font-bold text-slate-700">Langsung Aktifkan</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 shadow-md">Simpan Profil</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Profil Perkuliahan -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalEdit = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Edit Profil Waktu Perkuliahan</h3>
                        <p class="text-xs text-slate-500">Perbarui parameter jam dan durasi perkuliahan</p>
                    </div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <form :action="'{{ url('pengaturan') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Profil Pengaturan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pengaturan" x-model="editData.nama" required class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Menit per Jam Kuliah <span class="text-red-500">*</span></label>
                            <input type="number" name="menit_perjam" x-model="editData.perjam" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Menit Pergantian Jam <span class="text-red-500">*</span></label>
                            <input type="number" name="menit_pergantian" x-model="editData.pergantian" required class="form-input">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="text" name="jam_kuliah" x-model="editData.jam_kuliah" required class="form-input text-center font-mono">
                        </div>
                        <div>
                            <label class="form-label">Istirahat Mulai <span class="text-red-500">*</span></label>
                            <input type="text" name="jam_mulai_istirahat" x-model="editData.jam_mulai" required class="form-input text-center font-mono">
                        </div>
                        <div>
                            <label class="form-label">Istirahat Selesai <span class="text-red-500">*</span></label>
                            <input type="text" name="jam_selesai_istirahat" x-model="editData.jam_selesai" required class="form-input text-center font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 items-center pt-1">
                        <div>
                            <label class="form-label">Total Jam per Hari <span class="text-red-500">*</span></label>
                            <input type="number" name="jam_perhari" x-model="editData.perhari" required class="form-input">
                        </div>
                        <div class="pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-model="editData.is_active"
                                       class="rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                                <span class="text-xs font-bold text-slate-700">Status Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 shadow-md">Perbarui Profil</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
