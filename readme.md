# 🎓 Attendance FK - Sistem Absensi Biometrik & Monitoring Perkuliahan

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](LICENSE)

Sistem Informasi Absensi dan Monitoring Perkuliahan Terintegrasi berbasis Biometrik (Fingerprint & RFID) untuk lingkungan akademik perguruan tinggi (dirancang khusus untuk Fakultas Kedokteran / Institusi Pendidikan Tinggi). 

Aplikasi ini mendukung sinkronisasi multi-mesin fingerprint lintas brand, push webhook real-time, import log USB, dashboard display TV monitoring jadwal kuliah, serta rekapitulasi kehadiran dosen dan mahasiswa.

---

## 🌟 Fitur Utama

### 1. 🔄 Integrasi Mesin Fingerprint Multi-Brand
- **Dukungan Berbagai Merk Mesin**:
  - **Deli**: Seri E3960, E3961, E3962, 15152, 15153, E2180, dll.
  - **ZKTeco**: K40, iFace, MB20, IN01, dll (via protokol TCP/IP ZK).
  - **Solution**: X100-C, X105-ID, P207, dll.
  - **Fingerspot**: Revo Series, Neo, Duo.
  - **Hikvision**: Face & Fingerprint DS-K1T Series.
  - Serta BioFinger, Realand, Magic, dan Generic TCP/IP Standalone.
- **Push Webhook / Cloud ADMS**: Endpoint siap menerima kiriman log otomatis dari mesin (`/api/fingerprint/push`, `/api/fingerprint/deli`, `/iclock/cdata`).
- **Import Log USB**: Fitur upload file log manual jika mesin offline atau tidak tersambung jaringan.
- **Simulator Tap**: Fitur uji coba tap absensi dosen & mahasiswa langsung dari aplikasi tanpa perlu perangkat fisik.

### 2. 📺 Public TV Monitoring Perkuliahan
- Display publik responsif (`/monitoring`) yang dapat dipasang di layar TV koridor atau lobi kampus.
- Menampilkan jadwal kuliah harian secara otomatis lengkap dengan status:
  - 🟡 **Akan Datang**
  - 🟢 **Sedang Berlangsung** *(dengan indikator waktu berjalan)*
  - ⚪ **Selesai**
- Dilengkapi jam digital real-time dan running text pengumuman fakultas.

### 3. 📅 Manajemen Jadwal & Perkuliahan Terpadu
- Pengelolaan jadwal berbasis Blok, Semester, dan Kelas.
- Pembagian sesi perkuliahan: Hari, Jam Masuk, Jam Keluar, Ruangan, Departemen, dan Dosen Pengampu.
- Pemetaan peserta mahasiswa per kelas kuliah.

### 4. 👥 Manajemen Master Data
- **Mahasiswa**: Kelola data mahasiswa, ID Fingerprint, filter per angkatan/kelas, import Excel, dan fitur reset password.
- **Dosen**: Kelola data dosen, NIP, ID Biometrik, gelar, dan import Excel.
- **Mata Kuliah & Departemen**: Manajemen kurikulum dan departemen/bagian.
- **Ruang Kuliah**: Manajemen kapasitas dan alokasi mesin absensi per ruangan.

### 5. 📊 Laporan & Rekapitulasi Kehadiran
- **Laporan Harian**: Rekapitulasi absensi harian per sesi mata kuliah dan ruangan.
- **Laporan Mahasiswa**: Rekap persentase kehadiran mahasiswa sebagai syarat ujian.
- **Laporan Dosen**: Rekap jam mengajar dosen untuk keperluan beban kerja dosen (BKD) & honorarium.
- **Export & Cetak**: Cetak langsung ke format print dokumen rapi atau ekspor ke **Microsoft Excel (.xlsx)**.

### 6. 🎨 Kustomisasi Institusi & Branding Fleksibel
- Pengaturan nama institusi, fakultas, logo, running text, dan kontak.
- Pemilihan tema warna sistem yang dapat disesuaikan:
  - 🔴 *Unhas Red* (Default)
  - 🔵 *Royal Blue*
  - 🟢 *Emerald Green*
  - 🟣 *Violet Purple*
  - 🟠 *Amber Orange*
  - 🎨 *Custom Hex Color*

---

## 🛠️ Persyaratan Sistem

- **PHP**: >= 8.2 (Disarankan PHP 8.3+)
- **Composer**: >= 2.x
- **Node.js**: >= 18.x & NPM
- **Database**: MySQL 8.x / MariaDB 10.4+ / PostgreSQL / SQLite
- **Ekstensi PHP**: `pdo`, `mbstring`, `openssl`, `sockets` (diperlukan untuk komunikasi socket ZKTeco), `gd` / `imagick`, `fileinfo`, `curl`

---

## 🚀 Panduan Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/muhawaluddin/attendance-fk.git
cd attendance-fk
```

### 2. Instal Dependensi Backend & Frontend
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan konfigurasi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_fk
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Database Seeder
Jalankan migrasi tabel beserta data awal (master data demo, akun admin, dan jadwal):
```bash
php artisan migrate --seed
```

### 6. Build Aset Frontend
```bash
# Untuk mode pengembangan:
npm run dev

# Atau build untuk production:
npm run build
```

### 7. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: `http://localhost:8000`

---

## 🔑 Akun Default (Hasil Seeder)

Setelah menjalankan `php artisan db:seed`, Anda dapat masuk menggunakan akun default berikut:

| Level | Username | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password` | Administrator Penuh Sistem |
| **Operator** | `ict` | `password` | Operator / Tim ICT |

> ⚠️ **PENTING**: Segera ganti password default setelah instalasi pada lingkungan produksi melalui menu **Profil** atau **Manajemen Akun**.

---

## 📡 Endpoint Integrasi Biometrik

Aplikasi menyediakan endpoint tanpa CSRF untuk komunikasi mesin fingerprint:

| Metode | Endpoint | Keterangan |
| :--- | :--- | :--- |
| `POST` | `/api/fingerprint/push` | Webhook umum untuk push log absensi JSON |
| `POST` | `/api/fingerprint/deli` | Webhook khusus format log perangkat Deli |
| `GET / POST` | `/iclock/cdata` | Endpoint kompatibel ADMS / Push SDK ZKTeco |
| `GET` | `/monitoring` | Tampilan publik TV display jadwal & ruang kuliah |

---

## 📂 Struktur Direktori Utama

```
├── app/
│   ├── Http/Controllers/     # Controller (Mesin, Jadwal, Laporan, Monitoring, dll)
│   ├── Imports/              # Import Excel Mahasiswa & Dosen
│   ├── Models/               # Model Eloquent (AttLog, Jadwal, Mesin, Pengaturan, dll)
│   └── Services/             # Service helper (LogoProcessor, dll)
├── database/
│   ├── migrations/           # Skema database
│   └── seeders/              # Data awal institusi, akun, dan jadwal
├── resources/
│   └── views/                # Tampilan antarmuka Blade & Tailwind
├── routes/
│   └── web.php               # Rute aplikasi dan endpoint webhook
└── tests/                    # Pengujian otomatis Feature & Unit
```

---

## 📄 Lisensi

Proyek ini dirilis di bawah lisensi [MIT License](LICENSE).
