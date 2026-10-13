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
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('kode_departemen')->nullable()->unique();
            $table->string('nama_departemen');
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('angkatan')->nullable();
            $table->timestamps();
        });

        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->string('id_finger')->nullable()->index();
            $table->string('nip')->unique();
            $table->string('nama_dosen');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->foreignId('departemen_id')->nullable()->constrained('departemens')->nullOnDelete();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('id_finger')->nullable()->index();
            $table->string('nim')->unique();
            $table->string('nama_mahasiswa');
            $table->foreignId('departemen_id')->nullable()->constrained('departemens')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('angkatan')->nullable();
            $table->string('no_hp')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mk')->nullable()->unique();
            $table->string('nama_mk'); // Blok / Mata Kuliah
            $table->integer('sks')->default(2);
            $table->string('semester')->nullable();
            $table->timestamps();
        });

        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan')->nullable();
            $table->string('nama_ruangan');
            $table->integer('kapasitas')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('mesins', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->string('activation')->nullable();
            $table->string('kode')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('port')->default('4370');
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->boolean('status_online')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengaturan')->default('Default');
            $table->integer('menit_perjam')->default(50);
            $table->integer('menit_pergantian')->default(10);
            $table->string('jam_kuliah')->default('08:00');
            $table->string('jam_mulai_istirahat')->default('12:00');
            $table->string('jam_selesai_istirahat')->default('13:00');
            $table->integer('jam_perhari')->default(8);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
        Schema::dropIfExists('mesins');
        Schema::dropIfExists('ruangans');
        Schema::dropIfExists('mata_kuliahs');
        Schema::dropIfExists('mahasiswas');
        Schema::dropIfExists('dosens');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('departemens');
    }
};
