# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Siswa (Pelajar)**: Mengerjakan asesmen terjadwal dengan verifikasi token, mengakses materi pembelajaran mandiri di LMS, menyelesaikan tugas, serta meninjau hasil evaluasi capaian belajar. Mengakses melalui komputer laboratorium sekolah, laptop pribadi, maupun smartphone/tablet.
- **Guru (Pendidik)**: Mengimpor dan mengelola Rencana Pelaksanaan Pembelajaran (RPP / Modul Ajar Kurikulum Merdeka), menyusun bank soal multi-tipe (Pilihan Ganda, Benar/Salah, Menjodohkan, Esai), mempublikasikan materi LMS, mengoreksi jawaban esai, serta meninjau analisis butir soal psikometri.
- **Proktor (Pengawas Lab)**: Memantau pelaksanaan sesi ujian laboratorium secara real-time, mereset sesi peserta yang mengalami kendala teknis perangkat, dan mengawasi status detak koneksi (heartbeat) peserta.
- **Administrator & Super Admin**: Mengelola data otorisasi pengguna, rombel kelas, mata pelajaran, impor/ekspor data Dapodik, penjadwalan gelombang ujian, serta konfigurasi operasional server.
- **Pimpinan Sekolah (Kepala Sekolah & Wali Kelas)**: Memantau perkembangan pelaksanaan asesmen, rekapitulasi nilai, serta ketercapaian kompetensi peserta didik.

## Product Purpose

Menyediakan ekosistem pembelajaran digital terpadu untuk SMA Kartika III-1 Banyubiru yang menghubungkan secara langsung siklus perencanaan kurikulum (RPP/Modul Ajar), pelaksanaan pembelajaran daring (LMS), dan evaluasi terstandar berintegritas tinggi (CBT) dengan pemantauan laboratorium waktu-nyata serta analisis psikometri.

## Positioning

Sistem terpadu satu pintu yang menyelaraskan dokumen silabus RPP Kurikulum Merdeka ke dalam ruang kelas daring (LMS) dan pemetaan Tujuan Pembelajaran (TP) bank soal ujian (CBT), mengeliminasi fragmentasi operasional dan duplikasi input data akademik di lingkungan sekolah.

## Operating Context

- **Laboratorium Komputer Sekolah**: Digunakan untuk pelaksanaan asesmen serentak terawasi (Sumatif, Penilaian Harian, Ujian Sekolah) dengan autentikasi token dinamis dan monitoring proktor.
- **Kegiatan Belajar Mengajar (KBM) Mandiri**: Digunakan guru dan siswa baik di kelas reguler maupun secara fleksibel dari rumah untuk modul materi, diskusi interaktif, dan penugasan daring.
- **Infrastruktur Jaringan**: Dirancang tangguh berjalan pada jaringan intranet lokal (LAN laboratorium sekolah) maupun via cloud tunneling/internet publik secara aman.

## Capabilities and Constraints

- **Manajemen Modul Ajar (RPP)**: Impor RPP berbasis struktur dokumen, pengelolaan alokasi waktu, serta sinkronisasi otomatis ke materi LMS dan butir asesmen CBT.
- **LMS Belajar**: Manajemen modul pembelajaran, penugasan tugas mandiri, serta forum diskusi kelas daring.
- **Mesin Ujian Digital (CBT)**: Bank soal multi-tipe, pembobotan nilai, penjadwalan sesi & token, cockpit ujian interaktif dengan autosave jawaban, timer digital, palette nomor soal, dan mode fullscreen terkunci.
- **Monitoring & Evaluasi**: Dasbor proktor real-time (Laravel Echo / WebSockets), modul koreksi esai guru dengan rubrik penilaian, serta analisis psikometri butir soal (indeks kesukaran dan daya pembeda).
- **Integrasi Data**: Impor/ekspor data siswa, guru, kelas, bank soal, dan nilai yang kompatibel dengan format Dapodik.
- **Kendala Teknis**: Wajib mempertahankan kompatibilitas 117 PHPUnit tests dan Playwright E2E test suites (atribut selector form login, token, ujian, dan routing).

## Brand Commitments

- **Institusi**: SMA Kartika III-1 Banyubiru (Yayasan Kartika Jaya Cabang III Diponegoro).
- **Karakter & Nilai**: Berwibawa, disiplin, profesional, patriotik, dan modern.
- **Identitas Visual**: Menggunakan paduan warna hijau rimba institusional (*Kartika Forest Green*), aksen emas (*Gold*), dan netral slate modern dengan prinsip Bento Grid dan Glassmorphism halus.
- **Aset Resmi**: Menggunakan lambang resmi sekolah `public/images/logo-kartika.png`.

## Evidence on Hand

- Repositori aktif dengan arsitektur Laravel 12, Tailwind CSS v4, dan Vite 8.
- Basis data dan seeder yang telah dikonfigurasi untuk seluruh peran pengguna, rombel kelas, bank soal, dan paket ujian.
- Suite pengujian lengkap: 117 PHPUnit tests dan 6 spesifikasi Playwright E2E yang telah terverifikasi 100% lulus.

## Product Principles

1. **Keterpaduan Ekosistem (Unified Pipeline)**: Dokumen RPP menjadi hulu data yang mengalir secara otomatis ke ruang belajar LMS dan bank soal CBT.
2. **Kenyamanan & Stabilitas Ujian (Zero Distraction)**: Antarmuka pengerjaan siswa bersih, bebas distraksi visual, dan memastikan jawaban tersimpan aman setiap detik.
3. **Ergonomi Berdasarkan Peran**: Antarmuka disesuaikan secara presisi dengan kebutuhan tiap peran (Siswa, Guru, Proktor, Admin).
4. **Performa Ringan & Adaptif**: Cepat dimuat pada PC laboratorium berspesifikasi standar maupun perangkat mobile siswa.

## Accessibility & Inclusion

- Dilengkapi kontrol pembesaran ukuran font soal (A-, A, A+) untuk kenyamanan membaca siswa di layar monitor lab.
- Rasio kontras teks memenuhi standar WCAG AA terhadap latar belakang.
- Layout responsif penuh mulai dari resolusi monitor lab desktop 4:3 hingga layar ponsel vertikal.
