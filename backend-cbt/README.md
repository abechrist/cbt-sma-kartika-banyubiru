# CBT - SMA Kartika III-1 Banyubiru

Sistem Computer-Based Testing (CBT) berbasis web untuk ujian PTS/PAS SMA Kartika III-1 Banyubiru. Dibangun dengan Laravel + Blade + Tailwind CSS, berjalan local-first di jaringan LAN sekolah.

## Fitur Utama

- **Multi-peran**: super_admin, admin, guru, kepala_sekolah, proktor, siswa, wali_kelas
- **Manajemen soal**: PG, PG kompleks, benar/salah, isian singkat, menjodohkan, esai, dengan bank soal per mata pelajaran
- **Pembuatan ujian**: komposisi soal, durasi, pengacakan soal/opsi, perbedaan soal antar siswa
- **Sesi ujian (gelombang)**: jadwal waktu, token akses single-use, batas peserta per sesi
- **In-app proctoring**: pemantauan real-time per sesi (status peserta, progres, login) via WebSocket Reverb
- **Server-authoritative timer**: waktu tersisa dihitung di server, auto-submit saat timeout
- **Auto-save jawaban** berkala + pengiriman idempoten (aman dari refresh/duplikat)
- **Penilaian otomatis** untuk PG/PG kompleks/benar-salah/isian singkat/menjodohkan dan **manual untuk esai** (antrean koreksi per guru)
- **Hasil & rekap**: per ujian, per kelas, per siswa
- **Dashboard pimpinan**: statistik ujian, peserta, rata-rata nilai, distribusi nilai, status sesi, dan statistik kelas (kepala sekolah & wali kelas)
- **Analisis butir soal**: tingkat kesukaran, daya beda, dan jumlah benar/salah tiap soal (server-side)
- **Import / Export CSV**: siswa, guru, kelas, soal, peserta, rekap nilai, dan laporan
- **Audit log & deteksi aktivitas mencurigakan** pada ujian
- **Kiosk / Lockdown mode**: fullscreen paksa, blokir klik kanan/copy-paste/seleksi, blokir pintasan (F5/Ctrl+R/W/C), kunci tombol kembali, eskalasi pindah tab ke proktor, halaman peluncur kiosk per peserta
- **Integrasi Dapodik**: impor/ekspor data peserta didik (NISN, nama, JK, TTL, alamat, rombel) via CSV, pencocokan otomatis berdasarkan NISN — sinkronisasi tidak menghambat operasi CBT lokal
- **Background queue (database)**: rekonsiliasi hasil + pembaruan monitoring realtime dijalankan di worker queue (tidak memblokir halaman siswa); gratis tanpa Redis

## Persyaratan

- PHP 8.5+
- Composer
- Node.js 20+ & npm (untuk asset frontend)
- SQLite (default) atau MySQL

## Instalasi (Windows, tanpa Komposer di PATH)

```powershell
# 1. Clone / salin project
# 2. Install dependensi PHP
composer install

# 3. Setup file .env
copy .env.example .env
#   atur DB_CONNECTION=sqlite atau MySQL
#   SESSION_DRIVER=file (single server) — untuk bunga pindah-pindah di LAN disarankan database
#   Sanctum STATE_DOMAIN + APP_URL menunjuk IP server

# 4. Buat database SQLite & migrate + seed
touch database\database.sqlite
php artisan migrate --seed
```

## Seed & Akun Default

Seeder membuat role, kelas, mapel, contoh soal, ujian PTS Matematika, dan user uji:

| Peran          | Email                | Password  |
|----------------|----------------------|-----------|
| Super Admin    | superadmin@kartika.sch.id | password  |
| Admin          | admin@kartika.sch.id | password  |
| Kepala Sekolah | kepsek@kartika.sch.id | password  |
| Proktor        | proktor@kartika.sch.id | password  |
| Guru           | guru@kartika.sch.id  | password  |
| Siswa          | siswa@kartika.sch.id | password  |

> Seeder `ExamSessionSeeder` menghasilkan token contoh yang dicetak lewat menu **Sesi → Cetak Token**.

## Menjalankan (Development)

```bash
npm install
npm run build        # atau: npm run dev  (mode watch)

php artisan serve --host=0.0.0.0 --port=8000

# Realtime monitoring (proktor) — opsional
php artisan reverb:start --host=0.0.0.0 --port=8080

# Background queue (rekonsiliasi hasil & monitoring) — WAJIB untuk nilai realtime
php artisan queue:work --sleep=2 --tries=3

# Scheduler (cleanup, backup, maintenance)
php artisan schedule:work
```

Akses `http://<ip-server>:8000` dari semua client di LAN.

> **Kiosk mode**: jalankan peramban di komputer lab dengan URL ujian + `?kiosk=1`
> (parameter `--kiosk` pada Chrome/Edge), atau gunakan tombol "Kiosk" per peserta
> di halaman Monitoring.

## Menjalankan Test

```bash
php artisan test
```

Test mencakup autentikasi (email/NISN), otorisasi per role, alur ujian siswa (token → mulai → isi → kumpul), pemakaian token single-use, dan penilaian (benar/salah/tidak dijawab).

## Backup Database

Command untuk backup SQLite database dengan aman (hot backup):

```bash
php artisan app:backup-database
```

Opsi yang tersedia:
- `--path=/custom/backup/dir` - Direktori backup khusus
- `--keep=5` - Jumlah file backup yang disimpan (default: 10)
- `--compress` - Kompress file backup menjadi .gz

Contoh penggunaan:
```bash
# Backup dengan kompresi dan simpan 5 file terakhir
php artisan app:backup-database --compress --keep=5

# Backup ke direktori khusus
php artisan app:backup-database --path=/backup/cbt
```

Backup disimpan di `storage/app/backups/` secara default.

## Scheduler & Cron Setup

Laravel Scheduler telah dikonfigurasi untuk menjalankan tugas terjadwal:

Tugas terjadwal yang aktif:
- `cbt-auto-submit` - Setiap menit (auto-submit ujian yang expired)
- `cbt-cleanup-expired-attempts` - Setiap 5 menit (cleanup attempt yang expired)
- `cbt-daily-backup` - Setiap hari pukul 02:00 (backup database harian)
- `cbt-housekeeping` - Setiap hari pukul 03:00 (cleanup sessions + cache flush)

Untuk menjalankan scheduler di production, tambahkan cron entry berikut:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Untuk mengecek tugas terjadwal yang terdaftar:
```bash
php artisan schedule:list
```

## Struktur Penting

- `app/Models/` — model domain (`Exam`, `ExamSession`, `ExamToken`, `ExamAttempt`, `Answer`, `ExamResult`, `Question`, `QuestionOption`, dll.)
- `app/Http/Controllers/` — controller dikelompokkan per fitur:
  - `Api/` — REST API (auth, akademik, ujian, soal, penilaian, monitoring, RPP, LMS, hasil, impor/ekspor, Dapodik, kiosk)
  - `Auth/` — login & registrasi
  - `Dashboard/` — dashboard admin/guru/siswa
  - `Academic/` — kelas & mata pelajaran
  - `User/` — manajemen pengguna
  - `Question/` — soal & bank soal
  - `Examination/` — ujian, sesi, token, & alur pengerjaan siswa (`StudentExamController`)
  - `Grading/` — koreksi manual esai
  - `Monitoring/` — pemantauan sesi ujian
  - `Rpp/` — RPP / modul ajar
  - `Lms/` — kursus, materi, tugas, diskusi
  - `Result/` — hasil & analisis butir soal (`ItemAnalysisController`)
  - `ImportExport/` — CSV & Dapodik
  - `Kiosk/` — mode kiosk
- `app/Http/Requests/` — Form Request dikelompokkan per fitur (Auth, User, Academic, Examination, Question, Rpp, ImportExport, Api)
- `app/Jobs/FinalizeExamResult.php` — rekonsiliasi hasil ujian + broadcast monitoring di background queue (database)
- `app/Policies/` — otorisasi (Exam, Question, ExamAttempt)
- `database/seeders/` — seeder berurutan (Role → User → Kelas → Mapel → Soal → Ujian → Sesi)
- `tests/Feature/` — test dikelompokkan per fitur: `Auth/`, `Examination/`, `Grading/`, `Question/`, `ImportExport/`, `Lms/`, `Monitoring/`, `Kiosk/`, `Dashboard/`, `Jobs/`, `Api/`
- `docs/` — dokumentasi produk (PRD, PRODUCT, DECISIONS, progress fase)

## Deployment Produksi (Nginx + FPM)

```bash
# Build asset production
npm ci
npm run build

# Optimasi Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Konfigurasi Nginx: root -> public/, fastcgi_pass -> php-fpm
# Atur APP_ENV=production, APP_DEBUG=false, dan HTTPS bila memakai cert
```

### Deployment ke Hostinger (shared hosting)

Panduan lengkap langkah demi langkah ada di **[docs/DEPLOYMENT_HOSTINGER.md](docs/DEPLOYMENT_HOSTINGER.md)** — mencakup:
buat MySQL di hPanel, upload via File Manager/SSH, set document root ke `public/`,
`.env` produksi (template: `.env.production.example`), migrasi, cron `schedule:run`,
dan catatan khusus (Reverb tidak berfungsi di shared hosting → `BROADCAST_CONNECTION=null`,
queue `sync`).

Di jaringan sekolah disarankan:
- Gunakan **database session** agar login siswa konsisten antar device.
- Batasi akses ke halaman soal (mis. lewat firewall LAN).
- Cadangkan database secara berkala (`php artisan app:backup-database --keep=5` — mendukung SQLite & MySQL).

## Lisensi

Proyek internal sekolah; MIT untuk kode basis Laravel.