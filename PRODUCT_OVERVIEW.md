# PRODUCT OVERVIEW
# Sistem Terpadu RPP, LMS & CBT — SMA Kartika III-1 Banyubiru

---

| Metadata Dokumen | Rincian |
| :--- | :--- |
| **Nama Produk** | Sistem Terpadu RPP Kurikulum Merdeka, LMS & CBT Kartika |
| **Institusi Pemilik** | SMA Kartika III-1 Banyubiru (Yayasan Kartika Jaya Cabang III Diponegoro) |
| **Lokasi** | Kec. Banyubiru, Kab. Semarang, Jawa Tengah |
| **Versi Produk** | 1.0 (Produksi / Fase 3 Selesai) |
| **Target Pengguna** | ± 500 Siswa, Guru Mapel, Proktor, Wali Kelas, dan Manajemen Sekolah |
| **Akses Produksi** | Portal: [cbtkartika.koomit.com](https://cbtkartika.koomit.com) \| API/Mirror: [ipcbtkartika.koomit.com](https://ipcbtkartika.koomit.com) |
| **Format Dokumen** | Markdown (.md) |

---

## 1. Ringkasan Eksekutif (Executive Summary)

**Sistem Terpadu RPP, LMS & CBT SMA Kartika III-1 Banyubiru** adalah ekosistem digital pendidikan satu pintu (*unified single-platform*) yang menyelaraskan tiga pilar utama siklus pembelajaran di sekolah:
1. **Perencanaan Kurikulum (RPP / Modul Ajar Kurikulum Merdeka)**: Perumusan Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP) sebagai hulu data.
2. **Pelaksanaan Pembelajaran Daring (LMS - Learning Management System)**: Distribusi bahan ajar digital, penugasan mandiri/terstruktur, serta forum diskusi kelas interaktif.
3. **Evaluasi Berintegritas Tinggi (CBT - Computer-Based Testing)**: Pelaksanaan asesmen digital terjadwal berbasis multi-sesi dengan pengawasan waktu-nyata (*realtime proctoring*), mode peramban terkunci (*Kiosk/Lockdown Mode*), auto-grading, rubrik koreksi esai, serta analisis butir soal psikometri.

Platform ini dirancang khusus untuk memecahkan dilema operasional sekolah: menyelenggarakan evaluasi standar nasional (seperti ANBK/AKM, Sumatif Akhir Semester, dan Penilaian Harian) bagi **±500 siswa** di tengah keterbatasan jumlah komputer laboratorium dan tantangan kestabilan jaringan internet, melalui arsitektur hibrida **Local-First LAN (Server Sekolah)** yang tersinkronisasi dengan **Cloud Server (Hostinger)**.

---

## 2. Latar Belakang & Pernyataan Masalah

### 2.1 Masalah dalam Sistem Konvensional & Terfragmentasi
* **Beban Ujian Berbasis Kertas (*Paper-Based Test*)**: Biaya penggandaan berkas ujian yang tinggi, risiko kebocoran lembar soal dan kunci jawaban, serta proses koreksi manual yang memakan waktu berhari-hari hingga berminggu-minggu.
* **Silo Data Akademik**: Modul ajar (RPP) guru tersimpan terpisah sebagai dokumen fisik/Word, tidak terhubung dengan materi yang dipelajari siswa di LMS ataupun butir soal yang diujikan dalam CBT.
* **Rasio Fasilitas Laboratorium vs Jumlah Siswa**: Sekolah memiliki kapasitas laboratorium 40–60 unit komputer, sementara jumlah siswa mencapai ±500 orang. Diperlukan manajemen sesi dan gelombang ujian yang presisi, otomatis, dan tahan terhadap kendala listrik atau perangkat mati mendadak.
* **Ketergantungan Internet yang Rawan**: Akses internet di lingkungan sekolah terkadang mengalami fluktuasi bandwidth. Jika mengandalkan sistem cloud murni, ujian dapat terhenti di tengah jalan dan memicu kepanikan siswa.

### 2.2 Solusi yang Dihadirkan
Sistem ini memadukan **infrastruktur intranet lokal berkecepatan tinggi tanpa kuota internet** saat ujian berlangsung di laboratorium sekolah, dengan **sinkronisasi cloud mirror** untuk kebutuhan belajar mandiri siswa di rumah, koreksi guru dari luar sekolah, serta rekapitulasi data pimpinan sekolah secara berkala.

---

## 3. Nilai Strategis Produk (Value Proposition)

```
       +-------------------------------------------------------------+
       |             HULU: RPP & MODUL AJAR MERDEKA                  |
       |  Perumusan CP, TP, Alokasi JP, Asesmen & Bahan Pembelajaran |
       +------------------------------+------------------------------+
                                      |
                      +---------------+---------------+
                      |                               |
                      v                               v
       +------------------------------+ +------------------------------+
       |     LMS (RUANG BELAJAR)      | |   CBT (EVALUASI DIGITAL)     |
       | - Materi Belajar Multimedia  | | - Bank Soal Multi-Tipe       |
       | - Penugasan Siswa & Rubrik   | | - Ujian Multi-Sesi & Token   |
       | - Forum Diskusi Interaktif   | | - Anti-Curang & Heartbeat    |
       | - Pelacakan Kemajuan Siswa   | | - Koreksi Esai & Analisis    |
       +------------------------------+ +------------------------------+
                      |                               |
                      +---------------+---------------+
                                      |
                                      v
       +-------------------------------------------------------------+
       |             HILIR: REKAPITULASI & LAPORAN AKADEMIK          |
       |  Hasil Ujian, Daya Beda Soal, Nilai Rapor, Integrasi Dapodik|
       +-------------------------------------------------------------+
```

1. **Pipa Terpadu (Unified Pipeline)**: Dokumen RPP bukan sekadar administrasi formalitas, melainkan menjadi basis pembuatan materi LMS dan pemetaan kisi-kisi bank soal CBT.
2. **Kemandirian Operasional Sekolah**: Tidak bergantung pada lisensi pihak ketiga atau server kementerian; sekolah memiliki kontrol penuh atas database, privasi soal, dan jadwal pelaksanaan.
3. **Integritas Ujian Tanpa Kompromi**: Fitur anti-kecurangan komprehensif (pengacakan urutan soal, pengacakan opsi jawaban, token dinamis berkala, server-side timer, deteksi perpindahan tab, blokir tombol pintas/klik-kanan, dan mode kiosk).
4. **Resistensi Terhadap Gangguan (*Zero Data Loss*)**: Auto-save jawaban otomatis dengan mekanisme *debounce*, toleransi mati listrik mendadak (*seamless session resume*), dan auto-submit saat durasi server berakhir.
5. **Analisis Mutu Pendidikan Otomatis**: Dilengkapi modul psikometri butir soal (indeks kesukaran $P$ dan daya pembeda $D$) untuk membantu guru meningkatkan kualitas evaluasi secara objektif.

---

## 4. Target Pengguna & Peran (User Roles)

Sistem menerapkan **Role-Based Access Control (RBAC)** berbasis 7 peran pengguna terpisah:

| Peran | Deskripsi Tugas | Fitur & Hak Akses Utama |
| :--- | :--- | :--- |
| **Super Admin** | Administrator Utama TI Sekolah | Akses tak terbatas, manajemen pengguna, konfigurasi server, backup database SQLite/MySQL, audit log, dan integrasi Dapodik. |
| **Admin / Operator** | Staf Kurikulum & Tata Usaha | Penjadwalan ujian, alokasi kelas/sesi, cetak kartu peserta & token, pemetaan ruang lab, serta rekapitulasi nilai rapor. |
| **Guru (Pendidik)** | Guru Pengampu Mata Pelajaran | Penyusunan Modul Ajar (RPP), bank soal multi-tipe, publikasi materi LMS & penugasan, koreksi manual esai dengan rubrik, serta analisis butir soal. |
| **Proktor (Pengawas)** | Guru Piket / Laboran saat Ujian | Dashboard monitoring real-time laboratorium, pelacakan detak koneksi (heartbeat), reset sesi siswa berkendala, serta pencatatan log insiden. |
| **Siswa (Peserta)** | Peserta Didik SMA Kartika III-1 | Mengakses materi LMS, menyelesaikan tugas daring, mengikuti ujian digital dengan validasi token, navigasi soal adaptif, dan melihat hasil capaian. |
| **Wali Kelas** | Guru Pembina Kelas | Memantau kehadiran ujian siswa perwalian, memonitor perkembangan belajar LMS, dan mengunduh rekap nilai berkala kelas terkait. |
| **Kepala Sekolah** | Pimpinan Institusi Pendidikan | Dashboard eksekutif *read-only*: statistik kelulusan KKM, sebaran nilai (grade band A–E), tren pencapaian akademik, dan evaluasi guru. |

---

## 5. Fitur Utama Produk (Core Features & Modules)

### 5.1 Modul Modul Ajar (RPP Kurikulum Merdeka)
* **Penyusunan Terstruktur**: Input Capaian Pembelajaran (CP), Tujuan Pembelajaran (TP), Profil Pelajar Pancasila, alokasi jam pelajaran (JP), dan target asesmen.
* **Integrasi Otomatis**: Menghubungkan RPP ke materi mata pelajaran di LMS dan paket ujian di CBT dalam satu klik.
* **Status Integrasi Real-Time**: Indikator kesiapan dokumen modul ajar terhadap ketersediaan materi dan butir asesmen.

### 5.2 Modul Learning Management System (LMS)
* **Manajemen Kelas & Kursus (*Course Management*)**: Pengorganisasian topik materi per minggu/per bab berdasarkan mata pelajaran dan rombongan belajar.
* **Distribusi Materi Multimedia**: Unggah modul dokumen PDF, rangkuman teks kaya (Rich Text), tautan video pembelajaran YouTube/Google Drive, dan presentasi.
* **Pelacakan Kemajuan Belajar (*Progress Tracking*)**: Siswa dapat menandai penyelesaian materi; guru dapat melihat persentase penyelesaian modul setiap siswa.
* **Penugasan Digital (*Assignments*)**: Pengumpulan tugas berbasis dokumen/berkas dengan tenggat waktu (*deadline*), sistem penguncian keterlambatan, dan formulir penilaian guru.
* **Forum Diskusi Interaktif**: Ruang tanya jawab berutas (*threaded discussion*) antara guru dan siswa per materi kursus.

### 5.3 Modul Mesin Ujian Digital (CBT Engine)
* **Dukungan 6 Tipe Soal**:
  1. *Pilihan Ganda Biasa* (Satu jawaban benar).
  2. *Pilihan Ganda Kompleks* (Multi-jawaban benar, format standar ANBK/AKM).
  3. *Benar / Salah* atau *Ya / Tidak*.
  4. *Menjodohkan (Matching Pair)* antar-pernyataan.
  5. *Isian Singkat (Short Answer)* dengan verifikasi otomatis berbasis kecocokan teks/kunci kata.
  6. *Esai / Uraian Panjang* dengan editor teks komprehensif.
* **Editor Multimedia Soal**: Mendukung penyisipan gambar, tabel, simbol matematika (LaTeX/Equation), dan audio listening bahasa asing.
* **Pengacakan Ganda**: Pengacakan urutan nomor soal dan pengacakan opsi pilihan per individu siswa untuk mencegah contek-mencontek.
* **Manajemen Multi-Sesi & Gelombang**: Menampung 500 siswa dengan membaginya ke dalam 3–6 sesi laboratorium secara teratur.
* **Token Dinamis**: Sistem token berbasis sesi dengan masa kedaluwarsa otomatis untuk mencegah siswa memulai ujian di luar jadwalnya.
* **Cockpit Pengerjaan Siswa**:
  - *Autosave Background*: Setiap perubahan jawaban langsung tersimpan ke server dalam hitungan detik.
  - *Server-Authoritative Countdown Timer*: Waktu dihitung oleh jam server; manipulasi jam komputer client tidak memengaruhi sisa durasi.
  - *Palette Navigasi Soal*: Penanda nomor soal terjawab, belum terjawab, dan tanda 'Ragu-ragu'.
  - *Aksesibilitas Font*: Pilihan ukuran font soal (A-, A, A+) untuk kenyamanan visual di berbagai monitor lab.
  - *Auto-Submit*: Penutupan otomatis dan komputasi skor saat waktu habis.

### 5.4 Modul Keamanan Ujian (Kiosk & Anti-Cheating)
* **Mode Kiosk / Lockdown Browser**: Tampilan layar penuh (*enforced fullscreen*) yang mengunci antarmuka browser.
* **Pencegahan Aksi Terlarang**: Menonaktifkan klik kanan, seleksi teks, *copy-paste*, pintasan keyboard (F5, F11, F12 Developer Tools, Alt+Tab, Ctrl+C, Ctrl+V, Ctrl+R, PrintScreen).
* **Deteksi Pelanggaran & Pindah Tab**: Sistem mendeteksi perpindahan fokus jendela/tab dan mencatat riwayat pelanggaran ke log aktivitas proktor secara otomatis.
* **Pemberian Sanksi / Peringatan Proktor**: Proktor dapat mereset sesi, mengeluarkan (*kick*), atau memberi peringatan langsung pada siswa yang melanggar tata tertib.

### 5.5 Modul Monitoring Proktor Waktu-Nyata
* **Live Status Dashboard**: Menampilkan grid status seluruh peserta per ruang lab (Belum Mulai, Sedang Mengerjakan, Terputus/Offline, Selesai).
* **Detak Koneksi (*Heartbeat Monitor*)**: Mendeteksi sinyal aktif client siswa setiap interval waktu tertentu untuk memitigasi kendala putus kabel LAN atau PC hang.
* **One-Click Session Unlock**: Kemudahan bagi proktor untuk membuka kunci sesi siswa yang terpaksa berpindah PC karena kerusakan teknis (*resume ujian tanpa kehilangan jawaban sebelumnya*).

### 5.6 Modul Penilaian, Koreksi Esai & Analisis Butir Soal
* **Auto-Grading Instan**: Seluruh soal objektif langsung dinilai server secara otomatis saat siswa menekan tombol selesai.
* **Antrean Koreksi Esai Terpadu**: Antarmuka penilaian khusus guru untuk memeriksa jawaban uraian dengan panduan rubrik skor maksimal.
* **Analisis Psikometri Butir Soal**:
  - *Indeks Tingkat Kesukaran ($P$)*: Klasifikasi soal Mudah ($P > 0.70$), Sedang ($0.30 \le P \le 0.70$), dan Sukar ($P < 0.30$).
  - *Indeks Daya Pembeda ($D$)*: Mengukur kemampuan butir soal membedakan kelompok siswa pandai (atas 27%) dan kelompok siswa kurang (bawah 27%).
  - *Distribusi Pengecoh (Distractor Analysis)*: Efektivitas pilihan salah pada soal pilihan ganda.
* **Ekspor & Rekapitulasi Nilai**: Unduh laporan rekap nilai per kelas, per mapel, atau per siswa dalam format CSV/Excel siap impor ke buku nilai rapor.

### 5.7 Modul Integrasi & Administrasi Dapodik
* **Impor/Ekspor Data Master Massal**: Kemudahan memasukkan ratusan akun siswa, guru, rombel kelas, dan bank soal via template CSV.
* **Format Terstandar Dapodik**: Ekspor dan impor data siswa yang selaras dengan kolom Data Pokok Pendidikan (NISN, Nama, Rombel, Jenis Kelamin, Tanggal Lahir).
* **Pencadangan Basis Data Otomatis**: Perintah terjadwal (`scheduler`) untuk backup berkala database pada pukul 02:00 dini hari dan pembersihan sesi usang pada pukul 03:00.

---

## 6. Desain Visual & Identitas Institusi

Antarmuka webapp dirancang menggunakan identitas resmi **SMA Kartika III-1 Banyubiru (Yayasan Kartika Jaya Cabang III Diponegoro)**:
* **Kartika Forest Green (`#061d13` / `#0b3120`)**: Warna hijau rimba yang melambangkan wibawa, keteguhan, kedisiplinan, dan patriotisme.
* **Kartika Heritage Gold (`#d97706` / `#f59e0b`)**: Aksen emas yang melambangkan keunggulan prestasi akademik dan budi pekerti luhur.
* **Modern Slate & White Neutral**: Memberikan tingkat keterbacaan (*readability*) tinggi, bebas distraksi visual (*zero distraction*), serta memenuhi standar rasio kontras aksesibilitas WCAG AA.
* **Lambang Resmi Sekolah**: Menggunakan logo resmi SMA Kartika III-1 Banyubiru beresolusi tinggi pada seluruh portal, kartu ujian, dan laporan cetak.

---

## 7. Arsitektur Teknis & Topologi Sistem

```
                                      [ INTERNET PUBLIK ]
                                               |
                   +---------------------------+---------------------------+
                   |                                                       |
                   v                                                       v
       +-----------------------+                               +-----------------------+
       |   HOSTINGER CLOUD     |                               |   SISWA / GURU HOME   |
       |  cbtkartika.koomit.com|                               | (Akses Materi & Nilai)|
       +-----------------------+                               +-----------------------+
                   ^
                   |  (Sinkronisasi Data Cadangan / Mirroring)
                   v
   =========================================================================================
   JARINGAN INTRANET SEKOLAH (OFFLINE LAN / LABORATORIUM)
   =========================================================================================
                   |
       +-----------+-----------+
       |   SERVER LAB LOKAL    | <--- Menjalankan Laravel 13, SQLite / MySQL,
       |  (192.168.x.x / Host) |      Task Scheduler & Heartbeat Service
       +-----------+-----------+
                   |
         [ GIGABIT SWITCH LAN ]
                   |
     +-------------+-------------+-------------+-------------+
     |             |             |             |             |
     v             v             v             v             v
[PC Lab 01]   [PC Lab 02]   [PC Lab 03]   ... [PC Lab 40]   [Laptop Proktor]
(Client CBT)  (Client CBT)  (Client CBT)      (Client CBT)  (Monitoring Dashboard)
```

### 7.1 Spesifikasi Tumpukan Teknologi (Tech Stack)
* **Backend Framework**: PHP 8.4/8.5, Laravel 13 Framework (Modular Monolith architecture).
* **Admin & Teacher Engine**: Filament PHP v3 (Dasbor administratif, manajemen sumber daya, dan tabel data performa tinggi).
* **Client Frontend Ujian**:
  - *Student SPA & Portal*: React, Vite, Tailwind CSS v4, Blade templates teroptimasi.
  - *Rendering Akseleratif*: Server-driven UI dengan zero payload bloat untuk monitor lab spesifikasi standar.
* **Komunikasi Waktu-Nyata**: Laravel Reverb & Laravel Echo (WebSockets untuk pemantauan proktor, pelacakan status, dan reset ujian seketika).
* **Autentikasi & Keamanan**: Laravel Sanctum (session-based cookies untuk portal web, token-based untuk RESTful API), password hashing Bcrypt/Argon2.
* **Basis Data & Cache**:
  - *Dev / Lab Server*: SQLite hot backup & ACID transactions, kompatibel dengan MySQL 8.0+ / PostgreSQL untuk skala cloud.
  - *Cache Layer*: Laravel Database / File Cache untuk agregasi dashboard dan daftar soal terindeks.
* **Automated Task Scheduler**:
  - Pembersihan percobaan kedaluwarsa (`everyMinute`).
  - Pembersihan attempt terbengkalai (`everyFiveMinutes`).
  - Backup harian basis data (`dailyAt 02:00`).
  - Pembersihan sesi dan housekeeping cache (`dailyAt 03:00`).

---

## 8. Alur Kerja Operasional (Operational User Flows)

### 8.1 Alur Guru: Dari Modul Ajar ke Ujian
1. Guru masuk ke portal menggunakan email institusi.
2. Membuka menu **Modul Ajar (RPP)** untuk merumuskan CP/TP dan mengunggah silabus materi.
3. Menghubungkan RPP ke **LMS Courses** untuk mendistribusikan bahan tayang, video, dan tugas mingguan.
4. Menyusun butir pertanyaan di **Bank Soal** (memilih tipe soal, menyisipkan gambar/LaTeX, menetapkan kunci dan bobot nilai).
5. Merakit **Paket Ujian** (memilih komposisi soal, menentukan acak soal/opsi, dan durasi pengerjaan).

### 8.2 Alur Panitia / Admin: Penjadwalan Sesi Laboratorium
1. Admin membuat jadwal asesmen (misal: *Penilaian Akhir Semester Ganjil*).
2. Membagi ±500 siswa ke dalam beberapa rombel sesi (misal: Sesi 1 pukul 07.30–09.30, Sesi 2 pukul 10.00–12.00, Sesi 3 pukul 12.30–14.30).
3. Mengenerasi token ujian unik 6 karakter per sesi/ruang laboratorium.
4. Mencetak kartu login peserta dan lembar daftar token untuk proktor ruang.

### 8.3 Alur Siswa: Pelaksanaan Ujian Bebas Kendala
1. Siswa duduk di meja komputer laboratorium dan membuka peramban yang terkunci dalam mode kiosk.
2. Login menggunakan **NISN** dan kata sandi yang tertera pada kartu peserta.
3. Memasukkan **Token Ujian** yang dirilis oleh proktor di papan pengumuman ruang.
4. Sistem memvalidasi identitas peserta, menampilkan tata tertib, dan memulai hitung mundur timer server.
5. Siswa menjawab soal; setiap pilihan jawaban langsung tersimpan (*autosave indicator* aktif).
6. Jika terjadi listrik mati atau PC hang, siswa cukup dipindahkan ke PC pengganti atau menyalakan ulang komputer: seluruh jawaban yang telah dipilih tetap utuh dan sisa waktu tidak berkurang secara tidak adil.
7. Siswa menekan tombol kumpul jawaban (atau sistem melakukan auto-submit saat timer habis).

### 8.4 Alur Pasca-Ujian: Penilaian & Analisis
1. Sistem segera menghitung skor seluruh soal pilihan ganda, benar/salah, dan isian singkat.
2. Guru pengampu masuk ke menu **Koreksi Esai (Grading)** untuk memeriksa jawaban uraian dengan pedoman rubrik.
3. Guru membuka tab **Analisis Butir Soal** untuk melihat kualitas soal (soal mana yang terlalu mudah, terlalu sulit, atau memiliki kunci jawaban yang membingungkan siswa).
4. Nilai akhir diunduh panitia dalam format berkas Dapodik/rapor untuk dilaporkan kepada Kepala Sekolah dan Orang Tua Siswa.

---

## 9. Parameter Kualitas & Kesiapan Produksi (Quality Assurance)

Aplikasi telah melalui pengujian menyeluruh pada seluruh lapisan fungsionalitas dan keamanan:

* **Suite Pengujian Otomatis**:
  - **117 PHPUnit Tests**: Meliputi pengujian otentikasi peran, validasi token, mutasi jawaban, auto-grading, kalkulasi rumus psikometri, serta proteksi sesi ujian.
  - **Playwright End-to-End (E2E) Suites**: Pengujian simulasi perilaku siswa di peramban riil (login form, validasi token ujian, pengerjaan soal navigasi, responsifitas layar lab, dan submission).
* **Keandalan Jaringan Rendah**: Client dioptimalkan agar tetap lancar pada bandwidth terbatas di laboratorium komputer sekolah tanpa ketergantungan internet eksternal saat ujian berlangsung.
* **Kepatuhan Regulasi**: Kompatibel dengan struktur kurikulum merdeka nasional dan format pendataan Dapodik Kementerian Pendidikan.

---

## 10. Roadmap Pengembangan Mendatang (Future Roadmap)

Meskipun sistem telah beroperasi penuh pada Fase 3, rencana peningkatan di masa mendatang meliputi:
* **Aplikasi Client Khusus (Native Safe Exam Browser)**: Paket installer desktop Windows (.exe) ringan yang menutup akses ke task manager dan tombol OS secara total.
* **Proctoring AI Opsional**: Deteksi visual kecurangan via webcam ruang secara otomatis bagi sesi ujian mandiri dari rumah.
* **Live API Sync Dapodik**: Sinkronisasi langsung dua arah via web-service resmi Kementerian Pendidikan saat jaringan cloud aktif.

---

## 11. Kesimpulan

**Sistem Terpadu RPP, LMS & CBT SMA Kartika III-1 Banyubiru** adalah representasi modernisasi tata kelola pembelajaran dan evaluasi sekolah berbasis teknologi mandiri. Dengan memadukan efisiensi digital, kepatuhan kurikulum merdeka, keamanan ujian tingkat tinggi, serta fleksibilitas operasional offline-online, platform ini menjadi aset strategis institusi dalam mengantarkan peserta didik meraih prestasi terbaik berlandaskan nilai-nilai integritas dan disiplin tinggi.

---
*Dokumen ini diterbitkan sebagai dokumentasi resmi arsitektur dan kapabilitas produk SMA Kartika III-1 Banyubiru.*
