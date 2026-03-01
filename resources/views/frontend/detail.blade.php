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

    <!-- ================= GALLERY ================= -->
    <div class="mb-4">
        <div class="gallery-main bg-secondary" id="mainGallery">
            <picture>
                <source srcset="{{ $paket->photos->first() ? asset('storage/' . $paket->photos->first()->path_foto) : 'https://via.placeholder.com/400x250' }}"
                    type="image/webp">
                <img src="{{ $paket->photos->first() ? asset('storage/' . $paket->photos->first()->path_foto) : 'https://via.placeholder.com/400x250' }}"
                    class="img-fluid rounded w-100" alt="Gambar Paket">
            </picture>         
            class="img-fluid rounded w-100">
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
                            <span>
                                <i class="bi bi-geo-alt"></i>
                                {{ $paket->vendor->name ?? '-' }}
                            </span>

                            <span>
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($paket->jam_awal)->format('H:i') }} - {{ \Carbon\Carbon::parse($paket->jam_akhir)->format('H:i') }}
                            </span>

                            <span>
                                ⭐ {{ number_format($paket->reviews->avg('rating') ?? 0,1) }}
                                ({{ $paket->reviews->count() }} ulasan)
                            </span>
                        </div>

                        <p class="text-muted">
                            {{ $paket->deskripsi }}
                        </p>
                    </div>
                </div>

                <!-- GALLERY TAMBAHAN -->
                @if($paket->photos && $paket->photos->count())
                <div class="card card-agro">
                    <div class="card-body p-4">
                        <h3 class="mb-3">Gallery</h3>
                        <div class="row">
                            @foreach($paket->photos as $photo)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ asset('storage/' . $photo->photo) }}"
                                        class="img-fluid rounded">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- REVIEWS -->
                <div class="card card-agro">
                    <div class="card-body p-4">

                        <h3 class="mb-4">
                            Ulasan ({{ $paket->reviews->where('status','approved')->count() }})
                        </h3>

                        @forelse($paket->reviews->where('status','approved') as $review)
                            <div class="p-3 rounded-3 bg-light mb-3">

                                <div class="d-flex justify-content-between mb-2">
                                    <strong>{{ $review->user->name ?? 'User' }}</strong>
                                    <span>⭐ {{ $review->rating }}</span>
                                </div>

                                <p class="mb-2">{{ $review->comment }}</p>

                                @if($review->admin_reply)
                                    <div class="bg-white p-2 rounded">
                                        <strong>Admin:</strong>
                                        {{ $review->admin_reply }}
                                    </div>
                                @endif

                            </div>
                        @empty
                            <p class="text-muted">Belum ada review</p>
                        @endforelse

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

                        <div class="mb-3">
                            <span class="display-6 fw-bold text-primary-agro">
                                Rp{{ number_format($paket->harga_paket ?? 0,0,',','.') }}
                            </span>
                            <small>/orang</small>
                        </div>

                        <a href="{{ route('booking', $paket->id) }}"
                            class="btn btn-agro-primary w-100">
                            Beli Tiket
                        </a>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection