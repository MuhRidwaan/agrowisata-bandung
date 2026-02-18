@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid pl-3">

        <h1 class="mb-3">Data Review & Rating</h1>

        <div class="mb-3">
            <a href="{{ route('review.create') }}" class="btn btn-warning">
                + Tambah Review
            </a>
        </div>

        <!-- WRAPPER PUTIH TIPIS -->
        <div class="bg-white p-2 rounded shadow-sm">

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Vendor</th>
                        <th>Nama</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $review->vendor->name ?? '-' }}</td>

                            <td>{{ $review->name ?? '-' }}</td>

                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    {!! $i <= $review->rating ? '⭐' : '☆' !!}
                                @endfor
                            </td>

                            <td>{{ $review->comment }}</td>

                            <td>
                                <a href="{{ route('review.edit', $review->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('review.destroy', $review->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada data review
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </div>
@endsection
