@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">
        <br>
        <div class="d-flex justify-content-between mb-3">
            <h4>Diskon / Pricing Rule</h4>
            <a href="{{ route('pricing-rules.create') }}" class="btn btn-primary">Tambah Rule</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $item)
                            <tr>
                                <td>{{ $item->tourPackage->title }}</td>
                                <td>{{ $item->min_pax }}</td>
                                <td>{{ $item->max_pax }}</td>
                                <td>{{ $item->discount_type }}</td>
                                <td>{{ $item->discount_type == 'percent' ? $item->discount_value . '%' : 'Rp ' . number_format($item->discount_value) }}
                                </td>
                                <td>
                                    <a href="{{ route('pricing-rules.edit', $item->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('pricing-rules.destroy', $item->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada rule</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
