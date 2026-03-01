@extends('frontend.main')

@section('content')

<!-- ================= HERO ================= -->
<section class="hero-section">
    <picture>
        <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1920&q=80"
            class="hero-bg">
    </picture>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-4 animate-fade-in">
            <i class="bi bi-leaf text-accent fs-4"></i>
            <span class="text-white-50 tracking-widest text-uppercase small">Agro Tourism Bandung</span>
            <i class="bi bi-leaf text-accent fs-4"></i>
        </div>
        <h1 class="display-3 fw-bold text-white mb-4 animate-fade-in animate-delay-1">
            Jelajahi Keindahan <br>
            <span class="text-accent">Alam Bandung</span>
        </h1>
        <p class="lead text-white-50 mb-5 mx-auto animate-fade-in animate-delay-2" style="max-width: 600px;">
            Temukan wisata agro terbaik di Bandung dari kebun teh, strawberry,
            hingga kopi arabika.
        </p>
        <a href="#destinasi" class="btn btn-agro-accent animate-fade-in animate-delay-3">Lihat Destinasi</a>
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

        <!-- FORM (TETAP SAMA) -->
        <form method="GET">

            <!-- SEARCH -->
            <div class="search-container mb-4">
                <div class="position-relative mx-auto" style="max-width: 500px;">
                    <i class="bi bi-search search-icon"></i>

                    <input 
                        type="text" 
                        id="searchInput"
                        name="search"
                        class="search-input"
                        placeholder="Cari destinasi, wilayah, atau aktivitas..."
                        value="{{ request('search') }}"
                    >

                    <button type="submit" class="search-clear d-none">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- FILTER -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">

                <button type="submit" name="area" value="all"
                    class="region-pill active">
                    <i class="bi bi-grid-3x3-gap"></i> 
                    Semua
                    <span class="count-badge">{{ $pakets->count() }}</span>
                </button>

                @foreach($areas as $area)
                <button type="submit" name="area" value="{{ $area->name }}"
                    class="region-pill">
                    <i class="bi bi-geo-alt"></i> {{ $area->name }}
                    <span class="count-badge">
                        {{ $area->vendors->count() ?? 0 }}
                    </span>
                </button>
                @endforeach

            </div>

        </form>

        <!-- GRID -->
        <div class="row g-4">

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

                        <p class="text-muted small mb-3">
                            {{ \Illuminate\Support\Str::limit($paket->deskripsi, 80) }}
                        </p>

                        <!-- ================= AKTIVITAS ================= -->
                        <div class="mb-3">
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($paket->aktivitas ?? [] as $item)
                                    <span 
                                        style="
                                            background:#e5e7eb;
                                            color:#1f2937;
                                            padding:6px 14px;
                                            border-radius:999px;
                                            font-size:13px;
                                            font-weight:500;
                                        ">
                                        {{ $item }}
                                    </span>
                                @empty
                                    <span class="text-muted small">Tidak ada aktivitas</span>
                                @endforelse
                            </div>
                        </div>
                        <!-- ============================================================= -->

                        <div class="text-muted small mb-3">
                            <i class="bi bi-clock"></i>
                            {{ $paket->jam_awal ? \Carbon\Carbon::parse($paket->jam_awal)->format('H:i') : '-' }}
                            -
                            {{ $paket->jam_akhir ? \Carbon\Carbon::parse($paket->jam_akhir)->format('H:i') : '-' }}
                        </div>

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
                                class="btn btn-agro-primary">
                                Lihat Detail
                            </a>
                        </div>

                    </div>

                </div>

            </div>

            @empty
                <div class="col-12 text-center py-5">
                    <h5 class="fw-bold">Data tidak ditemukan</h5>
                    <p class="text-muted">Coba kata kunci lain</p>
                </div>
            @endforelse

        </div>

        <!-- NO RESULT TAMBAHAN -->
        <div id="noResultMessage" class="text-center py-5" style="display:none;">
            <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
            <h5 class="fw-bold">Tidak ada destinasi yang tersedia</h5>
            <p class="text-muted">Silakan pilih wilayah lain atau ubah kata kunci pencarian</p>
        </div>

    </div>
</section>

@endsection

<!-- ================= JS ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const searchInput = document.getElementById("searchInput");
    const buttons = document.querySelectorAll(".region-pill");
    const items = document.querySelectorAll(".paket-item");
    const noResult = document.getElementById("noResultMessage");

    let currentFilter = "all";

    //Disable submit reload
    if (form) {
        form.addEventListener("submit", function(e){
            e.preventDefault();
        });
    }

    function filterData() {
        const keyword = (searchInput?.value || "").toLowerCase().trim();
        let visible = 0;

        items.forEach(item => {
            const name = item.dataset.name || "";
            const area = item.dataset.area || "";

            const matchSearch = name.includes(keyword);
            const matchFilter = currentFilter === "all" || area === currentFilter;

            if (matchSearch && matchFilter) {

                //FADE IN (smooth & slow)
                item.style.display = "";
                item.style.transition = "all 0.8s cubic-bezier(0.25, 0.8, 0.25, 1)";
                item.style.opacity = "0";
                item.style.transform = "translateY(20px)";

                setTimeout(() => {
                    item.style.opacity = "1";
                    item.style.transform = "translateY(0)";
                }, 80);

                visible++;

            } else {

                //FADE OUT
                item.style.transition = "all 0.8s cubic-bezier(0.25, 0.8, 0.25, 1)";
                item.style.opacity = "0";
                item.style.transform = "translateY(20px)";

                setTimeout(() => {
                    item.style.display = "none";
                }, 800);
            }
        });

        //NO RESULT
        if (noResult) {
            noResult.style.transition = "none";
            noResult.style.display = visible === 0 ? "block" : "none";
        }
    }

    //Search input
    if (searchInput) {
        searchInput.addEventListener("input", filterData);
    }

    //Filter button
    buttons.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            let value = this.value || "all";
            currentFilter = value.toLowerCase();

            buttons.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            filterData();
        });
    });

});
</script>