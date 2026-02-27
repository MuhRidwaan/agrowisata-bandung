@extends('frontend.main')

@section('content')

<!-- ================= HERO ================= -->
<section class="hero-section">
    <picture>
        <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1920&q=80"
            class="hero-bg">
    </picture>

    <div class="hero-overlay"></div>

    <div class="container hero-content text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
            <i class="bi bi-leaf text-accent fs-4"></i>
            <span class="text-white-50 text-uppercase small">Agro Tourism Bandung</span>
            <i class="bi bi-leaf text-accent fs-4"></i>
        </div>

        <h1 class="display-3 fw-bold text-white mb-4">
            Jelajahi Keindahan <br>
            <span class="text-accent">Alam Bandung</span>
        </h1>

        <p class="text-white-50 mb-4" style="max-width:600px; margin:auto;">
            Temukan wisata agro terbaik di Bandung dari kebun teh, strawberry,
            hingga kopi arabika.
        </p>

        <a href="#destinasi" class="btn btn-agro-accent">
            Lihat Destinasi
        </a>
    </div>
</section>

<!-- ================= DESTINASI ================= -->
<section id="destinasi" class="py-5">
    <div class="container py-5">

        <!-- TITLE -->
        <div class="text-center mb-5">
            <span class="text-accent fw-semibold small tracking-widest text-uppercase">Destinasi Populer</span>
            <h2 class="font-display display-5 fw-bold mt-2 mb-3">Wisata Agro Terbaik</h2>
            <p class="text-muted mx-auto" style="max-width: 500px;">
                Pilih destinasi favorit Anda dan pesan tiket secara online dengan mudah dan cepat.
            </p>
        </div>

        <!-- SEARCH -->
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

        <!-- FILTER -->
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4" id="regionFilter" role="tablist"
                aria-label="Filter berdasarkan wilayah">
            <button class="region-pill active" data-filter="semua" role="tab" aria-selected="true">
                <i class="bi bi-grid-3x3-gap"></i> 
                Semua
                <span class="count-badge">{{ $pakets->count() }}</span>
            </button>

            @foreach($areas as $area)
            <button class="region-pill" data-filter="{{ strtolower($area->name) }}">
                 <i class="bi bi-geo-alt"> </i>{{ $area->name }}
                <span class="count-badge">
                    {{ $area->vendors->count() ?? 0 }}
                </span>
            </button>
            @endforeach

        </div>

        <!-- NO RESULT -->
        <div class="text-center py-5 d-none" id="noResults">
            <h5 class="fw-bold">Data tidak ditemukan</h5>
            <p class="text-muted">Coba kata kunci lain</p>
        </div>

        <!-- GRID -->
        <div class="row g-4" id="paketContainer">

            @forelse ($pakets as $paket)
            <div 
                class="col-md-6 col-lg-4 paket-item"
                data-name="{{ strtolower($paket->nama_paket) }}"
                data-area="{{ strtolower($paket->vendor->area->name ?? '') }}"
            >

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">

                    <!-- IMAGE -->
                    <div class="position-relative">
                        <img 
                            src="{{ $paket->photos->first() ? asset('storage/' . $paket->photos->first()->path_foto) : 'https://via.placeholder.com/400x250' }}"
                            class="w-100"
                            style="height:230px; object-fit:cover;"
                        >

                        <span class="badge bg-light text-dark position-absolute top-0 end-0 m-3 rounded-pill px-3 py-2">
                            ⭐ {{ number_format($paket->reviews->avg('rating') ?? 0, 1) }}
                        </span>
                    </div>

                    <!-- BODY -->
                    <div class="p-4">

                        <h5 class="fw-bold mb-2">
                            {{ $paket->nama_paket }}
                        </h5>

                        <!-- LOKASI -->
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                            <i class="bi bi-geo-alt"></i>

                            <span>
                                {{ $paket->vendor->name ?? '-' }}
                            </span>

                            @if($paket->vendor && $paket->vendor->area)
                            <span class="badge bg-success-subtle text-success rounded-pill">
                                {{ $paket->vendor->area->name }}
                            </span>
                            @endif
                        </div>

                        <!-- DESKRIPSI -->
                        <p class="text-muted small mb-3">
                            {{ \Illuminate\Support\Str::limit($paket->deskripsi, 80) }}
                        </p>

                        <!-- JAM -->
                        <div class="text-muted small mb-3">
                            <i class="bi bi-clock"></i>
                            {{ $paket->jam_awal ? \Carbon\Carbon::parse($paket->jam_awal)->format('H:i') : '-' }}
                            -
                            {{ $paket->jam_akhir ? \Carbon\Carbon::parse($paket->jam_akhir)->format('H:i') : '-' }}
                        </div>

                        <!-- HARGA -->
                       <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted d-block">Mulai dari</small>
                                <div class="d-flex align-items-end gap-2">
                                    <span class="fs-4 fw-bold text-primary-agro font-display">
                                        Rp{{ number_format($paket->harga_paket ?? 0, 0, ',', '.') }}
                                    </span>
                                    <small class="text-muted">/orang</small>
                                </div>
                            </div>
                            <a href="{{ route('detail', $paket->id) }}"
                                Class="btn btn-agro-primary">
                                Lihat Detail
                            </a>

                        </div>

                    </div>

                </div>

            </div>
            @empty
                <div class="text-center">
                    <p class="text-muted">Data belum ada</p>
                </div>
            @endforelse

        </div>

    </div>
</section>

<!-- ================= ABOUT ================= -->
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

<!-- ================= STYLE TAMBAHAN ================= -->
<style>
.region-pill {
    border: 1px solid #e5e7eb;
    padding: 10px 18px;
    border-radius: 999px;
    background: #fff;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.region-pill.active {
    background: #2d6a4f;
    color: #fff;
}

.count-badge {
    background: #e9ecef;
    border-radius: 999px;
    padding: 2px 8px;
    font-size: 12px;
}

.region-pill.active .count-badge {
    background: rgba(255,255,255,0.2);
}

.price-text {
    font-size: 24px;
    font-weight: 700;
    color: #2d6a4f;
    font-family: 'Playfair Display', serif;
}

.price-unit {
    font-size: 14px;
    color: #6c757d;
}
</style>

<!-- ================= JS ================= -->
<script>
const searchInput = document.getElementById('searchInput');
const items = document.querySelectorAll('.paket-item');
const buttons = document.querySelectorAll('#regionFilter button');
const noResults = document.getElementById('noResults');

let currentFilter = 'all';

function filterData() {
    let keyword = searchInput.value.toLowerCase();
    let visible = 0;

    items.forEach(item => {
        let name = item.dataset.name;
        let area = item.dataset.area;

        let matchSearch = name.includes(keyword);
        let matchFilter = currentFilter === 'all' || area.includes(currentFilter);

        if (matchSearch && matchFilter) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    noResults.classList.toggle('d-none', visible > 0);
}

searchInput.addEventListener('input', filterData);

buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        currentFilter = btn.dataset.filter;
        filterData();
    });
});
</script>

@endsection