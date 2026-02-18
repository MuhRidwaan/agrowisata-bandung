@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">
        <br>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Tanggal Ketersediaan Paket</h4>
            <a href="{{ route('available-dates.create') }}" class="btn btn-primary">
                Tambah Tanggal
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Paket Tour</th>
                            <th>Tanggal</th>
                            <th>Kuota</th>
                            <th>Terisi</th>
                            <th>Sisa</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dates as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->tourPackage->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                                <td>{{ $item->quota }}</td>
                                <td>{{ $item->booked }}</td>
                                <td>
                                    <strong
                                        class="{{ $item->quota - $item->booked <= 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $item->quota - $item->booked }}
                                    </strong>
                                </td>
                                <td>
                                    <a href="{{ route('available-dates.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('available-dates.destroy', $item->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Hapus tanggal ini?')"
                                            class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada jadwal tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection
