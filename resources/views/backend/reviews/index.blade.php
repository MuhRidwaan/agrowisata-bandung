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

            <!-- HEADER -->
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
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Reply</th>
                            <th width="25%">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($reviews as $review)
                            <tr>
                                <td>
                                    {{ ($reviews->currentPage() - 1) * $reviews->perPage() + $loop->iteration }}
                                </td>

                                <td>{{ $review->user->name ?? '-' }}</td>
                                <td>{{ $review->vendor->name ?? '-' }}</td>

                                <!-- RATING -->
                                <td>⭐ {{ $review->rating }}</td>

                                <td>{{ \Illuminate\Support\Str::limit($review->comment, 50) }}</td>

                                <!-- STATUS -->
                                <td>
                                    <span class="badge badge-{{ $review->status_badge }}">
                                        {{ ucfirst($review->status) }}
                                    </span>
                                </td>

                                <!-- REPLY -->
                                <td>
                                    {{ $review->admin_reply ?? '-' }}
                                </td>

                                <!-- ACTION -->
                                <td>

                                    <!-- APPROVE -->
                                    <form action="{{ route('review.approve', $review->id) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-success btn-sm" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <!-- REJECT -->
                                    <form action="{{ route('review.reject', $review->id) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-danger btn-sm" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>

                                    <!-- REPLY BUTTON -->
                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#replyModal{{ $review->id }}" title="Reply">
                                        <i class="fas fa-reply"></i>
                                    </button>

                                </td>
                            </tr>

                            <!-- MODAL REPLY -->
                            <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('review.reply', $review->id) }}" method="POST">
                                        @csrf

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reply to Review</h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <p><strong>Review:</strong> {{ $review->comment }}</p>

                                                <div class="form-group">
                                                    <label>Admin Reply</label>
                                                    <textarea name="admin_reply" class="form-control" required></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-primary">Send</button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    No review data available
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

                <!-- PAGINATION -->
                <div class="mt-3">
                    {{ $reviews->links() }}
                </div>

            </div>

        </div>

    </div>
</section>

{{-- ALERT SUCCESS --}}
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