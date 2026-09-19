# Prompt untuk AI Coding Agent — Pembangunan Sistem CBT SMA Kartika III-1 Banyubiru

# 1. IDENTITAS DAN KONTEKS PROYEK

Kamu adalah **AI Coding Agent** yang bertugas membangun:

**Sistem CBT (Computer Based Test) untuk SMA Kartika III-1 Banyubiru**, Kabupaten Semarang, Jawa Tengah.

Sekolah memiliki sekitar **±500 siswa** dan jumlah komputer/lab terbatas. Oleh karena itu, ujian harus mendukung pelaksanaan secara:

* multi-sesi;
* bergelombang;
* dalam satu hari;
* menggunakan jaringan LAN/WiFi lokal sekolah;
* dengan satu server lokal sebagai pusat aplikasi.

Sistem harus dirancang sebagai aplikasi yang **reliable dalam kondisi jaringan lokal**, mudah dipasang oleh operator sekolah, mudah di-backup, dan tidak bergantung pada koneksi internet ketika ujian sedang berlangsung.

Dokumen requirement lengkap terdapat pada:

```text
prd.md
```

`prd.md` merupakan **source of truth untuk kebutuhan bisnis, fitur, aktor, dan workflow sistem**.

### Aturan prioritas

Jika terdapat konflik antara dokumen ini dan `prd.md`:

1. Gunakan `prd.md` sebagai sumber kebenaran untuk:

   * kebutuhan bisnis;
   * fitur;
   * workflow;
   * role pengguna;
   * aturan proses ujian.

2. Gunakan dokumen ini sebagai sumber kebenaran untuk:

   * teknologi;
   * arsitektur aplikasi;
   * struktur kode;
   * database implementation;
   * deployment;
   * testing;
   * security implementation.

3. Jika terdapat konflik besar yang berpengaruh terhadap arsitektur, jangan mengambil keputusan sembarangan. Catat konflik tersebut dalam `DECISIONS.md` dan laporkan kepada user.

---

# 2. STACK TEKNIS — WAJIB

Gunakan stack berikut. Jangan mengganti framework utama tanpa persetujuan user.

## 2.1 Backend

* **PHP 8.3+**
* **Laravel 13**
* Laravel Eloquent ORM
* Laravel Migrations
* Laravel Seeders & Factories
* Laravel Form Requests
* Laravel Policies/Gates
* Laravel Events/Listeners
* Laravel Queues
* Laravel Notifications jika diperlukan
* Laravel Storage

Laravel harus menjadi **backend utama sekaligus application framework**.

Jangan menggunakan:

* Node.js + Express sebagai backend;
* NestJS;
* Django;
* FastAPI;
* Laravel Lumen;
* microservices.

---

# 3. DATABASE

Database utama harus mendukung dua skenario.

### Development dan deployment sekolah

Gunakan:

```text
SQLite
```

SQLite dipilih karena:

* instalasi sangat sederhana;
* tidak membutuhkan database server terpisah;
* cocok untuk deployment lokal satu sekolah;
* mudah di-backup;
* mudah dipindahkan.

### Future scalability

Schema harus tetap kompatibel dengan:

```text
PostgreSQL
```

sehingga sistem dapat dikembangkan menjadi deployment yang lebih besar tanpa perubahan arsitektur fundamental.

Gunakan:

```text
Laravel Eloquent ORM
Laravel Migration
Laravel Query Builder
```

### Larangan

Jangan:

* menulis raw SQL yang bergantung pada dialect SQLite;
* menggunakan fitur SQLite-specific yang menyebabkan migrasi ke PostgreSQL sulit;
* membuat schema yang tidak kompatibel dengan PostgreSQL;
* menanamkan SQL langsung di controller jika Eloquent/Query Builder sudah mencukupi.

Jika raw SQL benar-benar diperlukan:

1. dokumentasikan alasannya;
2. pastikan kompatibel dengan SQLite dan PostgreSQL;
3. letakkan pada layer repository/service yang sesuai;
4. tambahkan automated test.

---

# 4. FRONTEND

Frontend menggunakan:

* **React**
* **Vite**
* **Tailwind CSS**

React digunakan untuk membangun UI interaktif seperti:

* halaman pengerjaan ujian;
* navigasi soal;
* timer;
* dashboard monitoring;
* tabel data;
* modal;
* form;
* filtering;
* status peserta secara realtime.

Laravel tetap menjadi application/backend framework.

Struktur frontend harus terintegrasi secara rapi dengan Laravel.

Gunakan pendekatan yang sederhana dan maintainable.

Jangan membangun frontend sebagai aplikasi SPA terpisah yang membutuhkan backend Node.js terpisah.

---

# 5. REALTIME

Gunakan:

* **Laravel Reverb**
* **Laravel Echo**

untuk kebutuhan realtime.

Realtime digunakan terutama untuk:

* dashboard proktor;
* status peserta ujian;
* peserta online/offline;
* status sedang mengerjakan;
* status selesai;
* sisa waktu;
* indikasi koneksi terputus;
* aktivitas penting lainnya.

Arsitektur:

```text
Laravel Application
        │
        ├── Events
        │
        └── Laravel Reverb
                 │
                 └── Laravel Echo
                         │
                         └── React Client
```

Jangan menggunakan Socket.IO sebagai realtime layer utama.

Jangan membuat server WebSocket Node.js terpisah.

Realtime harus tetap berada dalam ekosistem Laravel.

---

# 6. AUTENTIKASI DAN OTORISASI

Gunakan:

```text
Laravel Sanctum
```

dengan session-based authentication untuk aplikasi web.

Jangan menggunakan JWT sebagai default apabila session-based authentication Laravel sudah mencukupi.

Role minimal:

* Super Admin
* Admin/Operator
* Guru
* Kepala Sekolah
* Proktor
* Siswa
* Wali Kelas

Detail permission mengikuti `prd.md`.

Gunakan kombinasi:

```text
Middleware
Policies
Gates
Form Requests
```

Jangan hanya mengandalkan pemeriksaan role di frontend.

### Prinsip penting

Frontend tidak boleh menjadi sumber kebenaran authorization.

Semua akses penting harus diverifikasi server.

Contoh:

```text
User → Request
     ↓
Authentication
     ↓
Authorization
     ↓
Validation
     ↓
Business Logic
     ↓
Database
```

---

# 7. STORAGE FILE

File seperti:

* gambar soal;
* attachment soal;
* dokumen;
* file import;
* file export;
* backup tertentu;

harus menggunakan:

```text
Laravel Storage
```

Gunakan abstraction filesystem Laravel agar deployment dapat menggunakan:

* local storage;
* public disk;
* cloud storage di masa depan.

Jangan menyimpan file upload secara langsung menggunakan path filesystem hardcoded.

Struktur storage harus dirancang agar aman dan mudah dibackup.

---

# 8. DEPLOYMENT TARGET

Target utama adalah:

```text
1 PC / Server Sekolah
        │
        └── Laravel Application
                │
                ├── SQLite
                ├── Web Server
                └── Reverb
                       │
                       └── LAN/WiFi
                            │
                            ├── PC Siswa
                            ├── PC Proktor
                            └── PC Admin/Guru
```

Aplikasi harus dapat berjalan tanpa koneksi internet selama ujian.

Internet hanya menjadi kebutuhan opsional untuk:

* update aplikasi;
* backup cloud;
* sinkronisasi;
* monitoring jarak jauh;
* integrasi eksternal.

### Prinsip penting

**Internet outage tidak boleh menghentikan ujian lokal.**

---

# 9. WEB SERVER DAN RUNTIME

Deployment production lokal harus menggunakan:

* PHP 8.3+
* Laravel 13
* SQLite
* web server seperti Apache atau Nginx
* Laravel Reverb untuk realtime

Development dapat menggunakan:

```bash
php artisan serve
```

atau environment Laravel development yang setara.

Production tidak boleh bergantung pada:

```text
npm run dev
```

Frontend harus dibuild terlebih dahulu:

```bash
npm run build
```

Kemudian aplikasi Laravel dijalankan menggunakan production configuration.

---

# 10. NODE.JS DALAM PROJECT

Node.js **bukan backend application server**.

Node.js hanya boleh digunakan sebagai development/build dependency untuk:

* Vite;
* React;
* Tailwind CSS;
* frontend asset compilation.

Contoh:

```bash
npm install
npm run build
```

Tidak boleh membuat:

```text
Node.js Express Server
```

sebagai backend aplikasi.

---

# 11. ARSITEKTUR APLIKASI

Gunakan:

# Modular Monolith

Jangan menggunakan microservices.

Alasan:

* tim developer kecil;
* sistem digunakan oleh satu sekolah;
* deployment harus sederhana;
* maintenance harus mudah;
* server sekolah memiliki resource terbatas.

Namun kode harus tetap dipisahkan berdasarkan domain bisnis.

Contoh:

```text
app/
├── Domain/
│   ├── Auth/
│   ├── User/
│   ├── Student/
│   ├── Teacher/
│   ├── Subject/
│   ├── QuestionBank/
│   ├── Examination/
│   ├── ExamSession/
│   ├── ExamAttempt/
│   ├── Monitoring/
│   ├── Grading/
│   ├── Reporting/
│   └── ImportExport/
│
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
│
├── Models/
├── Policies/
├── Services/
├── Events/
├── Listeners/
├── Jobs/
└── Support/
```

Jika pendekatan domain folder terlalu kompleks untuk kebutuhan tertentu, gunakan struktur Laravel standar selama pemisahan tanggung jawab tetap jelas.

Jangan membuat abstraksi hanya demi terlihat enterprise.

---

# 12. STRUKTUR FRONTEND

Gunakan struktur yang jelas, misalnya:

```text
resources/
├── js/
│   ├── app/
│   ├── components/
│   ├── layouts/
│   ├── pages/
│   ├── features/
│   │   ├── auth/
│   │   ├── students/
│   │   ├── teachers/
│   │   ├── question-bank/
│   │   ├── examinations/
│   │   ├── exam-session/
│   │   ├── monitoring/
│   │   ├── grading/
│   │   └── reports/
│   ├── hooks/
│   ├── services/
│   ├── lib/
│   └── types/
│
└── css/
    └── app.css
```

Gunakan component reusable.

Hindari duplikasi UI.

---

# 13. ARSITEKTUR DOMAIN CBT

Minimal domain:

```text
Authentication
User Management
Student Management
Teacher Management
Class Management
Subject Management
Question Bank
Examination
Exam Scheduling
Exam Session
Exam Token
Exam Attempt
Answer Management
Grading
Monitoring
Reporting
Import/Export
Audit Log
```

Detail fitur harus mengikuti `prd.md`.

---

# 14. PRINSIP DESAIN CBT YANG WAJIB

## 14.1 Server-authoritative timer

Timer ujian harus ditentukan oleh server.

Jangan mempercayai waktu dari browser siswa.

Server harus menentukan:

```text
started_at
duration
ends_at
remaining_time
```

Client hanya menampilkan countdown.

Validasi akhir waktu harus dilakukan server.

Contoh konsep:

```text
ends_at = started_at + duration
```

Jika client mengirim jawaban setelah waktu berakhir, server harus menolaknya sesuai aturan ujian.

---

# 15. AUTO-SAVE JAWABAN

Setiap perubahan jawaban siswa harus dikirim ke server secara otomatis.

Gunakan mekanisme:

```text
Client
  ↓
Debounce
  ↓
API Request
  ↓
Laravel
  ↓
Validation
  ↓
Persist Answer
```

Jangan menunggu tombol submit akhir.

Tujuannya untuk mengurangi risiko kehilangan jawaban karena:

* listrik mati;
* browser crash;
* komputer restart;
* jaringan terputus;
* siswa refresh halaman.

---

# 16. RESUME SESSION

Jika siswa:

* refresh browser;
* kehilangan koneksi;
* browser crash;
* komputer restart;

maka sistem harus dapat melanjutkan attempt yang masih valid.

Server harus mengembalikan:

* exam attempt;
* soal yang sedang dikerjakan;
* jawaban tersimpan;
* nomor soal;
* status attempt;
* waktu mulai;
* waktu berakhir;
* sisa waktu.

Jangan menyimpan state penting hanya di localStorage.

LocalStorage hanya boleh menjadi cache/pelengkap apabila diperlukan.

---

# 17. IDEMPOTENT SUBMISSION

Submit ujian harus idempotent.

Jika request submit terkirim dua kali:

```text
POST /exam-attempts/{id}/submit
```

maka sistem tidak boleh:

* menggandakan nilai;
* menggandakan submission;
* mengubah status secara tidak konsisten;
* membuat attempt baru.

Gunakan transaction dan state validation.

Contoh state:

```text
NOT_STARTED
IN_PROGRESS
SUBMITTED
AUTO_SUBMITTED
EXPIRED
CANCELLED
```

Transisi state harus dikontrol oleh server.

---

# 18. TOKEN UJIAN

Token ujian harus:

* dibuat untuk sesi tertentu;
* memiliki masa berlaku;
* terkait dengan exam session;
* dapat digunakan sesuai aturan PRD;
* tidak dapat digunakan secara tidak sah untuk login ganda.

Token harus divalidasi server.

Jangan menganggap token valid hanya karena formatnya benar.

---

# 19. MULTI-SESSION / GELOMBANG

Sistem harus mendukung:

```text
1 Exam
    │
    ├── Session 1
    │     ├── Room A
    │     └── Token set A
    │
    ├── Session 2
    │     ├── Room B
    │     └── Token set B
    │
    └── Session 3
          ├── Room C
          └── Token set C
```

Satu paket ujian dapat dijadwalkan ke beberapa sesi.

Setiap session dapat memiliki:

* tanggal;
* waktu mulai;
* durasi;
* ruangan;
* peserta;
* token;
* status.

---

# 20. RANDOMISASI SOAL

Pengacakan soal dan opsi jawaban dilakukan secara server-side.

Server harus menentukan urutan:

```text
Question Order
Option Order
```

per siswa sesuai konfigurasi ujian.

Client hanya menerima urutan yang sudah ditentukan.

Tujuannya untuk:

* konsistensi;
* mengurangi peluang manipulasi;
* mencegah siswa melihat source data asli;
* mendukung variasi paket ujian.

Randomisasi harus dapat direproduksi selama attempt yang sama jika diperlukan.

---

# 21. SECURITY

Terapkan security best practices Laravel.

Minimal:

* CSRF protection;
* authentication;
* authorization;
* validation;
* rate limiting;
* secure session configuration;
* password hashing;
* secure file upload;
* audit logging;
* input sanitization;
* mass assignment protection;
* database transaction;
* access control;
* secure error handling.

Jangan pernah mempercayai:

* user ID dari frontend;
* role dari frontend;
* exam ID tanpa authorization check;
* remaining time dari frontend;
* nilai yang dikirim frontend;
* status submission dari frontend.

---

# 22. SERVER-SIDE VALIDATION

Semua endpoint harus melakukan validation.

Gunakan Laravel:

```text
Form Request
```

atau validation mechanism yang sesuai.

Contoh:

```text
StoreQuestionRequest
UpdateQuestionRequest
StartExamRequest
SaveAnswerRequest
SubmitExamRequest
ImportStudentRequest
```

Jangan menempatkan seluruh validasi di controller.

---

# 23. DATABASE TRANSACTION

Gunakan database transaction untuk operasi kritis.

Terutama:

* mulai ujian;
* submit ujian;
* auto-submit;
* grading;
* import data;
* pembuatan session;
* pembuatan token;
* proses yang mengubah banyak tabel sekaligus.

Contoh:

```php
DB::transaction(function () {
    // critical operation
});
```

---

# 24. CONCURRENCY DAN DATA CONSISTENCY

Karena puluhan siswa dapat mengakses server secara bersamaan, sistem harus memperhatikan concurrency.

Terutama pada:

* auto-save;
* submit;
* auto-submit;
* token usage;
* exam start;
* exam session state.

Gunakan:

* database transaction;
* unique constraint;
* foreign key;
* state validation;
* locking bila benar-benar diperlukan.

Jangan mengandalkan frontend untuk mencegah duplicate request.

---

# 25. FASE PENGEMBANGAN

Ikuti fase secara berurutan.

**Jangan melompati fase.**

---

# FASE 1 — FONDASI & MVP

## 1. Project Foundation

Buat:

```text
Laravel 13
React
Vite
Tailwind CSS
SQLite
Laravel Reverb
Laravel Sanctum
```

Konfigurasikan:

* `.env.example`;
* database;
* migrations;
* seeders;
* frontend build;
* authentication;
* basic layout;
* routing;
* error handling.

---

## 2. Authentication & User Management

Implementasikan:

* login;
* logout;
* session management;
* role;
* permission;
* user management;
* password management.

Role:

```text
Super Admin
Admin/Operator
Guru
Kepala Sekolah
Proktor
Siswa
Wali Kelas
```

Implementasikan import massal data siswa/guru menggunakan Excel.

Gunakan library PHP yang sesuai untuk spreadsheet processing.

Jangan menambahkan dependency besar jika tidak diperlukan.

---

# 26. MODUL BANK SOAL

Implementasikan CRUD:

* mata pelajaran;
* bank soal;
* soal;
* opsi jawaban;
* kategori;
* tingkat kesulitan jika diperlukan PRD.

Minimal tipe soal:

```text
Pilihan Ganda
Esai
```

Soal harus mendukung:

* pertanyaan;
* opsi;
* jawaban benar;
* bobot;
* gambar jika diperlukan;
* status aktif/nonaktif.

---

# 27. MODUL MANAJEMEN UJIAN

Implementasikan:

* paket ujian;
* pemilihan soal;
* jadwal;
* session;
* ruangan;
* peserta;
* token;
* durasi;
* randomisasi.

Admin harus dapat:

```text
Create Exam
    ↓
Select Questions
    ↓
Create Session
    ↓
Assign Participants
    ↓
Generate Tokens
    ↓
Publish Exam
```

---

# 28. MODUL PENGERJAAN UJIAN SISWA

Implementasikan:

* login;
* validasi token;
* instruksi ujian;
* halaman pengerjaan;
* navigasi soal;
* pilihan jawaban;
* esai;
* countdown;
* auto-save;
* resume;
* submit;
* auto-submit ketika waktu habis.

UI harus ringan dan cepat.

---

# 29. AUTO-GRADING

Untuk Pilihan Ganda:

```text
Student Answer
       ↓
Compare With Correct Answer
       ↓
Calculate Score
       ↓
Persist Result
```

Jangan menerima nilai yang dihitung client.

Nilai harus dihitung server.

---

# 30. REKAP NILAI

Minimal tampilkan:

* nama siswa;
* kelas;
* ujian;
* jumlah benar;
* jumlah salah;
* nilai;
* status.

Sediakan rekap:

* per siswa;
* per kelas;
* per ujian.

---

# FASE 2 — PENYEMPURNAAN

Implementasikan setelah Fase 1 selesai dan mendapat konfirmasi.

## 1. Realtime Monitoring

Gunakan:

```text
Laravel Events
Laravel Reverb
Laravel Echo
React
```

Dashboard proktor minimal dapat melihat:

* daftar peserta;
* online/offline;
* belum mulai;
* sedang mengerjakan;
* selesai;
* auto-submitted;
* koneksi terputus;
* sisa waktu;
* aktivitas mencurigakan.

---

# 31. TIPE SOAL TAMBAHAN

Tambahkan:

```text
Benar/Salah
Menjodohkan
Isian Singkat
PG Kompleks
```

Pastikan architecture soal cukup extensible sehingga penambahan tipe soal baru tidak membutuhkan perubahan besar pada seluruh sistem.

Gunakan strategy/polymorphic approach hanya jika memang diperlukan.

Jangan membuat abstraction yang berlebihan.

---

# 32. KOREKSI MANUAL ESAI

Guru dapat:

* melihat jawaban esai;
* memberikan skor;
* memberikan catatan;
* menggunakan rubrik;
* menyimpan hasil koreksi.

Nilai akhir harus dihitung server-side.

---

# 33. IMPORT / EXPORT

Implementasikan:

### Import

* siswa;
* guru;
* kelas;
* soal;
* peserta ujian.

### Export

* soal;
* peserta;
* hasil ujian;
* rekap nilai;
* laporan.

Gunakan format Excel/CSV sesuai kebutuhan PRD.

Sediakan template import yang jelas.

---

# 34. ANALISIS BUTIR SOAL

Implementasikan jika diperlukan dalam PRD:

* tingkat kesukaran;
* daya beda;
* distribusi jawaban;
* statistik soal;
* jumlah benar/salah.

Perhitungan harus dilakukan server-side.

---

# 35. DASHBOARD PIMPINAN

Sediakan dashboard untuk:

* Kepala Sekolah;
* Wakakurikulum;
* Admin;
* Guru;

sesuai permission.

Dashboard dapat menampilkan:

* jumlah ujian;
* peserta;
* tingkat kehadiran;
* rata-rata nilai;
* distribusi nilai;
* status ujian;
* statistik kelas.

---

# 36. AUDIT LOG DAN AKTIVITAS MENCURIGAKAN

Sistem harus mencatat aktivitas penting.

Contoh:

```text
LOGIN
LOGOUT
EXAM_STARTED
ANSWER_SAVED
EXAM_SUBMITTED
AUTO_SUBMITTED
TOKEN_USED
TOKEN_REJECTED
SESSION_RESUMED
SUSPICIOUS_ACTIVITY
```

Untuk browser event seperti:

* pindah tab;
* kehilangan focus;
* reconnect;

gunakan sebagai **indikator aktivitas**, bukan sebagai bukti pasti kecurangan.

Jangan membuat klaim bahwa siswa pasti curang hanya berdasarkan satu event.

---

# FASE 3 — LANJUTAN

Fase ini bersifat opsional dan hanya dilakukan jika diminta.

## 1. Cloud Backup

Implementasikan backup ke cloud storage.

Backup harus:

* asynchronous;
* tidak menghentikan ujian;
* memiliki retry;
* memiliki logging;
* dapat dikonfigurasi.

---

# 37. KIOSK / LOCKDOWN MODE

Jika diminta, buat aplikasi client ringan untuk:

* fullscreen;
* mencegah akses aplikasi lain;
* membatasi navigasi;
* monitoring status client.

Jangan menjadikan fitur ini sebagai dependency Fase 1.

---

# 38. DAPODIK

Jika diminta, implementasikan integrasi/import/export dengan Dapodik sesuai format dan API yang tersedia.

Integrasi eksternal tidak boleh menghambat operasi CBT lokal.

---

# 39. QUEUE

Untuk deployment sederhana gunakan:

```text
database queue
```

sebisa mungkin.

Jangan menjadikan Redis sebagai dependency wajib untuk instalasi sekolah.

Redis hanya boleh ditambahkan apabila memang diperlukan berdasarkan kebutuhan performa.

---

# 40. CACHE

Gunakan Laravel Cache secara abstraction-based.

Jangan mengikat business logic langsung pada Redis.

Deployment default sekolah harus tetap dapat berjalan menggunakan infrastructure minimal.

---

# 41. SCHEDULER

Gunakan Laravel Scheduler untuk pekerjaan terjadwal seperti:

* backup;
* cleanup session;
* maintenance;
* laporan;
* housekeeping.

Untuk server sekolah, dokumentasikan cara menjalankan scheduler menggunakan mekanisme OS yang sesuai.

Contoh Linux:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

# 42. BACKUP DATABASE

Karena database default menggunakan SQLite, sistem harus menyediakan panduan backup sederhana.

Minimal:

```text
database/database.sqlite
```

harus dapat dibackup secara berkala.

Namun backup harus dilakukan dengan aman, terutama ketika terdapat proses write aktif.

Sediakan command/script backup melalui Laravel atau script OS yang sesuai.

Contoh konsep command:

```bash
php artisan app:backup-database
```

Jika command tersebut dibuat, dokumentasikan penggunaannya pada `README.md`.

---

# 43. TESTING

Gunakan testing framework Laravel yang sesuai.

Minimal test untuk:

### Authentication

* login valid;
* login invalid;
* authorization.

### Exam Token

* token valid;
* token expired;
* token invalid;
* token sudah digunakan;
* token tidak sesuai session.

### Timer

* exam belum dimulai;
* exam sedang berlangsung;
* exam sudah berakhir;
* client mengirim waktu manipulatif;
* auto-submit.

### Auto-Grading

* jawaban benar;
* jawaban salah;
* unanswered;
* bobot soal.

### Answer Saving

* save answer;
* update answer;
* duplicate request;
* invalid attempt.

### Submission

* submit normal;
* double submit;
* submit setelah expired;
* auto-submit.

### Authorization

Pastikan role tidak dapat mengakses resource yang bukan miliknya.

---

# 44. FEATURE TESTING

Prioritaskan Feature Test Laravel untuk workflow penting.

Contoh:

```text
Admin creates exam
        ↓
Creates session
        ↓
Assigns participants
        ↓
Generates tokens
        ↓
Student logs in
        ↓
Starts exam
        ↓
Answers questions
        ↓
Auto-save
        ↓
Submit
        ↓
Server grades
        ↓
Result appears
```

Workflow end-to-end Fase 1 harus dapat diuji.

---

# 45. SEED DATA

Sediakan seed data sehingga aplikasi langsung dapat dicoba.

Minimal:

```text
1 Super Admin
1 Admin
2 Guru
1 Kepala Sekolah
1 Proktor
10 Siswa
2 Kelas
2 Mata Pelajaran
20 Soal PG
5 Soal Esai
1 Paket Ujian
1 Exam Session
```

Gunakan dummy data yang jelas.

Jangan memasukkan password production.

Gunakan credential development yang terdokumentasi dengan jelas.

---

# 46. UI/UX

UI harus:

* ringan;
* sederhana;
* responsif;
* mudah digunakan;
* cocok untuk komputer sekolah;
* tidak membutuhkan koneksi internet;
* tidak menggunakan asset eksternal sebagai dependency runtime.

Hindari:

* animasi berlebihan;
* video background;
* font eksternal wajib;
* gambar besar;
* library UI berlebihan;
* request API eksternal yang tidak diperlukan.

---

# 47. KHUSUS HALAMAN UJIAN SISWA

Halaman ujian harus menjadi halaman paling ringan.

Prioritas:

```text
Question
Answer
Timer
Navigation
Save Status
```

Jangan memuat dashboard atau resource yang tidak diperlukan.

Gunakan lazy loading/code splitting jika diperlukan.

---

# 48. ERROR HANDLING

Error yang diterima user harus menggunakan Bahasa Indonesia.

Contoh:

```text
Token ujian tidak valid.
Sesi ujian telah berakhir.
Jawaban berhasil disimpan.
Koneksi ke server terputus.
Ujian telah dikirim.
Anda tidak memiliki izin untuk mengakses halaman ini.
```

Error teknis internal tidak boleh membocorkan:

* stack trace;
* SQL query;
* credentials;
* filesystem path sensitif;
* environment variables.

---

# 49. BAHASA KODE

Gunakan **Bahasa Inggris** untuk:

* variable;
* function;
* class;
* method;
* database table;
* database column;
* API endpoint;
* component;
* service;
* event;
* job.

Contoh:

```php
$examAttempt
$remainingTime
$saveAnswer()
CalculateExamScore
ExamSubmitted
```

Gunakan **Bahasa Indonesia** untuk:

* label UI;
* tombol;
* pesan error;
* notifikasi;
* instruksi siswa;
* heading yang ditampilkan user.

---

# 50. NAMING CONVENTION

Ikuti Laravel conventions.

Contoh:

```text
Student
Exam
ExamSession
ExamAttempt
Question
QuestionOption
Answer
ExamResult
```

Migration:

```text
create_students_table
create_exams_table
create_exam_sessions_table
```

Controller:

```text
ExamController
ExamSessionController
ExamAttemptController
```

Form Request:

```text
StoreExamRequest
UpdateExamRequest
SaveAnswerRequest
SubmitExamRequest
```

Policy:

```text
ExamPolicy
ExamAttemptPolicy
QuestionPolicy
```

---

# 51. API DESIGN

Jika menggunakan API endpoint untuk React, gunakan struktur yang konsisten.

Contoh:

```text
/api/auth/me

/api/exams
/api/exams/{exam}

/api/exam-sessions
/api/exam-sessions/{session}

/api/exam-attempts
/api/exam-attempts/{attempt}

/api/exam-attempts/{attempt}/answers
/api/exam-attempts/{attempt}/submit

/api/monitoring/exam-sessions/{session}
```

Gunakan HTTP status code dengan benar.

Jangan mengembalikan status `200` untuk semua kondisi.

---

# 52. RESOURCE / RESPONSE

Gunakan Laravel API Resources jika API response membutuhkan transformasi.

Contoh:

```text
ExamResource
ExamSessionResource
ExamAttemptResource
QuestionResource
StudentResource
ExamResultResource
```

Jangan mengekspos field database yang tidak perlu kepada client.

---

# 53. BUSINESS LOGIC

Jangan menaruh seluruh business logic dalam Controller.

Controller sebaiknya:

```text
Receive Request
      ↓
Validate
      ↓
Call Service / Domain Logic
      ↓
Return Response
```

Business logic kompleks dapat ditempatkan pada:

```text
Services
Actions
Domain classes
```

Gunakan abstraksi secara pragmatis.

Jangan membuat:

```text
Repository → Service → Manager → Handler → Factory
```

jika sebenarnya hanya membutuhkan satu service sederhana.

---

# 54. DATABASE CONSTRAINT

Gunakan database constraints untuk menjaga integritas.

Minimal:

* foreign keys;
* unique constraints;
* indexes;
* not-null;
* appropriate data types.

Contoh:

```text
student_id + exam_session_id
```

harus memiliki constraint sesuai kebutuhan agar satu siswa tidak memiliki duplicate attempt pada session yang sama.

---

# 55. INDEXING

Tambahkan index pada kolom yang sering digunakan untuk:

* lookup;
* filtering;
* join;
* monitoring;
* reporting.

Terutama:

```text
user_id
student_id
exam_id
exam_session_id
exam_attempt_id
status
started_at
ends_at
created_at
```

Jangan membuat index berlebihan.

---

# 56. TRANSACTIONAL EXAM FLOW

Operasi kritis harus atomic.

Contoh start exam:

```text
Validate Token
      ↓
Validate Session
      ↓
Validate Participant
      ↓
Create/Resume Attempt
      ↓
Set Started At
      ↓
Calculate Ends At
      ↓
Persist
      ↓
Return Exam State
```

Jika salah satu tahap gagal, database harus tetap konsisten.

---

# 57. REALTIME EVENT DESIGN

Gunakan Laravel Events untuk event penting.

Contoh:

```text
ExamStarted
AnswerSaved
ExamSubmitted
ExamAutoSubmitted
ParticipantConnected
ParticipantDisconnected
SuspiciousActivityDetected
```

Broadcast hanya informasi yang diperlukan.

Jangan broadcast jawaban benar atau data sensitif kepada client yang tidak berhak.

---

# 58. OFFLINE / NETWORK INTERRUPTION

Sistem harus tahan terhadap gangguan jaringan LAN sementara.

Jika koneksi siswa terputus:

```text
Client detects disconnect
        ↓
Display connection status
        ↓
Keep local temporary state
        ↓
Reconnect
        ↓
Resume session
        ↓
Sync unsaved answer if valid
```

Server tetap menjadi source of truth.

Client-side temporary storage tidak boleh menggantikan database server.

---

# 59. OBSERVABILITY

Sediakan logging untuk:

* authentication;
* exam lifecycle;
* errors;
* suspicious activity;
* realtime connection;
* import/export;
* backup.

Gunakan Laravel logging.

Jangan menulis password, token sensitif, atau data rahasia ke log.

---

# 60. DOCUMENTATION

Minimal repository harus memiliki:

```text
README.md
prd.md
agent-prompt.md
```

Jika diperlukan tambahkan:

```text
ARCHITECTURE.md
DECISIONS.md
DEPLOYMENT.md
BACKUP.md
TESTING.md
```

Dokumentasi harus ditulis sehingga developer lain dapat melanjutkan project tanpa harus memahami seluruh percakapan sebelumnya.

---

# 61. README.MD

`README.md` wajib menjelaskan secara step-by-step:

## Development

Contoh:

```bash
git clone <repository>
cd <project>

composer install

cp .env.example .env

php artisan key:generate

touch database/database.sqlite

php artisan migrate --seed

npm install

npm run dev
```

Jika menggunakan Reverb, dokumentasikan command yang diperlukan.

Contoh:

```bash
php artisan reverb:start
```

Sesuaikan command dengan implementasi aktual project.

---

# 62. PRODUCTION BUILD

Sebelum deployment:

```bash
composer install --no-dev --optimize-autoloader

php artisan migrate --force

npm install

npm run build

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jalankan konfigurasi production sesuai environment.

Jangan menjalankan development server sebagai production server tanpa pertimbangan.

---

# 63. LOCAL SCHOOL INSTALLATION

Dokumentasikan skenario:

```text
Server PC Sekolah
        │
        ├── Laravel
        ├── PHP
        ├── SQLite
        └── Reverb
              │
              ↓
        Router / Switch
              │
       ┌──────┼──────┐
       ↓      ↓      ↓
     PC 1   PC 2   PC 3
       ...
       ↓
     PC Proktor
```

Operator harus dapat memahami:

1. cara menjalankan server;
2. cara mengecek status server;
3. cara membuka aplikasi;
4. cara melakukan backup;
5. cara restore backup;
6. cara restart aplikasi;
7. cara mengecek koneksi LAN.

---

# 64. DEPLOYMENT TANPA INTERNET

Setelah aplikasi ter-install dan asset telah dibuild, sistem CBT harus dapat berjalan tanpa internet.

Jangan membuat runtime dependency seperti:

```text
Google Fonts
CDN JavaScript
CDN CSS
External API
Google Analytics
Cloud authentication
```

Asset utama harus tersedia secara lokal.

---

# 65. PERFORMA

Target desain:

* puluhan client aktif secara bersamaan;
* auto-save jawaban dari banyak client;
* monitoring realtime;
* SQLite sebagai default database;
* server lokal dengan resource terbatas.

Optimalkan:

* database queries;
* eager loading;
* pagination;
* indexes;
* caching;
* payload size;
* frontend bundle;
* realtime events.

Hindari N+1 query.

Gunakan Laravel tools untuk mendeteksi query yang tidak efisien.

---

# 66. ACCEPTANCE CRITERIA FASE 1

Fase 1 dianggap selesai apabila workflow berikut berhasil:

```text
Admin Login
    ↓
Create Teacher / Student
    ↓
Create Class
    ↓
Create Subject
    ↓
Create Question Bank
    ↓
Create Questions
    ↓
Create Exam
    ↓
Select Questions
    ↓
Create Exam Session
    ↓
Assign Students
    ↓
Generate Exam Token
    ↓
Student Login
    ↓
Enter Exam Token
    ↓
Start Exam
    ↓
Answer Multiple Choice Questions
    ↓
Auto-save
    ↓
Refresh / Reconnect
    ↓
Resume Exam
    ↓
Submit Exam
    ↓
Server Auto-Grades
    ↓
Exam Result
    ↓
Admin/Teacher Views Recap
```

Semua workflow utama harus berhasil tanpa membutuhkan internet.

---

# 67. OUTPUT YANG DIHARAPKAN DARI FASE 1

Pada akhir Fase 1 harus tersedia:

### Application

* Laravel 13 application;
* React frontend;
* Vite;
* Tailwind CSS;
* Laravel Sanctum;
* Laravel Reverb;
* SQLite;
* migrations;
* seeders;
* factories;
* authentication;
* role/authorization.

### Modules

* User Management;
* Student Management;
* Teacher Management;
* Class Management;
* Subject Management;
* Question Bank;
* Exam Management;
* Exam Session;
* Exam Token;
* Student Exam;
* Auto-save;
* Resume Session;
* Auto-grading;
* Result Recap.

### Testing

Minimal automated tests untuk:

* authentication;
* authorization;
* token;
* timer;
* auto-save;
* grading;
* submission.

### Documentation

Minimal:

```text
README.md
prd.md
agent-prompt.md
DECISIONS.md
```

---

# 68. DEFINITION OF DONE — FASE 1

Fase 1 **tidak boleh dianggap selesai hanya karena kode berhasil dibuat**.

Fase 1 selesai apabila:

* migration berhasil;
* seed berhasil;
* application dapat dijalankan;
* login bekerja;
* role bekerja;
* admin dapat membuat ujian;
* siswa dapat login;
* siswa dapat mengerjakan ujian;
* jawaban tersimpan;
* timer divalidasi server;
* refresh dapat resume;
* submit bekerja;
* auto-grading bekerja;
* hasil dapat dilihat;
* automated test kritis lulus;
* tidak ada error blocking;
* aplikasi dapat dijalankan melalui LAN;
* dokumentasi instalasi tersedia.

---

# 69. ATURAN IMPLEMENTASI UNTUK AI CODING AGENT

Sebelum coding:

1. Baca `prd.md` secara menyeluruh.
2. Identifikasi semua aktor.
3. Identifikasi seluruh modul.
4. Identifikasi workflow.
5. Identifikasi entity/database.
6. Identifikasi requirement security.
7. Identifikasi requirement non-functional.
8. Buat implementation plan.
9. Periksa apakah terdapat konflik dengan prompt ini.

Jangan langsung membuat seluruh aplikasi tanpa memahami PRD.

---

# 70. JANGAN OVER-ENGINEER

Project ini ditujukan untuk satu sekolah dengan tim developer kecil.

Prioritaskan:

```text
Simple
Reliable
Maintainable
Testable
Secure
Performant
Easy to Deploy
```

dibanding:

```text
Complex
Enterprise-looking
Over-abstracted
Microservices
Infrastructure-heavy
```

Jangan menambahkan teknologi hanya karena populer.

Setiap dependency harus memiliki alasan yang jelas.

---

# 71. DEPENDENCY POLICY

Sebelum menambahkan package baru, evaluasi:

1. Apakah Laravel sudah menyediakan fitur tersebut?
2. Apakah package benar-benar diperlukan?
3. Apakah package aktif dipelihara?
4. Apakah package kompatibel dengan Laravel 13?
5. Apakah package kompatibel dengan PHP 8.3+?
6. Apakah package menambah kompleksitas deployment sekolah?
7. Apakah package memiliki risiko security?

Jika tidak diperlukan, jangan tambahkan.

---

# 72. DECISION LOG

Setiap keputusan teknis yang tidak dijelaskan secara eksplisit dalam PRD harus dicatat pada:

```text
DECISIONS.md
```

Format:

```markdown
# Decision: <Title>

## Context

<Why the decision was needed>

## Decision

<Decision taken>

## Alternatives

<Alternative options>

## Reason

<Why this option was selected>
```

---

# 73. ASUMSI

Jika terdapat detail kecil yang tidak dijelaskan dalam PRD:

* buat asumsi yang wajar;
* implementasikan;
* dokumentasikan pada `DECISIONS.md`.

Contoh:

* format token;
* struktur folder gambar;
* default pagination;
* default timeout;
* UI behavior;
* default sorting.

**Jangan berhenti hanya untuk bertanya mengenai keputusan kecil.**

Namun, jika keputusan tersebut dapat mengubah arsitektur, keamanan, database fundamental, atau workflow bisnis utama, hentikan implementasi bagian tersebut dan laporkan kepada user.

---

# 74. ATURAN FASE

Setelah menyelesaikan satu fase:

**BERHENTI.**

Jangan otomatis melanjutkan fase berikutnya.

Laporan harus berisi:

## 1. Completed

Apa saja yang sudah dibangun.

## 2. Files Created / Modified

Daftar file penting.

## 3. Database

Migration/model apa yang dibuat.

## 4. Testing

Test apa yang dibuat dan hasilnya.

## 5. Run Instructions

Cara menjalankan aplikasi.

Minimal jelaskan command aktual yang digunakan, misalnya:

```bash
composer install
php artisan migrate --seed
npm install
npm run dev
php artisan reverb:start
```

Sesuaikan dengan implementasi aktual.

## 6. Decisions

Keputusan teknis yang diambil.

## 7. Known Issues

Jika ada.

## 8. Next Phase

Apa yang akan dikerjakan pada fase berikutnya.

Kemudian **STOP dan tunggu konfirmasi user**.

---

# 75. ATURAN KHUSUS UNTUK CODING AGENT

Jangan:

* mengganti Laravel 13 dengan framework lain;
* menggunakan Express sebagai backend;
* membuat backend Node.js;
* membuat microservices;
* menjadikan PostgreSQL sebagai dependency wajib;
* menjadikan Redis sebagai dependency wajib;
* menjadikan internet sebagai dependency runtime;
* menghitung nilai ujian di client;
* mempercayai timer client;
* mempercayai role dari client;
* menyimpan jawaban hanya di browser;
* menggunakan raw SQL yang SQLite-specific;
* melewati validation;
* melewati authorization;
* melanjutkan fase tanpa konfirmasi.

---

# 76. PRIORITAS TEKNIS

Jika harus memilih antara dua pendekatan, gunakan urutan prioritas:

```text
1. Security
2. Data Integrity
3. Exam Reliability
4. Correctness
5. Maintainability
6. Performance
7. Developer Convenience
8. Visual Complexity
```

Dalam sistem CBT:

**kehilangan jawaban siswa atau kesalahan timer lebih serius daripada masalah kosmetik UI.**

---

# 77. PRINSIP AKHIR

Bangun aplikasi ini dengan prinsip:

> **Laravel 13 sebagai core application framework, SQLite sebagai database default deployment sekolah, React sebagai interactive frontend, Laravel Reverb sebagai realtime layer, dan LAN sebagai lingkungan operasional utama.**

Aplikasi harus:

* sederhana untuk dipasang;
* stabil untuk ujian;
* aman;
* mudah dibackup;
* mudah dipelihara;
* dapat berkembang ke PostgreSQL;
* tidak bergantung pada internet;
* mampu melayani banyak siswa secara bersamaan dalam jaringan lokal.

---

# 78. START COMMAND

Setelah membaca seluruh dokumen ini dan `prd.md`, jangan langsung mengimplementasikan semua fase.

Mulai dengan:

```text
PHASE 0 — ANALYSIS & ARCHITECTURE REVIEW
```

Lakukan:

1. Baca `prd.md`.
2. Identifikasi seluruh requirement.
3. Identifikasi seluruh entity.
4. Identifikasi seluruh role.
5. Identifikasi workflow.
6. Identifikasi database relationship.
7. Identifikasi security requirement.
8. Identifikasi realtime requirement.
9. Identifikasi risiko teknis.
10. Buat architecture proposal.
11. Buat initial folder structure.
12. Buat initial database model proposal.
13. Buat `DECISIONS.md`.

Setelah Phase 0 selesai, **STOP** dan laporkan hasil analisis.

Jangan mulai Fase 1 sampai user memberikan konfirmasi.

---

# END OF AGENT PROMPT

