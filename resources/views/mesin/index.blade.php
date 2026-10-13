@extends('layouts.app')

@section('title', 'Mesin Biometrik & Fingerprint')
@section('page-title', 'Mesin Biometrik & Fingerprint')
@section('page-subtitle', 'Manajemen perangkat absensi multi-brand/model, uji koneksi TCP/IP, dan simulator scan')

@section('content')
<div class="space-y-5" x-data="{ 
    modalAdd: false, 
    modalEdit: false, 
    editData: {}, 
    modalPushInfo: false,
    modalImportDeli: false,
    addBrand: 'Deli',
    addPort: '5005',
    infoTab: 'deli'
}">

    <!-- Top Action Bar & Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <!-- Search & Filter Brand -->
        <form action="{{ route('mesin.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full lg:max-w-xl">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari serial number, model, IP address..."
                       class="form-input pl-9 py-2 shadow-sm" style="border-radius:8px;">
            </div>

            <select name="brand" onchange="this.form.submit()" class="form-input py-2 w-auto shadow-sm" style="border-radius:8px;">
                <option value="">Semua Brand Mesin</option>
                @foreach($brandOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('brand') == $key ? 'selected' : '' }}>{{ $key }}</option>
                @endforeach
            </select>

            @if(request('q') || request('brand'))
                <a href="{{ route('mesin.index') }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors" title="Reset Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <!-- Import Log USB Flashdisk Mesin Deli -->
            <button @click="modalImportDeli = true" type="button" class="px-3.5 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs border border-red-200/80 shadow-sm flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-file-import text-red-600"></i>
                <span>Import Log USB (Deli)</span>
            </button>

            <!-- Tombol Dokumentasi Webhook API Push & Panduan Deli -->
            <button @click="modalPushInfo = true" type="button" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs shadow-sm flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-cloud-arrow-up text-brand-600"></i>
                <span>Push API / Panduan Deli</span>
            </button>

            <!-- Sync All Button -->
            <form action="{{ route('mesin.sync-all') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-md flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Cek Status Semua</span>
                </button>
            </form>

            <!-- Tambah Mesin Multi-Model -->
            <button @click="modalAdd = true" 
                    class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold text-xs shadow-md shadow-brand-900/20 flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Daftarkan Mesin Baru</span>
            </button>
        </div>
    </div>

    <!-- Simulator Tap Quick Drawer / Banner -->
    <div class="p-5 rounded-xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-slate-50 border border-amber-500/20 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl font-bold shadow-md shadow-amber-500/20 shrink-0">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-sm">Simulator Tap Scan Biometrik</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900">Virtual Tester</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Uji coba simulasi presensi mahasiswa/dosen secara real-time tanpa perlu kehadiran fisik di mesin.</p>
                </div>
            </div>

            <!-- Form Simulator -->
            <form action="{{ route('mesin.simulate-tap') }}" method="POST" class="flex flex-wrap items-center gap-2">
                @csrf
                <select name="mesin_id" required class="form-input py-2 w-auto" style="border-radius:8px;font-size:0.75rem;">
                    @foreach($mesins as $m)
                        <option value="{{ $m->id }}">{{ $m->brand_mesin }} {{ $m->model_tipe ? "($m->model_tipe)" : '' }} - {{ $m->ruangan->nama_ruangan ?? 'Tanpa Ruangan' }}</option>
                    @endforeach
                </select>

                <select name="tipe_pengguna" class="form-input py-2 w-auto" style="border-radius:8px;font-size:0.75rem;">
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>

                <input type="text" name="pin" required placeholder="PIN Finger (cth: 2001)"
                       class="form-input py-2 font-mono font-bold" style="border-radius:8px;width:160px;font-size:0.75rem;">

                <input type="text" name="scan_time" value="{{ now()->format('H:i') }}" placeholder="HH:MM"
                       class="form-input py-2 font-mono font-bold text-center" style="border-radius:8px;width:80px;font-size:0.75rem;" title="Jam Scan (WITA)">

                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-hand-pointer"></i>
                    <span>Tap Scan</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Table Card Mesin Fingerprint -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Brand & Model Perangkat</th>
                        <th class="py-3 px-4">Protokol & Koneksi</th>
                        <th class="py-3 px-4">Lokasi Ruangan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Sync Terakhir</th>
                        <th class="py-3 px-4 text-right w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mesins as $index => $m)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">
                                {{ $mesins->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-50 to-brand-100 border border-brand-200 text-brand-700 flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                        @if(stripos($m->brand_mesin, 'deli') !== false)
                                            <span class="font-extrabold text-[10px] text-red-600 tracking-tight">DELI</span>
                                        @elseif(stripos($m->brand_mesin, 'zk') !== false)
                                            <i class="fa-solid fa-microchip text-sm"></i>
                                        @elseif(stripos($m->brand_mesin, 'hik') !== false)
                                            <i class="fa-solid fa-camera text-sm"></i>
                                        @elseif(stripos($m->brand_mesin, 'finger') !== false)
                                            <i class="fa-solid fa-fingerprint text-sm"></i>
                                        @else
                                            <i class="fa-solid fa-server text-sm"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 font-semibold text-slate-900 text-sm">
                                            <span>{{ $m->brand_mesin }}</span>
                                            @if($m->model_tipe)
                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-mono">{{ $m->model_tipe }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xs font-mono text-slate-500 mt-0.5">
                                            SN: <span class="font-bold text-slate-700">{{ $m->serial }}</span>
                                            @if($m->kode)
                                                <span class="text-slate-400">&bull; Kode: {{ $m->kode }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <div class="font-mono text-slate-700 font-semibold flex items-center gap-1">
                                    <span>{{ $m->ip_address ?: '-' }}</span>
                                    @if($m->port)
                                        <span class="text-slate-400">:{{ $m->port }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $m->tipe_koneksi == 'standalone_tcp' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $m->tipe_koneksi == 'cloud_adms' ? 'bg-purple-50 text-purple-700' : '' }}
                                        {{ $m->tipe_koneksi == 'webhook_api' ? 'bg-amber-50 text-amber-700' : '' }}
                                        {{ $m->tipe_koneksi == 'standalone_udp' ? 'bg-slate-100 text-slate-600' : '' }}">
                                        {{ str_replace('_', ' ', $m->tipe_koneksi) }}
                                    </span>
                                    @if($m->comm_key)
                                        <span class="text-[10px] text-slate-400 font-mono" title="Comm Key: {{ $m->comm_key }}"><i class="fa-solid fa-key text-[9px]"></i> Key</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-[10px]"></i>
                                    {{ $m->ruangan->nama_ruangan ?? 'Belum Ditugaskan' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($m->status_online)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Offline
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center text-xs text-slate-500 font-mono">
                                {{ $m->last_synced_at ? $m->last_synced_at->diffForHumans() : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Test Connection Button -->
                                    <form action="{{ route('mesin.test-connection', $m) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded hover:bg-emerald-50 transition-colors" title="Uji Ping / Socket">
                                            <i class="fa-solid fa-network-wired text-xs"></i>
                                        </button>
                                    </form>

                                    <!-- Edit Button -->
                                    <button @click="editData = {
                                                id: {{ $m->id }},
                                                serial: '{{ $m->serial }}',
                                                nama_mesin: '{{ addslashes($m->nama_mesin) }}',
                                                brand_mesin: '{{ $m->brand_mesin }}',
                                                model_tipe: '{{ $m->model_tipe }}',
                                                tipe_koneksi: '{{ $m->tipe_koneksi }}',
                                                activation: '{{ $m->activation }}',
                                                kode: '{{ $m->kode }}',
                                                no_mesin: '{{ $m->no_mesin }}',
                                                ip_address: '{{ $m->ip_address }}',
                                                port: '{{ $m->port }}',
                                                comm_key: '{{ $m->comm_key }}',
                                                ruangan_id: '{{ $m->ruangan_id }}'
                                            }; modalEdit = true"
                                            class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Edit Mesin">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('mesin.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mesin {{ $m->serial }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Hapus Mesin">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-14 text-slate-400">
                                <i class="fa-solid fa-fingerprint text-3xl text-slate-300 mb-2 block"></i>
                                <p>Tidak ada data mesin fingerprint ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mesins->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $mesins->links() }}
            </div>
        @endif
    </div>

    <!-- =========================================== -->
    <!-- Modal Push API / Cloud ADMS & Deli Info     -->
    <!-- =========================================== -->
    <div x-show="modalPushInfo" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalPushInfo = false" class="modal-box bg-white w-full max-w-xl">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-fingerprint text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Panduan Koneksi Mesin Fingerprint</h3>
                        <p class="text-xs text-slate-500">Integrasi Mesin Deli, ZKTeco, Solution, & Cloud ADMS</p>
                    </div>
                </div>
                <button @click="modalPushInfo = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Tab Switcher -->
            <div class="px-6 pt-3 border-b border-slate-100 flex gap-2">
                <button type="button" @click="infoTab = 'deli'" 
                        :class="infoTab === 'deli' ? 'border-red-600 text-red-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="pb-2.5 px-3 border-b-2 text-xs transition-colors flex items-center gap-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800 text-[10px] font-extrabold">DELI</span>
                    <span>Panduan Mesin Deli</span>
                </button>
                <button type="button" @click="infoTab = 'general'" 
                        :class="infoTab === 'general' ? 'border-brand-600 text-brand-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="pb-2.5 px-3 border-b-2 text-xs transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                    <span>Push Webhook & ADMS Umum</span>
                </button>
            </div>

            <div class="modal-body space-y-4 text-xs text-slate-600 leading-relaxed max-h-[70vh] overflow-y-auto">
                <!-- TAB 1: DELI -->
                <div x-show="infoTab === 'deli'" class="space-y-3.5">
                    <div class="p-3 bg-red-50/70 rounded-lg border border-red-200/80 text-red-950 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-xs text-red-900">
                            <i class="fa-solid fa-circle-check text-red-600"></i>
                            Dukungan Penuh Mesin Biometrik Merek Deli
                        </div>
                        <p class="text-[11px] text-red-800 leading-normal">
                            Sistem mendukung seluruh tipe mesin Deli (seperti E3960, E3961, E3962, 15152, 15153, 15154, E2180, dll) melalui 3 metode fleksibel berikut:
                        </p>
                    </div>

                    <!-- Metode 1: LAN Socket -->
                    <div class="border border-slate-200 rounded-lg p-3 space-y-1.5 bg-slate-50/50">
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">1</span>
                            Koneksi Jaringan Lokal (TCP/IP Port 5005 & 4370)
                        </div>
                        <p class="text-slate-600 text-[11px]">
                            Hubungkan mesin Deli ke Switch/Router LAN via kabel RJ45 atau Wi-Fi. Masuk ke <strong>Menu &rarr; Komunikasi (Comm) &rarr; Jaringan (Network)</strong>:
                        </p>
                        <ul class="list-disc pl-5 text-[11px] text-slate-600 space-y-0.5">
                            <li><strong>IP Address:</strong> Atur IP satu subnet dengan server (cth: <span class="font-mono font-semibold text-slate-800">192.168.1.205</span>).</li>
                            <li><strong>Port:</strong> Atur ke <span class="font-mono font-bold text-red-600">5005</span> (port standar Deli) atau <span class="font-mono font-bold text-slate-700">4370</span>.</li>
                            <li><strong>Comm Key / Password:</strong> Biarkan default <span class="font-mono font-semibold text-slate-800">0</span>.</li>
                            <li>Daftarkan mesin di web ini, lalu klik tombol <i class="fa-solid fa-network-wired text-slate-500"></i> (Uji Ping) pada tabel.</li>
                        </ul>
                    </div>

                    <!-- Metode 2: Push Webhook -->
                    <div class="border border-slate-200 rounded-lg p-3 space-y-1.5 bg-slate-50/50">
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">2</span>
                            Push Webhook / Cloud Server (Real-time Sync)
                        </div>
                        <p class="text-slate-600 text-[11px]">
                            Jika mesin Deli Anda mendukung fitur Cloud / ADMS Webhook, masuk ke <strong>Menu &rarr; Komunikasi &rarr; Cloud Server</strong>:
                        </p>
                        <div class="p-2.5 bg-slate-900 rounded font-mono text-[11px] text-emerald-400">
                            POST {{ url('/api/fingerprint/deli') }}
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Mendukung parameter Deli: <span class="font-mono text-slate-700">user_id, pin, dev_sn, verify_time</span> dalam format JSON atau form-data.
                        </p>
                    </div>

                    <!-- Metode 3: USB Flashdisk -->
                    <div class="border border-slate-200 rounded-lg p-3 space-y-1.5 bg-slate-50/50">
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">3</span>
                            Ekspor Flashdisk USB (Paling Praktis Tanpa Kabel LAN)
                        </div>
                        <p class="text-slate-600 text-[11px]">
                            Sangat cocok untuk ruang kuliah FK yang belum memiliki tarikan kabel LAN:
                        </p>
                        <ol class="list-decimal pl-5 text-[11px] text-slate-600 space-y-0.5">
                            <li>Colokkan flashdisk USB ke samping/bawah mesin Deli.</li>
                            <li>Buka <strong>Menu &rarr; Pengelolaan USB (USB Disk) &rarr; Unduh Data Kehadiran</strong>.</li>
                            <li>Mesin Deli akan menyimpan file laporan absensi (<span class="font-mono text-slate-800">.xlsx</span>, <span class="font-mono text-slate-800">.dat</span>, atau <span class="font-mono text-slate-800">.csv</span>).</li>
                            <li>Klik tombol <strong>"Import Log USB (Deli)"</strong> di atas untuk memproses file tersebut ke absensi sistem.</li>
                        </ol>
                    </div>
                </div>

                <!-- TAB 2: GENERAL ADMS & WEBHOOK -->
                <div x-show="infoTab === 'general'" class="space-y-3.5">
                    <p>
                        Sistem mendukung integrasi multi-brand (ZKTeco, Solution, Fingerspot, Hikvision, BioFinger) baik melalui <strong>TCP/IP Polling Port 4370</strong> maupun <strong>Push Webhook / Cloud ADMS</strong>.
                    </p>
                    <div class="p-3.5 bg-slate-900 rounded-lg text-slate-200 font-mono text-[11px] space-y-1">
                        <div class="text-slate-400">// Endpoint Webhook Penerima Log Absensi:</div>
                        <div class="text-emerald-400 font-bold">POST {{ url('/api/fingerprint/push') }}</div>
                        <div class="text-slate-400 mt-2">// Endpoint Khusus Deli / ADMS CData:</div>
                        <div class="text-emerald-400 font-bold">POST {{ url('/iclock/cdata') }}</div>
                        <div class="text-slate-400 mt-2">// Format Payload JSON / Form-Data:</div>
                        <div class="text-slate-300">{</div>
                        <div class="text-slate-300 pl-4">"sn": "SN-DEVICE-01",</div>
                        <div class="text-slate-300 pl-4">"pin": "2001",</div>
                        <div class="text-slate-300 pl-4">"scan_date": "2026-09-29 08:15:00"</div>
                        <div class="text-slate-300">}</div>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900 text-xs">
                        <strong>Catatan ADMS:</strong> Untuk mesin ZKTeco / Solution dengan fitur ADMS / Cloud Server, atur <em>Server IP</em> ke IP server ini dan <em>Server Port</em> ke port web aplikasi.
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" @click="modalPushInfo = false" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs">Tutup</button>
            </div>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Import Log USB (Deli / Flashdisk)     -->
    <!-- =========================================== -->
    <div x-show="modalImportDeli" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalImportDeli = false" class="modal-box bg-white w-full max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-file-excel text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Import Log USB Mesin Deli</h3>
                        <p class="text-xs text-slate-500">Unggah file presensi offline dari USB Flashdisk</p>
                    </div>
                </div>
                <button @click="modalImportDeli = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('mesin.import-log') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body space-y-4">
                    <div class="p-3 bg-red-50/80 rounded-lg border border-red-200/80 text-xs text-red-950 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-red-900">
                            <i class="fa-solid fa-usb text-red-600"></i>
                            Sinkronisasi Presensi Offline
                        </div>
                        <p class="text-[11px] text-red-900 leading-relaxed">
                            Unduh data kehadiran dari mesin Deli ke flashdisk (Menu &rarr; Pengelolaan USB &rarr; Unduh Log), lalu unggah file hasil ekspor ke formulir di bawah.
                        </p>
                    </div>

                    <!-- Pilih Mesin Penempatan (Opsional) -->
                    <div>
                        <label class="form-label">Tautkan ke Perangkat / Ruangan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <select name="mesin_id" class="form-input">
                            <option value="">-- Otomatis (Gunakan Serial Default) --</option>
                            @foreach($mesins as $m)
                                <option value="{{ $m->id }}">{{ $m->brand_mesin }} {{ $m->model_tipe ? "($m->model_tipe)" : '' }} - {{ $m->ruangan->nama_ruangan ?? 'Tanpa Ruangan' }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Jika dipilih, lokasi log scan akan otomatis diarahkan ke ruangan mesin tersebut.</p>
                    </div>

                    <!-- File Dropzone -->
                    <div>
                        <label class="form-label">File Laporan / Log Presensi <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required accept=".xlsx,.xls,.csv,.dat,.txt" class="form-input text-xs py-2 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            Mendukung file: <span class="font-mono font-semibold text-slate-700">.xlsx, .xls</span> (Excel Deli), <span class="font-mono font-semibold text-slate-700">.dat</span> (attlog.dat / GLG_001.dat), <span class="font-mono font-semibold text-slate-700">.csv, .txt</span>.
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="modalImportDeli = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-red-600 text-white hover:bg-red-700 transition-colors flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Proses & Simpan Log</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Tambah Mesin Multi-Model              -->
    <!-- =========================================== -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-8 p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-2xl mb-8">
            <!-- Header -->
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-plus-circle text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftarkan Mesin Biometrik Baru</h3>
                        <p class="text-xs text-slate-500">Tambahkan perangkat fingerprint Deli, ZKTeco, Solution, atau RFID card.</p>
                    </div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('mesin.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">

                    <!-- Brand & Model -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Brand / Pabrikan <span class="text-red-500">*</span></label>
                            <select name="brand_mesin" x-model="addBrand" @change="if(addBrand === 'Deli') { addPort = '5005'; } else if (addPort === '5005') { addPort = '4370'; }" required class="form-input">
                                @foreach($brandOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Model / Tipe Perangkat</label>
                            <input type="text" name="model_tipe" placeholder="Contoh: E3960, 15152, K40, X100-C" class="form-input">
                        </div>
                    </div>

                    <!-- Serial & Nama -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Serial Number (SN) <span class="text-red-500">*</span></label>
                            <input type="text" name="serial" required placeholder="Contoh: DELI-E3960-001" class="form-input font-mono">
                        </div>
                        <div>
                            <label class="form-label">Nama / Label Mesin</label>
                            <input type="text" name="nama_mesin" placeholder="Contoh: Fingerprint R. Kuliah Anatomi" class="form-input">
                        </div>
                    </div>

                    <!-- Tipe Koneksi & Ruangan -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Tipe Koneksi / Protokol <span class="text-red-500">*</span></label>
                            <select name="tipe_koneksi" required class="form-input">
                                <option value="standalone_tcp">Standalone TCP/IP (LAN / Wi-Fi Socket)</option>
                                <option value="cloud_adms">Push ADMS (Cloud Server Sync)</option>
                                <option value="webhook_api">Webhook API / HTTP Push</option>
                                <option value="standalone_udp">Standalone UDP Socket</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Ruangan Penempatan</label>
                            <select name="ruangan_id" class="form-input">
                                <option value="">-- Pilih Ruang Kuliah --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- IP, Port, CommKey, DeviceID -->
                    <div class="grid grid-cols-4 gap-3">
                        <div class="col-span-2">
                            <label class="form-label">IP Address Perangkat</label>
                            <input type="text" name="ip_address" placeholder="192.168.1.205" class="form-input font-mono">
                        </div>
                        <div>
                            <label class="form-label">Port</label>
                            <input type="text" name="port" x-model="addPort" class="form-input font-mono">
                            <p class="text-[10px] text-red-600 font-semibold mt-1" x-show="addBrand === 'Deli'">Deli: 5005 / 4370</p>
                        </div>
                        <div>
                            <label class="form-label">No. Perangkat (ID)</label>
                            <input type="text" name="no_mesin" value="1" class="form-input font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Comm Key / Password</label>
                        <input type="text" name="comm_key" value="0" placeholder="0" class="form-input font-mono" style="max-width:200px;">
                    </div>

                    <!-- Metode Verifikasi -->
                    <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                        <label class="form-label mb-2">Metode Verifikasi yang Didukung</label>
                        <div class="flex flex-wrap gap-4 text-xs text-slate-700">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="metode_verifikasi[]" value="fingerprint" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>Fingerprint</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="metode_verifikasi[]" value="face" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>Face Recognition</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="metode_verifikasi[]" value="rfid" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>RFID Card</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="metode_verifikasi[]" value="pin" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>PIN / Password</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perangkat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- Modal Edit Mesin                            -->
    <!-- =========================================== -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-8 p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalEdit = false" class="modal-box bg-white w-full max-w-2xl mb-8">
            <!-- Header -->
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Edit Konfigurasi Mesin Biometrik</h3>
                        <p class="text-xs text-slate-500">Ubah pengaturan model, koneksi, atau lokasi penempatan perangkat.</p>
                    </div>
                </div>
                <button @click="modalEdit = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form :action="'{{ url('mesin') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body space-y-4">

                    <!-- Brand & Model -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Brand / Pabrikan <span class="text-red-500">*</span></label>
                            <select name="brand_mesin" x-model="editData.brand_mesin" @change="if(editData.brand_mesin === 'Deli' && (!editData.port || editData.port == '4370')) editData.port = '5005'" required class="form-input">
                                @foreach($brandOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Model / Tipe Perangkat</label>
                            <input type="text" name="model_tipe" x-model="editData.model_tipe" placeholder="Contoh: E3960, 15152, K40, X100-C" class="form-input">
                        </div>
                    </div>

                    <!-- Serial & Nama -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Serial Number (SN) <span class="text-red-500">*</span></label>
                            <input type="text" name="serial" x-model="editData.serial" required class="form-input font-mono">
                        </div>
                        <div>
                            <label class="form-label">Nama / Label Mesin</label>
                            <input type="text" name="nama_mesin" x-model="editData.nama_mesin" class="form-input">
                        </div>
                    </div>

                    <!-- Tipe Koneksi & Ruangan -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Tipe Koneksi / Protokol <span class="text-red-500">*</span></label>
                            <select name="tipe_koneksi" x-model="editData.tipe_koneksi" required class="form-input">
                                <option value="standalone_tcp">Standalone TCP/IP (LAN / Wi-Fi Socket)</option>
                                <option value="cloud_adms">Push ADMS (Cloud Server Sync)</option>
                                <option value="webhook_api">Webhook API / HTTP Push</option>
                                <option value="standalone_udp">Standalone UDP Socket</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Ruangan Penempatan</label>
                            <select name="ruangan_id" x-model="editData.ruangan_id" class="form-input">
                                <option value="">-- Pilih Ruang Kuliah --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- IP, Port, CommKey, Kode -->
                    <div class="grid grid-cols-4 gap-3">
                        <div class="col-span-2">
                            <label class="form-label">IP Address Perangkat</label>
                            <input type="text" name="ip_address" x-model="editData.ip_address" class="form-input font-mono">
                        </div>
                        <div>
                            <label class="form-label">Port</label>
                            <input type="text" name="port" x-model="editData.port" class="form-input font-mono">
                            <p class="text-[10px] text-red-600 font-semibold mt-1" x-show="editData.brand_mesin === 'Deli'">Deli: 5005 / 4370</p>
                        </div>
                        <div>
                            <label class="form-label">No. Perangkat (ID)</label>
                            <input type="text" name="no_mesin" x-model="editData.no_mesin" class="form-input font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Comm Key / Password</label>
                            <input type="text" name="comm_key" x-model="editData.comm_key" class="form-input font-mono">
                        </div>
                        <div>
                            <label class="form-label">Kode / Label</label>
                            <input type="text" name="kode" x-model="editData.kode" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Perbarui Mesin
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
