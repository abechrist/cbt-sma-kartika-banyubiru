# Panduan Deployment — Hostinger Shared Hosting

Aplikasi: **CBT SMA Kartika III-1 Banyubiru** (Laravel 13 + Filament 3)
Server target: Hostinger Shared Hosting (paket Premium / Business, PHP 8.3+)

---

## Arsitektur Dua-Domain (Backend API + SPA Frontend)

> ⚠️ **Aplikasi ini kini memakai arsitektur **split-domain** dengan SPA React terpisah.**
> Bagian 1–9 di bawah mendokumentasikan deployment *backend* (Laravel + API + Filament).
> Bagian **10 (SPA Frontend)** menjelaskan cara deploy SPA React ke domain terpisah.

| Peran | Domain | Isi |
|---|---|---|
| **Backend** (Laravel API + Filament admin) | `https://ipcbtkartika.koomit.com` | Seluruh codebase Laravel, document root ke `public/` |
| **Frontend** (SPA React siswa) | `https://cbtkatika.koomit.com` | Hasil build `frontend/dist/` (statis, tanpa PHP) |

- Frontend SPA memanggil API backend via **Bearer token** (bukan cookie session).
- Backend mengizinkan origin frontend melalui CORS (`config/cors.php` + `CORS_ALLOWED_ORIGINS` di `.env`).
- Autentikasi: siswa login di SPA → backend mengembalikan `{ user, token }` → SPA simpan token di `localStorage` → kirim header `Authorization: Bearer <token>`.

---

## Ringkasan

| Kebutuhan | Nilai |
|---|---|
| PHP | 8.3 atau 8.4 (Hostinger hPanel → Advanced → PHP Configuration) |
| Database | MySQL (buat via hPanel) |
| Node.js | **Tidak perlu** di server — asset sudah di-build |
| Queue worker | Tidak ada di shared hosting → pakai `QUEUE_CONNECTION=sync` |
| Realtime (Reverb) | **Tidak berfungsi** di shared hosting → monitoring tetap berfungsi via refresh |

---

## 1. Persiapan di hPanel (Hostinger)

### a. Buat database
1. Login ke hPanel → **Websites** → pilih domain → **Databases → MySQL Databases**
2. Buat database baru (misal: `u123456789_cbt`)
3. Buat user database baru (misal: `u123456789_cbt_user`) dengan password kuat
4. **Jangan lupa assign user ke database tersebut** dengan ALL PRIVILEGES

### b. Set PHP version
1. hPanel → **Advanced → PHP Configuration**
2. Pilih **PHP 8.3** (atau 8.4) dan aktifkan ekstensi: `pdo_mysql`, `mbstring`, `gd`, `zip`, `intl`, `fileinfo` (biasanya sudah aktif)

### c. Pilih metode upload (salah satu):
- **Metode A (paling mudah):** Upload seluruh folder project ke direktori mana pun di dalam `public_html`, lalu arahkan domain ke subfolder `public/` (lihat bagian 3).
- **Metode B (SSH + Git):** jika paket Hostinger punya SSH, clone repo di `~/domains/<domain>/public_html` — detail di bagian 4.

---

## 2. Build asset (di komputer lokal — SEKALI SAJA)

Asset sudah ter-build dan tersimpan di `public/build/`. Kalau kamu mengubah frontend (JS/CSS), jalankan di lokal:

```bash
npm install
npm run build
```

Commit hasil build-nya, lalu upload.

---

## 3. Upload via File Manager (Metode A)

### a. Struktur folder

Laravel mengharuskan **document root mengarah ke folder `public/`**. Struktur di server:

```
~/domains/<domain>/
└── public_html/          ← document root domain
    ├── .env              ← (buat baru di server, lihat langkah d)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/           ← isi konten public/ ada di SINI
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── artisan
    ├── composer.json
    └── composer.lock
```

⚠️ **PENTING:** Jangan taruh `public/index.php` langsung di `public_html/`. Simpan seluruh codebase (termasuk `public/`) di dalam `public_html/`, lalu atur document root.

Cara Hostinger mengatur document root ke subfolder:
1. hPanel → **Websites** → pilih domain → **Settings** (atau **Manage**)
2. Cari **Document Root** → ubah menjadi `/public_html/public`
3. Simpan. (Jika menu tidak tersedia di paketmu, gunakan `.htaccess` di `public_html/` — file sudah disiapkan di root project, lihat bagian 5.)

### b. Yang di-upload vs tidak di-upload

**Upload (wajib):**
- Semua folder & file project **kecuali** yang di bawah
- `vendor/` → **boleh upload** (paling mudah) atau composer install via SSH
- `public/build/` → **wajib** (asset hasil build)

**JANGAN upload:**
- `.env` lokal (berisi APP_KEY & konfigurasi lokal) → buat baru di server
- `node_modules/` → tidak perlu
- `.git/` → tidak perlu
- `tests/`, `docs/`, `playwright-report/`, `test-results/` → opsional (tidak dipakai runtime)
- `database/database.sqlite` & `storage/logs/*` → bersihkan dulu

### c. Upload
1. Di komputer: `zip -r cbt.zip . -x "node_modules/*" ".git/*" "tests/*" "docs/*" "playwright-report/*" "test-results/*" "vendor/*"`
2. Upload `cbt.zip` via File Manager hPanel → **Upload**
3. Extract di `public_html/`
4. Hapus `cbt.zip` dari server

### d. Buat `.env` di server
1. Di server, duplikasi `.env.production.example` → beri nama `.env`
2. Isi `APP_KEY` dengan menjalankan via SSH/cron (atau pakai `php artisan key:generate --show` di terminal lokal lalu paste — **tapi wajib generate yang baru**, jangan pakai key dari `.env` lokal untuk produksi... sebenarnya boleh, tapi lebih aman generate baru):
   ```bash
   php artisan key:generate
   ```
3. Sesuaikan `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` dengan database yang dibuat di langkah 1a
4. Sesuaikan `APP_URL` dan `MAIL_*`

### e. Migrasi & storage link
Jalankan perintah berikut (via SSH, atau buat file `artisan-run.php` sementara... **cara terbaik: gunakan SSH**. Jika tidak ada SSH, sempatkan akses **Terminal** di hPanel — Hostinger menyediakan fitur Terminal untuk paket Premium ke atas):

```bash
cd ~/domains/<domain>/public_html
php artisan migrate --force
php artisan storage:link
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Buat super admin pertama:
```bash
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=UserSeeder --force   # cek dulu isi seeder, sesuaikan email/password admin
```

⚠️ Jika akses artisan tidak tersedia sama sekali: jalankan migrasi manual dengan meng-import file SQL dari lokal ekspor, dan atur storage link dengan membuat symlink via File Manager (Advanced → symlink creator) `public/storage → storage/app/public`.

### f. Permission
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage
```

---

## 4. Upload via SSH + Git (Metode B — lebih profesional)

```bash
ssh u<USERNAME>@<server>
cd ~/domains/<domain>/public_html

# clone
git clone <repo-url> .
# atau upload manual lewat sftp/scp

# install dependency (tanpa dev, untuk produksi)
composer install --no-dev --optimize-autoloader

# env + key
cp .env.production.example .env
php artisan key:generate
# edit .env, isi DB_* dan APP_URL

php artisan migrate --force
php artisan storage:link
php artisan optimize      # config:cache + route:cache + view:cache
```

---

## 5. `.htaccess` untuk document root non-standar

File `.htaccess` di root project (`public_html/.htaccess`) otomatis me-redirect semua request ke folder `public/`. File ini sudah disertakan di repo. Isinya:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

> Jika kamu sudah mengubah Document Root ke `/public_html/public` via hPanel, file ini TIDAK diperlukan (tidak masalah jika tetap ada).

---

## 6. Cron Job (wajib untuk fitur auto-submit ujian)

Laravel scheduler perlu dijalankan tiap menit. Di hPanel → **Advanced → Cron Jobs**:

```bash
/usr/bin/php /home/u<USERNAME>/domains/<domain>/public_html/artisan schedule:run
```

Set interval **every minute** (Once per minute). Ini penting untuk auto-submit ujian yang waktunya habis.

Jika `QUEUE_CONNECTION=database` (bukan sync), tambahkan cron kedua:
```bash
/usr/bin/php /home/u<USERNAME>/domains/<domain>/public_html/artisan queue:work --once --stop-when-empty
```

---

## 7. Catatan khusus aplikasi ini

### Realtime monitoring (Reverb WebSocket)
- Shared hosting **tidak bisa** menjalankan `php artisan reverb:start` (butuh proses terus-menerus + port terbuka).
- Solusi bawaan: set `BROADCAST_CONNECTION=null` di `.env` → halaman monitoring menampilkan banner *"Mode siaga — menunggu koneksi realtime"*, data tetap tampil dari server dan bisa di-refresh.
- Kalau butuh real-time sungguhan: (a) upgrade ke Hostinger VPS lalu jalankan Reverb, atau (b) pakai Push (Pusher) — set `BROADCAST_CONNECTION=pusher` + isi `PUSHER_*` dan `routes/channels.php` sudah siap.

### Job FinalizeExamResult
- Dengan `QUEUE_CONNECTION=sync`, job dijalankan langsung saat submit → hasil langsung final. Aman untuk skala sekolah.

### Storage (artefak ujian, upload)
- Semua file upload tersimpan di `storage/app/public/` — pastikan symlink `public/storage` ada.
- Folder `public_html/storage` harus writable oleh PHP (owner user hosting).

---

## 8. Checklist final sebelum go-live

- [ ] `APP_ENV=production`, `APP_DEBUG=false` di `.env` server
- [ ] `APP_KEY` sudah di-generate di server
- [ ] `APP_URL` = domain asli dengan `https://`
- [ ] DB MySQL sudah dibuat & kredensial benar, `migrate --force` sukses
- [ ] Ada akun admin (seeder/registrasi)
- [ ] `php artisan storage:link` sudah jalan
- [ ] `public/build/` terupload (asset produksi)
- [ ] Cron job `schedule:run` tiap menit aktif
- [ ] SSL aktif (hPanel → SSL) → `https://` di-browser sudah hijau
- [ ] `.htaccess`/document root → request masuk ke `public/`
- [ ] Log akses: `storage/logs/laravel.log` normal (tidak ada error 500)

---

## 9. Troubleshooting umum

| Gejala | Solusi |
|---|---|
| **500 error** setelah upload | `storage/logs/laravel.log` cek isinya; cek permission `storage/` & `bootstrap/cache/`; pastikan `.env` ada & `APP_KEY` terisi |
| **404 pada semua route** | Document root belum mengarah ke `public/` → atur di hPanel atau gunakan `.htaccess` |
| **Koneksi DB ditolak** | Cek `DB_HOST` (Hostinger biasanya `127.0.0.1`), username, password |
| **Sessions error / "Cannot write session"** | Cek `storage/framework/sessions/` ada & writable |
| **Asset tidak muncul** | Pastikan `public/build/` terupload; jalankan `php artisan view:clear` |
| **Mau update kode** | Metode A: re-upload file berubah. Metode B: `git pull` + `composer install --no-dev` + `php artisan optimize:clear` + `php artisan migrate --force` |

---

## 10. Deployment SPA Frontend (React) — `cbtkatika.koomit.com`

Bagian ini khusus untuk domain **frontend** yang menghosting SPA React hasil build Vite.
Backend API tetap mengikuti Bagian 1–9 di atas pada domain `ipcbtkartika.koomit.com`.

### a. Build SPA (di komputer lokal)

```bash
cd frontend
npm install
# set base URL API backend (lihat .env)
cp .env.example .env.local    # lalu isi VITE_API_BASE_URL
npm run build
```

Hasil build berada di `frontend/dist/`:
```
frontend/dist/
├── index.html
└── assets/
    ├── index-*.js
    └── index-*.css
```

`VITE_API_BASE_URL` di `.env.local` diarahkan ke backend:
```
VITE_API_BASE_URL=https://ipcbtkartika.koomit.com/api/v1
```
Nilai ini di-bundle ke JS saat `npm run build`, jadi ubah dulu `.env.local` **sebelum** build, lalu re-build jika URL berubah.

> Untuk development lokal, `vite.config.js` mem-proxy `/api` → `http://localhost:8000`, jadi
> tidak perlu override URL. Di produksi, SPA memakai `VITE_API_BASE_URL` di atas.

### b. Upload ke public_html frontend

1. Buat/arahkan `cbtkatika.koomit.com` di hPanel ke folder `public_html` milik frontend.
2. Upload **isi** `frontend/dist/` ke `public_html/` (jadi `index.html` berada langsung di `public_html/`).
   ```
   ~/domains/cbtkatika.koomit.com/public_html/
   ├── index.html
   └── assets/
   ```
3. Karena ini SPA (client-side routing), semua request ke route SPA (mis. `/dashboard`,
   `/exam/take/123`) harus di-rewrite ke `index.html`. Siapkan `.htaccess` di `public_html/`:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       # jangan rewrite file/asset yang benar-benar ada
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule . index.html [L]
   </IfModule>
   ```

### c. Konfigurasi backend agar menerima SPA frontend

Di `.env` backend (`ipcbtkartika.koomit.com`), pastikan:

```
FRONTEND_URL=https://cbtkatika.koomit.com
APP_URL=https://ipcbtkartika.koomit.com
CORS_ALLOWED_ORIGINS=https://cbtkatika.koomit.com
SANCTUM_STATEFUL_DOMAINS=https://cbtkatika.koomit.com,https://ipcbtkartika.koomit.com
```

Lalu jalankan:
```bash
php artisan config:clear && php artisan optimize:clear
```

> Backend memakai **Bearer token** untuk API SPA, jadi `supports_credentials` di `config/cors.php`
> boleh `true` (untuk cookie Sanctum) atau `false` (murni Bearer). Nilai saat ini `true` aman
> selama `allowed_origins` hanya berisi domain sendiri.

### d. Checklist SPA

- [ ] `frontend/dist/` ter-build dengan `VITE_API_BASE_URL=https://ipcbtkartika.koomit.com/api/v1`
- [ ] `index.html` + `assets/` terupload di `public_html` domain `cbtkatika.koomit.com`
- [ ] `.htaccess` rewrite ke `index.html` siap (untuk router SPA)
- [ ] SSL aktif untuk kedua domain (`https://cbtkatika.koomit.com` & `https://ipcbtkartika.koomit.com`)
- [ ] Backend `.env` berisi `CORS_ALLOWED_ORIGINS` & `SANCTUM_STATEFUL_DOMAINS`
- [ ] Test di browser: buka `https://cbtkatika.koomit.com/login` → login → dashboard → mulai ujian