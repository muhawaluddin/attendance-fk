@extends('layouts.app')

@section('title', 'Jadwal Perkuliahan')
@section('page-title', 'Jadwal Kuliah & Blok')
@section('page-subtitle', 'Manajemen jadwal kegiatan akademik, blok mata kuliah, dan pembagian kelas')

@section('content')
<div class="space-y-6" x-data="{ modalAdd: false }">

    <!-- Top Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        
        <!-- Filters -->
        <form action="{{ route('jadwal.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <select name="semester" onchange="this.form.submit()"
                    class="py-2 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-600 shadow-sm">
                <option value="">Semua Semester</option>
                <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
            </select>

            <select name="thn_ajaran" onchange="this.form.submit()"
                    class="py-2 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-600 shadow-sm">
                <option value="">Semua Tahun Ajaran</option>
                <option value="2025/2026" {{ request('thn_ajaran') == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                <option value="2026/2027" {{ request('thn_ajaran') == '2026/2027' ? 'selected' : '' }}>2026/2027</option>
            </select>

            <select name="mata_kuliah_id" onchange="this.form.submit()"
                    class="py-2 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-600 shadow-sm">
                <option value="">Semua Blok / MK</option>
                @foreach($mataKuliahs as $mk)
                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                        {{ $mk->nama_mk }}
                    </option>
                @endforeach
            </select>

            @if(request()->anyFilled(['semester', 'thn_ajaran', 'mata_kuliah_id']))
                <a href="{{ route('jadwal.index') }}" class="text-xs text-red-600 font-semibold hover:underline">
                    Reset Filter
                </a>
            @endif
        </form>

        <button @click="modalAdd = true" 
                class="px-4 py-2.5 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-sm shadow-md shadow-brand-900/20 flex items-center justify-center gap-2 shrink-0">
            <i class="fa-solid fa-calendar-plus text-sm"></i>
            <span>Buat Jadwal Baru</span>
        </button>
    </div>

    <!-- Cards Grid of Jadwal -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($jadwals as $j)
            <div class="bg-white table-card border border-slate-200/80 shadow-sm hover:shadow-md transition-all p-5 sm:p-6 flex flex-col justify-between">
                
                <!-- Card Header -->
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider
                            {{ $j->semester === 'Ganjil' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                            {{ $j->semester }} {{ $j->thn_ajaran }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            {{ ucfirst($j->status) }}
                        </span>
                    </div>

                    <h3 class="text-lg font-black text-slate-900 tracking-tight leading-snug">
                        {{ $j->mataKuliah->nama_mk ?? 'Blok Mata Kuliah' }}
                    </h3>
                    <p class="text-xs font-semibold text-brand-700 font-mono mt-0.5">
                        Kode: {{ $j->mataKuliah->kode_mk ?: '-' }} &bull; {{ $j->mataKuliah->sks ?? '4' }} SKS
                    </p>

                    <!-- Periode Tanggal -->
                    <div class="mt-4 p-3 rounded-lg bg-slate-50 border border-slate-100 flex items-center gap-3 text-xs text-slate-600">
                        <i class="fa-regular fa-calendar-days text-brand-700 text-base"></i>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Periode Perkuliahan:</span>
                            <span class="font-bold text-slate-800">
                                {{ $j->tgl_mulai ? $j->tgl_mulai->format('d M Y') : '-' }} s/d {{ $j->tgl_selesai ? $j->tgl_selesai->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Dosen Pengampu -->
                    <div class="mt-3.5 space-y-1 text-xs">
                        <div class="flex items-start gap-1.5 text-slate-600">
                            <i class="fa-solid fa-user-tie text-slate-400 mt-0.5 w-3.5 text-center"></i>
                            <span class="text-slate-500">Koordinator:</span>
                            <span class="font-bold text-slate-800 truncate">{{ $j->koordinator->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="flex items-start gap-1.5 text-slate-600">
                            <i class="fa-solid fa-user-pen text-slate-400 mt-0.5 w-3.5 text-center"></i>
                            <span class="text-slate-500">Sekretaris:</span>
                            <span class="font-bold text-slate-800 truncate">{{ $j->sekretaris->nama_lengkap ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Kelas Pelaksana & Submenu Shortcuts -->
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                            Kelas Terdaftar ({{ $j->jadwalKelas->count() }}):
                        </span>
                        
                        <div class="space-y-2">
                            @foreach($j->jadwalKelas as $jk)
                                <div class="p-2.5 rounded-lg bg-slate-50/80 border border-slate-200/80 flex items-center justify-between gap-2">
                                    <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-chalkboard text-brand-700 text-xs"></i>
                                        {{ $jk->kelas->nama_kelas ?? 'Kelas' }}
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <!-- Submenu Jadwal Perkelas -->
                                        <a href="{{ route('jadwal.perkelas', $jk) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-[11px] transition-colors flex items-center gap-1"
                                           title="Kelola slot jam dan tanggal sesi perkuliahan">
                                            <i class="fa-solid fa-clock"></i>
                                            <span>Sesi Jadwal</span>
                                        </a>

                                        <!-- Submenu Mahasiswa Perkelas -->
                                        <a href="{{ route('jadwal.mahasiswa', $jk) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-navy-900 hover:bg-navy-800 text-white font-bold text-[11px] transition-colors flex items-center gap-1"
                                           title="Kelola mahasiswa peserta kelas ini">
                                            <i class="fa-solid fa-users"></i>
                                            <span>Peserta</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('jadwal.show', $j) }}" class="font-bold text-brand-700 hover:text-brand-800 flex items-center gap-1">
                        <span>Lihat Detail Ringkasan</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>

                    <form action="{{ route('jadwal.destroy', $j) }}" method="POST" class="inline" onsubmit="return confirm('Hapus seluruh jadwal blok ini beserta semua sesi kelasnya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded transition-colors" title="Hapus Jadwal">
                            <i class="fa-regular fa-trash-can text-sm"></i>
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white table-card border border-slate-200 p-12 text-center text-slate-400">
                <i class="fa-regular fa-calendar-xmark text-4xl mb-3 block"></i>
                <p class="font-semibold text-slate-700">Belum ada data jadwal perkuliahan ditemukan.</p>
                <p class="text-xs text-slate-400 mt-1">Klik tombol "Buat Jadwal Baru" di atas untuk menambahkan jadwal perkuliahan.</p>
            </div>
        @endforelse
    </div>

    @if($jadwals->hasPages())
        <div class="p-4 bg-white table-card border border-slate-200">
            {{ $jadwals->links() }}
        </div>
    @endif

    <!-- Modal Buat Jadwal Baru -->
    <div x-show="modalAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalAdd = false" class="modal-box bg-white w-full max-w-xl my-8">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-plus text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Buat Jadwal Perkuliahan Blok Baru</h3>
                        <p class="text-xs text-slate-500">Kalender hari perkuliahan otomatis dibuatkan di antara rentang tanggal</p>
                    </div>
                </div>
                <button @click="modalAdd = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Semester <span class="text-red-500">*</span></label>
                            <select name="semester" required class="form-input">
                                <option value="Ganjil">Semester Ganjil</option>
                                <option value="Genap">Semester Genap</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Tahun Ajaran <span class="text-red-500">*</span></label>
                            <input type="text" name="thn_ajaran" value="2025/2026" required placeholder="Contoh: 2025/2026" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Pilih Blok / Mata Kuliah <span class="text-red-500">*</span></label>
                        <select name="mata_kuliah_id" required class="form-input font-semibold text-slate-800">
                            <option value="">-- Pilih Blok Mata Kuliah --</option>
                            @foreach($mataKuliahs as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->nama_mk }} ({{ $mk->kode_mk ?: '-' }} &bull; {{ $mk->sks }} SKS)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_mulai" required value="{{ now()->toDateString() }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_selesai" required value="{{ now()->addWeeks(2)->toDateString() }}" class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Dosen Koordinator</label>
                            <select name="dosen_koordinator_id" class="form-input">
                                <option value="">-- Pilih Koordinator --</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Dosen Sekretaris</label>
                            <select name="dosen_sekretaris_id" class="form-input">
                                <option value="">-- Pilih Sekretaris --</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Pengaturan Durasi Jam</label>
                        <select name="pengaturan_id" class="form-input">
                            @foreach($pengaturans as $p)
                                <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>
                                    {{ $p->nama_pengaturan }} ({{ $p->menit_perjam }} mnt/jam &bull; pergantian: {{ $p->menit_pergantian }} mnt)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Pilih Kelas yang Mengambil Blok Ini</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                            @foreach($kelas as $k)
                                <label class="flex items-center gap-2 p-1.5 text-xs cursor-pointer hover:bg-white rounded-md">
                                    <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" checked
                                           class="rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                                    <span class="font-bold text-slate-800">{{ $k->nama_kelas }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="modalAdd = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Buat Jadwal & Kalender</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
