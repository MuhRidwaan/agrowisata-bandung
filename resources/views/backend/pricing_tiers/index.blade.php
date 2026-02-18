@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">
        <br>
        <div class="d-flex justify-content-between mb-3">
            <h4>Pricing Tier</h4>
            <a href="{{ route('pricing-tiers.create') }}" class="btn btn-primary">Tambah</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No </th>
                            <th>Paket</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tiers as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->tourPackage->title }}</td>
                                <td>{{ $item->name }}</td>
                                <td>Rp {{ number_format($item->price) }}</td>
                                <td>
                                    <a href="{{ route('pricing-tiers.edit', $item->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('pricing-tiers.destroy', $item->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus data?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
