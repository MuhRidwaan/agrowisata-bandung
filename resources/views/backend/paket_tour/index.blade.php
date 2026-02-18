@extends('backend.backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Paket Tour</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Paket Tour</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h3 class="card-title mb-2">Data Paket Tour</h3>
                                <a href="{{ route('paket-tours.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Paket Tour
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Paket</th>
                                        <th>Deskripsi</th>
                                        <th>Jam Operasional</th>
                                        <th>Harga Paket</th>
                                        <th>Kuota</th>
                                        <th>Tanggal Available</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paketTours as $key => $paket)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $paket->nama_paket }}</td>
                                            <td>{{ $paket->deskripsi }}</td>
                                            <td>{{ $paket->jam_operasional }}</td>
                                            <td>Rp {{ number_format($paket->harga_paket, 0, ',', '.') }}</td>
                                            <td>{{ $paket->kuota }}</td>
                                            <td>
                                                @if ($paket->tanggalAvailables && $paket->tanggalAvailables->count())
                                                    @foreach ($paket->tanggalAvailables as $tgl)
                                                        <span class="badge badge-info mb-1">{{ $tgl->tanggal }} (Kuota:
                                                            {{ $tgl->kuota }})</span><br>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('paket-tours.edit', $paket->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('paket-tours.destroy', $paket->id) }}"
                                                    method="POST" style="display:inline-block"
                                                    onsubmit="return confirm('Yakin hapus paket ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                Belum ada data paket tour.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
