<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RuanganController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Public Academic TV Monitoring Display & Biometric Push Webhook (Multi-brand: Deli, ZK, Solution, Hikvision)
Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
Route::post('/api/fingerprint/push', [MesinController::class, 'pushAttLog'])->name('api.fingerprint.push')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
Route::post('/api/fingerprint/deli', [MesinController::class, 'pushAttLog'])->name('api.fingerprint.deli')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
Route::match(['get', 'post'], '/iclock/cdata', [MesinController::class, 'pushAttLog'])->name('api.fingerprint.cdata')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Master Data
    Route::resource('departemen', DepartemenController::class)->except(['create', 'show', 'edit']);
    Route::resource('kelas', KelasController::class)->except(['create', 'show', 'edit']);
    Route::resource('dosen', DosenController::class)->except(['create', 'show', 'edit']);
    Route::post('dosen/import', [DosenController::class, 'import'])->name('dosen.import');
    
    // Mahasiswa
    Route::post('mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::post('mahasiswa/destroy-many', [MahasiswaController::class, 'destroyMany'])->name('mahasiswa.destroy-many');
    Route::post('mahasiswa/{mahasiswa}/reset-password', [MahasiswaController::class, 'resetPassword'])->name('mahasiswa.reset-password');
    Route::resource('mahasiswa', MahasiswaController::class)->except(['create', 'show', 'edit']);

    Route::resource('mata-kuliah', MataKuliahController::class)->except(['create', 'show', 'edit']);
    Route::resource('ruangan', RuanganController::class)->except(['create', 'show', 'edit']);

    // Mesin Fingerprint, Import Log USB & Simulator
    Route::get('log-realtime', [MesinController::class, 'logRealtime'])->name('log-realtime.index');
    Route::get('log-realtime/feed', [MesinController::class, 'logRealtimeFeed'])->name('log-realtime.feed');
    Route::post('log-realtime/ping/{mesin}', [MesinController::class, 'pingMachineJson'])->name('log-realtime.ping');
    Route::post('log-realtime/pull/{mesin}', [MesinController::class, 'pullFromDevice'])->name('log-realtime.pull');
    Route::post('mesin/import-log', [MesinController::class, 'importDeliLog'])->name('mesin.import-log');
    Route::post('mesin/sync-all', [MesinController::class, 'syncAll'])->name('mesin.sync-all');
    Route::post('mesin/simulate-tap', [MesinController::class, 'simulateTap'])->name('mesin.simulate-tap');
    Route::post('mesin/{mesin}/test-connection', [MesinController::class, 'testConnection'])->name('mesin.test-connection');
    Route::resource('mesin', MesinController::class)->except(['create', 'show', 'edit']);

    // Pengaturan Perkuliahan & Branding Institusi
    Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan', [PengaturanController::class, 'store'])->name('pengaturan.store');
    Route::put('pengaturan/{pengaturan}', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::post('pengaturan/{pengaturan}/set-active', [PengaturanController::class, 'setActive'])->name('pengaturan.set-active');
    Route::post('pengaturan/branding', [PengaturanController::class, 'updateBranding'])->name('pengaturan.branding');

    // Manajemen Akun
    Route::resource('akun', AkunController::class)->except(['create', 'show', 'edit']);

    // Jadwal Kuliah Terpadu
    Route::resource('jadwal', JadwalController::class);
    
    // Sub-jadwal perkelas
    Route::get('jadwal-kelas/{jadwalKelas}/perkelas', [JadwalController::class, 'jadwalPerkelas'])->name('jadwal.perkelas');
    Route::post('jadwal-kelas/{jadwalKelas}/hari', [JadwalController::class, 'storeHari'])->name('jadwal.hari.store');
    Route::delete('jadwal-hari/{jadwalHari}', [JadwalController::class, 'destroyHari'])->name('jadwal.hari.destroy');
    Route::post('jadwal-hari/{jadwalHari}/jam', [JadwalController::class, 'storeJam'])->name('jadwal.jam.store');
    Route::delete('jadwal-jam/{jadwalJam}', [JadwalController::class, 'destroyJam'])->name('jadwal.jam.destroy');

    // Sub-mahasiswa perkelas
    Route::get('jadwal-kelas/{jadwalKelas}/mahasiswa', [JadwalController::class, 'mahasiswaPerkelas'])->name('jadwal.mahasiswa');
    Route::post('jadwal-kelas/{jadwalKelas}/mahasiswa', [JadwalController::class, 'storeMahasiswa'])->name('jadwal.mahasiswa.store');
    Route::delete('jadwal-mahasiswa/{jadwalMahasiswa}', [JadwalController::class, 'destroyMahasiswa'])->name('jadwal.mahasiswa.destroy');

    // Laporan Absensi & Rekap Jam
    Route::get('laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('laporan/harian/print', [LaporanController::class, 'printHarian'])->name('laporan.harian.print');
    Route::get('laporan/harian/excel', [LaporanController::class, 'excelHarian'])->name('laporan.harian.excel');
    Route::get('laporan/mahasiswa', [LaporanController::class, 'mahasiswa'])->name('laporan.mahasiswa');
    Route::get('laporan/dosen', [LaporanController::class, 'dosen'])->name('laporan.dosen');
});
