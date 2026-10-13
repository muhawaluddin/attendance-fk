<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Jadwal Perkuliahan - {{ $appSetting->institution_name ?? 'Universitas Hasanuddin' }}</title>
    <link rel="icon" href="{{ $appSetting->institution_logo ? asset($appSetting->institution_logo) : asset('assets/img/favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @php
        $theme = $appSetting->theme_config ?? [
            'primary' => '#b91c1c',
            'primary_hover' => '#991b1b',
            'primary_light' => '#fef2f2',
            'primary_border' => '#fecaca',
            'primary_text' => '#7f1d1d',
        ];
        $primaryColor = $theme['primary'];
        $primaryHover = $theme['primary_hover'];
    @endphp

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '{{ $theme['primary_light'] }}',
                            100: '{{ $theme['primary_border'] }}',
                            500: '{{ $primaryColor }}',
                            600: '{{ $primaryColor }}',
                            700: '{{ $primaryColor }}',
                            800: '{{ $primaryHover }}',
                            900: '{{ $primaryHover }}',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; letter-spacing: -0.01em; }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="h-full flex flex-col justify-between bg-slate-950 font-sans overflow-hidden select-none">

    <!-- Top Display Header -->
    <header class="h-24 bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 border-b border-slate-800 px-8 flex items-center justify-between shadow-2xl relative z-10"
            style="border-bottom-color: {{ $primaryColor }}40;">
        
        <!-- Logo and University Identity -->
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-white/10 backdrop-blur-md p-2 border border-white/20 shadow-inner flex items-center justify-center">
                <img src="{{ $appSetting->institution_logo ? asset($appSetting->institution_logo) : asset('assets/img/unhas.png') }}" 
                     alt="Logo Institusi" 
                     class="h-full object-contain" 
                     onerror="this.src='https://upload.wikimedia.org/wikipedia/id/8/8a/Logo-unhas.png'">
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    <span>JADWAL KEGIATAN PERKULIAHAN</span>
                    <span class="px-2 py-0.5 rounded text-xs font-bold text-white uppercase tracking-wider" style="background-color: {{ $primaryColor }};">LIVE</span>
                </h1>
                <p class="text-sm font-bold uppercase tracking-wide" style="color: {{ $primaryColor }};">
                    {{ $appSetting->faculty_name ?: $appSetting->institution_name }} &bull; {{ $appSetting->institution_name }}
                </p>
                <div class="text-[11px] text-slate-400 flex items-center gap-3 mt-0.5">
                    @if($appSetting->website_url)
                        <span><i class="fa-solid fa-globe text-slate-400"></i> {{ $appSetting->website_url }}</span>
                        <span>&bull;</span>
                    @endif
                    @if($appSetting->email_contact)
                        <span><i class="fa-solid fa-envelope text-slate-400"></i> {{ $appSetting->email_contact }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Clock & Date Display -->
        <div class="flex items-center gap-6">
            <div class="text-right">
                <div id="liveClock" class="font-mono text-3xl sm:text-4xl font-black text-white tracking-wider">
                    00:00:00
                </div>
                <div class="text-xs font-semibold text-slate-300 uppercase tracking-widest mt-0.5">
                    {{ $today->locale('id')->isoFormat('dddd, D MMMM Y') }} (WITA)
                </div>
            </div>

            <!-- Fullscreen Toggle -->
            <button onclick="toggleFullScreen()" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/10 transition-colors" title="Toggle Fullscreen (F11)">
                <i class="fa-solid fa-expand text-lg"></i>
            </button>
        </div>

    </header>

    <!-- Main Content: Schedule Table -->
    <main class="flex-1 p-6 md:p-8 overflow-y-auto">

        <div class="bg-slate-900/60 backdrop-blur-xl rounded-xl border border-slate-800/80 shadow-xl overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900 text-xs font-semibold tracking-wider text-slate-400 border-b border-slate-800">
                        <th class="py-3.5 px-6 w-44">Waktu</th>
                        <th class="py-3.5 px-6">Mata Kuliah / Blok</th>
                        <th class="py-3.5 px-6 w-28 text-center">Kelas</th>
                        <th class="py-3.5 px-6">Ruangan</th>
                        <th class="py-3.5 px-6">Kegiatan & Dosen</th>
                        <th class="py-3.5 px-6 text-right w-44">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm">
                    @forelse($jadwals as $j)
                        @php $isOngoing = $j->current_status === 'ongoing'; @endphp
                        <tr class="transition-colors {{ $isOngoing ? 'bg-emerald-500/[0.04]' : 'hover:bg-slate-800/30' }}">
                            
                            <!-- Waktu -->
                            <td class="py-4 px-6 whitespace-nowrap {{ $isOngoing ? 'border-l-2 border-l-emerald-500' : 'border-l-2 border-l-transparent' }}">
                                <div class="font-mono text-sm font-semibold {{ $isOngoing ? 'text-emerald-400' : 'text-slate-200' }}">
                                    {{ $j->jam_masuk }} – {{ $j->calculated_jam_keluar }}
                                </div>
                                <span class="text-[11px] text-slate-500 font-mono">
                                    {{ $j->jml_jam }} Jam
                                </span>
                            </td>

                            <!-- Blok Mata Kuliah -->
                            <td class="py-4 px-6">
                                <div class="font-medium text-sm text-slate-100">
                                    {{ $j->jadwalHari->jadwalKelas->jadwal->mataKuliah->nama_mk ?? 'Blok Perkuliahan' }}
                                </div>
                                <span class="text-xs text-slate-500 font-mono">
                                    {{ $j->jadwalHari->jadwalKelas->jadwal->mataKuliah->kode_mk ?: '-' }} &bull; Semester {{ $j->jadwalHari->jadwalKelas->jadwal->semester }}
                                </span>
                            </td>

                            <!-- Kelas -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <span class="text-xs text-slate-300">
                                    {{ $j->jadwalHari->jadwalKelas->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>

                            <!-- Ruangan -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="text-xs font-medium text-slate-200">
                                    {{ $j->ruangan->nama_ruangan ?? '-' }}
                                </div>
                                <span class="text-[11px] text-slate-500">{{ $j->ruangan->lokasi ?? '' }}</span>
                            </td>

                            <!-- Kegiatan & Dosen -->
                            <td class="py-4 px-6">
                                <div class="text-xs text-slate-300 font-medium">{{ $j->kegiatan }}</div>
                                @if($j->jadwalDosens->isNotEmpty())
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $j->jadwalDosens->map(fn($d) => $d->dosen->nama_lengkap ?? '')->join(', ') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                @if($isOngoing)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Sedang berjalan
                                    </span>
                                @elseif($j->current_status === 'upcoming')
                                    <span class="text-xs text-slate-400">Akan datang</span>
                                @else
                                    <span class="text-xs text-slate-600">Selesai</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-20 text-slate-500">
                                <p class="text-sm text-slate-400">Tidak ada perkuliahan terjadwal hari ini</p>
                                <p class="text-xs text-slate-600 mt-1">{{ $today->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </main>

    <!-- Bottom Marquee & Footer -->
    <footer class="h-20 bg-slate-900 border-t border-slate-800 flex flex-col justify-center overflow-hidden">
        
        <!-- Marquee Ticker -->
        <div class="h-10 text-white flex items-center px-4 overflow-hidden shadow-inner" style="background-color: {{ $primaryColor }};">
            <span class="px-2.5 py-0.5 rounded bg-white text-slate-950 font-extrabold text-[10px] uppercase tracking-wider shrink-0 mr-4">
                PENGUMUMAN
            </span>
            <marquee direction="left" scrollamount="6" class="text-sm font-semibold text-white">
                {{ $appSetting->running_text ?? 'Disampaikan kepada seluruh mahasiswa untuk selalu melakukan tap absensi sidik jari pada mesin fingerprint sebelum perkuliahan dimulai.' }}
            </marquee>
        </div>

        <!-- Footnote copyright -->
        <div class="h-10 px-8 flex items-center justify-between text-xs text-slate-400 bg-slate-950">
            <div>
                &copy; {{ date('Y') }}. <strong>{{ $appSetting->app_name ?? 'Absensi' }} {{ $appSetting->institution_name ?? 'Universitas' }}</strong>.
            </div>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Auto-refresh aktif (60s)</span>
                </span>
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white font-medium">Dashboard Admin &rarr;</a>
            </div>
        </div>

    </footer>

    <!-- Scripts for Live Clock, Fullscreen & Auto Refresh -->
    <script>
        function updateClock() {
            const now = new Date();
            const formatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Makassar',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            document.getElementById('liveClock').textContent = formatter.format(now).replace(/\./g, ':');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Auto Refresh every 60 seconds
        setTimeout(function() {
            window.location.reload();
        }, 60000);

        // Fullscreen Toggle
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
</body>
</html>
