@extends('main_dashboard')
@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Data Tanggal Available</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('tanggal-available.create') }}" class="btn btn-primary">Tambah Tanggal</a>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paket Tour</th>
                        <th>Tanggal</th>
                        <th>Kuota</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tanggalAvailables as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->paketTour->nama_paket ?? '-' }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->kuota }}</td>
                            <td>
                                <span class="badge badge-{{ $item->status == 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('tanggal-available.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('tanggal-available.destroy', $item) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $tanggalAvailables->links() }}
        </div>
    </div>
</div>
@endsection
