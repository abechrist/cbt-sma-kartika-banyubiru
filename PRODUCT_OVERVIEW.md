# PRODUCT OVERVIEW
# Sistem Terpadu RPP, LMS & CBT — SMA Kartika III-1 Banyubiru

---

| Metadata Dokumen | Rincian |
| :--- | :--- |
| **Nama Produk** | Sistem Terpadu RPP Kurikulum Merdeka, LMS & CBT Kartika |
| **Institusi Pemilik** | SMA Kartika III-1 Banyubiru (Yayasan Kartika Jaya Cabang III Diponegoro) |
| **Lokasi** | Kec. Banyubiru, Kab. Semarang, Jawa Tengah |
| **Versi Produk** | 1.0 (Produksi Cloud) |
| **Target Pengguna** | ± 500 Siswa, Guru Mapel, Koordinator Kurikulum, Proktor, Wali Kelas, dan Manajemen Sekolah |
| **Akses Produksi** | Portal Utama: [cbtkartika.koomit.com](https://cbtkartika.koomit.com) \| API/Backend: [ipcbtkartika.koomit.com](https://ipcbtkartika.koomit.com) |
| **Format Dokumen** | Markdown (.md) |

---

## 1. Ringkasan Eksekutif (Executive Summary)

**Sistem Terpadu RPP, LMS & CBT SMA Kartika III-1 Banyubiru** adalah ekosistem digital pendidikan satu pintu (*unified web platform*) berbasis cloud murni (*Pure Cloud Web-Based*) yang menyelaraskan tiga pilar utama siklus pembelajaran di sekolah:
1. **Perencanaan Kurikulum (RPP / Modul Ajar Kurikulum Merdeka)**: Perumusan Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP) sebagai hulu data perencanaan pembelajaran yang dapat disupervisi langsung oleh Koordinator Kurikulum.
2. **Pelaksanaan Pembelajaran Daring (LMS - Learning Management System)**: Distribusi bahan ajar digital, penugasan mandiri/terstruktur, serta forum diskusi interaktif per mata pelajaran dan rombongan belajar.
3. **Evaluasi Berintegritas Tinggi (CBT - Computer-Based Testing)**: Pelaksanaan asesmen digital terjadwal berbasis multi-sesi dengan pengawasan waktu-nyata (*real-time proctoring*), proteksi peramban (*Enforced Fullscreen & Focus Blur Detection*), *auto-grading*, rubrik koreksi esai terpadu, serta analisis butir soal psikometri.

Platform ini dihosting secara terpusat pada infrastruktur **Cloud Server (Hostinger)** dengan satu basis data tunggal (*Single Source of Truth*), memungkinkan siswa mengakses ujian dan materi pembelajaran langsung melalui perangkat laptop, komputer, maupun tablet/ponsel tanpa membutuhkan server lokal fisik ataupun instalasi jaringan intranet khusus di sekolah.

---

## 2. Latar Belakang & Pernyataan Masalah

### 2.1 Masalah dalam Sistem Konvensional & Terfragmentasi
* **Beban Ujian Berbasis Kertas (*Paper-Based Test*)**: Biaya penggandaan berkas ujian yang tinggi, risiko kebocoran lembar soal dan kunci jawaban, serta proses koreksi manual yang memakan waktu berhari-hari hingga berminggu-minggu.
* **Silo Data Akademik**: Modul ajar (RPP) guru tersimpan terpisah sebagai dokumen fisik/Word, tidak terhubung dengan materi yang dipelajari siswa di LMS ataupun butir soal yang diujikan dalam CBT.
* **Kompleksitas Perawatan Server Fisik di Sekolah**: Pengadaan dan pemeliharaan server lokal fisik di sekolah sering kali terkendala ketiadaan tenaga teknisi khusus, risiko kerusakan perangkat keras (*hardware failure*), serta kerumitan sinkronisasi dua arah offline-online.

### 2.2 Solusi yang Dihadirkan
Sistem ini menggunakan pendekatan **Cloud Web-Based Terpusat**:
* **Akses Tanpa Hambatan Lokasi**: Ujian, kelas daring, dan modul ajar dapat diakses secara fleksibel dari ruang kelas sekolah maupun dari rumah menggunakan koneksi internet standar.
* **Satu Database Terpusat (Zero Data Loss & Zero Split-Brain)**: Seluruh pencatatan jawaban ujian, progres materi siswa, dan penilaian guru tersimpan langsung ke basis data cloud MySQL InnoDB dengan *row-level locking* dan transaksi ACID.

---

## 3. Nilai Strategis Produk (Value Proposition)

```
       +-------------------------------------------------------------+
       |             HULU: RPP & MODUL AJAR MERDEKA                  |
       |  Perumusan CP, TP, Alokasi JP, Asesmen & Supervisi Kurikulum|
       +------------------------------+------------------------------+
                                      |
                      +---------------+---------------+
                      |                               |
                      v                               v
       +------------------------------+ +------------------------------+
       |     LMS (RUANG BELAJAR)      | |   CBT (EVALUASI DIGITAL)     |
       | - Materi Belajar Multimedia  | | - Bank Soal Multi-Tipe       |
       | - Penugasan Siswa & Rubrik   | | - Ujian Terjadwal & Token    |
       | - Forum Diskusi Interaktif   | | - Layar Penuh & Deteksi Tab  |
       | - Pelacakan Kemajuan Siswa   | | - Koreksi Esai & Analisis    |
       +------------------------------+ +------------------------------+
                      |                               |
                      +---------------+---------------+
                                      |
                                      v
       +-------------------------------------------------------------+
       |             HILIR: REKAPITULASI & LAPORAN AKADEMIK          |
       |  Hasil Ujian, Daya Beda Soal, Nilai Rapor, Format Dapodik   |
       +-------------------------------------------------------------+
```

1. **Pipa Terpadu (Unified Pipeline)**: Dokumen RPP terhubung langsung dengan materi LMS dan pemetaan kisi-kisi bank soal CBT.
2. **Kemandirian Operasional Sekolah**: Sekolah memiliki kontrol penuh atas akun pengguna, database soal, serta kebijakan privasi tanpa bergantung pada aplikasi pihak ketiga berbayar.
3. **Integritas Ujian Digital**: Proteksi terukur melalui pengacakan urutan soal dan opsi jawaban, token dinamis berkala, penghitung waktu terpusat dari server (*server-authoritative countdown timer*), mode layar penuh wajib (*enforced fullscreen*), deteksi perpindahan tab/jendela (*visibility change tracking*), serta penonaktifan klik kanan dan pintasan salin-tempel.
4. **Penyimpanan Handal (*Debounced Autosave*)**: Setiap perubahan jawaban siswa disimpan secara otomatis ke server cloud dengan mekanisme *debounce* (500ms pilihan ganda, 1.500ms esai) dan pemulihan sesi otomatis saat koneksi internet kembali normal.
5. **Analisis Mutu Pendidikan Otomatis**: Dilengkapi modul psikometri butir soal (indeks kesukaran $P$ dan daya pembeda $D$) untuk mengevaluasi kualitas evaluasi secara objektif.

---

## 4. Target Pengguna & Peran (User Roles)

Sistem menerapkan **Role-Based Access Control (RBAC)** berbasis 8 peran pengguna terpisah:

| Peran | Deskripsi Tugas | Fitur & Hak Akses Utama |
| :--- | :--- | :--- |
| **Super Admin** | Administrator Utama TI Sekolah | Akses tak terbatas ke seluruh sistem, konfigurasi server cloud, manajemen hak akses pengguna, backup data, dan audit log. |
| **Admin / Operator** | Staf Kurikulum & Tata Usaha | Penjadwalan ujian, alokasi kelas/sesi, cetak token ruang/kelas, penetapan Koordinator Kurikulum, serta impor/ekspor data format Dapodik. |
| **Koordinator Kurikulum** | Guru Pengampu yang Menjabat Waka Kurikulum | Memantau seluruh 30 guru dalam penyusunan RPP, keaktifan kelas LMS, kesiapan bank soal CBT, serta memberikan catatan supervisi/validasi RPP. |
| **Guru (Pendidik)** | Guru Pengampu Mata Pelajaran | Penyusunan Modul Ajar (RPP), bank soal multi-tipe, publikasi materi LMS & penugasan, koreksi manual esai dengan rubrik, serta analisis butir soal. |
| **Proktor (Pengawas)** | Guru Piket / Pengawas Ruang Ujian | Dashboard monitoring real-time ruang/kelas, pelacakan status peserta, reset sesi siswa berkendala, serta pencatatan log pelanggaran. |
| **Siswa (Peserta)** | Peserta Didik SMA Kartika III-1 | Mengakses materi LMS, mengumpulkan tugas daring, mengikuti ujian digital dengan validasi token, navigasi soal adaptif, dan melihat hasil nilai. |
| **Wali Kelas** | Guru Pembina Kelas | Memantau kehadiran ujian siswa perwalian, memonitor perkembangan belajar LMS, dan mengunduh rekap nilai berkala kelas terkait. |
| **Kepala Sekolah** | Pimpinan Institusi Pendidikan | Dashboard eksekutif *read-only*: statistik kelulusan, sebaran nilai (grade band A–E), tren pencapaian akademik, dan evaluasi guru. |

---

## 5. Fitur Utama Produk (Core Features & Modules)

### 5.1 Modul Modul Ajar (RPP Kurikulum Merdeka)
* **Penyusunan Terstruktur**: Input Capaian Pembelajaran (CP), Tujuan Pembelajaran (TP), Profil Pelajar Pancasila, alokasi jam pelajaran (JP), dan target asesmen.
* **Integrasi LMS & CBT**: Menghubungkan modul RPP ke kelas pembelajaran di LMS dan kisi-kisi asesmen di CBT.
* **Alur Supervisi Kurikulum**: Dilengkapi fitur evaluasi oleh Koordinator Kurikulum dengan status (*Menunggu Review*, *Disetujui*, *Perlu Revisi*) beserta catatan umpan balik.

### 5.2 Modul Learning Management System (LMS)
* **Manajemen Kelas Pembelajaran (*Course Management*)**: Pengorganisasian topik materi per minggu/per bab berdasarkan mata pelajaran dan rombongan belajar (Kelas X-1 s/d XII-6).
* **Distribusi Materi Multimedia**: Publikasi berkas dokumen PDF, rangkuman teks kaya (Rich Text), tautan video pembelajaran, dan modul ajar.
* **Pelacakan Kemajuan Belajar (*Progress Tracking*)**: Siswa dapat menandai penyelesaian materi; guru dapat melihat persentase ketuntasan tiap siswa.
* **Penugasan Digital (*Assignments*)**: Pengumpulan tugas berbasis dokumen/berkas dengan batas waktu, sistem penguncian keterlambatan, dan penilaian guru.
* **Forum Diskusi Interaktif**: Ruang tanya jawab berutas (*threaded discussion*) antara guru dan siswa per kelas mata pelajaran.

### 5.3 Modul Mesin Ujian Digital (CBT Engine)
* **Dukungan 6 Tipe Soal**:
  1. *Pilihan Ganda Biasa* (Satu jawaban benar).
  2. *Pilihan Ganda Kompleks* (Multi-jawaban benar, format standar ANBK/AKM).
  3. *Benar / Salah* atau *Ya / Tidak*.
  4. *Menjodohkan (Matching Pair)* antar-pernyataan.
  5. *Isian Singkat (Short Answer)* dengan verifikasi otomatis berbasis kecocokan teks/kunci kata.
  6. *Esai / Uraian Panjang* dengan editor teks komprehensif.
* **Editor Multimedia Soal**: Mendukung penyisipan gambar, tabel, simbol matematika (LaTeX/Equation), dan audio listening bahasa asing.
* **Pengacakan Ganda**: Pengacakan urutan nomor soal dan opsi jawaban per siswa untuk meminimalkan kecurangan.
* **Token Dinamis**: Sistem token berbasis ruang/kelas dengan masa kedaluwarsa otomatis.
* **Cockpit Pengerjaan Siswa**:
  - *Debounced Autosave*: Setiap pemilihan jawaban disimpan otomatis ke server cloud dengan jeda debounce optimal.
  - *Server-Authoritative Countdown Timer*: Durasi dihitung oleh server; manipulasi jam komputer client tidak memengaruhi sisa waktu.
  - *Palette Navigasi Soal*: Penanda nomor soal terjawab, belum terjawab, dan tanda 'Ragu-ragu'.
  - *Auto-Submit*: Penutupan otomatis dan komputasi skor saat durasi ujian berakhir.

### 5.4 Modul Keamanan Ujian (Enforced Fullscreen & Anti-Cheating)
* **Mode Layar Penuh Wajib (*Enforced Fullscreen*)**: Antarmuka ujian mengharuskan siswa berada dalam mode layar penuh selama pengerjaan; keluar dari mode layar penuh akan menampilkan peringatan dan hitung mundur pengembalian.
* **Pencegahan Aksi Terlarang**: Menonaktifkan klik kanan (*context menu*), seleksi teks, *copy-paste*, serta pintasan keyboard tertentu (`Ctrl+C`, `Ctrl+V`, `Ctrl+R`, `F5`).
* **Deteksi Pelanggaran & Pindah Tab (*Visibility Change & Blur Detection*)**: Sistem mendeteksi perpindahan fokus jendela/tab peramban secara otomatis dan mencatat waktu kejadian serta frekuensi pelanggaran ke log proktor waktu-nyata.
* **Tindakan Pengawas / Proktor**: Proktor dapat memantau status pelanggaran, membuka kembali sesi yang terkunci (*unlock*), atau mengeluarkan peserta yang melanggar tata tertib.

### 5.5 Modul Monitoring Proktor Waktu-Nyata
* **Live Status Dashboard**: Menampilkan status seluruh peserta per ruang/kelas (Belum Mulai, Sedang Mengerjakan, Selesai).
* **Detak Koneksi (*Heartbeat Monitor*)**: Mendeteksi sinyal aktif client siswa secara periodik untuk memitigasi kendala koneksi internet yang terputus mendadak.
* **One-Click Session Unlock**: Kemudahan bagi proktor untuk membuka kunci sesi siswa yang mengalami kendala teknis (siswa dapat melanjutkan ujian tanpa kehilangan jawaban yang telah tersimpan).

### 5.6 Modul Penilaian, Koreksi Esai & Analisis Butir Soal
* **Auto-Grading Instan**: Soal objektif dinilai server secara otomatis saat siswa menekan tombol selesai.
* **Antrean Koreksi Esai Terpadu**: Antarmuka penilaian khusus guru untuk memeriksa jawaban uraian dengan panduan rubrik skor maksimal.
* **Analisis Psikometri Butir Soal**:
  - *Indeks Tingkat Kesukaran ($P$)*: Klasifikasi soal Mudah ($P > 0.70$), Sedang ($0.30 \le P \le 0.70$), dan Sukar ($P < 0.30$).
  - *Indeks Daya Pembeda ($D$)*: Mengukur kemampuan butir soal membedakan kelompok siswa pandai (atas 27%) dan kelompok siswa kurang (bawah 27%).
  - *Distribusi Pengecoh (Distractor Analysis)*: Analisis efektivitas pilihan pada soal pilihan ganda.
* **Ekspor & Rekapitulasi Nilai**: Unduh laporan rekap nilai per kelas, per mapel, atau per siswa dalam format CSV/Excel siap impor ke buku nilai rapor.

### 5.7 Modul Pertukaran Data Format Dapodik
* **Impor/Ekspor Data Master Massal**: Memasukkan ratusan akun siswa, guru, rombel kelas, dan bank soal via berkas CSV.
* **Format Kolom Kompatibel Dapodik**: Ekspor dan impor data siswa yang selaras dengan kolom Data Pokok Pendidikan (NISN, Nama, Rombel, Jenis Kelamin, Tanggal Lahir).
* **Pencadangan Basis Data Otomatis**: Perintah terjadwal (`scheduler`) untuk backup berkala database pada pukul 02:00 dini hari dan pembersihan sesi usang pada pukul 03:00.

---

## 6. Desain Visual & Identitas Institusi (School Assessment UI Kit)

Antarmuka webapp dirancang menggunakan identitas resmi **SMA Kartika III-1 Banyubiru (Yayasan Kartika Jaya Cabang III Diponegoro)**:
* **Kartika Forest Green (`#061d13` / `#143625`)**: Warna hijau institusional yang melambangkan wibawa, keteguhan, kedisiplinan, dan patriotisme.
* **Kartika Heritage Gold (`#d97706` / `#f59e0b`)**: Aksen emas yang melambangkan keunggulan prestasi akademik dan budi pekerti luhur.
* **Clean Neutral Slate & High Contrast**: Memberikan tingkat keterbacaan tinggi, bebas distraksi visual (*zero distraction*), serta memenuhi standar rasio kontras aksesibilitas WCAG AA.
* **Identitas Resmi Sekolah**: Menampilkan logo resmi SMA Kartika III-1 Banyubiru pada login gate, navigasi, dan seluruh lembar cetak laporan.

---

## 7. Arsitektur Teknis Cloud Terpusat

```
                                [ PENGGUNA SISTEM ]
        +--------------------------------+--------------------------------+
        |                                |                                |
        v                                v                                v
  [SISWA / PESERTA]               [GURU / PENDIDIK]              [ADMIN / MANAJEMEN]
  (PC, Laptop, HP)               (Laptop, Komputer)             (Laptop, Komputer)
        |                                |                                |
        +--------------------------------+--------------------------------+
                                         |
                                         v  HTTPS (SSL / TLS Encrypted)
                   +--------------------------------------------+
                   |          HOSTINGER CLOUD INGRESS           |
                   |   cbtkartika.koomit.com (Web Portal)       |
                   |   ipcbtkartika.koomit.com (API / Mirror)   |
                   +---------------------+----------------------+
                                         |
                                         v
                   +--------------------------------------------+
                   |       LARAVEL 11 LTS / PHP 8.4 ENGINE      |
                   | - Modular Monolith Architecture            |
                   | - Session & Sanctum Authentication         |
                   | - Fullscreen & Blur Violation Engine       |
                   | - Auto-Grading & Psychometric Calculator   |
                   | - Laravel Reverb (WebSocket Real-Time)     |
                   | - Automated Task Scheduler                 |
                   +---------------------+----------------------+
                                         |
                                         v
                   +--------------------------------------------+
                   |       CENTRAL CLOUD DATABASE (MYSQL 8.0)   |
                   | - InnoDB Storage Engine (Row-Level Locking)|
                   | - Single Source of Truth                   |
                   | - Automated Daily Off-Site Backups         |
                   +--------------------------------------------+
```

### 7.1 Spesifikasi Tumpukan Teknologi (Tech Stack)
* **Backend Framework**: PHP 8.4, Laravel 11 LTS Framework (Modular Monolith architecture).
* **Admin & Teacher Engine**: Filament PHP v3 & Laravel Blade teroptimasi.
* **Client Frontend Ujian & Portal**:
  - *Arsitektur Antarmuka*: Laravel Blade + React/Vite components, Tailwind CSS.
  - *Rendering Akseleratif*: Server-driven UI dengan payload ringan untuk perangkat berspesifikasi standar.
* **Komunikasi Waktu-Nyata**: Laravel Reverb & WebSockets (pemantauan proktor, status detak koneksi, dan reset sesi seketika).
* **Autentikasi & Keamanan**: Laravel Sanctum (session-based cookies untuk portal web, token-based untuk RESTful API), password hashing Bcrypt/Argon2.
* **Basis Data Terpusat**: MySQL 8.0+ dengan InnoDB Storage Engine (*row-level locking*, integritas foreign keys, dan transaksi ACID).
* **Automated Task Scheduler**:
  - Pembersihan percobaan kedaluwarsa secara berkala.
  - Backup harian basis data cloud (`dailyAt 02:00`).
  - Pembersihan sesi usang dan housekeeping cache (`dailyAt 03:00`).

---

## 8. Alur Kerja Operasional (Operational User Flows)

### 8.1 Alur Guru: Dari Modul Ajar ke Ujian
1. Guru masuk ke portal menggunakan username/email institusi.
2. Membuka menu **Modul Ajar (RPP)** untuk merumuskan CP/TP dan mengunggah silabus materi.
3. Menghubungkan RPP ke **LMS Courses** untuk mendistribusikan bahan tayang, video, dan tugas mingguan.
4. Menyusun butir pertanyaan di **Bank Soal** (memilih tipe soal, menyisipkan gambar/LaTeX, menetapkan kunci dan bobot nilai).
5. Merakit **Paket Ujian** (memilih komposisi soal, menentukan acak soal/opsi, dan durasi pengerjaan).

### 8.2 Alur Panitia / Admin: Penjadwalan & Token Ujian
1. Admin membuat jadwal asesmen (misal: *Penilaian Akhir Semester Ganjil*).
2. Menentukan alokasi waktu dan ruang/kelas ujian.
3. Mengenerasi token ujian unik 6 karakter per sesi/ruang kelas.
4. Membagikan lembar daftar token kepada pengawas ruang.

### 8.3 Alur Siswa: Pelaksanaan Ujian Bebas Kendala
1. Siswa membuka peramban web pada perangkat laptop atau ponsel dan mengakses portal ujian.
2. Login menggunakan **NISN / Username** dan kata sandi default/terdaftar.
3. Memasukkan **Token Ujian** 6 karakter yang diberikan oleh pengawas ruang/kelas.
4. Sistem memvalidasi identitas peserta, mengaktifkan mode layar penuh wajib (*enforced fullscreen*), dan memulai hitung mundur timer server.
5. Siswa menjawab soal; setiap pilihan jawaban langsung tersimpan ke cloud secara otomatis (*debounced autosave*).
6. Jika terjadi gangguan perangkat, siswa dapat membuka kembali peramban pada perangkat pengganti: seluruh jawaban yang telah dipilih tetap utuh dan durasi tersisa terhitung akurat sesuai timer server.
7. Siswa menekan tombol kumpul jawaban (atau sistem melakukan auto-submit saat timer habis).

### 8.4 Alur Pasca-Ujian: Penilaian & Analisis
1. Sistem segera menghitung skor seluruh soal pilihan ganda, benar/salah, dan isian singkat.
2. Guru pengampu masuk ke menu **Koreksi Esai (Grading)** untuk memeriksa jawaban uraian dengan pedoman rubrik.
3. Guru membuka menu **Analisis Butir Soal** untuk melihat kualitas soal (daya beda dan tingkat kesukaran).
4. Nilai akhir diunduh panitia dalam format berkas Dapodik/rapor untuk dilaporkan kepada Kepala Sekolah dan Orang Tua Siswa.

---

## 9. Parameter Kualitas & Kesiapan Produksi (Quality Assurance)

Aplikasi telah melalui pengujian menyeluruh pada seluruh lapisan fungsionalitas dan keamanan:
* **Suite Pengujian Otomatis**:
  - **131 PHPUnit Tests (397 assertions)** lulus 100%: Meliputi pengujian otentikasi peran, validasi token, mutasi jawaban, auto-grading, kalkulasi rumus psikometri, serta supervisi Koordinator Kurikulum.
* **Kepatuhan Regulasi**: Kompatibel dengan struktur kurikulum merdeka nasional dan format pendataan Dapodik Kementerian Pendidikan.
* **Produksi Live**: Telah dideploy dan aktif pada server cloud Hostinger dengan PHP 8.4.

---

## 10. Roadmap Pengembangan Mendatang (Future Roadmap)

Rencana peningkatan teknis di masa mendatang meliputi:
* **Aplikasi Khusus Desktop (Safe Exam Browser / SEB Integration)**: Dukungan konfigurasi resmi Safe Exam Browser dengan validasi hash header (`BrowserExamKey`) bagi sekolah yang menghendaki penguncian OS menyeluruh.
* **Integrasi Langsung API Web-Service Dapodik**: Sinkronisasi data dua arah langsung dengan server resmi kementerian saat token web service diterbitkan.

---

## 11. Kesimpulan

**Sistem Terpadu RPP, LMS & CBT SMA Kartika III-1 Banyubiru** adalah representasi modernisasi tata kelola pembelajaran dan evaluasi sekolah berbasis platform cloud terpusat. Dengan memadukan efisiensi digital, kepatuhan kurikulum merdeka, keamanan ujian berbasis peramban modern, serta ketiadaan beban perawatan server fisik lokal, platform ini menjadi aset strategis institusi dalam mengantarkan peserta didik meraih prestasi terbaik berlandaskan nilai-nilai integritas dan disiplin tinggi.

---
*Dokumen ini diterbitkan sebagai dokumentasi resmi arsitektur dan kapabilitas produk SMA Kartika III-1 Banyubiru.*
