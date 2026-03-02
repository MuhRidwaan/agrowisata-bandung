<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Vendor;
use App\Models\PaketTour;
use App\Models\PricingRule;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SEED AREAS
        $areas = [
            ['name' => 'Lembang'],
            ['name' => 'Ciwidey'],
            ['name' => 'Pangalengan'],
            ['name' => 'Dago'],
            ['name' => 'Punclut'],
            ['name' => 'Maribaya'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(['name' => $area['name']], $area);
        }

        $lembang = Area::where('name', 'Lembang')->first();
        $ciwidey = Area::where('name', 'Ciwidey')->first();
        $pangalengan = Area::where('name', 'Pangalengan')->first();
        $dago = Area::where('name', 'Dago')->first();
        $punclut = Area::where('name', 'Punclut')->first();
        $maribaya = Area::where('name', 'Maribaya')->first();

        $vendorUser = \App\Models\User::where('email', 'vendor@gmail.com')->first();

        // 2. SEED VENDORS
        $vendors = [
            ['name' => 'Agro Lembang Sejahtera', 'email' => 'contact@agrolembang.com', 'phone' => '081234567891', 'address' => 'Jl. Maribaya No. 123, Lembang', 'area_id' => $lembang->id, 'status' => 'active', 'user_id' => $vendorUser->id],
            ['name' => 'Ciwidey Green Tour', 'email' => 'info@ciwideygreen.com', 'phone' => '081234567892', 'address' => 'Jl. Raya Ciwidey KM 5, Ciwidey', 'area_id' => $ciwidey->id, 'status' => 'active'],
            ['name' => 'Dago Adventure', 'email' => 'hello@dagoadventure.com', 'phone' => '081234567893', 'address' => 'Dago Atas, Bandung', 'area_id' => $dago->id, 'status' => 'active'],
            ['name' => 'Punclut View Tour', 'email' => 'booking@punclutview.com', 'phone' => '081234567894', 'address' => 'Ciumbuleuit, Bandung', 'area_id' => $punclut->id, 'status' => 'active'],
            ['name' => 'Maribaya Waterfall', 'email' => 'info@maribaya.com', 'phone' => '081234567895', 'address' => 'Lembang, Bandung', 'area_id' => $maribaya->id, 'status' => 'active'],
            ['name' => 'Pangalengan Milk Tour', 'email' => 'susu@pangalengan.com', 'phone' => '081234567896', 'address' => 'Situ Cileunca, Pangalengan', 'area_id' => $pangalengan->id, 'status' => 'active'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(['email' => $vendor['email']], $vendor);
        }

        $vLembang = Vendor::where('email', 'contact@agrolembang.com')->first();
        $vCiwidey = Vendor::where('email', 'info@ciwideygreen.com')->first();
        $vDago = Vendor::where('email', 'hello@dagoadventure.com')->first();
        $vPunclut = Vendor::where('email', 'booking@punclutview.com')->first();
        $vMaribaya = Vendor::where('email', 'info@maribaya.com')->first();
        $vPangalengan = Vendor::where('email', 'susu@pangalengan.com')->first();

        // 3. SEED PAKET TOURS (Tambah 15 data baru)
        $pakets = [
            // Lembang
            ['nama_paket' => 'Edukasi Petik Strawberry Lembang', 'harga_paket' => 75000, 'vendor_id' => $vLembang->id, 'jam_awal' => '08:00:00', 'jam_akhir' => '16:00:00', 'kuota' => 50, 'aktivitas' => ['Tour Kebun', 'Petik Buah'], 'deskripsi' => 'Nikmati pengalaman memetik buah strawberry segar.'],
            ['nama_paket' => 'Berkuda di DeRanch', 'harga_paket' => 50000, 'vendor_id' => $vLembang->id, 'jam_awal' => '09:00:00', 'jam_akhir' => '17:00:00', 'kuota' => 30, 'aktivitas' => ['Berkuda', 'Kostum Koboi'], 'deskripsi' => 'Pengalaman berkuda seru dengan kostum koboi.'],
            ['nama_paket' => 'Floating Market Culinary', 'harga_paket' => 35000, 'vendor_id' => $vLembang->id, 'jam_awal' => '09:00:00', 'jam_akhir' => '18:00:00', 'kuota' => 100, 'aktivitas' => ['Kuliner', 'Perahu'], 'deskripsi' => 'Jelajahi kuliner di pasar terapung.'],
            
            // Ciwidey
            ['nama_paket' => 'Tea Walk & Factory Visit Ciwidey', 'harga_paket' => 120000, 'vendor_id' => $vCiwidey->id, 'jam_awal' => '09:00:00', 'jam_akhir' => '15:00:00', 'kuota' => 30, 'aktivitas' => ['Tea Walking', 'Pabrik Tour'], 'deskripsi' => 'Berjalan di kebun teh dan kunjungan pabrik.'],
            ['nama_paket' => 'Kawah Putih Exploration', 'harga_paket' => 85000, 'vendor_id' => $vCiwidey->id, 'jam_awal' => '07:00:00', 'jam_akhir' => '16:00:00', 'kuota' => 40, 'aktivitas' => ['Jelajah Kawah', 'Ontang-anting'], 'deskripsi' => 'Eksplorasi keindahan Kawah Putih.'],
            ['nama_paket' => 'Petik Sayur Organik', 'harga_paket' => 45000, 'vendor_id' => $vCiwidey->id, 'jam_awal' => '08:00:00', 'jam_akhir' => '14:00:00', 'kuota' => 20, 'aktivitas' => ['Edukasi Tani', 'Petik Sayur'], 'deskripsi' => 'Belajar bertani sayur organik.'],

            // Dago
            ['nama_paket' => 'Dago Nature Walk', 'harga_paket' => 60000, 'vendor_id' => $vDago->id, 'jam_awal' => '06:00:00', 'jam_akhir' => '10:00:00', 'kuota' => 25, 'aktivitas' => ['Walking', 'Breakfast'], 'deskripsi' => 'Jalan pagi menikmati udara Dago.'],
            ['nama_paket' => 'Workshop Batik Dago', 'harga_paket' => 150000, 'vendor_id' => $vDago->id, 'jam_awal' => '10:00:00', 'jam_akhir' => '15:00:00', 'kuota' => 15, 'aktivitas' => ['Membatik', 'Karya Sendiri'], 'deskripsi' => 'Belajar membatik khas Bandung.'],
            ['nama_paket' => 'Night City View Dago', 'harga_paket' => 40000, 'vendor_id' => $vDago->id, 'jam_awal' => '18:00:00', 'jam_akhir' => '22:00:00', 'kuota' => 50, 'aktivitas' => ['City View', 'Dinner'], 'deskripsi' => 'Menikmati pemandangan kota Bandung di malam hari.'],

            // Punclut
            ['nama_paket' => 'Edukasi Kopi Punclut', 'harga_paket' => 95000, 'vendor_id' => $vPunclut->id, 'jam_awal' => '10:00:00', 'jam_akhir' => '16:00:00', 'kuota' => 20, 'aktivitas' => ['Roasting', 'Brewing'], 'deskripsi' => 'Belajar proses kopi dari biji hingga seduh.'],
            ['nama_paket' => 'Petik Jeruk Punclut', 'harga_paket' => 55000, 'vendor_id' => $vPunclut->id, 'jam_awal' => '08:00:00', 'jam_akhir' => '15:00:00', 'kuota' => 35, 'aktivitas' => ['Petik Jeruk', 'Jus Segar'], 'deskripsi' => 'Memetik jeruk segar di bukit Punclut.'],
            ['nama_paket' => 'Camping Ground Experience', 'harga_paket' => 250000, 'vendor_id' => $vPunclut->id, 'jam_awal' => '14:00:00', 'jam_akhir' => '11:00:00', 'kuota' => 10, 'aktivitas' => ['Camping', 'Api Unggun'], 'deskripsi' => 'Pengalaman berkemah dengan fasilitas lengkap.'],

            // Maribaya
            ['nama_paket' => 'Trekking Curug Maribaya', 'harga_paket' => 70000, 'vendor_id' => $vMaribaya->id, 'jam_awal' => '07:00:00', 'jam_akhir' => '12:00:00', 'kuota' => 30, 'aktivitas' => ['Trekking', 'Pemandian Air Panas'], 'deskripsi' => 'Trekking menuju air terjun Maribaya.'],
            ['nama_paket' => 'Eco Park Education', 'harga_paket' => 45000, 'vendor_id' => $vMaribaya->id, 'jam_awal' => '09:00:00', 'jam_akhir' => '16:00:00', 'kuota' => 60, 'aktivitas' => ['Feeding Bird', 'Sky Bridge'], 'deskripsi' => 'Edukasi lingkungan di Eco Park.'],
            ['nama_paket' => 'Offroad Adventure', 'harga_paket' => 350000, 'vendor_id' => $vMaribaya->id, 'jam_awal' => '08:00:00', 'jam_akhir' => '14:00:00', 'kuota' => 12, 'aktivitas' => ['Jeep Offroad', 'Lunch'], 'deskripsi' => 'Petualangan offroad di hutan Maribaya.'],

            // Pangalengan
            ['nama_paket' => 'Perah Susu Sapi', 'harga_paket' => 40000, 'vendor_id' => $vPangalengan->id, 'jam_awal' => '06:00:00', 'jam_akhir' => '09:00:00', 'kuota' => 20, 'aktivitas' => ['Memerah Susu', 'Minum Susu'], 'deskripsi' => 'Belajar memerah susu sapi segar.'],
            ['nama_paket' => 'Rafting Situ Cileunca', 'harga_paket' => 185000, 'vendor_id' => $vPangalengan->id, 'jam_awal' => '09:00:00', 'jam_akhir' => '14:00:00', 'kuota' => 24, 'aktivitas' => ['Rafting', 'Safety Gear'], 'deskripsi' => 'Arung jeram seru di sungai Palayangan.'],
        ];

        foreach ($pakets as $p) {
            PaketTour::updateOrCreate(['nama_paket' => $p['nama_paket']], $p);
        }

        // 4. SEED PRICING RULES (Contoh untuk beberapa paket)
        $p1 = PaketTour::where('nama_paket', 'Edukasi Petik Strawberry Lembang')->first();
        $p2 = PaketTour::where('nama_paket', 'Tea Walk & Factory Visit Ciwidey')->first();
        $p3 = PaketTour::where('nama_paket', 'Rafting Situ Cileunca')->first();

        $rules = [
            ['paket_tour_id' => $p1->id, 'min_pax' => 5, 'max_pax' => 10, 'discount_type' => 'percent', 'discount_value' => 10, 'description' => 'Grup Kecil 10%'],
            ['paket_tour_id' => $p1->id, 'min_pax' => 11, 'max_pax' => 999, 'discount_type' => 'percent', 'discount_value' => 20, 'description' => 'Grup Besar 20%'],
            ['paket_tour_id' => $p2->id, 'min_pax' => 10, 'max_pax' => 999, 'discount_type' => 'nominal', 'discount_value' => 50000, 'description' => 'Potongan 50rb'],
            ['paket_tour_id' => $p3->id, 'min_pax' => 6, 'max_pax' => 999, 'discount_type' => 'percent', 'discount_value' => 15, 'description' => 'Rafting Group 15%'],
        ];

        foreach ($rules as $r) {
            PricingRule::updateOrCreate(['paket_tour_id' => $r['paket_tour_id'], 'min_pax' => $r['min_pax']], $r);
        }
    }
}
