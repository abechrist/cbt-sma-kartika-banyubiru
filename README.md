# CBT SMA Kartika III-1 Banyubiru

Sistem Terpadu Computer-Based Testing (CBT), Modul Ajar RPP Kurikulum Merdeka, dan Learning Management System (LMS) SMA Kartika III-1 Banyubiru.

## Arsitektur Repositori

Repositori ini terdiri dari dua komponen utama:

- **`backend-cbt/`**: Aplikasi backend berbasis **Laravel 13 + Filament 3 + PHP 8.4**.
  - Portal utama (Landing Page RPP, LMS & CBT) di `/`
  - Autentikasi terpadu (Siswa via NISN, Guru/Admin via Email) di `/login`
  - Panel Administrasi & Guru di `/admin`
  - RESTful API terstandar untuk integrasi ujian siswa
  - Manajemen Soal, Sesi Ujian, RPP, LMS, dan Analisis Butir Soal
- **`frontend-cbt/`**: Client SPA Ujian Siswa berbasis **React + Tailwind CSS + Vite**.
  - Mode pengerjaan ujian ringkas dan responsif
  - Kompatibel dengan mode Kiosk / Safe Exam

## Akses Produksi (Hostinger)

- **Portal Utama**: [https://cbtkartika.koomit.com](https://cbtkartika.koomit.com)
- **Login Terpadu**: [https://cbtkartika.koomit.com/login](https://cbtkartika.koomit.com/login)
- **Panel Admin / Guru**: [https://cbtkartika.koomit.com/admin](https://cbtkartika.koomit.com/admin)
- **Backend Mirror & API**: [https://ipcbtkartika.koomit.com](https://ipcbtkartika.koomit.com)

## Lisensi & Kepemilikan

Hak cipta milik SMA Kartika III-1 Banyubiru / Yayasan Kartika Jaya.
