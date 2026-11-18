# Decision: Phase 0 - Analysis & Architecture Review

## Context
As per agent-prompt.md, I need to start with Phase 0 - Analysis & Architecture Review before beginning implementation. I must read both prd.md and agent-prompt.md to understand the requirements, then document my analysis and proposed architecture.

## Decision
Create a comprehensive analysis document covering:
1. Requirements analysis from PRD
2. Entity relationship modeling
3. Role and permission mapping
4. Workflow identification
5. Technical architecture proposal
6. Initial folder structure proposal
7. Database schema proposal

## Alternatives
1. Skip analysis and jump directly to implementation (violates agent-prompt requirements)
2. Begin with database migrations without proper modeling (risks rework)
3. Start building frontend components before defining API contracts

## Reason
Following the explicit instructions in agent-prompt.md to complete Phase 0 before moving to Phase 1 ensures we have a solid foundation and reduces the likelihood of architectural mistakes that would be costly to fix later.

# Decision: Phase 0 - Analysis & Architecture Review

## Context
As per agent-prompt.md, I need to start with Phase 0 - Analysis & Architecture Review before beginning implementation. I must read both prd.md and agent-prompt.md to understand the requirements, then document my analysis and proposed architecture.

## Decision
Create a comprehensive analysis document covering:
1. Requirements analysis from PRD
2. Entity relationship modeling
3. Role and permission mapping
4. Workflow identification
5. Technical architecture proposal
6. Initial folder structure proposal
7. Database schema proposal

## Alternatives
1. Skip analysis and jump directly to implementation (violates agent-prompt requirements)
2. Begin with database migrations without proper modeling (risks rework)
3. Start building frontend components before defining API contracts

## Reason
Following the explicit instructions in agent-prompt.md to complete Phase 0 before moving to Phase 1 ensures we have a solid foundation and reduces the likelihood of architectural mistakes that would be costly to fix later.

## Detailed Analysis

### 1. Aktor/User Roles
Based on PRD section 4:
- **Super Admin**: Admin TI sekolah/operator utama - Kelola seluruh sistem, user, server, backup
- **Admin/Operator Ujian**: Tata Usaha / Kurikulum - Buat jadwal ujian, kelola sesi, cetak kartu peserta, generate token
- **Guru/Pembuat Soal**: Guru mata pelajaran - Kelola bank soal mapel yang diampu, koreksi esai, lihat nilai
- **Kepala Sekolah/Wakakurikulum**: Pimpinan sekolah - Lihat laporan rekap & analisis hasil ujian (read-only dashboard)
- **Proktor/Pengawas Ruang**: Guru piket saat ujian - Monitoring real-time peserta di ruangnya, buka kunci ujian, lapor gangguan
- **Siswa/Peserta**: Peserta ujian - Login, mengerjakan ujian sesuai jadwal & token, lihat hasil (jika diizinkan)
- **Wali Kelas**: Guru wali kelas - Lihat rekap nilai siswa di kelas yang diampu

### 2. Modul Utama (Functional Requirements)
Based on PRD section 6:
- **Modul Autentikasi & Manajemen Pengguna** (FR-1.1 s.d. FR-1.4)
- **Modul Bank Soal** (FR-2.1 s.d. FR-2.6)
- **Modul Manajemen Ujian** (FR-3.1 s.d. FR-3.8)
- **Modul Pengerjaan Ujian (Client Siswa)** (FR-4.1 s.d. FR-4.7)
- **Modul Monitoring & Pengawasan** (FR-5.1 s.d. FR-5.4)
- **Modul Penilaian & Hasil** (FR-6.1 s.d. FR-6.6)
- **Modul Laporan** (FR-7.1 s.d. FR-7.3)
- **Modul Administrasi Sistem** (FR-8.1 s.d. FR-8.3)

### 3. Workflow Utama (User Flows)
Based on PRD section 5:
- **Alur Guru Membuat Ujian** (5.1)
- **Alur Admin Menjadwalkan Ujian** (5.2)
- **Alur Siswa Mengerjakan Ujian** (5.3)
- **Alur Proktor Mengawasi Ujian** (5.4)

### 4. Entity/Database Model
Based on PRD section 10:
- **User** (id, nama, role, username, password_hash, NISN/NIP)
- **Kelas/Rombel** (id, nama_kelas, tingkat, tahun_ajaran)
- **MataPelajaran** (id, nama_mapel, guru_pengampu)
- **BankSoal** (id, mapel_id, tipe_soal, konten_soal, opsi_jawaban, kunci_jawaban, bobot, tingkat_kesulitan)
- **PaketUjian** (id, nama_ujian, mapel_id, daftar_soal, durasi, pengaturan_acak)
- **JadwalUjian/Sesi** (id, paket_ujian_id, tanggal, jam_mulai, jam_selesai, ruang, token)
- **PesertaUjian** (id, sesi_id, siswa_id, status, waktu_mulai, waktu_selesai)
- **JawabanSiswa** (id, peserta_ujian_id, soal_id, jawaban, nilai, waktu_dijawab)
- **LogAktivitas** (id, peserta_ujian_id, jenis_kejadian, waktu, keterangan)
- **HasilUjian** (id, peserta_ujian_id, nilai_akhir, status_koreksi)

### 5. Kebutuhan Teknis dari Agent-Prompt.md

#### Stack Teknis (WAJIB):
- Backend: PHP 8.3+, Laravel 13, Laravel Eloquent ORM
- Database: SQLite (dev) dengan kompatibilitas PostgreSQL (future)
- Frontend: React, Vite, Tailwind CSS
- Realtime: Laravel Reverb, Laravel Echo
- Auth: Laravel Sanctum (session-based)
- Storage: Laravel Storage

#### Prinsip Arsitektur:
- Modular Monolith (bukan microservices)
- Struktur kode berdasarkan domain bisnis
- ~~Server-local-first design (LAN operational)~~ **SUPERSEDED** — produksi cloud-only (lihat keputusan "Penghapusan Mode Server Lokal/LAN")
- ~~No internet dependency during exams~~ **SUPERSEDED** — aplikasi cloud; ketahanan dijaga via auto-save + resume sesi
- Server-authoritative timer (client-only display)
- Auto-save jawaban dengan debounce
- Idempotent submission
- Token-based exam access
- Database transactions untuk operasi kritis
- Laravel Events untuk realtime broadcast

### 6. Implementasi Fase Berdasarkan Agent-Prompt.md

#### Fase 0 (Saat ini): Analysis & Architecture Review
- Baca PRD dan agent-prompt
- Identifikasi requirement, aktor, modul, workflow, entity
- Identifikasi security & non-functional requirement
- Buat implementation plan
- Periksa konflik dengan prompt
- Buat DECISIONS.md

#### Fase 1 - MVP (Fungsi Inti):
- Project Foundation (Laravel 13, React, Vite, Tailwind CSS, SQLite, Laravel Reverb, Laravel Sanctum)
- Authentication & User Management
- Modul Bank Soal (minimal tipe: Pilihan Ganda, Esai)
- Modul Manajemen Ujian (paket ujian, pemilihan soal, jadwal, session, ruangan, peserta, token, durasi, randomisasi)
- Modul Pengerjaan Ujian Siswa (login, validasi token, instruksi ujian, halaman pengerjaan, navigasi, pilihan jawaban, esai, countdown, auto-save, resume, submit, auto-submit)
- Auto-Grading (untuk Pilihan Ganda)
- Rekap Nilai (per siswa, per kelas, per ujian)

## 7. Security Requirements
Dari PRD section 7 (Keamanan) dan agent-prompt.md sections 21-23:
- Enkripsi password
- Proteksi sesi ujian dari manipulasi client-side
- Pencegahan akses tidak sah ke bank soal
- HTTPS wajib untuk seluruh akses produksi
- Token ujian tidak dapat digunakan ulang setelah expired
- CSRF protection
- Authentication & Authorization
- Validation
- Rate limiting
- Secure session configuration
- Secure file upload
- Audit logging
- Input sanitization
- Mass assignment protection
- Database transaction
- Access control
- Secure error handling
- Prinsip: Frontend tidak boleh menjadi sumber kebenaran authorization
- Semua akses penting harus diverifikasi server

### 8. Realtime Requirements
Dari agent-prompt.md sections 170-189:
- Laravel Reverb + Laravel Echo untuk realtime
- Dashboard proktor: status peserta (online/offline/selesai/bermasalah)
- Admin monitoring seluruh ruang
- Log aktivitas mencurigakan
- Events: ExamStarted, AnswerSaved, ExamSubmitted, ExamAutoSubmitted, ParticipantConnected, ParticipantDisconnected, SuspiciousActivityDetected
- Broadcast hanya informasi yang diperlukan (tidak broadcast jawaban benar atau data sensitif)

### 9. Technical Constraints & Assumptions
- Jumlah siswa ±500 orang, tetapi hanya 40-60 siswa per sesi karena keterbatasan laboratorium
- ~~Sistem harus berjalan tanpa internet (LAN-only operational)~~ **SUPERSEDED** — sistem dihosting di cloud dan diakses lewat internet
- Backup otomatis harian minimal
- Anti-contek: pengacakan soal, token per sesi, monitoring realtime
- Auto-save berkala untuk mencegah kehilangan data
- Mechanisme resume untuk koneksi terputus/restart komputer
- Timer server-side (client hanya display)
- Token masa berlaku tertentu
- Struktur kode modular untuk maintainability
- Dokumentasi lengkap untuk deployment cloud
### 10. FASE 2 - Penyempurnaan
- **Koreksi manual esai** (F2.4): antrean koreksi per guru; skor pada tabel `answers.score`, ditulis oleh `GradingController::update` dengan filter `exam_attempt_id` (bukan `attempt_id`) dan re-kalkulasi `ExamResult` via `recomputeResult()` (auto-soal dinilai ulang dengan `isCorrect()`).
- **Dashboard pimpinan** (F2.5): kepsek melihat jumlah ujian, peserta, rata-rata, distribusi nilai (band A-E), status sesi, dan statistik per kelas; wali kelas melihat rata-rata & hasil kelas. Menghindari `Collection::where('result')` yang tidak akurat → gunakan `filter(fn($a)=>$a->result)`.
- **Import/Export** (F2.6): format CSV native (dependency Excel dihindari agar tidak menambah dependency runtime). Import: siswa, guru, kelas, soal. Export: siswa, guru, kelas, soal, peserta, rekap nilai, laporan. Akses dikelompokkan: import siswa/guru/kelas + rekap/laporan = admin; import/export soal = guru.
- **Analisis butir soal** (F2.7): tingkat kesukaran (P = benar/peserta, esai = rasio skor), daya beda (selisih kelompok atas-bawah 27%), distribusi & jumlah benar/salah. Server-side. Hindari `$optionDist[$k]++` pada Collection → gunakan `->put()`.
- **Catatan penting**: `php artisan view:cache`/`optimize` dapat menghasilkan compiled view yang korup dan membuat beberapa route 500 (mis. markdown.blade ParseError). HANYA `route:cache` yang aman. Gunakan `view:clear` bila terjadi.
- Laravel 13: nama route controller tanpa `use` di `routes/web.php` menyebabkan "Target class not found"; semua controller harus diimpor.

### 11. Kiosk/Lockdown, Dapodik, Queue (agent-prompt #37-39)
- **Kiosk / Lockdown** (#37): dikerjakan sebagai enhancement browser (bukan aplikasi native dependency) — fullscreen paksa + autolis, blokir klik-kanan/copy/paste/seleksi, blokir pintasan (F5/F11/F12/PrintScreen, Ctrl+R/W/S/P/N/T/U/C/D, Alt+Tab), kunci tombol kembali (history lock), eskalasi pindah tab/fullscreen-broken ke proktor via /exam/activity. Aktif saat URL ujian berisi `?kiosk=1`; halaman `/kiosk/launch` + `/kiosk/{attempt}` menyediakan link kiosk per peserta untuk proktor. Tidak menjadi dependency Fase 1.
- **Dapodik** (#38): sinkronisasi via CSV (bukan webservice live agar tidak menghambat ujian yang sedang berlangsung). Template + import (pencocokan NISN) + export siswa berformat Dapodik (nisn, nama, jenis_kelamin L/P, tanggal_lahir, alamat, rombel). NISN dipakai sebagai identifier login (LoginController memakai kolom `nisn` bila login bukan email). Live webservice API dapat ditambahkan belakangan tanpa mengubah metode ini.
- **Queue** (#39): `QUEUE_CONNECTION=database` (bukan Redis). Grading tetap sinkron & server-authoritative (hasil langsung terlihat siswa); `FinalizeExamResult` (job background) melakukan rekonsiliasi skor + broadcast monitoring tanpa memblokir redirect. WAJIB menjalankan `php artisan queue:work`. Hindari eager-load `result.user` pada job (ExamResult tidak punya relasi `user`).

---

# Decision: Penghapusan Mode Server Lokal/LAN (Cloud-Only)

## Context
Dokumen awal (prd.md dan agent-prompt.md) merancang CBT berjalan local-first pada server fisik di sekolah dengan jaringan LAN, plus opsi sinkronisasi cloud. Implementasi aktual tidak pernah memiliki mode server lokal: tidak ada konfigurasi, toggle, atau jalur kode LAN. Produksi berjalan cloud-only di Hostinger pada `https://cbtkartika.koomit.com`.

## Decision
- Produksi **cloud-only**: Laravel + MySQL di Hostinger, diakses via HTTPS dari laboratorium sekolah maupun perangkat siswa.
- Tidak ada server lokal, sinkronisasi dua arah, atau mode offline penuh.
- Ketahanan ujian dipenuhi lewat **auto-save jawaban, resume sesi, dan timer server-authoritative**, bukan lewat server lokal.
- Asset frontend dibundel sendiri (tanpa CDN runtime), tetapi aplikasi tetap membutuhkan koneksi internet.
- Backup database dilakukan di sisi cloud (MySQL dump / command `app:backup-database`).

## Alternatives
1. Mempertahankan server lokal sebagai mode operasional — ditolak: tidak ada implementasi, menambah beban perawatan perangkat dan sinkronisasi.
2. Menulis ulang aplikasi agar mendukung offline-first — ditolak: kompleksitas besar tanpa kebutuhan terbukti.
3. Menyembunyikan referensi lama tanpa catatan — ditolak: dokumentasi menjadi menyesatkan.

## Reason
Menyesuaikan dokumentasi dengan arsitektur yang benar-benar dijalankan, menghilangkan ekspektasi keliru bahwa sistem dapat beroperasi tanpa internet atau memerlukan server fisik di sekolah.

## Dampak pada keputusan lama
- "Server-local-first design (LAN operational)" — **SUPERSEDED**.
- "No internet dependency during exams" — **SUPERSEDED**.
- "Sistem harus berjalan tanpa internet (LAN-only operational)" — **SUPERSEDED**.
