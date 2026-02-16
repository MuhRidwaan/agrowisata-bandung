@extends('frontend.main')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <picture>
            <source srcset="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1920&fm=webp&q=80"
                type="image/webp">
            <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1920&q=80"
                alt="Pemandangan kebun teh hijau di dataran tinggi Bandung dengan pegunungan di latar belakang"
                class="hero-bg" loading="eager" fetchpriority="high">
        </picture>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-4 animate-fade-in">
                <i class="bi bi-leaf text-accent fs-4"></i>
                <span class="text-white-50 tracking-widest text-uppercase small">Agro Tourism Bandung</span>
                <i class="bi bi-leaf text-accent fs-4"></i>
            </div>
            <h1 class="display-3 fw-bold text-white mb-4 animate-fade-in animate-delay-1">
                Jelajahi Keindahan<br>
                <span class="text-accent">Alam Bandung</span>
            </h1>
            <p class="lead text-white-50 mb-5 mx-auto animate-fade-in animate-delay-2" style="max-width: 600px;">
                Temukan wisata agro terbaik di Bandung — dari kebun teh, strawberry, hingga kopi arabika. Pesan tiket online
                dengan mudah.
            </p>
            <a href="#destinasi" class="btn btn-agro-accent animate-fade-in animate-delay-3">Lihat Destinasi</a>
        </div>
    </section>

    <!-- Destinations Section -->
    <section id="destinasi" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-accent fw-semibold small tracking-widest text-uppercase">Destinasi Populer</span>
                <h2 class="font-display display-5 fw-bold mt-2 mb-3">Wisata Agro Terbaik</h2>
                <p class="text-muted mx-auto" style="max-width: 500px;">
                    Pilih destinasi favorit Anda dan pesan tiket secara online dengan mudah dan cepat.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="search-container mb-4">
                <div class="position-relative mx-auto" style="max-width: 500px;">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" id="searchInput"
                        placeholder="Cari destinasi, wilayah, atau aktivitas..." aria-label="Cari destinasi wisata">
                    <button class="search-clear d-none" id="searchClear" aria-label="Hapus pencarian">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Region Filter -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4" id="regionFilter" role="tablist"
                aria-label="Filter berdasarkan wilayah">
                <button class="region-pill active" data-filter="semua" role="tab" aria-selected="true">
                    <i class="bi bi-grid-3x3-gap"></i> Semua
                    <span class="region-count">10</span>
                </button>
                <button class="region-pill" data-filter="bandung-selatan" role="tab" aria-selected="false">
                    <i class="bi bi-geo-alt"></i> Bandung Selatan
                    <span class="region-count">3</span>
                </button>
                <button class="region-pill" data-filter="bandung-barat" role="tab" aria-selected="false">
                    <i class="bi bi-geo-alt"></i> Bandung Barat
                    <span class="region-count">5</span>
                </button>
                <button class="region-pill" data-filter="bandung-utara" role="tab" aria-selected="false">
                    <i class="bi bi-geo-alt"></i> Bandung Utara
                    <span class="region-count">1</span>
                </button>
                <button class="region-pill" data-filter="bandung-timur" role="tab" aria-selected="false">
                    <i class="bi bi-geo-alt"></i> Bandung Timur
                    <span class="region-count">1</span>
                </button>
            </div>

            <!-- No Results -->
            <div class="text-center py-5 d-none" id="noResults">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                <h3 class="font-display fs-4 fw-bold mb-2">Destinasi tidak ditemukan</h3>
                <p class="text-muted">Coba kata kunci lain atau hapus filter untuk melihat semua destinasi.</p>
            </div>

            <div class="row g-4" id="destinationGrid">
                <!-- Card: Kebun Strawberry Ciwidey -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-selatan"
                    data-name="kebun strawberry ciwidey">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=800&q=80"
                                    class="card-img-top"
                                    alt="Kebun strawberry segar di Ciwidey dengan buah merah matang siap petik"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.7</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Kebun Strawberry Ciwidey</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Ciwidey, Bandung Selatan</span>
                                <span class="region-badge">Bandung Selatan</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Nikmati pengalaman memetik strawberry segar langsung dari kebun di dataran tinggi Ciwidey.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Petik Strawberry</span>
                                <span class="badge-activity">Foto Instagramable</span>
                                <span class="badge-activity">Café &amp; Resto</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 08:00 - 17:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp40.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-strawberry.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Kebun Teh Rancabali -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-selatan"
                    data-name="kebun teh rancabali">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80"
                                    class="card-img-top"
                                    alt="Hamparan kebun teh hijau Rancabali dengan pemandangan pegunungan" loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.8</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Kebun Teh Rancabali</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Rancabali, Bandung Selatan</span>
                                <span class="region-badge">Bandung Selatan</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Jelajahi hamparan kebun teh hijau dengan pemandangan pegunungan yang memukau.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Tea Walk</span>
                                <span class="badge-activity">Tea Tasting</span>
                                <span class="badge-activity">Pemandangan Alam</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 07:00 - 17:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp25.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-tea.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Taman Bunga Cihideung -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-barat"
                    data-name="taman bunga cihideung">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=800&q=80"
                                    class="card-img-top"
                                    alt="Taman bunga warna-warni Cihideung dengan berbagai jenis bunga tropis"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.5</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Taman Bunga Cihideung</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Lembang, Bandung Barat</span>
                                <span class="region-badge">Bandung Barat</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Taman bunga tropis dengan berbagai jenis anggrek dan tanaman hias eksotis.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Wisata Bunga</span>
                                <span class="badge-activity">Belanja Tanaman</span>
                                <span class="badge-activity">Edukasi Botani</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 08:00 - 17:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp23.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-flower.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Kopi Arabika Pangalengan -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-selatan"
                    data-name="kopi arabika pangalengan">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800&q=80"
                                    class="card-img-top" alt="Biji kopi arabika Pangalengan yang baru dipetik dari kebun"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.6</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Kopi Arabika Pangalengan</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Pangalengan, Bandung Selatan</span>
                                <span class="region-badge">Bandung Selatan</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Pelajari proses pengolahan kopi arabika dari biji hingga secangkir kopi nikmat.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Coffee Tour</span>
                                <span class="badge-activity">Roasting Workshop</span>
                                <span class="badge-activity">Cupping Session</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 08:00 - 16:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp35.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-coffee.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Kebun Begonia Lembang -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-barat"
                    data-name="kebun begonia lembang">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1508610048659-a06b669e3321?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1508610048659-a06b669e3321?w=800&q=80"
                                    class="card-img-top"
                                    alt="Taman begonia warna-warni dengan berbagai varietas bunga begonia" loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.4</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Kebun Begonia Lembang</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Lembang, Bandung Barat</span>
                                <span class="region-badge">Bandung Barat</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Taman begonia terbesar di Bandung dengan ribuan varietas bunga begonia warna-warni.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Wisata Taman</span>
                                <span class="badge-activity">Foto Bunga</span>
                                <span class="badge-activity">Workshop Tanam</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 08:00 - 17:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp30.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-begonia.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Agrowisata Madu Cimenyan -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-utara"
                    data-name="agrowisata madu cimenyan">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?w=800&q=80"
                                    class="card-img-top"
                                    alt="Peternakan lebah madu dengan sarang lebah dan pemandangan perbukitan"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.3</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Agrowisata Madu Cimenyan</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Cimenyan, Bandung Utara</span>
                                <span class="region-badge">Bandung Utara</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Wisata edukasi peternakan lebah madu alami dengan pemandangan perbukitan Cimenyan.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Panen Madu</span>
                                <span class="badge-activity">Edukasi Lebah</span>
                                <span class="badge-activity">Tasting Madu</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 09:00 - 16:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp25.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-honey.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Kebun Jeruk Lembang -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-barat"
                    data-name="kebun jeruk lembang">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1547514701-42782101795e?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1547514701-42782101795e?w=800&q=80"
                                    class="card-img-top" alt="Kebun jeruk Lembang dengan buah jeruk matang di pohon"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.5</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Kebun Jeruk Lembang</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Lembang, Bandung Barat</span>
                                <span class="region-badge">Bandung Barat</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Petik jeruk segar langsung dari pohonnya di kebun jeruk organik dataran tinggi Lembang.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Petik Jeruk</span>
                                <span class="badge-activity">Juice Bar</span>
                                <span class="badge-activity">Wisata Edukasi</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 08:00 - 16:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp35.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-orange.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Kebun Sayur Organik Cimahi -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-barat"
                    data-name="kebun sayur organik cimahi">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&q=80"
                                    class="card-img-top" alt="Kebun sayur organik dengan sayuran hijau segar di Cimahi"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.2</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Kebun Sayur Organik Cimahi</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Cimahi, Bandung Barat</span>
                                <span class="region-badge">Bandung Barat</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Wisata edukasi pertanian organik modern dengan berbagai jenis sayuran segar tanpa pestisida.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Petik Sayur</span>
                                <span class="badge-activity">Edukasi Organik</span>
                                <span class="badge-activity">Cooking Class</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 07:00 - 15:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp20.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-vegetable.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Wisata Jamur Cisarua -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-barat"
                    data-name="wisata jamur cisarua">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1504545102780-26774c1bb073?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1504545102780-26774c1bb073?w=800&q=80"
                                    class="card-img-top" alt="Budidaya jamur tiram di kumbung jamur Cisarua"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.3</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Wisata Jamur Cisarua</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Cisarua, Bandung Barat</span>
                                <span class="region-badge">Bandung Barat</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Wisata edukasi budidaya jamur dengan aneka olahan jamur khas dataran tinggi Cisarua.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Panen Jamur</span>
                                <span class="badge-activity">Edukasi Budidaya</span>
                                <span class="badge-activity">Kuliner Jamur</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 08:00 - 16:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp25.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-mushroom.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Kebun Cokelat Cicalengka -->
                <div class="col-md-6 col-lg-4 destination-card" data-region="bandung-timur"
                    data-name="kebun cokelat cicalengka">
                    <div class="card card-agro h-100">
                        <div class="card-img-wrapper">
                            <picture>
                                <source
                                    srcset="https://images.unsplash.com/photo-1481391319762-47dff72954d9?w=800&fm=webp&q=80"
                                    type="image/webp">
                                <img src="https://images.unsplash.com/photo-1481391319762-47dff72954d9?w=800&q=80"
                                    class="card-img-top"
                                    alt="Kebun cokelat dengan buah kakao matang dan proses pengolahan cokelat"
                                    loading="lazy">
                            </picture>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span
                                    class="badge bg-light text-dark d-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill star-filled"></i>
                                    <span class="fw-semibold">4.4</span>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title font-display fw-bold fs-5 mb-2">Kebun Cokelat Cicalengka</h5>
                            <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-1"></i> Cicalengka, Bandung Timur</span>
                                <span class="region-badge">Bandung Timur</span>
                            </p>
                            <p class="text-muted small mb-3 line-clamp-2">
                                Jelajahi kebun kakao dan pelajari proses pembuatan cokelat dari biji hingga batangan.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-activity">Chocolate Tour</span>
                                <span class="badge-activity">Making Workshop</span>
                                <span class="badge-activity">Tasting Session</span>
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-clock me-1"></i> 09:00 - 17:00
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="fs-4 fw-bold text-primary-agro font-display">Rp40.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="detail-chocolate.html" class="btn btn-agro-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="py-5 bg-secondary bg-opacity-25">
        <div class="container py-5">
            <div class="text-center mx-auto" style="max-width: 700px;">
                <i class="bi bi-leaf text-primary-agro fs-1 mb-3 d-block"></i>
                <h2 class="font-display display-6 fw-bold mb-4">Tentang AgroBandung</h2>
                <p class="text-muted lead">
                    AgroBandung adalah platform pemesanan tiket wisata agro di kawasan Bandung dan sekitarnya.
                    Kami menghubungkan Anda dengan kebun-kebun terbaik untuk pengalaman wisata alam yang tak terlupakan —
                    dari memetik strawberry segar, berjalan di hamparan teh hijau, hingga menikmati kopi arabika langsung
                    dari sumbernya.
                </p>
            </div>
        </div>
    </section>
@endsection
