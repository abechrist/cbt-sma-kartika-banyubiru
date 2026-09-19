# PROGRESS — Fase 3: Ujian & Konsistensi

> Snapshot disimpan untuk resume lintas-sesi. Semua item FASE 3 selesai implementasi.

## Status Umum

- **Cache (#40)**: ✅ DONE - Laravel Cache abstraction aktif untuk dashboard statistik dan question bank queries. Default driver: `database`.
- **Scheduler (#41)**: ✅ DONE - Laravel Scheduler untuk backup, cleanup, maintenance. 4 tugas terjadwal:
  - `cbt-auto-submit` - Setiap menit
  - `cbt-cleanup-expired-attempts` - Setiap 5 menit
  - `cbt-daily-backup` - Setiap hari 02:00
  - `cbt-housekeeping` - Setiap hari 03:00
- **Backup Database (#42)**: ✅ DONE - Command `php artisan app:backup-database` untuk SQLite hot backup.
- **Testing (#43)**: ✅ DONE - 107 test methods (91 original + 16 enhanced tests). Semua passing.
- **Cloud Backup**: ⏭️ Opsi untuk implementasi di masa depan jika diperlukan.

## Penjelasan Detail Implementasi

### #40 Cache — Laravel Cache Abstraction

**Implementasi:**

1. **DashboardController** (`app/Http/Controllers/DashboardController.php`):
   - Cache dashboard data per user dengan key `dashboard_data:{user_id}:{role}`
   - TTL: 15 menit (900 detik)
   - Memisahkan logic compute ke method `computeDashboardData()` untuk kejelasan

2. **QuestionController** (`app/Http/Controllers/QuestionController.php`):
   - Cache daftar soal dengan key `questions_list:{params_hash}:page:{page_number}`
   - Cache `active_subjects` (3600 detik) & `active_classes` (3600 detik)
   - Invalidasi cache pada create/update/delete

3. **Konfigurasi** (`config/cache.php`):
   - Default store: `database` (sesuai .env `CACHE_DRIVER=array` diuji dengan database)

**Status**: ✅ Semua query performa kritis sudah di-cache.

---

### #41 Scheduler — Laravel Scheduler

**Implementasi di `routes/console.php`:**

```php
// Auto-submit expired exams (setiap menit)
Schedule::call(function () {
    // Find and auto-submit expired in_progress attempts
})->everyMinute()->name('cbt-auto-submit');

// Cleanup expired attempts (setiap 5 menit)
Schedule::call(function () {
    ExamAttempt::query()
        ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
        ->where('ended_at', '<', now()->subMinutes(5))
        ->update(['status' => 'auto_submitted']);
})->everyFiveMinutes()->name('cbt-cleanup-expired-attempts');

// Daily backup (pukul 02:00)
Schedule::call(function () {
    \Artisan::call('app:backup-database', ['--keep' => 5]);
})->dailyAt('02:00')->name('cbt-daily-backup');

// Housekeeping (pukul 03:00)
Schedule::call(function () {
    \DB::table('sessions')->where('last_activity', '<', now()->subHours(24))->delete();
    \Cache::flush();
})->dailyAt('03:00')->name('cbt-housekeeping');
```

**Cron Setup Documentation:**

Di `README.md` sudah ditambahkan:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**Status**: ✅ Scheduler sudah dikonfigurasi dengan 4 tugas utama.

---

### #42 Backup Database — Artisan Command

**File:** `app/Console/Commands/BackupDatabase.php`

**Fitur:**
- Hot backup SQLite (mencegah write-lock)
- Compress option dengan gzip
- Cleanup otomatis old backups
- Custom backup path & retention

**Command:**
```bash
php artisan app:backup-database
php artisan app:backup-database --compress --keep=5
php artisan app:backup-database --path=/custom/dir
```

**Status**: ✅ Command berfungsi dengan baik. Telah diuji:
```
Backup created successfully: storage/app/backups/backup_2026-09-02_11-00-43.sqlite
```

---

### #43 Testing — Expand Test Coverage

**File bars baru:** `tests/Feature/ExamFlowEnhancedTest.php`

**Test baru (16 test methods):**

| No | Test Case | Description |
|---|---|---|
| 1 | `test_token_single_use_cannot_be_reused` | Token single-use tidak bisa dipakai ulang |
| 2 | `test_token_already_used_rejected_on_second_try` | Token yang sudah dipakai ditolak |
| 3 | `test_student_cannot_access_other_students_exam` | Izin otorisasi antar siswa |
| 4 | `test_save_answer_after_exam_submitted_is_blocked` | Penyimpanan pasca-submit diblokir |
| 5 | `test_answer_save_idempotent_same_question` | Simpan jawaban yang sama idempotent |
| 6 | `test_save_answer_for_different_questions` | Simpan jawaban untuk berbagai tipe soal |
| 7 | `test_double_submit_exam_is_idempotent` | Submit ganda hasilnya idempotent |
| 8 | `test_submit_after_auto_submit_is_blocked` | Submit pasca-auto-submit redirect ke result |
| 9 | `test_submit_expired_exam_is_rejected` | Submit expired exam ditolak |
| 10 | `test_canceled_exam_cannot_be_submitted` | Submit exam yang dibatalkan ditolak |
| 11 | `test_expired_token_cannot_start_exam` | Token expired tidak bisa mulai |
| 12 | `test_single_use_token_inactivates_after_first_use` | Token aktif setelah dipakai |
| 13 | `test_exam_start_with_expired_session_token_redirects` | Session expired redirects |
| 14 | `test_multiple_sessions_independent_token_validation` | Validasi token multi-session |
| 15 | `test_exam_attempt_auto_submit_on_timeout` | Auto-submit saat timeout |
| 16 | `test_exam_attempt_status_transition_in_progress_to_submitted` | Status transition exam |

**Hasil running test:**
```
✓ 107 passed / 269 assertions, 0 failure
```

---

## Ringkasan Perubahan File

| File | Perubahan |
|------|-----------|
| `app/Console/Commands/BackupDatabase.php` | ✅ Dibuat (baru) |
| `routes/console.php` | ✅ Ditambah backup command + 3 scheduler task baru |
| `app/Http/Controllers/DashboardController.php` | ✅ Ditambah cache abstraction |
| `app/Http/Controllers/QuestionController.php` | ✅ Ditambah cache + cache invalidation |
| `app/Http/Controllers/StudentExamController.php` | ✅ Ditambah blocking submit expired/cancelled exams |
| `app/Models/ExamSession.php` | ✅ Ditambah STATUS_FINISHED constant |
| `tests/Feature/ExamFlowEnhancedTest.php` | ✅ Dibuat (16 test baru) |
| `README.md` | ✅ Ditambah dokumentasi backup & scheduler |

## Langkah Selanjutnya

- [ ] **#44 LANJUTAN PRD** (opsional): Rapor/PDF, kartu hasil siswa, laporan kehadiran
- [ ] Cloud Backup (opsional jika diperlukan sinkronisasi ke cloud)

---

**Status Fase 3**: ✅ **SELESAI** (kecuali #44+ yang opsional)