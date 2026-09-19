# PRD - Sistem CBT (Computer Based Test)
## SMA Kartika III-1 Banyubiru, Kabupaten Semarang

| | |
|---|---|
| **Nama Dokumen** | Product Requirements Document (PRD) - Sistem CBT |
| **Nama Sekolah** | SMA Kartika III-1 Banyubiru |
| **Lokasi** | Kecamatan Banyubiru, Kabupaten Semarang, Jawa Tengah |
| **Jumlah Siswa (estimasi)** | ± 500 siswa |
| **Versi Dokumen** | 1.0 |
| **Status** | Draft |

---

## 1. Latar Belakang

SMA Kartika III-1 Banyubiru membutuhkan sistem Computer Based Test (CBT) mandiri untuk menyelenggarakan ujian berbasis komputer (Penilaian Harian, PTS/UTS, PAS/UAS, Tryout, hingga simulasi ANBK) secara internal di sekolah. Selama ini pelaksanaan ujian berbasis kertas (Paper Based Test) memiliki kelemahan seperti proses koreksi manual yang lama, potensi kecurangan (kunci jawaban bocor), biaya cetak, serta kesulitan rekap nilai secara cepat dan akurat.

Dengan jumlah siswa ±500 orang sementara jumlah unit komputer/laboratorium di sekolah terbatas, sistem CBT harus dirancang untuk mendukung **pelaksanaan ujian secara bersesi/bergelombang (multi-sesi)** dalam satu hari, mengikuti pola yang lazim digunakan pada sistem CBT UNBK/ANBK di sekolah-sekolah Indonesia.

## 2. Tujuan Produk

1. Menyediakan platform ujian digital yang dapat digunakan mandiri oleh sekolah (tanpa bergantung pihak ketiga/Kemendikbud) untuk PH, PTS, PAS, Tryout, dan simulasi AKM/ANBK.
2. Mengurangi kecurangan melalui pengacakan soal, token ujian per sesi, dan monitoring real-time.
3. Mempercepat proses koreksi dan rekap nilai (otomatis untuk soal objektif).
4. Mendukung pelaksanaan ujian dengan jaringan lokal (LAN) mengingat keterbatasan/ketidakstabilan koneksi internet di lokasi sekolah.
5. Memberikan kemudahan bagi guru dalam membuat, mengelola, dan mendistribusikan bank soal.
6. Menghasilkan laporan hasil ujian yang dapat diakses oleh siswa, guru, wali kelas, dan kepala sekolah.

## 3. Ruang Lingkup (Scope)

### 3.1 Termasuk dalam Ruang Lingkup
- Manajemen bank soal (CRUD soal, kategori, mata pelajaran, tingkat kesulitan).
- Manajemen ujian (jadwal, sesi/gelombang, durasi, kelas peserta).
- Token ujian per sesi/ruang.
- Pelaksanaan ujian oleh siswa (antarmuka pengerjaan soal).
- Pengacakan soal dan opsi jawaban.
- Auto-grading untuk soal objektif (PG, benar-salah, menjodohkan, isian singkat).
- Penilaian manual untuk soal esai oleh guru.
- Monitoring peserta ujian secara real-time oleh proktor/pengawas.
- Manajemen pengguna dan hak akses (role-based access).
- Laporan dan rekap nilai (per siswa, per kelas, per mapel).
- Import/export soal massal (Excel/Word ke sistem).
- Mode operasi berbasis jaringan lokal (LAN) dengan opsi sinkronisasi ke server pusat/cloud.

### 3.2 Tidak Termasuk dalam Ruang Lingkup (Fase 1)
- Aplikasi mobile native (Android/iOS) - fase 1 berbasis web responsif saja.
- Integrasi langsung dengan Dapodik/PDSS (dapat menjadi fase berikutnya).
- Payment gateway (tidak relevan, sistem internal sekolah, gratis).
- Fitur pembelajaran (LMS) seperti materi ajar, forum diskusi, dsb - fokus hanya pada ujian.
- Proctoring berbasis AI (deteksi wajah/gerakan) - opsional, dipertimbangkan di fase lanjutan.

## 4. Target Pengguna & Peran (User Roles)

| Peran | Deskripsi | Hak Akses Utama |
|---|---|---|
| **Super Admin** | Admin TI sekolah/operator utama | Kelola seluruh sistem, user, server, backup |
| **Admin/Operator Ujian** | Tata Usaha / Kurikulum | Buat jadwal ujian, kelola sesi, cetak kartu peserta, generate token |
| **Guru/Pembuat Soal** | Guru mata pelajaran | Kelola bank soal mapel yang diampu, koreksi esai, lihat nilai |
| **Kepala Sekolah/Wakakurikulum** | Pimpinan sekolah | Lihat laporan rekap & analisis hasil ujian (read-only dashboard) |
| **Proktor/Pengawas Ruang** | Guru piket saat ujian | Monitoring real-time peserta di ruangnya, buka kunci ujian, lapor gangguan |
| **Siswa/Peserta** | Peserta ujian | Login, mengerjakan ujian sesuai jadwal & token, lihat hasil (jika diizinkan) |
| **Wali Kelas** | Guru wali kelas | Lihat rekap nilai siswa di kelas yang diampu |

## 5. Alur Pengguna Utama (User Flow)

### 5.1 Alur Guru Membuat Ujian
1. Login sebagai Guru.
2. Membuat/memilih bank soal sesuai mapel.
3. Menyusun paket soal (jumlah soal, bobot nilai, urutan/acak).
4. Mengajukan paket soal ke Admin untuk dijadwalkan (atau langsung membuat jadwal jika punya izin).
5. Setelah ujian selesai, guru mengoreksi soal esai (jika ada) dan memverifikasi nilai akhir.

### 5.2 Alur Admin Menjadwalkan Ujian
1. Login sebagai Admin.
2. Membuat jadwal ujian: nama ujian, mapel, kelas/rombel peserta, tanggal, jumlah sesi.
3. Menetapkan durasi per sesi & kapasitas ruang (menyesuaikan jumlah komputer tersedia).
4. Generate token unik per sesi/ruang.
5. Mencetak kartu peserta/daftar token untuk dibagikan proktor.

### 5.3 Alur Siswa Mengerjakan Ujian
1. Siswa login menggunakan NISN/username & password ke aplikasi client (browser/kiosk mode).
2. Memasukkan token ujian yang diberikan proktor.
3. Sistem menampilkan halaman konfirmasi identitas & tata tertib.
4. Siswa mengerjakan soal (timer berjalan, auto-save tiap jawaban).
5. Siswa submit jawaban (manual) atau otomatis ter-submit saat waktu habis/koneksi terputus lama.
6. Sistem menampilkan halaman "Ujian Selesai" (nilai ditampilkan/disembunyikan sesuai pengaturan).

### 5.4 Alur Proktor Mengawasi Ujian
1. Login sebagai Proktor di ruang yang ditugaskan.
2. Melihat status real-time seluruh peserta di ruang tersebut (belum login, sedang mengerjakan, selesai, terputus).
3. Membuka/reset token bagi siswa yang mengalami kendala (misal logout tidak sengaja).
4. Mencatat kejadian/pelanggaran selama ujian (log kejadian).

## 6. Kebutuhan Fungsional (Functional Requirements)

### 6.1 Modul Autentikasi & Manajemen Pengguna
- FR-1.1: Sistem mendukung login berbeda untuk tiap peran (role-based).
- FR-1.2: Import data siswa & guru secara massal via Excel (NISN, nama, kelas, mapel).
- FR-1.3: Reset password mandiri oleh Admin.
- FR-1.4: Manajemen struktur sekolah: tahun ajaran, kelas/rombel, mata pelajaran.

### 6.2 Modul Bank Soal
- FR-2.1: CRUD soal dengan tipe: Pilihan Ganda (single/multi jawaban), Benar-Salah, Menjodohkan, Isian Singkat, Esai/Uraian.
- FR-2.2: Dukungan soal bergambar, berformat matematika (equation editor/LaTeX), audio/video (untuk mapel bahasa).
- FR-2.3: Kategori soal berdasarkan mapel, kelas, tingkat kesulitan (mudah/sedang/sulit), dan tujuan (indikator/KD/CP - Kurikulum Merdeka).
- FR-2.4: Import soal massal dari template Excel/Word.
- FR-2.5: Bank soal dapat digunakan ulang lintas ujian (reusable question bank).
- FR-2.6: Fitur duplikasi & versioning soal.

### 6.3 Modul Manajemen Ujian
- FR-3.1: Pembuatan paket ujian: pilih soal dari bank (manual atau acak otomatis berdasarkan kriteria).
- FR-3.2: Pengaturan durasi ujian, tanggal & jam mulai-selesai.
- FR-3.3: Dukungan multi-sesi/gelombang dalam satu hari ujian (penting karena keterbatasan jumlah komputer vs 500 siswa).
- FR-3.4: Pengaturan pengacakan soal dan opsi jawaban per siswa (anti-contek).
- FR-3.5: Pengaturan bobot nilai per soal/per tipe soal.
- FR-3.6: Token ujian unik per sesi/ruang, dengan masa berlaku (expired) tertentu.
- FR-3.7: Pengaturan apakah siswa dapat melihat hasil/nilai langsung setelah ujian atau tidak.
- FR-3.8: Pengaturan mode kembali ke soal sebelumnya (boleh/tidak boleh, sesuai kebijakan ujian misal ANBK tidak boleh kembali).

### 6.4 Modul Pengerjaan Ujian (Client Siswa)
- FR-4.1: Tampilan antarmuka ujian yang sederhana, ringan (low bandwidth friendly), dapat diakses via browser (Chrome/Firefox) atau mode kiosk/lockdown.
- FR-4.2: Auto-save jawaban setiap kali siswa memilih/mengubah jawaban (mencegah kehilangan data saat listrik/koneksi mati).
- FR-4.3: Timer countdown yang tervalidasi di sisi server (server-side timer) agar tidak bisa dimanipulasi dari client.
- FR-4.4: Navigasi antar soal (nomor soal, penanda ragu-ragu/flag).
- FR-4.5: Auto-submit saat waktu habis.
- FR-4.6: Mekanisme resume otomatis jika koneksi terputus/komputer restart (siswa login ulang, jawaban tersimpan sebelumnya tetap ada).
- FR-4.7: Deteksi & pencegahan sederhana: disable klik kanan, disable copy-paste, deteksi pindah tab (log peringatan ke proktor).

### 6.5 Modul Monitoring & Pengawasan
- FR-5.1: Dashboard real-time proktor: status tiap peserta (online/offline/selesai/bermasalah).
- FR-5.2: Admin dapat memantau seluruh ruang dari satu dashboard pusat.
- FR-5.3: Log aktivitas mencurigakan (pindah tab berkali-kali, waktu login ganda, dsb).
- FR-5.4: Fitur "kick/reset" siswa tertentu oleh proktor (jika terjadi kendala teknis).

### 6.6 Modul Penilaian & Hasil
- FR-6.1: Auto-grading otomatis untuk soal objektif segera setelah submit.
- FR-6.2: Antarmuka koreksi manual untuk soal esai oleh guru (dengan rubrik nilai).
- FR-6.3: Rekap nilai per siswa, per kelas, per mapel, per ujian.
- FR-6.4: Analisis butir soal (tingkat kesukaran, daya beda) - berguna untuk evaluasi kualitas soal.
- FR-6.5: Export hasil ke Excel/PDF untuk kebutuhan rapor & arsip.
- FR-6.6: Cetak kartu hasil ujian per siswa (opsional).

### 6.7 Modul Laporan
- FR-7.1: Dashboard ringkasan untuk Kepala Sekolah (rata-rata nilai per kelas/mapel, tingkat kelulusan KKM).
- FR-7.2: Laporan kehadiran ujian (siswa yang tidak mengikuti/terlambat).
- FR-7.3: Riwayat ujian siswa dari waktu ke waktu (tren nilai).

### 6.8 Modul Administrasi Sistem
- FR-8.1: Backup & restore data (harian/manual).
- FR-8.2: Pengaturan tahun ajaran & semester aktif.
- FR-8.3: Log audit (siapa mengubah apa, kapan) untuk keperluan akuntabilitas.

## 7. Kebutuhan Non-Fungsional (Non-Functional Requirements)

| Kategori | Kebutuhan |
|---|---|
| **Performa** | Sistem harus mampu menangani minimal 40-60 siswa mengerjakan ujian secara bersamaan per sesi (menyesuaikan kapasitas lab sekolah), dengan waktu respon halaman < 2 detik pada jaringan LAN lokal. |
| **Skalabilitas** | Arsitektur mendukung penambahan sesi/gelombang tanpa downtime, serta dapat menangani total ±500 siswa terjadwal dalam beberapa gelombang per hari. |
| **Ketersediaan (Availability)** | Sistem harus tetap berjalan dalam kondisi internet sekolah terputus, dengan mengandalkan **server lokal (on-premise/LAN)**; sinkronisasi ke cloud dilakukan saat internet tersedia. |
| **Keamanan** | Enkripsi password, proteksi sesi ujian dari manipulasi client-side, pencegahan akses tidak sah ke bank soal, HTTPS untuk akses (jika ada lapisan internet), token ujian tidak dapat digunakan ulang setelah expired. |
| **Keandalan Data** | Auto-save berkala, mekanisme recovery jika server/komputer client mati mendadak saat listrik padam. |
| **Kompatibilitas** | Berjalan di browser umum (Chrome, Firefox, Edge) pada spesifikasi komputer lab standar sekolah (low-spec friendly), idealnya juga dapat diinstal sebagai aplikasi client ringan (kiosk mode) di Windows. |
| **Usability** | Antarmuka sederhana dan intuitif, mengingat pengguna (siswa & guru) memiliki tingkat literasi digital yang beragam. |
| **Maintainability** | Kode terstruktur modular agar mudah dikembangkan oleh 1-2 orang tim developer/IT sekolah. |
| **Backup & Disaster Recovery** | Backup otomatis harian minimal, dengan opsi restore cepat sebelum/sesudah pelaksanaan ujian. |

## 8. Kebutuhan Infrastruktur

Mengingat konteks sekolah dengan ±500 siswa dan keterbatasan jumlah komputer/lab serta koneksi internet, arsitektur yang disarankan mengikuti pola umum CBT sekolah di Indonesia:

- **Server Lokal (Server Sekolah):** 1 unit PC/server spesifikasi menengah diletakkan di ruang server/lab utama, menjalankan aplikasi CBT (backend + database) yang diakses seluruh client via jaringan LAN/WiFi sekolah.
- **Client Siswa:** Komputer/laptop lab yang sudah ada, mengakses CBT via browser menuju alamat IP server lokal.
- **Mode Ujian Bergelombang:** Karena jumlah komputer terbatas (mis. 1-2 lab @ 40 unit), 500 siswa dibagi menjadi beberapa sesi per hari (contoh: 4-6 sesi @ 40-80 siswa).
- **Sinkronisasi Cloud (opsional, fase lanjutan):** Server lokal dapat melakukan sinkronisasi data hasil ujian ke server cloud/pusat saat koneksi internet tersedia, untuk keperluan backup terpusat atau akses laporan dari luar sekolah.
- **Genset/UPS:** Rekomendasi non-teknis namun penting - server & minimal jaringan disarankan tersambung UPS untuk mengantisipasi listrik padam saat ujian berlangsung.

## 9. Tipe Soal yang Didukung

1. Pilihan Ganda (satu jawaban benar)
2. Pilihan Ganda Kompleks (lebih dari satu jawaban benar) - sesuai standar AKM/ANBK
3. Benar/Salah atau Ya/Tidak
4. Menjodohkan (matching)
5. Isian Singkat (short answer, dinilai otomatis dengan pencocokan teks/keyword)
6. Esai/Uraian (dinilai manual oleh guru dengan rubrik)

## 10. Model Data (Gambaran Tingkat Tinggi)

Entitas utama yang perlu ada dalam basis data sistem:

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

## 11. Metrik Keberhasilan (Success Metrics)

- Seluruh ±500 siswa dapat menyelesaikan ujian tanpa kendala teknis berarti (target: >95% sesi berjalan lancar tanpa gangguan sistem).
- Waktu rekap nilai dari sebelumnya berhari-hari (manual) menjadi < 1 jam setelah ujian selesai (otomatis untuk soal objektif).
- Penurunan indikasi kecurangan dibanding ujian kertas (diukur secara kualitatif oleh guru/proktor).
- Adopsi penuh oleh seluruh guru mapel dalam 1 tahun ajaran pertama penggunaan sistem.

## 12. Batasan & Risiko

| Risiko | Mitigasi |
|---|---|
| Listrik padam saat ujian berlangsung | Auto-save berkala, UPS pada server, mekanisme resume ujian |
| Jumlah komputer lab tidak mencukupi untuk 500 siswa sekaligus | Skema ujian multi-sesi/gelombang |
| Siswa membuka tab lain / mencontek | Deteksi pindah tab, kiosk mode/lockdown browser, pengacakan soal |
| Guru belum terbiasa membuat soal digital | Pelatihan/onboarding, template import Excel yang mudah diisi |
| Server lokal down saat ujian | Backup server cadangan/rencana kontingensi manual (kertas cadangan) |
| Kesalahan input soal oleh guru | Fitur preview soal sebelum ujian dimulai, validasi sebelum publish |

## 13. Rencana Fase Pengembangan (Roadmap Usulan)

**Fase 1 - MVP (Fungsi Inti)**
- Manajemen user & bank soal
- Pembuatan paket ujian & penjadwalan sesi
- Pengerjaan ujian siswa (PG, Esai dasar)
- Auto-grading soal objektif
- Rekap nilai sederhana

**Fase 2 - Penyempurnaan**
- Monitoring real-time proktor
- Analisis butir soal
- Import/export massal
- Dashboard laporan untuk Kepala Sekolah
- Tipe soal lengkap (menjodohkan, PG kompleks, dsb)

**Fase 3 - Lanjutan (Opsional)**
- Sinkronisasi cloud/backup terpusat
- Aplikasi client kiosk mode khusus (desktop app)
- Integrasi dengan Dapodik/data pokok pendidikan
- Proctoring lanjutan (webcam monitoring)

## 14. Lampiran

### 14.1 Asumsi
- Sekolah memiliki minimal 1 laboratorium komputer dengan jaringan LAN/WiFi internal.
- Tersedia minimal 1 orang operator/admin yang bertanggung jawab mengelola sistem.
- Jaringan internet sekolah tidak selalu stabil, sehingga sistem harus dapat berjalan secara lokal (offline dari internet, online dalam LAN).

### 14.2 Glosarium
- **CBT**: Computer Based Test, ujian berbasis komputer.
- **Token Ujian**: Kode unik yang diberikan proktor kepada siswa untuk membuka akses ujian pada sesi tertentu.
- **Proktor**: Pengawas ruang ujian.
- **Sesi/Gelombang**: Pembagian waktu pelaksanaan ujian karena keterbatasan jumlah komputer.
- **Auto-grading**: Penilaian otomatis oleh sistem untuk soal objektif.

---
*Dokumen ini adalah draft awal PRD dan dapat disesuaikan lebih lanjut berdasarkan diskusi dengan pihak sekolah (Kepala Sekolah, Kurikulum, dan Tim TI).*
