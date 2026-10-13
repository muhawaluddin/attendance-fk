<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi') - {{ $appSetting->institution_name ?? 'Universitas Hasanuddin' }}</title>
    <link rel="icon" href="{{ $appSetting->institution_logo ? asset($appSetting->institution_logo) : asset('assets/img/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:  '{{ $theme['primary_light'] }}',
                            100: '{{ $theme['primary_border'] }}',
                            500: '{{ $primaryColor }}',
                            600: '{{ $primaryColor }}',
                            700: '{{ $primaryColor }}',
                            800: '{{ $primaryHover }}',
                            900: '{{ $primaryHover }}',
                        },
                        navy: { 800: '#1e293b', 900: '#0f172a', 950: '#020617' }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --color-brand-primary: {{ $primaryColor }};
            --color-brand-hover:   {{ $primaryHover }};
            --sidebar-w:  260px;
            --sidebar-collapsed: 60px;
        }
        body { font-family: 'Inter', sans-serif; letter-spacing: -0.01em; }
        [x-cloak] { display: none !important; }

        /* ── Sidebar transition ── */
        #sidebar {
            width: var(--sidebar-w);
            transition: width 0.25s cubic-bezier(.4,0,.2,1);
        }
        #sidebar.collapsed { width: var(--sidebar-collapsed); }

        /* Hide text/labels when collapsed */
        #sidebar.collapsed .sb-label,
        #sidebar.collapsed .sb-section-label,
        #sidebar.collapsed .sb-badge,
        #sidebar.collapsed .brand-text,
        #sidebar.collapsed .sb-footer-text { display: none !important; }

        /* Center nav-link icon when collapsed */
        #sidebar.collapsed .nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.collapsed .nav-link .sb-icon { margin: 0; }

        /* Section labels become dividers when collapsed */
        #sidebar.collapsed .nav-section-label { justify-content: center; pointer-events: none; }

        /* Footer when collapsed */
        #sidebar.collapsed .sb-footer { justify-content: center; }
        #sidebar.collapsed .sb-footer-avatar { margin: 0; }

        /* Brand header collapsed */
        #sidebar.collapsed .brand-logo { margin: 0 auto; }

        /* Tooltip for icon-only mode */
        #sidebar.collapsed .nav-link { position: relative; }
        #sidebar.collapsed .nav-link:hover::after {
            content: attr(data-label);
            position: absolute;
            left: calc(var(--sidebar-collapsed) + 6px);
            top: 50%;
            transform: translateY(-50%);
            background: #0f172a;
            color: #f1f5f9;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* ── Table card ── */
        .table-card { border-radius: 10px !important; overflow: hidden; }

        /* ── Modal ── */
        .modal-box {
            border-radius: 12px !important;
            box-shadow: 0 25px 60px -10px rgba(0,0,0,0.25), 0 10px 30px -5px rgba(0,0,0,0.1);
        }
        .modal-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-body  { padding: 1.25rem 1.5rem; }
        .modal-footer {
            padding: 0.875rem 1.5rem;
            border-top: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;
            background: #f8fafc;
            border-radius: 0 0 12px 12px;
        }

        /* ── Form controls ── */
        .form-input {
            width: 100%; padding: 0.5rem 0.75rem;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
            font-size: 0.875rem; transition: border-color .15s, box-shadow .15s;
            font-family: 'Inter', sans-serif;
        }
        .form-input:focus {
            outline: none; border-color: var(--color-brand-primary);
            box-shadow: 0 0 0 3px rgba(185,28,28,.1);
        }
        .form-label {
            display: block; font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .05em;
            color: #475569; margin-bottom: .3rem;
        }

        /* ── Import dropzone ── */
        .import-dropzone {
            border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;
            transition: border-color .2s, background .2s;
        }
        .import-dropzone:hover, .import-dropzone.dragover {
            border-color: var(--color-brand-primary); background: #fef2f2;
        }

        /* ── Nav link ── */
        .nav-link {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.45rem 0.75rem; border-radius: 7px;
            font-size: 0.8125rem; font-weight: 500; color: #cbd5e1;
            transition: background .15s, color .15s; cursor: pointer;
            text-decoration: none; white-space: nowrap; overflow: hidden;
        }
        .nav-link:hover  { background: rgba(51,65,85,.6); color: #fff; }
        .nav-link.active {
            background: var(--color-brand-primary); color: #fff; font-weight: 600;
            box-shadow: 0 3px 10px -2px rgba(0,0,0,.4);
        }
        .nav-section-label {
            padding: 0.55rem 0.75rem 0.25rem;
            font-size: 0.6rem; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: #475569;
            display: flex; align-items: center; justify-content: space-between;
            cursor: pointer; user-select: none;
        }
        .nav-section-label:hover { color: #94a3b8; }

        /* ── Scrollbar ── */
        .custom-scroll::-webkit-scrollbar { width: 3px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }

        /* ── Dropdown ── */
        .dropdown-menu { border-radius: 10px; box-shadow: 0 10px 40px -5px rgba(0,0,0,.2); }

        /* ── Dashboard stat cards ── */
        .stat-card { border-radius: 10px !important; }
    </style>

    <!-- Alpine.js Collapse plugin MUST load before core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-full overflow-hidden flex bg-slate-50 text-slate-800"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: JSON.parse(localStorage.getItem('sb_collapsed') || 'false'),
          openGroups: { master: true, jadwal: true, laporan: true, sistem: true },
          toggleGroup(k) { this.openGroups[k] = !this.openGroups[k]; },
          toggleCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sb_collapsed', this.sidebarCollapsed);
          },
          isFullscreen: false,
          toggleFullscreen() {
              if (!document.fullscreenElement) {
                  document.documentElement.requestFullscreen().catch(() => {});
                  this.isFullscreen = true;
              } else {
                  if (document.exitFullscreen) {
                      document.exitFullscreen().catch(() => {});
                      this.isFullscreen = false;
                  }
              }
          }
      }"
      @fullscreenchange.window="isFullscreen = !!document.fullscreenElement"
      @keydown.escape.window="sidebarOpen = false">

    <!-- Mobile backdrop -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden">
    </div>

    <!-- ============================================================ -->
    <!-- SIDEBAR                                                       -->
    <!-- ============================================================ -->
    <aside id="sidebar"
           :class="[
               sidebarOpen ? 'translate-x-0' : '-translate-x-full',
               sidebarCollapsed ? 'collapsed' : ''
           ]"
           class="fixed inset-y-0 left-0 z-50 bg-navy-950 text-slate-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col border-r border-slate-800/80 shadow-2xl shrink-0">

        <!-- Brand header -->
        <div class="h-[68px] flex items-center px-3 gap-3 border-b border-slate-800/80 bg-navy-900/40 shrink-0">
            <!-- Logo — click goes to settings to change it -->
            <a href="{{ route('pengaturan.index') }}" title="Ubah logo di Pengaturan"
               class="brand-logo w-9 h-9 rounded-lg bg-white/10 p-1 flex items-center justify-center border border-white/10 hover:bg-white/20 transition-colors group relative shrink-0">
                <img src="{{ $appSetting->institution_logo ? asset($appSetting->institution_logo) : asset('assets/img/unhas.png') }}"
                     alt="Logo" class="w-full h-full object-contain"
                     onerror="this.src='{{ asset('assets/img/unhas.png') }}'">
                <span class="absolute inset-0 rounded-lg flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fa-solid fa-camera text-white text-[9px]"></i>
                </span>
            </a>
            <!-- App name — hidden when collapsed -->
            <div class="brand-text overflow-hidden flex-1 min-w-0">
                <h1 class="text-white font-extrabold tracking-tight text-[13px] leading-tight truncate">
                    {{ $appSetting->app_name ?? 'ABSENSI' }} {{ $appSetting->short_name ?? 'FK UNHAS' }}
                </h1>
                <p class="text-[10px] text-brand-500 font-semibold truncate">
                    {{ $appSetting->faculty_name ?: ($appSetting->institution_name ?? '') }}
                </p>
            </div>
            <!-- Collapse toggle (desktop only) -->
            <button @click="toggleCollapse()" title="Collapse sidebar"
                    class="hidden lg:flex p-1.5 rounded-lg text-slate-500 hover:text-slate-300 hover:bg-slate-800/60 transition-colors shrink-0">
                <i class="fa-solid text-sm"
                   :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
            </button>
        </div>

        <!-- Nav links -->
        <nav class="flex-1 overflow-y-auto custom-scroll py-2.5 space-y-0.5">

            <!-- Beranda -->
            <div class="px-2">
                <a href="{{ route('dashboard') }}" data-label="Beranda"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="sb-icon fa-solid fa-gauge-high text-sm w-4 text-center shrink-0"></i>
                    <span class="sb-label">Beranda</span>
                </a>
            </div>

            <!-- Monitoring TV -->
            <div class="px-2">
                <a href="{{ route('monitoring') }}" target="_blank" data-label="Monitoring TV"
                   class="nav-link" style="color:#fbbf24;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.15);">
                    <i class="sb-icon fa-solid fa-tv text-sm w-4 text-center shrink-0"></i>
                    <span class="sb-label flex-1">Layar Monitoring TV</span>
                    <i class="sb-badge fa-solid fa-arrow-up-right-from-square text-[9px] opacity-50"></i>
                </a>
            </div>


            <!-- ─── Master Data ─── -->
            <div class="mt-2 px-2">
                <div class="nav-section-label sb-section-label" @click="toggleGroup('master')">
                    <span>Master Data</span>
                    <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"
                       :class="openGroups.master ? '' : '-rotate-90'"></i>
                </div>
                <div class="space-y-0.5" x-show="openGroups.master" x-collapse>
                    <a href="{{ route('mesin.index') }}" data-label="Fingerprint / Mesin"
                       class="nav-link {{ request()->routeIs('mesin.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-fingerprint text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Fingerprint / Mesin</span>
                    </a>
                    <a href="{{ route('departemen.index') }}" data-label="Departemen"
                       class="nav-link {{ request()->routeIs('departemen.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-building-columns text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Departemen</span>
                    </a>
                    <a href="{{ route('kelas.index') }}" data-label="Kelas"
                       class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-chalkboard text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Kelas</span>
                    </a>
                    <a href="{{ route('dosen.index') }}" data-label="Dosen"
                       class="nav-link {{ request()->routeIs('dosen.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-user-doctor text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Dosen</span>
                    </a>
                    <a href="{{ route('mahasiswa.index') }}" data-label="Mahasiswa"
                       class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-user-graduate text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Mahasiswa</span>
                    </a>
                    <a href="{{ route('mata-kuliah.index') }}" data-label="Mata Kuliah / Blok"
                       class="nav-link {{ request()->routeIs('mata-kuliah.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-book-medical text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Mata Kuliah / Blok</span>
                    </a>
                    <a href="{{ route('ruangan.index') }}" data-label="Ruang Kuliah"
                       class="nav-link {{ request()->routeIs('ruangan.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-door-open text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Ruang Kuliah</span>
                    </a>
                </div>
            </div>

            <!-- ─── Jadwal & Perkuliahan ─── -->
            <div class="mt-2 px-2">
                <div class="nav-section-label sb-section-label" @click="toggleGroup('jadwal')">
                    <span>Jadwal & Perkuliahan</span>
                    <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"
                       :class="openGroups.jadwal ? '' : '-rotate-90'"></i>
                </div>
                <div class="space-y-0.5" x-show="openGroups.jadwal" x-collapse>
                    <a href="{{ route('jadwal.index') }}" data-label="Jadwal Kuliah"
                       class="nav-link {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-calendar-days text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Jadwal Kuliah</span>
                    </a>
                </div>
            </div>

            <!-- ─── Rekap & Laporan ─── -->
            <div class="mt-2 px-2">
                <div class="nav-section-label sb-section-label" @click="toggleGroup('laporan')">
                    <span>Rekap & Laporan</span>
                    <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"
                       :class="openGroups.laporan ? '' : '-rotate-90'"></i>
                </div>
                <div class="space-y-0.5" x-show="openGroups.laporan" x-collapse>
                    <a href="{{ route('log-realtime.index') }}" data-label="Live Log Presensi"
                       class="nav-link {{ request()->routeIs('log-realtime.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-bolt-lightning text-sm w-4 text-center shrink-0" style="color:#38bdf8;"></i>
                        <span class="sb-label flex-1 font-semibold">Live Log Presensi</span>
                        <span class="sb-badge px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-emerald-500/20 text-emerald-400">LIVE</span>
                    </a>
                    <a href="{{ route('laporan.harian') }}" data-label="Rekap Jam Harian"
                       class="nav-link {{ request()->routeIs('laporan.harian*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-business-time text-sm w-4 text-center shrink-0" style="color:#fbbf24;"></i>
                        <span class="sb-label flex-1">Rekap Jam Harian</span>
                        <span class="sb-badge px-1.5 py-0.5 rounded text-[9px] font-bold" style="background:rgba(245,158,11,.2);color:#fcd34d;">Baru</span>
                    </a>
                    <a href="{{ route('laporan.mahasiswa') }}" data-label="Laporan Mahasiswa"
                       class="nav-link {{ request()->routeIs('laporan.mahasiswa') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-clipboard-user text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Laporan Mahasiswa</span>
                    </a>
                    <a href="{{ route('laporan.dosen') }}" data-label="Laporan Dosen"
                       class="nav-link {{ request()->routeIs('laporan.dosen') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-id-badge text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Laporan Dosen</span>
                    </a>
                </div>
            </div>

            <!-- ─── Sistem ─── -->
            <div class="mt-2 mb-2 px-2">
                <div class="nav-section-label sb-section-label" @click="toggleGroup('sistem')">
                    <span>Sistem</span>
                    <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"
                       :class="openGroups.sistem ? '' : '-rotate-90'"></i>
                </div>
                <div class="space-y-0.5" x-show="openGroups.sistem" x-collapse>
                    <a href="{{ route('pengaturan.index') }}" data-label="Pengaturan"
                       class="nav-link {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-sliders text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Pengaturan</span>
                    </a>
                    <a href="{{ route('akun.index') }}" data-label="Kelola Akun"
                       class="nav-link {{ request()->routeIs('akun.*') ? 'active' : '' }}">
                        <i class="sb-icon fa-solid fa-users-gear text-sm w-4 text-center shrink-0"></i>
                        <span class="sb-label">Kelola Akun</span>
                    </a>
                </div>
            </div>

        </nav>

        <!-- Footer: role badge + logout only, no full name -->
        <div class="px-2 py-3 border-t border-slate-800/80 bg-navy-900/50 shrink-0">
            <div class="sb-footer flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="sb-footer-avatar w-8 h-8 rounded-lg bg-brand-700/80 flex items-center justify-center text-white font-bold text-xs border border-brand-500/30 shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="sb-footer-text overflow-hidden">
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-bold rounded bg-slate-700 text-slate-300 capitalize">
                            <i class="fa-solid fa-shield-halved text-brand-500 text-[8px]"></i>
                            {{ auth()->user()->level ?? 'Admin' }}
                        </span>
                        <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ auth()->user()->username ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ route('logout') }}" title="Keluar"
                   class="p-2 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors shrink-0">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT                                                  -->
    <!-- ============================================================ -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        <!-- Topbar -->
        <header class="h-[68px] bg-white border-b border-slate-200/80 px-4 md:px-5 flex items-center justify-between shadow-sm z-10 shrink-0">

            <div class="flex items-center gap-3 min-w-0">
                <!-- Mobile toggle -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" title="Buka Menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="min-w-0">
                    <h2 class="text-base font-bold text-slate-900 tracking-tight leading-tight truncate">@yield('page-title', 'Sistem Absensi')</h2>
                    <p class="text-[11px] text-slate-400 truncate">@yield('page-subtitle', ($appSetting->faculty_name ?? 'Fakultas Kedokteran') . ' ' . ($appSetting->institution_name ?? 'Universitas Hasanuddin'))</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
                <!-- Live Calendar & Realtime Clock (WITA) -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs shadow-sm"
                     x-data="{
                         dateStr: '',
                         timeStr: '',
                         init() {
                             const update = () => {
                                 const now = new Date();
                                 try {
                                     this.dateStr = new Intl.DateTimeFormat('id-ID', {
                                         timeZone: 'Asia/Makassar',
                                         weekday: 'short',
                                         day: 'numeric',
                                         month: 'short',
                                         year: 'numeric'
                                     }).format(now);
                                     this.timeStr = new Intl.DateTimeFormat('id-ID', {
                                         timeZone: 'Asia/Makassar',
                                         hour: '2-digit',
                                         minute: '2-digit',
                                         second: '2-digit',
                                         hour12: false
                                     }).format(now).replace(/\./g, ':');
                                 } catch (e) {
                                     this.timeStr = now.toTimeString().split(' ')[0];
                                 }
                             };
                             update();
                             setInterval(update, 1000);
                         }
                     }">
                    <div class="flex items-center gap-1.5 font-medium text-slate-600">
                        <i class="fa-regular fa-calendar-days text-brand-700"></i>
                        <span x-text="dateStr">{{ now()->locale('id')->isoFormat('ddd, D MMM Y') }}</span>
                    </div>
                    <span class="text-slate-300 font-light">|</span>
                    <div class="flex items-center gap-1.5 font-mono font-semibold text-slate-800">
                        <i class="fa-regular fa-clock text-slate-400 text-[11px]"></i>
                        <span x-text="timeStr">{{ now()->format('H:i:s') }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold tracking-wider bg-brand-50 text-brand-700 border border-brand-200/60">WITA</span>
                    </div>
                </div>

                <!-- Live Monitor Button -->
                <a href="{{ route('monitoring') }}" target="_blank"
                   title="Buka Layar Display Kehadiran Realtime (Tab Baru)"
                   class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-navy-900 text-white text-xs font-semibold hover:bg-navy-800 shadow-sm border border-slate-700/60 transition-all hover:shadow hover:-translate-y-0.5 group">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="font-medium">Layar Monitor</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-white transition-colors"></i>
                </a>

                <!-- Fullscreen Toggle Button -->
                <button type="button" @click="toggleFullscreen()"
                        title="Layar Penuh"
                        class="hidden lg:flex w-9 h-9 items-center justify-center rounded-lg border border-slate-200/80 bg-white hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition-colors shadow-sm">
                    <i :class="isFullscreen ? 'fa-solid fa-compress' : 'fa-solid fa-expand'" class="text-xs"></i>
                </button>

                <!-- User Profile Capsule Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2.5 p-1.5 pr-2.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                        <!-- Avatar -->
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-700 to-brand-800 text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <!-- User identity (Hidden on very small screens, visible on md+) -->
                        <div class="hidden md:flex flex-col text-left leading-tight">
                            <span class="text-xs font-bold text-slate-800 truncate max-w-[130px] lg:max-w-[170px]">
                                {{ auth()->user()->name ?? 'Administrator' }}
                            </span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-medium text-slate-500 capitalize tracking-wide">
                                    {{ auth()->user()->level ?? 'Admin' }}
                                </span>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 ml-0.5"
                           :class="open ? 'rotate-180 text-brand-700' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute right-0 mt-2 w-64 bg-white dropdown-menu border border-slate-200/90 py-1.5 z-50 shadow-xl">
                        <!-- Header user info -->
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/60">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Sedang Masuk Sebagai</p>
                            <p class="text-sm font-bold text-slate-900 truncate mt-0.5">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ auth()->user()->username ?? '' }}</p>
                            <div class="mt-2.5 flex items-center justify-between">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-brand-50 text-brand-700 border border-brand-200/70 capitalize">
                                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                    {{ auth()->user()->level ?? 'Admin' }}
                                </span>
                                <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                                </span>
                            </div>
                        </div>

                        <!-- Menu links -->
                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-700 transition-colors">
                                <i class="fa-regular fa-user text-slate-400 w-4 text-center"></i>
                                <span>Profil & Ubah Password</span>
                            </a>
                            <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-700 transition-colors">
                                <i class="fa-solid fa-sliders text-slate-400 w-4 text-center"></i>
                                <span>Pengaturan Sistem & Branding</span>
                            </a>
                            <a href="{{ route('monitoring') }}" target="_blank" class="flex items-center justify-between px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-700 transition-colors sm:hidden">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-desktop text-slate-400 w-4 text-center"></i>
                                    <span>Layar Monitor Realtime</span>
                                </div>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
                            </a>
                        </div>

                        <div class="my-1 border-t border-slate-100"></div>

                        <!-- Logout -->
                        <div class="p-1.5">
                            <a href="{{ route('logout') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fa-solid fa-arrow-right-from-bracket text-red-500 w-4 text-center"></i>
                                <span>Keluar dari Aplikasi</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page body -->
        <main class="flex-1 overflow-y-auto p-5 md:p-6 bg-slate-50">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0"><i class="fa-solid fa-check text-sm"></i></div>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="p-1 text-emerald-400 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-lg bg-red-50 border border-red-200 text-red-800 flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center text-red-600 shrink-0"><i class="fa-solid fa-triangle-exclamation text-sm"></i></div>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="p-1 text-red-400 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="mb-5 p-3.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 mt-0.5 shrink-0"><i class="fa-solid fa-circle-exclamation text-sm"></i></div>
                            <div>
                                <p class="text-sm font-bold">Periksa form Anda:</p>
                                <ul class="list-disc list-inside text-xs mt-1 space-y-0.5 text-amber-700">
                                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                                </ul>
                            </div>
                        </div>
                        <button @click="show = false" class="p-1 text-amber-400 hover:text-amber-700 shrink-0"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="h-10 bg-white border-t border-slate-200 px-5 flex items-center justify-between text-[11px] text-slate-400 shrink-0">
            <span>&copy; {{ date('Y') }} <strong class="text-slate-600">{{ $appSetting->institution_name ?? 'Universitas Hasanuddin' }}</strong></span>
            <span class="hidden sm:flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span class="font-semibold text-brand-700">{{ $appSetting->short_name ?? 'FK UNHAS' }}</span>
                &bull; {{ $appSetting->app_name ?? 'Sistem Absensi' }}
            </span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
