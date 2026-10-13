<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('semester'); // Ganjil / Genap / Semester 1, 2, dsb
            $table->string('thn_ajaran'); // contoh: 2025/2026
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('dosen_koordinator_id')->nullable()->constrained('dosens')->nullOnDelete();
            $table->foreignId('dosen_sekretaris_id')->nullable()->constrained('dosens')->nullOnDelete();
            $table->foreignId('pengaturan_id')->nullable()->constrained('pengaturans')->nullOnDelete();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        Schema::create('jadwal_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('jadwal_haris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kelas_id')->constrained('jadwal_kelas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('hari')->nullable();
            $table->timestamps();
        });

        Schema::create('jadwal_jams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_hari_id')->constrained('jadwal_haris')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->foreignId('departemen_id')->nullable()->constrained('departemens')->nullOnDelete();
            $table->string('jam_masuk'); // contoh '08:00'
            $table->integer('jml_jam')->default(1);
            $table->string('jam_keluar')->nullable(); // calculated or saved
            $table->string('kegiatan'); // Materi kuliah / Praktikum / Tutorial / CSL / Ujian
            $table->timestamps();
        });

        Schema::create('jadwal_dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_jam_id')->constrained('jadwal_jams')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('dosens')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('jadwal_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kelas_id')->constrained('jadwal_kelas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_mahasiswas');
        Schema::dropIfExists('jadwal_dosens');
        Schema::dropIfExists('jadwal_jams');
        Schema::dropIfExists('jadwal_haris');
        Schema::dropIfExists('jadwal_kelas');
        Schema::dropIfExists('jadwals');
    }
};
