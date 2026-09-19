# PROGRESS — Kiosk/Lockdown, Dapodik, Queue (#37,#38,#39)

> Snapshot disimpan untuk resume lintas-sesi. Semua item TAMAT (DONE), diverifikasi
> E2E live + 91 test hijau. Langkah berikutnya: **lanjutkan ke FASE 3** (lihat akhir file).

## Status Umum
- **App** (Laravel 13.30, PHP 8.5.4, SQLite, Sanctum, Reverb): berjalan di `127.0.0.1:8000` (`php artisan serve`), Reverb di `:8080`, **queue worker aktif** (`php artisan queue:work`).
- **routes di-cache**: `bootstrap/cache/routes-v7.php` (HANYA `route:cache` yang aman; JANGAN `view:cache`/`config:cache`/`optimize` — dapat korup compiled view → 500. Gunakan `view:clear` bila muncul ParseError).
- `bootstrap/cache/config.php` harus TIDAK ada (hindari CSRF 419 dari app.env tersimpan).
- Login field = `login` (email/NISN/NIP); password semua seed = `password`.
- DB dev sudah di-seed (`migrate:fresh --seed`): sesi "Sesi 1 Gelombang Pagi" status `open`, ada ≤6 attempt hasil demo di-rekap.

## Selesai Sesi Ini (FASE 2 -> 2.5: agent-prompt #37 #38 #39)

### #37 Kiosk / Lockdown Mode — DONE
- `app/Http/Controllers/KioskController.php` — `launch()` (peluncur + panduan) & `show(ExamAttempt)` (link kiosk per peserta).
- `resources/views/kiosk/{launch,show}.blade.php`.
- Rute (grup `role:super_admin,admin,proktor`): `kiosk.launch` (GET `kiosk/launch`), `kiosk.show` (GET `kiosk/{attempt}`). Import `use App\Http\Controllers\KioskController;` di `routes/web.php`.
- Lockdown di `resources/views/exam/take.blade.php` (blok `<script>` ~baris 299+, body `oncontextmenu/onselectstart/ondragstart/oncopy/oncut/onpaste`):
  - Aktif bila URL berisi `?kiosk=1` (`const kioskActive`).
  - Fullscreen paksa + auto-reassert (event `fullscreenchange`/`webkitfullscreenchange`), eskalasi `kiosk_fullscreen_broken` (suspicious).
  - Blokir pintasan: F5/F11/F12/PrintScreen + Ctrl+R/W/S/P/N/T/U/C/D + Alt+Tab.
  - History lock (`pushState` + `popstate`), `beforeunload` guard, badge "KIOSK AKTIF".
  - Blokir klik-kanan/copy/paste/seleksi (CSS `body.locked-down` + atribut body).
  - Menyalakan ulang fullscreen pada gesture pertama (`click` once).
- Tombol **Kiosk** per peserta ditambah di `resources/views/monitoring/session.blade.php` (link `route('kiosk.show', participant['attempt_id'])`).
- `tests/Feature/KioskTest.php` (4 test).

### #38 Integrasi Dapodik — DONE (CSV, offline-safe)
- `app/Http/Controllers/DapodikController.php` — `template()`/`import()`/`export()`.
- Rute (grup `role:super_admin,admin`): `dapodik.template` (GET), `dapodik.import` (POST), `dapodik.export` (GET). Import `use App\Http\Controllers\DapodikController;`.
- Kolom Dapodik: `nisn, nama, jenis_kelamin(L/P), tanggal_lahir, alamat, rombel`. Pencocokan NISN; updateOrCreate; email fallback `nisn@kartika.sch.id`; NISN = identifier login.
- Seksi UI "Integrasi Dapodik" ditambah di `resources/views/import_export/index.blade.php`.
- KEPENTINGAN: tabel `users` TIDAK punya kolom `birth_place` / `username` — jangan pakai. Kolom ada: `nisn, nip, phone, birth_date, address, gender, class_id, is_active`. Sudah fixed (pakai `birth_date`, jangan `username`).
- `tests/Feature/DapodikTest.php` (6 test).

### #39 Queue (database) — DONE
- `app/Jobs/FinalizeExamResult.php` — rekonsiliasi skor + broadcast `AttemptStatusUpdated` di background.
- Dipasang di `app/Http/Controllers/StudentExamController.php::submit()` & `autoSubmit()` via `FinalizeExamResult::dispatch($attempt->id)` (hapus broadcast sinkron yang lama; job yang melakukan broadcast). Import `use App\Jobs\FinalizeExamResult;`.
- `QUEUE_CONNECTION=database` (.env) / `sync` (phpunit). Tanpa Redis.
- WAJIB `php artisan queue:work --sleep=2 --tries=3` agar nilai/monitoring realtime ter-push.
- GOTCHA: jangan eager-load `result.user` pada job (`ExamResult` TIDAK punya relasi `user`) → `RelationNotFoundException` 500. Pakai `with(['session.exam','user','result'])`.
- `tests/Feature/QueueTest.php` (2 test).

## Pengujian
- `php artisan test` → **91 passed / 224 assertions, 0 failure, 0 error** (naik dari 79 — tambahan 12 test Kiosk/Dapodik/Queue).
- Live E2E diverifikasi: kiosk.launch 200, kiosk.show 200 (URL berisi `?kiosk=1`), monitoring session 200 (6 peserta + tombol Kiosk), dapodik template/export 200 CSV, dapodik import sukses (Citra Lestari & Doni Wijaya dibuat, class_id=7/XII-MIPA), import-export page 200 menampilkan seksi Dapodik.

## Dokumen
- `README.md`: Fitur Utama + Menjalankan (tambah `queue:work`) + Struktur + daftar test — diperbarui.
- `DECISIONS.md` §11: Kiosk/Dapodik/Queue (~baris 173+).

## LANGKAH BERIKUTNYA (SAAT USER KEMBALI)
**Menawarkan & melanjutkan ke FASE 3.** Pastikan:
1. Cek service: app `127.0.0.1:8000`, reverb `:8080`, queue worker.
2. `php artisan route:cache` jika ada perubahan rute; JANGAN `view:cache`/`config:cache`/`optimize`.
3. `php artisan view:clear` bila muncul ParseError/500 korup.
4. Kerjakan fitur apa pun yang diminta di `agent-prompt.md` untuk FASE 3, atau tunggu instruksi user.

### Cakupan FASE 3 (dari agent-prompt.md, #40+)
- **#40 CACHE**: Laravel Cache abstraction-based; jangan mengikat logic ke Redis; deployment sekolah pakai infra minimal.
- **#41 SCHEDULER**: Laravel Scheduler untuk backup, cleanup session, maintenance, laporan, housekeeping. Dokumentasikan cron `* * * * * ... php artisan schedule:run`.
- **#42 BACKUP DATABASE**: backup SQLite aman (saat write aktif) — command `php artisan app:backup-database` + dokumentasi di README.
- **#43 TESTING**: framework Laravel; pastikan cakupan token (valid/expired/invalid/used/wrong-session), timer (belum mulai/sedang/berakhir), submit, autorisasi, dll.
- **(kemungkinan #44+ lanjutan prd.md)**: rapor/export PDF, kartu hasil siswa, laporan kehadiran, dsb. — periksa agent-prompt.md & prd.md lebih lanjut.

