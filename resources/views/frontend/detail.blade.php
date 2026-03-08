@extends('frontend.main')

@section('header')
    @include('frontend.layouts.header')
<body>
@endsection

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
                        <h3 class="font-display fs-5 fw-semibold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-tag-fill text-accent"></i>Penawaran Spesial (Diskon)
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

                        <div class="d-flex flex-column gap-4 review-scroll">
                            @forelse($paket->reviews->where('status','approved')->sortByDesc('created_at') as $review)
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

                                    @if($review->photos->count())
                                    <div class="mt-3 d-flex gap-2 flex-wrap">
                                        @foreach($review->photos as $photo)
                                        <img 
                                            src="{{ asset('storage/'.$photo->photo) }}"
                                            class="rounded-3 shadow-sm review-img"
                                            style="width:100px;height:100px;object-fit:cover;cursor:pointer;"
                                            onclick="openReviewImage(this.src)">
                                        @endforeach
                                    </div>
                                    @endif

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

                <!-- LIGHTBOX FOTO REVIEW -->
                <div id="reviewLightbox" class="review-lightbox" onclick="closeReviewImage()">

                    <button class="review-close" onclick="event.stopPropagation(); closeReviewImage()">✕</button>

                    <button class="review-nav prev" onclick="event.stopPropagation(); prevReviewImage()">❮</button>

                    <img id="reviewLightboxImg" onclick="event.stopPropagation()">

                    <button class="review-nav next" onclick="event.stopPropagation(); nextReviewImage()">❯</button>

                </div>

                <!-- FORM ULASAN -->
                <div class="card card-agro">
                    <div class="card-body p-4">
                        <h3 class="font-display fs-5 fw-semibold mb-4">
                            Tulis Ulasan
                        </h3>

                        <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                            <input type="file" name="photos[]" id="realFileInput" multiple hidden>
                            @csrf
                            <input type="hidden" name="paket_id" value="{{ $paket->id }}">
                            <input type="hidden" name="rating" id="ratingInput">

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="reviewName" name="name" class="form-control review-field" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex gap-3">

                                    <label id="cameraBtn"
                                        class="d-flex flex-column align-items-center justify-content-center border rounded p-3 cursor-pointer"
                                        style="width:120px;border-style:dashed;">
                                        <i class="bi bi-camera fs-4 mb-1"></i>
                                        <span class="small">Foto</span>

                                        <input 
                                            type="file"
                                            name="photos[]"
                                            accept="image/*"
                                            capture="environment"
                                            class="d-none"
                                            onchange="previewImage(event,4)">
                                    </label>

                                    <label id="folderBtn"
                                        class="d-flex flex-column align-items-center justify-content-center border rounded p-3 cursor-pointer"
                                        style="width:120px;border-style:dashed;">
                                        <i class="bi bi-folder fs-4 mb-1"></i>
                                        <span class="small">Folder</span>

                                        <input 
                                            type="file"
                                            name="photos[]"
                                            accept="image/*"
                                            multiple
                                            class="d-none"
                                            onchange="previewImage(event,4)">
                                    </label>

                                </div>

                                <div id="previewContainer" class="d-flex gap-2 flex-wrap mt-3"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold d-block mb-2">
                                    Rating <span class="text-danger">*</span>
                                </label>

                                <div id="starRating" class="d-flex gap-2 fs-4">
                                    <i class="bi bi-star star-rating" data-value="1"></i>
                                    <i class="bi bi-star star-rating" data-value="2"></i>
                                    <i class="bi bi-star star-rating" data-value="3"></i>
                                    <i class="bi bi-star star-rating" data-value="4"></i>
                                    <i class="bi bi-star star-rating" data-value="5"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold mb-2">
                                    Komentar <span class="text-danger">*</span>
                                </label>

                                <textarea id="reviewComment"
                                    name="comment"
                                    rows="6"
                                    class="form-control"
                                    style="resize:none;"
                                    placeholder="Bagikan pengalaman Anda..."
                                    required></textarea>
                            </div>

                            <button type="submit" id="submitReview" class="btn btn-agro-primary" disabled>
                                Kirim Ulasan
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= RIGHT ================= -->
        <div class="col-lg-4">
            <div class="position-sticky" style="top:80px;">
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

    </div>
</div>

@endsection


<!-- ======================== JS ======================== -->
@push('scripts')
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
    const submitBtn = document.getElementById("submitReview");
    const nameInput = document.getElementById("reviewName");
    const commentInput = document.getElementById("reviewComment");

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

                const clickedValue = this.dataset.value;

                if(selectedRating == clickedValue){
                    selectedRating = 0;
                    ratingInput.value = "";
                }else{
                    selectedRating = clickedValue;
                    ratingInput.value = clickedValue;
                }

                highlightStars(selectedRating);
                checkForm();

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


    /* ================= VALIDASI FORM ================= */

    function checkForm(){

        const nameFilled = nameInput?.value.trim() !== "";
        const commentFilled = commentInput?.value.trim() !== "";
        const ratingFilled = ratingInput?.value !== "";

        if(nameFilled && commentFilled && ratingFilled){
            submitBtn.disabled = false;
            submitBtn.style.opacity = "1";
        }else{
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.6";
        }

    }

    nameInput?.addEventListener("input", checkForm);
    commentInput?.addEventListener("input", checkForm);


    /* ================= UPLOAD INDICATOR ================= */

    const cameraInput = document.querySelector("input[name='photo_camera']");
    const folderInput = document.querySelector("input[name='photo_file']");

    const cameraBtn = document.getElementById("cameraBtn");
    const folderBtn = document.getElementById("folderBtn");

    function resetUpload(){
        cameraBtn.style.borderColor = "#dee2e6";
        cameraBtn.style.background = "";
        folderBtn.style.borderColor = "#dee2e6";
        folderBtn.style.background = "";
    }

    if(cameraInput){
        cameraInput.addEventListener("change", function(){
            resetUpload();
            cameraBtn.style.borderColor = "#22c55e";
            cameraBtn.style.background = "#ecfdf5";
        });
    }

    if(folderInput){
        folderInput.addEventListener("change", function(){
            resetUpload();
            folderBtn.style.borderColor = "#22c55e";
            folderBtn.style.background = "#ecfdf5";
        });
    }

});


/* ================= REVIEW IMAGE LIGHTBOX ================= */

let reviewImages = [];
let reviewIndex = 0;

function openReviewImage(src){

    const lightbox = document.getElementById("reviewLightbox");
    const img = document.getElementById("reviewLightboxImg");

    const imgs = document.querySelectorAll(".review-img");

    reviewImages = Array.from(imgs).map(i => i.src);

    reviewIndex = reviewImages.indexOf(src);

    if(reviewIndex === -1){
        reviewIndex = 0;
    }

    img.src = reviewImages[reviewIndex];
    lightbox.style.display = "flex";
}

function nextReviewImage(e){

    if(e) e.stopPropagation();

    reviewIndex++;

    if(reviewIndex >= reviewImages.length){
        reviewIndex = 0;
    }

    document.getElementById("reviewLightboxImg").src = reviewImages[reviewIndex];

}

function prevReviewImage(e){

    if(e) e.stopPropagation();

    reviewIndex--;

    if(reviewIndex < 0){
        reviewIndex = reviewImages.length - 1;
    }

    document.getElementById("reviewLightboxImg").src = reviewImages[reviewIndex];

}

function closeReviewImage(){

    document.getElementById("reviewLightbox").style.display = "none";

}


/* ================= PREVIEW FOTO UPLOAD ================= */

let selectedFiles = [];

function previewImage(event, max = 5) {

    const container = document.getElementById("previewContainer");
    const files = event.target.files;

    if(!files.length) return;

    if(selectedFiles.length + files.length > max){
        alert("Maksimal upload " + max + " foto");
        return;
    }

    Array.from(files).forEach(file => {
        selectedFiles.push(file);
    });

    renderPreview(container);

    updateRealInput();

    event.target.value = "";
}

function updateRealInput(){

    const realInput = document.getElementById("realFileInput");

    const dataTransfer = new DataTransfer();

    selectedFiles.forEach(file=>{
        dataTransfer.items.add(file);
    });

    realInput.files = dataTransfer.files;

}


/* ================= RENDER PREVIEW ================= */

function renderPreview(container){

    container.innerHTML = "";

    selectedFiles.forEach((file,index)=>{

        const reader = new FileReader();

        reader.onload = function(e){

            const wrapper = document.createElement("div");
            wrapper.style.position = "relative";
            wrapper.style.width = "80px";
            wrapper.style.height = "80px";
            wrapper.style.display = "inline-block";

            const img = document.createElement("img");

            img.src = e.target.result;
            img.style.width = "100%";
            img.style.height = "100%";
            img.style.objectFit = "cover";
            img.classList.add("rounded","shadow-sm");

            const removeBtn = document.createElement("button");
            removeBtn.innerHTML = "✕";

            removeBtn.style.position = "absolute";
            removeBtn.style.top = "4px";
            removeBtn.style.right = "4px";
            removeBtn.style.background = "#ef4444";
            removeBtn.style.color = "white";
            removeBtn.style.border = "none";
            removeBtn.style.width = "18px";
            removeBtn.style.height = "18px";
            removeBtn.style.borderRadius = "50%";
            removeBtn.style.cursor = "pointer";
            removeBtn.style.fontSize = "11px";
            removeBtn.style.display = "flex";
            removeBtn.style.alignItems = "center";
            removeBtn.style.justifyContent = "center";
            removeBtn.style.zIndex = "10";
            removeBtn.style.boxShadow = "0 2px 4px rgba(0,0,0,0.25)";

            removeBtn.onclick = function(){

                selectedFiles.splice(index,1);

                renderPreview(container);

                updateRealInput();

            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);

            container.appendChild(wrapper);

        }

        reader.readAsDataURL(file);

    });

}


/* ================= UPDATE INPUT FILE ================= */

function updateInputFiles(input){

    const dataTransfer = new DataTransfer();

    selectedFiles.forEach(file=>{
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;

}
</script>
@endpush



<!-- ======================== Styling CSS ======================== -->
<style>
.review-img{
    transition: transform .25s ease, box-shadow .25s ease;
}

.review-img:hover{
    transform: translateY(-6px) scale(1.05);
    box-shadow: 0 15px 30px rgba(0,0,0,0.25);
}
#cameraBtn, #folderBtn{
    transition: all 0.25s ease;
}

#cameraBtn:hover, #folderBtn:hover{
    transform: translateY(-6px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    background:#ecfdf5;
    border-color:#22c55e !important;
}

#cameraBtn:active, #folderBtn:active{
    transform: scale(0.95);
}
/* REVIEW IMAGE */

.review-photo img{
    cursor:pointer;
    transition: transform 0.25s ease;
}

.review-photo img:hover{
    transform:scale(1.08);
}


/* LIGHTBOX */

.review-lightbox{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.9);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:9999;
}

.review-lightbox img{
    max-width:90%;
    max-height:90%;
    border-radius:12px;
}

/* tombol close */
.review-close{
    position:absolute;
    top:20px;
    right:25px;
    font-size:28px;
    background:rgba(0,0,0,0.5);
    color:white;
    border:none;
    width:40px;
    height:40px;
    border-radius:50%;
    cursor:pointer;
}

/* tombol navigasi */
.review-nav{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    background:rgba(0,0,0,0.5);
    color:white;
    border:none;
    font-size:35px;
    padding:10px 18px;
    cursor:pointer;
    border-radius:8px;
}

.review-nav.prev{
    left:30px;
}

.review-nav.next{
    right:30px;
}

.review-scroll{
    max-height:600px;
    overflow-y:auto;
    padding-right:5px;
}

.review-scroll::-webkit-scrollbar{
    width:6px;
}

.review-scroll::-webkit-scrollbar-thumb{
    background:#ccc;
    border-radius:10px;
}
</style>