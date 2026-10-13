@extends('layouts.app')

@section('title', 'Log Presensi Realtime')
@section('page-title', 'Live Stream Log Presensi')
@section('page-subtitle', 'Pantau aktivitas scan sidik jari dan konektivitas mesin biometrik secara langsung (real-time).')

@section('content')
<div x-data="realtimeLogger({
        feedUrl: '{{ route('log-realtime.feed') }}',
        pingUrlBase: '{{ url('log-realtime/ping') }}',
        pullUrlBase: '{{ url('log-realtime/pull') }}',
        simulateUrl: '{{ route('mesin.simulate-tap') }}',
        initialLogs: {{ Js::from($initialLogs) }},
        initialLatestId: {{ $latestId }},
        totalScansToday: {{ $totalScansToday }}
    })" 
    x-init="startStreaming()" 
    class="space-y-6">

    <!-- ================================================= -->
    <!-- 1. LIVE CONTROL BAR & MESIN CONNECTIVITY STATUS   -->
    <!-- ================================================= -->
    <div class="card p-5 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white rounded-2xl shadow-xl border border-slate-700/60 relative overflow-hidden">
        <!-- Background decorative ambient blur -->
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 mb-1.5">
                    <span class="relative flex h-3 w-3">
                        <span x-show="isStreaming" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" :class="isStreaming ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    </span>
                    <h2 class="text-base font-extrabold text-white tracking-tight flex items-center gap-2">
                        <span>LIVE SCAN RADAR</span>
                        <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded-full font-bold"
                              :class="isStreaming ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30'"
                              x-text="isStreaming ? 'Terhubung (Polling Aktif)' : 'Streaming Dijeda'">
                        </span>
                    </h2>
                </div>
                <p class="text-xs text-slate-300">
                    Sistem mendengarkan scan masuk dari mesin biometrik LAN/Wi-Fi socket (port 4370) dan push webhook.
                </p>
            </div>

            <!-- Controls: Auto refresh, Sound, Refresh button -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <!-- Sound chime toggle -->
                <button type="button" @click="toggleSound()" 
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors border"
                        :class="soundEnabled ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-800 text-slate-400 border-slate-700 hover:text-slate-200'">
                    <i class="fa-solid" :class="soundEnabled ? 'fa-volume-high text-emerald-400' : 'fa-volume-xmark'"></i>
                    <span x-text="soundEnabled ? 'Suara Aktif' : 'Suara Bisu'"></span>
                </button>

                <!-- Pause / Play Stream -->
                <button type="button" @click="toggleStreaming()" 
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors border"
                        :class="isStreaming ? 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700' : 'bg-amber-500 text-slate-950 border-amber-400 font-bold'">
                    <i class="fa-solid" :class="isStreaming ? 'fa-pause' : 'fa-play'"></i>
                    <span x-text="isStreaming ? 'Jeda Stream' : 'Lanjutkan Stream'"></span>
                </button>

                <!-- Manual refresh -->
                <button type="button" @click="fetchLogs(true)" :disabled="isLoading"
                        class="px-3 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs flex items-center gap-1.5 transition-colors shadow-sm disabled:opacity-50">
                    <i class="fa-solid fa-arrows-rotate" :class="isLoading ? 'fa-spin' : ''"></i>
                    <span>Cek Sekarang</span>
                </button>
            </div>
        </div>

        <!-- Quick Summary Metrics -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-slate-700/60 text-xs">
            <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                <span class="text-slate-400 block text-[11px]">Total Scan Hari Ini</span>
                <span class="text-lg font-black text-white font-mono" x-text="totalScansTodayCount">
                    {{ $totalScansToday }}
                </span>
            </div>
            <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                <span class="text-slate-400 block text-[11px]">Log Ditampilkan</span>
                <span class="text-lg font-black text-emerald-400 font-mono" x-text="logs.length"></span>
            </div>
            <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                <span class="text-slate-400 block text-[11px]">Mesin Terdaftar</span>
                <span class="text-lg font-black text-sky-400 font-mono">{{ $mesins->count() }} Mesin</span>
            </div>
            <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                <span class="text-slate-400 block text-[11px]">Jam Server Lokal</span>
                <span class="text-lg font-black text-amber-400 font-mono" x-text="serverClock">
                    {{ now()->format('H:i:s') }}
                </span>
            </div>
        </div>
    </div>

    <!-- ================================================= -->
    <!-- 2. STATUS PERANGKAT / MESIN FINGERPRINT AKTIF      -->
    <!-- ================================================= -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-network-wired text-brand-600"></i>
                Status Konektivitas Perangkat Fisik (Socket Socket Port 4370)
            </h3>
            <span class="text-xs text-slate-500">
                Klik tombol <strong class="text-slate-700">Uji Ping</strong> untuk memeriksa apakah mesin merespons langsung.
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
            @forelse($mesins as $m)
                <div class="card p-4 rounded-xl border transition-all hover:shadow-md"
                     :class="mesinStatus[{{ $m->id }}]?.isOnline ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-white'"
                     x-data="{ pinging: false }">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold text-slate-900 truncate" title="{{ $m->nama_mesin }}">
                                {{ $m->nama_mesin }}
                            </h4>
                            <p class="text-[11px] text-slate-500 truncate">
                                {{ $m->ruangan->nama_ruangan ?? 'Tanpa Lokasi Ruangan' }}
                            </p>
                        </div>
                        <!-- Status indicator badge -->
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="mesinStatus[{{ $m->id }}]?.isOnline ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="mesinStatus[{{ $m->id }}]?.isOnline ? 'bg-emerald-600 animate-pulse' : 'bg-slate-400'"></span>
                            <span x-text="mesinStatus[{{ $m->id }}]?.isOnline ? 'ONLINE' : 'OFFLINE'">
                                {{ $m->status_online ? 'ONLINE' : 'OFFLINE' }}
                            </span>
                        </span>
                    </div>

                    <div class="space-y-1 my-2.5 text-[11px] font-mono text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-100">
                        <div class="flex justify-between">
                            <span class="text-slate-400">IP Socket:</span>
                            <strong class="text-slate-800">{{ $m->ip_address ?: '-' }}:{{ $m->port ?: '4370' }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Pabrikan:</span>
                            <span class="text-slate-700 truncate max-w-[120px]">{{ $m->brand_mesin }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Serial No:</span>
                            <span class="text-slate-700 truncate max-w-[120px]">{{ $m->serial }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 gap-1.5" x-data="{ pulling: false }">
                        <button type="button" 
                                @click="testPingMachine({{ $m->id }})" 
                                :disabled="pinging"
                                class="flex-1 py-1.5 px-2 rounded-lg text-[10px] font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors shadow-sm disabled:opacity-50 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-satellite-dish text-[9px]" :class="pinging ? 'fa-spin text-emerald-400' : ''"></i>
                            <span>Uji Ping</span>
                        </button>
                        <button type="button" 
                                @click="pullFromMachine({{ $m->id }})" 
                                :disabled="pulling"
                                class="flex-1 py-1.5 px-2 rounded-lg text-[10px] font-bold bg-brand-700 text-white hover:bg-brand-800 transition-colors shadow-sm disabled:opacity-50 flex items-center justify-center gap-1"
                                title="Tarik semua data scan yang ada di memori mesin fisik">
                            <i class="fa-solid fa-cloud-arrow-down text-[9px]" :class="pulling ? 'fa-bounce text-amber-300' : ''"></i>
                            <span>Tarik Scan</span>
                        </button>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1 text-center truncate" x-text="mesinStatus[{{ $m->id }}]?.msg || '{{ $m->last_synced_at ? $m->last_synced_at->diffForHumans() : 'Belum sync' }}'"></div>
                </div>
            @empty
                <div class="col-span-full p-6 text-center text-slate-400 text-xs bg-white rounded-xl border border-slate-200">
                    Belum ada mesin yang didaftarkan. Tambahkan mesin di menu <a href="{{ route('mesin.index') }}" class="text-brand-600 underline font-bold">Fingerprint / Mesin</a>.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ================================================= -->
    <!-- 3. DUA KOLOM: LIVE LOG STREAM + TEST SCAN TOOL     -->
    <!-- ================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI (2 Kolom): LIVE LOG SCAN STREAM -->
        <div class="lg:col-span-2 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-brand-600"></i>
                        <span>Aktivitas Scan Terbaru</span>
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-brand-50 text-brand-700 border border-brand-200"
                          x-text="logs.length + ' Data'">
                    </span>
                </div>

                <!-- Search / filter box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" x-model="searchQuery" 
                           placeholder="Filter nama, PIN, atau NIM..."
                           class="form-input text-xs py-1.5 pl-8 pr-3 rounded-lg border-slate-200 w-full focus:ring-brand-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- List Card Feed -->
            <div class="space-y-2.5">
                <template x-for="(log, idx) in filteredLogs" :key="log.id">
                    <div class="p-3.5 rounded-xl border transition-all duration-300 bg-white shadow-sm flex items-center justify-between gap-3.5 hover:shadow-md"
                         :class="{
                             'border-emerald-300 ring-2 ring-emerald-500/20 bg-emerald-50/20': log.isNew,
                             'border-slate-200': !log.isNew
                         }">
                        <!-- Icon User / Identity -->
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm border font-bold text-sm"
                                 :class="{
                                     'bg-blue-50 text-blue-700 border-blue-200': log.tipe === 'mahasiswa',
                                     'bg-purple-50 text-purple-700 border-purple-200': log.tipe === 'dosen',
                                     'bg-slate-100 text-slate-600 border-slate-300': log.tipe === 'unknown'
                                 }">
                                <template x-if="log.tipe === 'mahasiswa'">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </template>
                                <template x-if="log.tipe === 'dosen'">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </template>
                                <template x-if="log.tipe === 'unknown'">
                                    <i class="fa-solid fa-fingerprint"></i>
                                </template>
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-xs font-black text-slate-900 truncate" x-text="log.nama"></h4>
                                    <!-- Badge tipe -->
                                    <span class="text-[9px] uppercase px-1.5 py-0.2 rounded font-extrabold"
                                          :class="{
                                              'bg-blue-100 text-blue-800': log.tipe === 'mahasiswa',
                                              'bg-purple-100 text-purple-800': log.tipe === 'dosen',
                                              'bg-slate-100 text-slate-600': log.tipe === 'unknown'
                                          }"
                                          x-text="log.tipe">
                                    </span>
                                    <template x-if="log.isNew">
                                        <span class="text-[9px] px-1.5 py-0.2 rounded font-black bg-emerald-500 text-white animate-pulse">
                                            BARU
                                        </span>
                                    </template>
                                </div>

                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    <span class="font-mono font-bold text-brand-700" x-text="'PIN ' + log.pin"></span>
                                    <span class="text-slate-300 mx-1">&bull;</span>
                                    <span x-text="log.identitas"></span>
                                    <span class="text-slate-300 mx-1">&bull;</span>
                                    <span x-text="log.sub"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Lokasi Ruangan & Waktu -->
                        <div class="text-right shrink-0">
                            <div class="text-xs font-mono font-black text-slate-900" x-text="log.scan_time"></div>
                            <p class="text-[10px] text-slate-400 truncate max-w-[140px]" x-text="log.ruangan"></p>
                            <span class="text-[9px] text-emerald-600 font-semibold inline-flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[8px]"></i>
                                <span x-text="log.time_ago"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Empty state -->
                <div x-show="filteredLogs.length === 0" class="card p-12 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                    <i class="fa-solid fa-fingerprint text-4xl text-slate-300 mb-3 block animate-pulse"></i>
                    <p class="font-bold text-slate-600">Belum ada rekaman scan yang cocok.</p>
                    <p class="text-[11px] text-slate-400 mt-1">Tempelkan jari di mesin fisik atau jalankan uji coba simulasi tap di samping.</p>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (1 Kolom): SIMULATOR & PANDUAN KONEKSI -->
        <div class="space-y-4">

            <!-- Panel Simulator Cepat -->
            <div class="card p-5 bg-white rounded-2xl shadow-sm border border-slate-200">
                <div class="flex items-center gap-2 pb-3 mb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                        <i class="fa-solid fa-vial text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-900">Uji Coba Tap (Simulator)</h4>
                        <p class="text-[10px] text-slate-500">Test kirim data scan tanpa mesin fisik</p>
                    </div>
                </div>

                <form @submit.prevent="submitSimulation()" class="space-y-3">
                    <div>
                        <label class="form-label text-[11px]">Pilih Pengguna / Target Test</label>
                        <select x-model="simData.preset" @change="onPresetChange($event.target.value)" class="form-input text-xs py-1.5 rounded-lg border-slate-300 w-full">
                            <option value="">-- Ketik PIN Manual --</option>
                            <optgroup label="Mahasiswa Terdaftar">
                                @foreach($mahasiswaSample as $mhs)
                                    <option value="mhs_{{ $mhs->id_finger }}" data-pin="{{ $mhs->id_finger }}" data-tipe="mahasiswa">
                                        {{ $mhs->nama_mahasiswa }} (PIN: {{ $mhs->id_finger }} - {{ $mhs->nim }})
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Dosen Terdaftar">
                                @foreach($dosenSample as $dsn)
                                    <option value="dsn_{{ $dsn->id_finger }}" data-pin="{{ $dsn->id_finger }}" data-tipe="dosen">
                                        {{ $dsn->nama_lengkap }} (PIN: {{ $dsn->id_finger }})
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="form-label text-[11px]">PIN / ID Finger <span class="text-red-500">*</span></label>
                            <input type="text" x-model="simData.pin" required placeholder="cth: 1001" 
                                   class="form-input text-xs font-mono py-1.5 rounded-lg border-slate-300 w-full">
                        </div>
                        <div>
                            <label class="form-label text-[11px]">Tipe <span class="text-red-500">*</span></label>
                            <select x-model="simData.tipe_pengguna" class="form-input text-xs py-1.5 rounded-lg border-slate-300 w-full">
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-[11px]">Pilih Mesin Fingerprint</label>
                        <select x-model="simData.mesin_id" required class="form-input text-xs py-1.5 rounded-lg border-slate-300 w-full">
                            @foreach($mesins as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->brand_mesin }} - {{ $m->nama_mesin }} ({{ $m->ruangan->nama_ruangan ?? 'Ruangan' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" :disabled="isSubmittingSim"
                            class="w-full py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-black text-xs transition-colors shadow-sm disabled:opacity-50 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-fingerprint" :class="isSubmittingSim ? 'fa-beat' : ''"></i>
                        <span x-text="isSubmittingSim ? 'Mengirim Scan...' : 'Kirim Scan Sekarang'"></span>
                    </button>
                </form>

                <!-- Quick Button AWAL 1001 -->
                <div class="mt-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="quickScanAwal()"
                            class="w-full py-1.5 px-3 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                        <i class="fa-solid fa-bolt text-amber-600"></i>
                        <span>Quick Test: Tap AWAL (PIN 1001)</span>
                    </button>
                </div>
            </div>

            <!-- Petunjuk Koneksi Fisik Mesin -->
            <div class="card p-4 bg-slate-900 text-white rounded-2xl shadow-sm border border-slate-800 text-xs space-y-2.5">
                <div class="flex items-center gap-2 font-bold text-slate-200">
                    <i class="fa-solid fa-circle-info text-sky-400"></i>
                    <span>Verifikasi Mesin Fisik</span>
                </div>
                <p class="text-slate-300 text-[11px] leading-relaxed">
                    Jika Anda sudah menempelkan jari di mesin fisik namun log belum muncul di atas:
                </p>
                <ol class="list-decimal pl-4 space-y-1.5 text-[11px] text-slate-400 leading-normal">
                    <li>Pastikan indikator mesin di atas berstatus <strong class="text-emerald-400">ONLINE</strong>. Klik tombol <em>Uji Ping</em>.</li>
                    <li>Pada mesin fisik (menu <strong>Comm. &rarr; Cloud Server / ADMS</strong>), atur Server IP ke IP Mini PC (<code class="text-sky-300">192.168.2.50</code>) dan Server Port ke port web (<code class="text-sky-300">8000</code>).</li>
                    <li>Pastikan User ID / PIN yang didaftarkan di mesin sama dengan kolom ID Finger di sistem web.</li>
                </ol>
            </div>

        </div>

    </div>
</div>

<!-- ================================================= -->
<!-- SCRIPTS: STREAMING & AUDIO WEBAUDIO SYNTHESIZER   -->
<!-- ================================================= -->
<script>
function realtimeLogger(config) {
    return {
        feedUrl: config.feedUrl,
        pingUrlBase: config.pingUrlBase,
        pullUrlBase: config.pullUrlBase,
        simulateUrl: config.simulateUrl,
        logs: config.initialLogs || [],
        latestId: config.initialLatestId || 0,
        totalScansTodayCount: config.totalScansToday || 0,
        
        isStreaming: true,
        isLoading: false,
        soundEnabled: true,
        searchQuery: '',
        streamInterval: null,
        serverClock: '{{ now()->format("H:i:s") }}',
        mesinStatus: {},

        simData: {
            preset: '',
            pin: '1001',
            tipe_pengguna: 'mahasiswa',
            mesin_id: '{{ $mesins->first()?->id ?? 1 }}'
        },
        isSubmittingSim: false,

        get filteredLogs() {
            if (!this.searchQuery.trim()) {
                return this.logs;
            }
            const q = this.searchQuery.toLowerCase();
            return this.logs.filter(l => 
                (l.nama && l.nama.toLowerCase().includes(q)) ||
                (l.pin && l.pin.toString().includes(q)) ||
                (l.identitas && l.identitas.toLowerCase().includes(q)) ||
                (l.ruangan && l.ruangan.toLowerCase().includes(q))
            );
        },

        startStreaming() {
            // Inisialisasi status mesin bawaan
            @foreach($mesins as $m)
                this.mesinStatus[{{ $m->id }}] = {
                    isOnline: {{ $m->status_online ? 'true' : 'false' }},
                    msg: '{{ $m->status_online ? "Terkoneksi" : "Menunggu respon" }}'
                };
            @endforeach

            // Mulai polling setiap 2.5 detik
            if (this.streamInterval) clearInterval(this.streamInterval);
            this.streamInterval = setInterval(() => {
                if (this.isStreaming) {
                    this.fetchLogs(false);
                }
            }, 2500);

            // Update jam lokal server setiap detik
            setInterval(() => {
                const now = new Date();
                this.serverClock = now.toTimeString().split(' ')[0];
            }, 1000);
        },

        toggleStreaming() {
            this.isStreaming = !this.isStreaming;
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            if (this.soundEnabled) {
                this.playChime(660); // feedback beep
            }
        },

        async fetchLogs(isManual = false) {
            if (this.isLoading && !isManual) return;
            this.isLoading = true;

            try {
                const url = new URL(this.feedUrl);
                url.searchParams.set('limit', '30');

                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) throw new Error('Network error');
                const data = await response.json();

                if (data.status === 'success') {
                    this.totalScansTodayCount = data.total_scans_today;
                    if (data.server_time) this.serverClock = data.server_time;

                    if (data.logs && data.logs.length > 0) {
                        const newItems = [];
                        data.logs.forEach(fresh => {
                            const exists = this.logs.some(existing => existing.id === fresh.id);
                            if (!exists) {
                                fresh.isNew = true;
                                newItems.push(fresh);
                            }
                        });

                        if (newItems.length > 0) {
                            // Suara chime jika ada scan baru masuk
                            if (this.soundEnabled) {
                                this.playChime(880);
                            }

                            // Tambahkan ke paling atas list
                            this.logs = [...newItems, ...this.logs].slice(0, 50);

                            // Matikan efek highlight BARU setelah 6 detik
                            setTimeout(() => {
                                this.logs.forEach(l => l.isNew = false);
                            }, 6000);
                        } else if (isManual) {
                            // Replace list jika manual reload
                            this.logs = data.logs;
                        }
                    }
                }
            } catch (err) {
                console.warn('Gagal mengambil feed log realtime:', err);
            } finally {
                this.isLoading = false;
            }
        },

        async testPingMachine(mesinId) {
            const card = this.mesinStatus[mesinId];
            if (card) card.msg = 'Mengirim ping...';

            try {
                const res = await fetch(`${this.pingUrlBase}/${mesinId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const result = await res.json();
                this.mesinStatus[mesinId] = {
                    isOnline: result.success,
                    msg: result.success ? `Online (${result.response_ms}ms)` : 'Timeout / Gagal'
                };

                if (result.success && this.soundEnabled) {
                    this.playChime(523.25); // C5 note
                }
            } catch (e) {
                this.mesinStatus[mesinId] = { isOnline: false, msg: 'Error socket' };
            }
        },

        async pullFromMachine(mesinId) {
            const card = this.mesinStatus[mesinId];
            if (card) card.msg = 'Menarik log dari mesin...';

            try {
                const res = await fetch(`${this.pullUrlBase}/${mesinId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const result = await res.json();
                if (result.success) {
                    if (card) card.msg = `Tersinkron (${result.count} baru)`;
                    await this.fetchLogs(true);
                    if (this.soundEnabled) this.playChime(880);
                    alert(result.message);
                } else {
                    if (card) card.msg = 'Gagal sinkron';
                    alert(result.message);
                }
            } catch (e) {
                if (card) card.msg = 'Error socket';
                alert('Gagal komunikasi socket dengan mesin: ' + e.message);
            }
        },

        onPresetChange(val) {
            if (!val) return;
            const select = event.target;
            const opt = select.options[select.selectedIndex];
            this.simData.pin = opt.getAttribute('data-pin');
            this.simData.tipe_pengguna = opt.getAttribute('data-tipe');
        },

        quickScanAwal() {
            this.simData.pin = '1001';
            this.simData.tipe_pengguna = 'mahasiswa';
            this.submitSimulation();
        },

        async submitSimulation() {
            if (!this.simData.pin) return;
            this.isSubmittingSim = true;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('pin', this.simData.pin);
            formData.append('tipe_pengguna', this.simData.tipe_pengguna);
            formData.append('mesin_id', this.simData.mesin_id);

            try {
                const res = await fetch(this.simulateUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                });
                
                // Segera refresh feed untuk menampilkan log yang baru saja disimulasikan
                await this.fetchLogs(true);
            } catch (e) {
                console.error('Simulasi gagal:', e);
            } finally {
                this.isSubmittingSim = false;
            }
        },

        // Web Audio Synthesizer Tone
        playChime(freq = 880) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(freq * 1.5, ctx.currentTime + 0.15);

                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {
                // AudioContext not allowed before user interaction
            }
        }
    }
}
</script>
@endsection
