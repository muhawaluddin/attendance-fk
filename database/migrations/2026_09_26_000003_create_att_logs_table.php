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
        Schema::create('att_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sn')->nullable()->index(); // serial number mesin
            $table->string('pin')->index(); // id_finger mahasiswa atau dosen
            $table->dateTime('scan_date')->index();
            $table->time('scan_time')->nullable();
            $table->integer('verified')->default(1);
            $table->integer('status_scan')->default(0); // 0: Check-in, 1: Check-out
            $table->string('work_code')->nullable();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->string('tipe_pengguna')->nullable(); // mahasiswa, dosen
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('att_logs');
    }
};
