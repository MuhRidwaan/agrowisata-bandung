@extends('frontend.main')

@section('content')
<header class="bg-white border-bottom position-sticky top-0" style="z-index: 1000;">
    <div class="container">
        <div class="d-flex align-items-center gap-3 py-3">
            <a href="/" class="btn btn-light rounded-circle p-2">
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

    <!-- ================= PHOTO GALLERY ================= -->
    <div class="mb-4">
        <div class="gallery-main bg-secondary position-relative" id="mainGallery">
            <img id="mainImage"
                src="{{ $paket->photos->first() 
                    ? asset('storage/'.$paket->photos->first()->path_foto) 
                    : 'https://via.placeholder.com/1200x600' }}"
                alt="{{ $paket->nama_paket }}"
                style="width:100%; height:100%; object-fit:cover; display:block;"
                loading="eager">

            <button class="gallery-nav-btn prev" onclick="prevImage()">
                <i class="bi bi-chevron-left"></i>
            </button>

            <button class="gallery-nav-btn next" onclick="nextImage()">
                <i class="bi bi-chevron-right"></i>
            </button>

            <div class="gallery-dots">
                @foreach($paket->photos as $index => $photo)
                    <button class="gallery-dot {{ $index == 0 ? 'active' : '' }}"
                        onclick="setImage({{ $index }})">
                    </button>
                @endforeach
            </div>
        </div>

        @if($paket->photos && $paket->photos->count() > 1)
        <div class="gallery-thumbs">
            @foreach($paket->photos as $index => $photo)
                <button class="gallery-thumb {{ $index == 0 ? 'active' : '' }}"
                    onclick="setImage({{ $index }})">
                    <img src="{{ asset('storage/'.$photo->path_foto) }}">
                </button>
            @endforeach
        </div>
        @endif
    </div>

    <div class="row g-4">

        <!-- ================= LEFT ================= -->
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">

                <!-- TITLE -->
                <div class="card card-agro">
                    <div class="card-body p-4">
                        <h1 class="font-display display-6 fw-bold mb-3">
                            {{ $paket->nama_paket }}
                        </h1>

                        <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-geo-alt"></i>
                                {{ $paket->vendor->name ?? '-' }},
                                {{ $paket->vendor->area->name ?? '-' }}
                            </span>

                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-clock"></i>
                                {{ $paket->jam_awal ? \Carbon\Carbon::parse($paket->jam_awal)->format('H:i') : '-' }}
                                -
                                {{ $paket->jam_akhir ? \Carbon\Carbon::parse($paket->jam_akhir)->format('H:i') : '-' }}
                            </span>

                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-star-fill star-filled"></i>
                                {{ number_format($paket->reviews->avg('rating') ?? 0,1) }}
                                ({{ $paket->reviews->where('status','approved')->count() }} ulasan)
                            </span>
                        </div>

                        <p class="text-muted">
                            {{ $paket->deskripsi }}
                        </p>
                    </div>
                </div>

                <!-- AKTIVITAS -->
                @if($paket->aktivitas)
                <div class="card card-agro">
                    <div class="card-body p-4">
                        <h3 class="font-display fs-5 fw-semibold mb-3">Aktivitas</h3>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($paket->aktivitas as $item)
                                <span class="badge-primary-light">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- PRICING RULES -->
                @if($paket->pricingRules && $paket->pricingRules->count() > 0)
                <div class="card card-agro">
                    <div class="card-body p-4">
                        <h3 class="font-display fs-5 fw-semibold mb-3">
                            Penawaran Spesial (Diskon)
                        </h3>

                        <div class="row g-3">
                            @foreach($paket->pricingRules as $rule)
                            <div class="col-lg-4 col-md-6 col-6">
                                <div class="price-tier-card">
                                    <p class="text-muted small mb-1">
                                        {{ $rule->min_pax }}
                                        {{ $rule->max_pax ? '-' . $rule->max_pax : '+' }}
                                        orang
                                    </p>

                                    <p class="font-display fw-bold text-primary-agro mb-0">
                                        @if($rule->discount_type === 'percent')
                                            Potongan {{ $rule->discount_value }}%
                                        @else
                                            Potongan Rp{{ number_format($rule->discount_value,0,',','.') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <p class="text-muted small mt-3 mb-0 d-flex align-items-center gap-1">
                            <i class="bi bi-people"></i>
                            Semakin banyak peserta, harga per orang semakin murah!
                        </p>
                    </div>
                </div>
                @endif

                <!-- REVIEWS -->
                <div class="card card-agro">
                    <div class="card-body p-4">
                        <h3 class="font-display fs-5 fw-semibold mb-4">
                            Ulasan Pengunjung ({{ $paket->reviews->where('status','approved')->count() }})
                        </h3>

                        <div class="d-flex flex-column gap-4">
                            @forelse($paket->reviews->where('status','approved') as $review)
                                <div class="p-4 rounded-4 bg-light">

                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                 style="width:45px; height:45px; background:#e9f5ee; color:#2f6d4f;">
                                                {{ strtoupper(substr($review->name ?? 'U',0,1)) }}
                                            </div>

                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $review->name ?? 'User' }}
                                                </div>

                                                <div class="text-muted small">
                                                    {{ \Carbon\Carbon::parse($review->created_at)->translatedFormat('d F Y') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-1">
                                            @for($i=1;$i<=5;$i++)
                                                <i class="bi bi-star-fill {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>

                                    <p class="text-muted mt-3 mb-0">
                                        {{ $review->comment }}
                                    </p>

                                    @if(!empty($review->admin_reply))
                                    <div class="mt-3 ms-5">
                                        <div class="p-3 rounded-4 shadow-sm"
                                             style="background:#f5faf7; border-left:4px solid #2f6d4f;">
                                            <div class="fw-semibold small text-success mb-1">
                                                <i class="bi bi-patch-check-fill"></i>
                                                Admin AgroTourism Bandung
                                            </div>
                                            <p class="small mb-0 text-muted">
                                                {{ $review->admin_reply }}
                                            </p>
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            @empty
                                <p class="text-muted">Belum ada ulasan</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- FORM ULASAN -->
                <div class="card card-agro">
                    <div class="card-body p-4">
                        <h3 class="font-display fs-5 fw-semibold mb-4">
                            Tulis Ulasan
                        </h3>

                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="paket_id" value="{{ $paket->id }}">
                            <input type="hidden" name="rating" id="ratingInput">

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">
                                    Nama Lengkap
                                </label>
                                <input type="text" name="name" class="form-control review-field" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold d-block mb-2">Rating</label>
                                <div id="starRating" class="d-flex gap-2 fs-4">
                                    <i class="bi bi-star star-rating text-gray-300 hover:text-yellow-500 transition duration-200 cursor-pointer" data-value="1"></i>
                                    <i class="bi bi-star star-rating text-gray-300 hover:text-yellow-500 transition duration-200 cursor-pointer" data-value="2"></i>
                                    <i class="bi bi-star star-rating text-gray-300 hover:text-yellow-500 transition duration-200 cursor-pointer" data-value="3"></i>
                                    <i class="bi bi-star star-rating text-gray-300 hover:text-yellow-500 transition duration-200 cursor-pointer" data-value="4"></i>
                                    <i class="bi bi-star star-rating text-gray-300 hover:text-yellow-500 transition duration-200 cursor-pointer" data-value="5"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold mb-2">Komentar</label>
                                <textarea name="comment" rows="4" class="form-control review-field" placeholder="Bagikan pengalaman Anda..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-agro-primary">
                                Kirim Ulasan
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>

        <!-- ================= RIGHT ================= -->
<div class="col-lg-4">
    <div class="position-sticky" style="top: 80px;">
        <div class="card card-agro">
            <div class="card-body p-4">

                <p class="text-muted small mb-1">Harga mulai dari</p>

                <div class="d-flex align-items-baseline gap-1 mb-3">
                    <span class="font-display display-6 fw-bold text-primary-agro">
                        Rp{{ number_format($paket->harga_paket ?? 0,0,',','.') }}
                    </span>
                    <span class="text-muted small">/orang</span>
                </div>

                <div class="bg-agro-light rounded-3 p-3 mb-4 d-flex gap-2">
                    <i class="bi bi-shield-check text-primary-agro flex-shrink-0"></i>
                    <p class="text-muted small mb-0">
                        Pemesanan harus dilakukan minimal 24 jam sebelum jadwal kunjungan.
                    </p>
                </div>

                <a href="{{ route('booking',$paket->id) }}"
                   class="btn btn-agro-primary w-100">
                   Beli Tiket
                </a>

            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ================= GALLERY ================= */

    const images = [
        @foreach($paket->photos as $photo)
            "{{ asset('storage/'.$photo->path_foto) }}",
        @endforeach
    ];

    let currentIndex = 0;

    const mainImage = document.getElementById("mainImage");
    const thumbs = document.querySelectorAll(".gallery-thumb");
    const dots = document.querySelectorAll(".gallery-dot");

    if (mainImage && images.length > 0) {

        mainImage.style.transition = "opacity 0.3s ease";

        function updateActiveState(index) {
            thumbs.forEach(t => t.classList.remove("active"));
            dots.forEach(d => d.classList.remove("active"));

            if (thumbs[index]) thumbs[index].classList.add("active");
            if (dots[index]) dots[index].classList.add("active");
        }

        window.setImage = function(index) {
            if (!images[index]) return;

            currentIndex = index;
            mainImage.style.opacity = 0;

            setTimeout(() => {
                mainImage.src = images[index];
                mainImage.style.opacity = 1;
            }, 150);

            updateActiveState(index);
        };

        window.nextImage = function() {
            currentIndex = (currentIndex + 1) % images.length;
            setImage(currentIndex);
        };

        window.prevImage = function() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            setImage(currentIndex);
        };
    }


    /* ================= STAR RATING ================= */

    const stars = document.querySelectorAll(".star-rating");
    const ratingInput = document.getElementById("ratingInput");
    let selectedRating = 0;

    if (stars.length > 0 && ratingInput) {

        stars.forEach(star => {

            star.addEventListener("mouseover", function () {
                highlightStars(this.dataset.value);
            });

            star.addEventListener("mouseout", function () {
                highlightStars(selectedRating);
            });

            star.addEventListener("click", function () {
                selectedRating = this.dataset.value;
                ratingInput.value = selectedRating;
                highlightStars(selectedRating);
            });

        });

        function highlightStars(value) {
            stars.forEach(star => {
                if (star.dataset.value <= value) {
                    star.classList.remove("bi-star");
                    star.classList.add("bi-star-fill", "text-warning");
                } else {
                    star.classList.remove("bi-star-fill", "text-warning");
                    star.classList.add("bi-star");
                }
            });
        }

    }

});
</script>