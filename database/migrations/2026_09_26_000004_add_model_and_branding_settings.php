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
        // 1. Tambah detail model dan koneksi pada tabel mesins
        Schema::table('mesins', function (Blueprint $table) {
            $table->string('nama_mesin')->nullable()->after('serial');
            $table->string('brand_mesin')->default('ZKTeco')->after('nama_mesin'); // ZKTeco, Solution, Fingerspot, Hikvision, BioFinger, Realand, Magic, Generic
            $table->string('model_tipe')->nullable()->after('brand_mesin'); // e.g. K40, X100-C, Revo-185, DS-K1T
            $table->string('tipe_koneksi')->default('standalone_tcp')->after('model_tipe'); // standalone_tcp, standalone_udp, cloud_adms, webhook_api
            $table->string('comm_key')->default('0')->after('port'); // communication key / password mesin
            $table->string('metode_verifikasi')->default('fingerprint,rfid,pin')->after('comm_key'); // fingerprint, face, rfid, pin
        });

        // 2. Tambah pengaturan branding institusi dan tema warna pada tabel pengaturans
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->string('app_name')->default('ABSENSI')->after('nama_pengaturan');
            $table->string('institution_name')->default('Universitas Hasanuddin')->after('app_name');
            $table->string('faculty_name')->default('Fakultas Kedokteran')->after('institution_name');
            $table->string('short_name')->default('FK UNHAS')->after('faculty_name');
            $table->string('institution_logo')->nullable()->after('short_name');
            $table->string('website_url')->default('www.med.unhas.ac.id')->after('institution_logo');
            $table->string('email_contact')->default('prodis1_fkuh@gmail.com')->after('website_url');
            $table->text('running_text')->nullable()->after('email_contact');
            $table->string('theme_color')->default('unhas-red')->after('running_text'); // unhas-red, umb-blue, emerald-green, violet-purple, amber-orange, custom
            $table->string('primary_color_hex')->default('#b91c1c')->after('theme_color');
            $table->string('secondary_color_hex')->default('#0f172a')->after('primary_color_hex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->dropColumn([
                'app_name',
                'institution_name',
                'faculty_name',
                'short_name',
                'institution_logo',
                'website_url',
                'email_contact',
                'running_text',
                'theme_color',
                'primary_color_hex',
                'secondary_color_hex',
            ]);
        });

        Schema::table('mesins', function (Blueprint $table) {
            $table->dropColumn([
                'nama_mesin',
                'brand_mesin',
                'model_tipe',
                'tipe_koneksi',
                'comm_key',
                'metode_verifikasi',
            ]);
        });
    }
};
