@extends('backend.main_dashboard')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Review & Rating Data</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Reviews</li>
                    </ol>
                </div>

            </div>
        </div>
    </section>


    <section class="content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Review List</h3>
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>User</th>
                                <th>Vendor</th>
                                <th width="130">Rating</th>
                                <th>Review</th>
                                <th width="160">Photo</th>
                                <th>Status</th>
                                <th>Reply</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($reviews as $review)
                                <tr>

                                    <td>
                                        {{ ($reviews->currentPage() - 1) * $reviews->perPage() + $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $review->name ?? ($review->user->name ?? '-') }}

                                        @if (!$review->user_id)
                                            <span class="badge badge-secondary">Guest</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $review->vendor->name ?? '-' }}
                                    </td>

                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="fas fa-star text-secondary"></i>
                                            @endif
                                        @endfor
                                    </td>

                                    <td>
                                        {{ \Illuminate\Support\Str::limit($review->comment, 50) }}
                                    </td>
                                    {{-- <?php
                                    print_r('<pre>');
                                    print_r($review);
                                    ?> --}}
                                    <td>
                                        @if ($review->photos->count())
                                            <div class="d-flex gap-1 flex-wrap">

                                                @foreach ($review->photos as $photo)
                                                    <img src="{{ $photo->photo_url }}" class="review-thumb"
                                                        onclick="openAdminImage(this)"
                                                        style="width:60px;height:60px;object-fit:cover;border-radius:6px;margin:2px;">
                                                @endforeach

                                            </div>
                                        @elseif($review->photo)
                                            <img src="{{ $review->photo_url }}"
                                                style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge badge-{{ $review->status_badge }}">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $review->admin_reply ?? '-' }}
                                    </td>

                                    <td>

                                        {{-- PENDING --}}
                                        @if ($review->status == 'pending')
                                            <form action="{{ route('review.approve', $review->id) }}" method="POST"
                                                style="display:inline-block; margin-right:5px;">
                                                @csrf

                                                <button class="btn btn-success btn-sm" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>

                                            </form>


                                            <form action="{{ route('review.reject', $review->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf

                                                <button class="btn btn-danger btn-sm" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>

                                            </form>
                                        @endif



                                        {{-- APPROVED --}}
                                        @if ($review->status == 'approved')
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#replyModal{{ $review->id }}" title="Reply"
                                                style="margin-right:5px;">
                                                <i class="fas fa-reply"></i>
                                            </button>


                                            <form action="{{ route('review.destroy', $review->id) }}" method="POST"
                                                style="display:inline-block;"
                                                onsubmit="return confirm('Yakin mau hapus review ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-dark btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>
                                        @endif



                                        {{-- REJECTED --}}
                                        @if ($review->status == 'rejected')
                                            <form action="{{ route('review.destroy', $review->id) }}" method="POST"
                                                style="display:inline-block;"
                                                onsubmit="return confirm('Yakin mau hapus review ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-dark btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>
                                        @endif

                                    </td>

                                </tr>



                                <!-- MODAL REPLY -->
                                <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1">
                                    <div class="modal-dialog">

                                        <form action="{{ route('review.reply', $review->id) }}" method="POST">
                                            @csrf

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Reply to Review
                                                    </h5>

                                                    <button type="button" class="close" data-dismiss="modal">
                                                        &times;
                                                    </button>

                                                </div>


                                                <div class="modal-body">

                                                    <p>
                                                        <strong>Review:</strong>
                                                        {{ $review->comment }}
                                                    </p>


                                                    <div class="form-group">

                                                        <label>Admin Reply</label>

                                                        <textarea name="admin_reply" class="form-control" required></textarea>

                                                    </div>

                                                </div>


                                                <div class="modal-footer">

                                                    <button class="btn btn-primary">
                                                        Send
                                                    </button>

                                                </div>

                                            </div>

                                        </form>

                                    </div>
                                </div>


                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        No review data available
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>


                    <div class="mt-3 d-flex justify-content-end">
                        {{ $reviews->links() }}
                    </div>

                </div>

            </div>

        </div>


        <!-- LIGHTBOX VIEW IMAGE -->
        <div id="adminLightbox" class="admin-lightbox" onclick="closeAdminImage()">

            <span class="admin-close" onclick="event.stopPropagation(); closeAdminImage()">✕</span>

            <span class="admin-nav admin-prev" onclick="event.stopPropagation(); prevAdminImage()">❮</span>

            <img id="adminLightboxImg" onclick="event.stopPropagation()">

            <span class="admin-nav admin-next" onclick="event.stopPropagation(); nextAdminImage()">❯</span>

        </div>


    </section>



    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

@endsection


<style>
    .review-thumb {
        transition: 0.25s;
        cursor: pointer;
    }

    .review-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
        z-index: 2;
    }

    .admin-lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .admin-lightbox img {
        max-width: 85%;
        max-height: 85%;
        border-radius: 8px;
    }

    .admin-close {
        position: absolute;
        top: 25px;
        right: 40px;
        font-size: 28px;
        color: white;
        cursor: pointer;
    }

    .admin-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 40px;
        color: white;
        cursor: pointer;
        padding: 10px;
    }

    .admin-prev {
        left: 40px;
    }

    .admin-next {
        right: 40px;
    }
</style>


@push('scripts')
    <script>
        let adminImages = [];
        let adminIndex = 0;

        function openAdminImage(img) {

            const lightbox = document.getElementById("adminLightbox");
            const lightboxImg = document.getElementById("adminLightboxImg");

            adminImages = [];

            document.querySelectorAll(".review-thumb").forEach(i => {
                adminImages.push(i.src);
            });

            adminIndex = adminImages.indexOf(img.src);

            lightboxImg.src = img.src;
            lightbox.style.display = "flex";

        }

        function nextAdminImage() {

            adminIndex++;

            if (adminIndex >= adminImages.length) {
                adminIndex = 0;
            }

            document.getElementById("adminLightboxImg").src = adminImages[adminIndex];

        }

        function prevAdminImage() {

            adminIndex--;

            if (adminIndex < 0) {
                adminIndex = adminImages.length - 1;
            }

            document.getElementById("adminLightboxImg").src = adminImages[adminIndex];

        }

        function closeAdminImage() {

            document.getElementById("adminLightbox").style.display = "none";

        }
    </script>
@endpush
