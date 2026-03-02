# Agrowisata Bandung - Platform Manajemen Wisata

Agrowisata Bandung adalah platform manajemen destinasi wisata agrikultur di Bandung yang menghubungkan pengelola wisata (Vendor) dengan wisatawan melalui sistem pemesanan tiket yang modern, cepat, dan terintegrasi.

## 🚀 Fitur Utama

### 1. Multi-Tenant System (Role Based Access Control)
Sistem memiliki pemisahan akses yang ketat menggunakan **Spatie Laravel Permission**:
- **Super Admin**: Memiliki kendali penuh atas konfigurasi sistem, manajemen user, wilayah (area), data seluruh vendor, dan laporan konsolidasi.
- **Vendor / Mitra Wisata**: Dashboard khusus untuk mengelola operasional mandiri (Paket Tour, Galeri Foto, Kalender Ketersediaan, Ulasan Pelanggan, dan Laporan Penjualan sendiri).

### 2. Tour & Booking Management
- **Paket Tour Dinamis**: Pengaturan harga, jam operasional, aktivitas, dan galeri foto per paket.
- **Dynamic Pricing Rules**: Sistem diskon otomatis berdasarkan jumlah peserta (Pax) untuk mendukung pemesanan rombongan.
- **Guest Checkout**: Wisatawan dapat memesan tiket dengan cepat tanpa perlu registrasi/login (Quick Booking).
- **Kalender Ketersediaan**: Manajemen kuota harian untuk setiap paket wisata.

### 3. Payment Integration (Midtrans)
- Terintegrasi dengan **Midtrans Snap** untuk berbagai metode pembayaran (VA, E-Wallet, Kartu Kredit).
- **Invoice Otomatis**: Pengiriman invoice resmi ke email pelanggan setelah pembayaran terkonfirmasi.

### 4. Advanced Reporting
- **Sales & Booking Report**: Laporan detail yang mencakup informasi kontak pelanggan (Nama, Email, WhatsApp) untuk kebutuhan operasional vendor.
- **Export Excel**: Mendukung penarikan data laporan ke format Excel.

## 🛠️ Tech Stack
- **Framework**: Laravel 12.x
- **UI/UX**: AdminLTE 4 (Bootstrap 5)
- **Database**: MySQL / MariaDB
- **Library Utama**:
  - `spatie/laravel-permission` (RBAC)
  - `midtrans/midtrans-php` (Payment Gateway)
  - `maatwebsite/excel` (Export Reports)

## ⚙️ Instalasi

1. Clone repository:
   ```bash
   git clone https://github.com/MuhRidwaan/agrowisata-bandung.git
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Konfigurasi Environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Setup Database & Seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Akun Default (Testing):
   - **Super Admin**: `admin@gmail.com` | pass: `password`
   - **Vendor**: `vendor@gmail.com` | pass: `password`

## 📄 Lisensi
Platform ini dikembangkan untuk kebutuhan manajemen Agrowisata di wilayah Bandung dan sekitarnya.
