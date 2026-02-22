@extends('frontend.main')

@section('content')
    <!-- Header -->
    <header class="bg-white border-bottom position-sticky top-0" style="z-index: 1000;">
        <div class="container">
            <div class="d-flex align-items-center gap-3 py-3">
                <a href="index.html" class="btn btn-light rounded-circle p-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-leaf text-primary-agro"></i>
                    <span class="font-display fs-5 fw-bold">Detail Paket</span>
                </div>
            </div>
        </div>
    </header>

    <div class="container py-4">
        <!-- Photo Gallery -->
        <div class="mb-4">
            <div class="gallery-main bg-secondary" id="mainGallery">
                <picture>
                    <source srcset="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=1200&fm=webp&q=80"
                        type="image/webp">
                    <img src="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=1200&q=80"
                        alt="Kebun Strawberry Ciwidey dengan buah strawberry merah segar" id="mainImage" loading="eager">
                </picture>
                <button class="gallery-nav-btn prev" onclick="prevImage()" aria-label="Gambar sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="gallery-nav-btn next" onclick="nextImage()" aria-label="Gambar selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <div class="gallery-dots" role="tablist" aria-label="Galeri gambar">
                    <button class="gallery-dot active" onclick="setImage(0)" aria-label="Gambar 1" role="tab"
                        aria-selected="true"></button>
                    <button class="gallery-dot" onclick="setImage(1)" aria-label="Gambar 2" role="tab"
                        aria-selected="false"></button>
                    <button class="gallery-dot" onclick="setImage(2)" aria-label="Gambar 3" role="tab"
                        aria-selected="false"></button>
                </div>
            </div>
            <div class="gallery-thumbs">
                <button class="gallery-thumb active" onclick="setImage(0)" aria-label="Lihat gambar 1">
                    <picture>
                        <source srcset="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=200&fm=webp&q=80"
                            type="image/webp">
                        <img src="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=200&q=80"
                            alt="Thumbnail kebun strawberry" loading="lazy">
                    </picture>
                </button>
                <button class="gallery-thumb" onclick="setImage(1)" aria-label="Lihat gambar 2">
                    <picture>
                        <source srcset="https://images.unsplash.com/photo-1587393855524-087f83d95bc9?w=200&fm=webp&q=80"
                            type="image/webp">
                        <img src="https://images.unsplash.com/photo-1587393855524-087f83d95bc9?w=200&q=80"
                            alt="Thumbnail aktivitas petik strawberry" loading="lazy">
                    </picture>
                </button>
                <button class="gallery-thumb" onclick="setImage(2)" aria-label="Lihat gambar 3">
                    <picture>
                        <source srcset="https://images.unsplash.com/photo-1518635017498-87f514b751ba?w=200&fm=webp&q=80"
                            type="image/webp">
                        <img src="https://images.unsplash.com/photo-1518635017498-87f514b751ba?w=200&q=80"
                            alt="Thumbnail pemandangan kebun" loading="lazy">
                    </picture>
                </button>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="d-flex flex-column gap-4">
                    <!-- Title & Info -->
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <h1 class="font-display display-6 fw-bold mb-3">Kebun Strawberry Ciwidey</h1>
                            <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                <span class="d-flex align-items-center gap-1">
                                    <i class="bi bi-geo-alt"></i> Ciwidey, Bandung Selatan
                                </span>
                                <span class="d-flex align-items-center gap-1">
                                    <i class="bi bi-clock"></i> 08:00 - 17:00
                                </span>
                                <span class="d-flex align-items-center gap-1">
                                    <i class="bi bi-star-fill star-filled"></i> 4.7 (3 ulasan)
                                </span>
                            </div>
                            <p class="text-muted">
                                Kebun Strawberry Ciwidey terletak di dataran tinggi Ciwidey dengan ketinggian 1.200 mdpl.
                                Pengunjung dapat menikmati pengalaman memetik strawberry segar langsung dari kebun,
                                menikmati hidangan berbahan dasar strawberry di café, serta berfoto di spot-spot
                                instagramable yang tersedia.
                                Kebun ini memiliki varietas strawberry lokal dan impor yang ditanam secara organik tanpa
                                pestisida berbahaya.
                            </p>
                        </div>
                    </div>

                    <!-- Activities -->
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <h3 class="font-display fs-5 fw-semibold mb-3">Aktivitas</h3>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge-primary-light">Petik Strawberry</span>
                                <span class="badge-primary-light">Foto Instagramable</span>
                                <span class="badge-primary-light">Café & Resto</span>
                            </div>
                        </div>
                    </div>

                    <!-- Includes -->
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <h3 class="font-display fs-5 fw-semibold mb-3">Yang Termasuk</h3>
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <i class="bi bi-check-circle-fill text-primary-agro"></i>
                                        <span>Tiket masuk</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <i class="bi bi-check-circle-fill text-primary-agro"></i>
                                        <span>1 keranjang strawberry (250gr)</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <i class="bi bi-check-circle-fill text-primary-agro"></i>
                                        <span>Akses area foto</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <i class="bi bi-check-circle-fill text-primary-agro"></i>
                                        <span>Parkir gratis</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Volume Discounts -->
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <h3 class="font-display fs-5 fw-semibold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-tag-fill text-accent"></i> Harga Grup
                            </h3>
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="price-card text-center">
                                        <p class="text-muted small mb-1">1-4 orang</p>
                                        <p class="font-display fs-4 fw-bold text-primary-agro mb-0">Rp50.000</p>
                                        <p class="text-muted small mb-0">/orang</p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="price-card text-center">
                                        <p class="text-muted small mb-1">5-9 orang</p>
                                        <p class="font-display fs-4 fw-bold text-primary-agro mb-0">Rp45.000</p>
                                        <p class="text-muted small mb-0">/orang</p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="price-card text-center">
                                        <p class="text-muted small mb-1">10+ orang</p>
                                        <p class="font-display fs-4 fw-bold text-primary-agro mb-0">Rp40.000</p>
                                        <p class="text-muted small mb-0">/orang</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted small mt-3 mb-0 d-flex align-items-center gap-1">
                                <i class="bi bi-people"></i> Semakin banyak peserta, harga per orang semakin murah!
                            </p>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <h3 class="font-display fs-5 fw-semibold mb-4">Ulasan Pengunjung (3)</h3>
                            <div class="d-flex flex-column gap-3">
                                <!-- Review 1 -->
                                <div class="p-3 rounded-3 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary-agro bg-agro-light"
                                                style="width: 36px; height: 36px;">S</div>
                                            <div>
                                                <p class="fw-medium small mb-0">Sari Dewi</p>
                                                <p class="text-muted small mb-0">15 Desember 2025</p>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0">Strawberry-nya segar banget! Anak-anak suka sekali.
                                        Tempatnya juga bersih dan terawat.</p>
                                </div>
                                <!-- Review 2 -->
                                <div class="p-3 rounded-3 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary-agro bg-agro-light"
                                                style="width: 36px; height: 36px;">B</div>
                                            <div>
                                                <p class="fw-medium small mb-0">Budi Hartono</p>
                                                <p class="text-muted small mb-0">20 November 2025</p>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star small text-muted"></i>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0">Pemandangan bagus, cocok untuk keluarga. Cuma parkir
                                        agak jauh dari kebun.</p>
                                </div>
                                <!-- Review 3 -->
                                <div class="p-3 rounded-3 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary-agro bg-agro-light"
                                                style="width: 36px; height: 36px;">R</div>
                                            <div>
                                                <p class="fw-medium small mb-0">Rina Safitri</p>
                                                <p class="text-muted small mb-0">10 Oktober 2025</p>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                            <i class="bi bi-star-fill star-filled small"></i>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0">Harga terjangkau untuk pengalaman yang luar biasa.
                                        Café-nya juga enak!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 80px;">
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <p class="text-muted small mb-1">Harga mulai dari</p>
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="font-display display-6 fw-bold text-primary-agro">Rp40.000</span>
                                    <span class="text-muted small">/orang</span>
                                </div>
                            </div>

                            <div class="bg-agro-light rounded-3 p-3 mb-4 d-flex gap-2">
                                <i class="bi bi-shield-check text-primary-agro flex-shrink-0"></i>
                                <p class="text-muted small mb-0">
                                    Pemesanan harus dilakukan minimal 24 jam sebelum jadwal kunjungan.
                                </p>
                            </div>

                            <a href="booking-strawberry.html" class="btn btn-agro-primary w-100 mb-3">Beli Tiket</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
