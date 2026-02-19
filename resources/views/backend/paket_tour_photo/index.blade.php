@extends('backend..main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gallery Foto Paket Tour</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Gallery Foto</li>
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
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h3 class="card-title mb-2">Data Gallery Foto</h3>
                            <a href="{{ route('paket-tour-photos.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Foto
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Paket Tour</th>
                                        <th>Path Foto</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($photos as $key => $photo)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $photo->paketTour->nama_paket ?? '-' }}</td>
                                            <td>{{ $photo->path_foto }}</td>
                                            <td>
                                                <a href="{{ route('paket-tour-photos.edit', $photo->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('paket-tour-photos.destroy', $photo->id) }}"
                                                    method="POST" style="display:inline-block"
                                                    onsubmit="return confirm('Yakin hapus foto ini?')">
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
                                            <td colspan="4" class="text-center">
                                                Belum ada data foto.
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
