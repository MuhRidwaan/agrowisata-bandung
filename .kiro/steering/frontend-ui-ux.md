# Frontend UI/UX Improvement Guide — Agrowisata Bandung

## Tujuan
Merapihkan dan meningkatkan UI/UX seluruh halaman frontend agar nyaman digunakan di HP maupun laptop, dengan tampilan yang bersih, modern, dan user-friendly.

## Stack Frontend
- Bootstrap 5.3.3
- Bootstrap Icons 1.11.3
- Google Fonts: Playfair Display (heading) + DM Sans (body)
- CSS Variables di `public/frontend/css/style.css`
- Booking logic di `public/frontend/js/booking.js`
- Layout utama: `resources/views/frontend/main.blade.php`
- Layouts: `nav.blade.php`, `header.blade.php`, `footer.blade.php`

## Halaman Frontend yang Perlu Diperbaiki (Prioritas)
1. `home.blade.php` — Homepage (hero, listing paket, filter area)
2. `detail.blade.php` — Detail paket (galeri, info, pricing, review)
3. `booking.blade.php` — Booking wizard 4-step
4. `resume_payment.blade.php` — Lanjutkan pembayaran
5. `invoice.blade.php` — Invoice publik customer

## CSS Variables (Wajib Dipakai)
```css
--agro-primary: #2d6a4f
--agro-primary-light: #40916c
--agro-accent: #d4a24c
--agro-bg: #f8f9f7
--agro-text: #1a2e23
--agro-text-muted: #6c757d
--font-display: 'Playfair Display', serif
--font-body: 'DM Sans', sans-serif
```

---

## Prinsip Desain

### 1. Mobile-First
- Semua layout harus nyaman di layar 375px ke atas
- Touch target minimal 44x44px untuk semua tombol interaktif
- Hindari elemen yang terlalu kecil di mobile (font min 14px, button min 44px height)
- Gunakan `col-12` sebagai default, baru `col-md-*` / `col-lg-*` untuk desktop

### 2. Konsistensi Visual
- Selalu gunakan CSS variables yang sudah ada
- Radius card: `border-radius: 1rem` (16px) untuk card utama, `0.75rem` untuk elemen dalam
- Shadow: gunakan `var(--shadow-card)` dan `var(--shadow-card-hover)`
- Spacing: gunakan Bootstrap spacing utilities (p-4, gap-3, mb-4, dll)
- Warna tombol utama: `btn-agro-primary` (hijau), aksen: `btn-agro-accent` (emas)

### 3. Typography
- Heading: `font-display` (Playfair Display)
- Body: `font-body` (DM Sans)
- Ukuran minimum body text: 14px
- Line height body: 1.6

### 4. Feedback & State
- Semua tombol harus punya hover state yang jelas
- Loading state: gunakan class `.loading` yang sudah ada di style.css
- Error/success: gunakan SweetAlert2 (sudah ada di footer)
- Form validation: tampilkan pesan error inline, bukan hanya alert

---

## Saran Perbaikan Per Halaman

### HOME (`home.blade.php`)
- **Hero**: Pastikan teks terbaca di mobile, CTA button cukup besar (min 48px height)
- **Filter pills**: Buat scrollable horizontal di mobile (`overflow-x: auto; white-space: nowrap`)
- **Search bar**: Sudah bagus, pastikan keyboard tidak menutupi input di iOS
- **Card paket**: Tinggi gambar konsisten (230px desktop, 200px mobile), tambah `loading="lazy"` pada gambar
- **Card grid**: `col-12` di mobile, `col-md-6` di tablet, `col-lg-4` di desktop
- **Rating badge**: Posisi sudah bagus (top-right), pastikan kontras warna cukup
- **Aktivitas badges**: Batasi tampilan di card (max 3 badge, sisanya "+N lagi")
- **Harga**: Tampilkan dengan jelas, font besar dan bold
- **CTA "Lihat Detail"**: Tombol harus full-width di mobile atau minimal 44px height

### DETAIL (`detail.blade.php`)
- **Sticky header**: Sudah ada, pastikan tidak overlap konten
- **Galeri foto**: 
  - Tambah swipe gesture di mobile (touch events)
  - Thumbnail sembunyikan di mobile (sudah ada di CSS)
  - Aspect ratio 16:9 di mobile, 16:7 di desktop
- **Info paket**: Gunakan icon + teks, jangan hanya teks
- **Pricing rules**: Card diskon harus jelas dan menarik
- **Sticky booking card** (kanan): Di mobile, ubah jadi fixed bottom bar dengan tombol "Beli Tiket"
- **Review section**: 
  - Avatar dengan initial huruf sudah bagus
  - Batasi teks review (line-clamp) dengan tombol "Baca selengkapnya"
  - Form review: validasi real-time sudah ada, pertahankan
- **Minimum person notice**: Tampilkan dengan warna warning yang jelas

### BOOKING (`booking.blade.php`)
- **Stepper**: 
  - Di mobile, tampilkan hanya nomor + label aktif (sembunyikan label step lain)
  - Garis penghubung harus proporsional
- **Kalender custom**:
  - Ukuran cell kalender harus cukup besar di mobile (min 40px)
  - Legenda kalender harus jelas
  - Animasi buka/tutup kalender
- **Step 1 (Pilih Tiket)**:
  - Bundling cards: tampilkan dengan gambar jika ada
  - UMKM products: tombol +/- harus cukup besar (44px)
  - Pricing rules: highlight rule yang aktif berdasarkan jumlah peserta
- **Step 2 (Data Peserta)**:
  - Input jumlah peserta: tombol +/- harus besar dan jelas
  - Validasi email real-time
  - Keyboard type yang tepat (tel untuk telepon, email untuk email)
- **Step 3 (Pembayaran)**:
  - Payment method card: visual yang jelas mana yang dipilih
  - Tambah ikon bank/wallet yang lebih representatif
- **Step 4 (Konfirmasi)**:
  - Kode booking: font besar, mudah disalin
  - Tombol "Lanjutkan Pembayaran" harus prominent
- **Order Summary (sidebar)**:
  - Di mobile: tampilkan sebagai collapsible panel di bawah stepper
  - Di desktop: sticky di kanan

### RESUME PAYMENT (`resume_payment.blade.php`)
- Layout sudah cukup baik, perbaikan:
  - Tambah progress indicator status pembayaran
  - Upload bukti transfer: tambah preview gambar sebelum submit
  - Instruksi pembayaran: tampilkan lebih visual (icon bank, highlight nomor rekening)
  - Tombol aksi: lebih prominent dan jelas hierarkinya

### INVOICE (`invoice.blade.php`)
- Standalone page (tidak extend main), perbaikan:
  - Responsive di mobile
  - Status badge lebih besar dan jelas
  - Tabel invoice: scroll horizontal di mobile
  - Tombol print: sembunyikan di mobile atau ganti dengan "Download PDF"
  - WhatsApp button: lebih prominent
  - Upload bukti transfer: tambah preview

---

## Komponen yang Perlu Dibuat/Diperbaiki

### Navbar
- Tambah backdrop blur yang lebih kuat saat scroll
- Active state untuk link navigasi
- Di mobile: hamburger menu dengan animasi smooth

### Footer
- Sudah cukup baik, pastikan responsive di mobile
- Tambah social media links jika ada

### Floating Elements
- Pending booking notification (sudah ada): perbaiki posisi di mobile
- Scroll-to-top button: tambahkan untuk halaman panjang

---

## Checklist Responsivitas

### Breakpoints Bootstrap
- `xs`: < 576px (HP kecil)
- `sm`: ≥ 576px (HP besar)
- `md`: ≥ 768px (Tablet)
- `lg`: ≥ 992px (Laptop)
- `xl`: ≥ 1200px (Desktop)

### Yang Harus Dicek di Setiap Halaman
- [ ] Tidak ada horizontal scroll yang tidak disengaja
- [ ] Semua teks terbaca (min 14px, kontras cukup)
- [ ] Semua tombol bisa diklik dengan jari (min 44x44px)
- [ ] Form input tidak terlalu kecil di mobile
- [ ] Gambar tidak overflow container
- [ ] Spacing antar elemen cukup (tidak terlalu rapat)
- [ ] Loading state pada semua aksi async

---

## Urutan Pengerjaan (Bertahap)

### Fase 1 — Fondasi & Komponen Global
1. Perbaiki `nav.blade.php` — navbar responsif, smooth scroll, active state
2. Perbaiki `footer.blade.php` — layout mobile
3. Update `style.css` — tambah utility classes yang dibutuhkan

### Fase 2 — Homepage
4. Perbaiki `home.blade.php` — hero, filter, card grid

### Fase 3 — Detail Paket
5. Perbaiki `detail.blade.php` — galeri, sticky booking card mobile, review

### Fase 4 — Booking Flow
6. Perbaiki `booking.blade.php` — stepper, kalender, steps, order summary

### Fase 5 — Payment Pages
7. Perbaiki `resume_payment.blade.php`
8. Perbaiki `invoice.blade.php`

---

## Hal yang JANGAN Diubah
- Logic PHP/Blade (controller, model, route) — hanya UI
- Nama class CSS yang dipakai di `booking.js` (bisa menyebabkan bug)
- Struktur data yang dikirim ke JS (`window.BOOKING_CONFIG`, `window.AVAILABLE_DATES`)
- CSRF token dan form action
- Nama ID elemen yang dipakai di `booking.js` (misal: `#visitDate`, `#customerName`, dll)

## ID Penting di booking.js (JANGAN DIUBAH)
- `#paketTourId`, `#visitDate`, `#visitDateSisa`
- `#calendarToggle`, `#customCalendar`, `#calendarBody`
- `#customerName`, `#customerEmail`, `#customerPhone`
- `#participantCountInput`, `#participantTotal`
- `#bookingStep1`, `#bookingStep2`, `#bookingStep3`, `#bookingStep4`
- `#bookingCode`, `#waitingTotal`, `#paymentMethodName`
- `#umkmData`, `#qty-{id}`
- `.booking-stepper-item`, `.booking-stepper-line`, `.booking-stepper-circle`
- `.payment-method-card`, `.bundling-card`, `.discount-card`
