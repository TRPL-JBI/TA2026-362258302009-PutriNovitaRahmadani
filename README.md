<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# OutdoorKriss - Sistem Manajemen Franchise Penyewaan Perlengkapan Outdoor

OutdoorKriss merupakan aplikasi berbasis web yang dikembangkan untuk mendukung pengelolaan usaha franchise penyewaan perlengkapan outdoor. Sistem ini mengintegrasikan proses pengelolaan antara owner (franchisor), admin pusat, admin cabang (franchisee), dan penyewa dalam satu platform.

Sistem dibangun untuk mengatasi proses administrasi franchise yang sebelumnya masih dilakukan secara konvensional, seperti pencatatan produk, pelaporan pendapatan cabang, distribusi produk dari pusat ke cabang, perhitungan bagi hasil, serta pengelolaan penyewaan. Dengan sistem ini, seluruh proses dapat dilakukan secara terintegrasi sehingga meningkatkan efisiensi, transparansi, dan kemudahan pengawasan operasional setiap cabang.

Selain mendukung manajemen franchise, sistem juga menyediakan layanan penyewaan secara online. Penyewa dapat melakukan reservasi produk, sedangkan admin cabang mengelola transaksi penyewaan, stok cabang, permintaan produk ke pusat, serta laporan penyewaan. Owner dapat memantau perkembangan seluruh cabang secara real-time melalui dashboard yang terpusat.

---

## Fitur Utama

| Penyewa | Admin Cabang | Admin Pusat | Owner |
|------------|------------------|----------------|-----------|
|  Dashboard |  Dashboard |  Dashboard |  Dashboard |
|  Melihat katalog produk |  Melihat kontrak franchise |  Mengelola data penyewa |  Mengelola data produk |
|  Mencari & memfilter produk |  Mengelola data penyewa |  Mengelola penyewaan skala besar |  Mengelola kategori produk |
|  Melihat detail produk & stok |  Mengelola penyewaan & pengembalian |  Melihat riwayat penyewaan |  Mengelola distribusi produk |
|  Melakukan penyewaan online |  Melihat riwayat penyewaan |  Mengelola data produk |  Mengelola data cabang |
|  Memilih metode pembayaran |  Melihat laporan pendapatan cabang |  Mengelola kategori produk |  Melihat laporan cabang |
|  Melihat status & riwayat penyewaan |  Mengelola data produk cabang |  Mengelola profil admin |  Mengelola sistem bagi hasil franchise |
|  Mengelola profil akun |  Mengirim permintaan produk ke pusat | |  Mengelola profil owner |
| |  Mengelola pembayaran fee (bagi hasil) | | |
| |  Melihat riwayat pembayaran fee | | |
| |  Mengelola profil admin | | |

## Alur Sistem

```text
══════════════════════════════════════════════════════
                     👑 OWNER
══════════════════════════════════════════════════════
            │
            ├── Mengelola Data Cabang
            ├── Mengelola Produk & Kategori
            ├── Mengatur Distribusi Produk
            └── Mengelola Sistem Bagi Hasil
                          │
                          ▼
══════════════════════════════════════════════════════
                  🏢 ADMIN PUSAT
══════════════════════════════════════════════════════
            │
            ├── Mengelola Data Produk
            ├── Mengelola Kategori
            ├── Mengelola Penyewaan Skala Besar
            └── Menyiapkan Produk untuk Cabang
                          │
                          ▼
══════════════════════════════════════════════════════
                 👨‍💼 ADMIN CABANG
══════════════════════════════════════════════════════
            │
            ├── Mengelola Produk Cabang
            ├── Mengelola Data Penyewa
            ├── Mengajukan Permintaan Produk ke Pusat
            ├── Mengelola Penyewaan
            └── Mengelola Pengembalian
                          │
                          ▼
══════════════════════════════════════════════════════
                    👤 PENYEWA
══════════════════════════════════════════════════════
            │
            ├── Registrasi / Login
            ├── Melihat Katalog Produk
            ├── Memilih Produk
            ├── Melakukan Penyewaan
            ├── Memilih Metode Pembayaran
            └── Melihat Status Penyewaan
                          │
                          ▼
══════════════════════════════════════════════════════
                 👨‍💼 ADMIN CABANG
══════════════════════════════════════════════════════
            │
            ├── Verifikasi Penyewaan
            ├── Menyiapkan Barang
            ├── Menyerahkan Barang
            ├── Memproses Pengembalian
            ├── Membuat Laporan Pendapatan
            └── Mengirim Pembayaran Fee Franchise
                          │
                          ▼
══════════════════════════════════════════════════════
                     👑 OWNER
══════════════════════════════════════════════════════
            │
            ├── Memantau Laporan Cabang
            ├── Memverifikasi Bagi Hasil
            ├── Memantau Distribusi Produk
            └── Monitoring Seluruh Cabang
```

---

## Teknologi yang Digunakan

| Teknologi                  | Kegunaan                                   |
| -------------------------- | ------------------------------------------ |
| **Laravel**                | Framework backend                          |
| **PHP**                    | Bahasa pemrograman                         |
| **MySQL**                  | Database                                   |
| **HTML, CSS & JavaScript** | Antarmuka pengguna                         |
| **API Fonnte**             | Integrasi notifikasi WhatsApp dan kode OTP |

## Modul Sistem

```text
Dashboard
│
├── Autentikasi
│   ├── Login
│   └── Registrasi
│
├── Penyewa
│   ├── Dashboard
│   ├── Profil
│   ├── Katalog Produk
│   ├── Penyewaan
│   └── Riwayat Penyewaan
│
├── Admin Cabang
│   ├── Dashboard
│   ├── Kontrak Franchise
│   ├── Data Penyewa
│   ├── Produk Cabang
│   ├── Penyewaan
│   ├── Riwayat Penyewaan
│   ├── Laporan Pendapatan
│   ├── Permintaan Produk
│   ├── Bagi Hasil
│   └── Profil
│
├── Admin Pusat
│   ├── Dashboard
│   ├── Data Penyewa
│   ├── Penyewaan Skala Besar
│   ├── Riwayat Penyewaan
│   ├── Data Produk
│   ├── Kategori Produk
│   └── Profil
│
└── Owner
    ├── Dashboard
    ├── Data Cabang
    ├── Data Produk
    ├── Kategori Produk
    ├── Distribusi Produk
    ├── Laporan Cabang
    ├── Bagi Hasil
    └── Profil
```

---
## Cara Menjalankan Project

### Clone Repository

```bash
git clone https://github.com/TRPL-JBI/TA2026-362258302090-NurWeldaSari.git
```

Masuk ke folder project

```bash
cd TA2026-362258302090-NurWeldaSari
```

Install dependency

```bash
composer install
```

Copy file environment

```bash
cp .env.example .env
```

Generate key aplikasi

```bash
php artisan key:generate
```

Konfigurasi database pada file `.env`

```env
DB_DATABASE=outdoor
DB_USERNAME=root
DB_PASSWORD=
```
```env
SESSION_DRIVER=file
```
Integrasi Whats'App

```env
FONTE_TOKEN=WMkbYQWPcYKWKWhxoZTR
FONTE_URL=https://api.fonnte.com/send
```

Jalankan migrasi

```bash
php artisan migrate
```

Jalankan seeder

```bash
php artisan db:seed
```

Jalankan server

```bash
php artisan serve
```

Akses aplikasi melalui

```
http://127.0.0.1:8000
```

---
## 👩‍💻 Pengembang

**Putri Novita Rahmadani**

Program Studi D4 Teknologi Rekayasa Perangkat Lunak

Politeknik Negeri Banyuwangi

---
