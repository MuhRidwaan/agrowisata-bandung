@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">


        </br>

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Paket Tour</h4>
            <a href="{{ route('tour-packages.create') }}" class="btn btn-primary">
                + Tambah Paket
            </a>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- TABLE CARD --}}
        <div class="card">
            <div class="card-body table-responsive p-0">

                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="60">No </th>
                            <th>Thumbnail</th>
                            <th>Nama Paket</th>
                            <th>Lokasi</th>
                            <th>Jam</th>
                            <!-- <th>Kuota</th> -->
                            <th>Harga Dasar</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($packages as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- THUMBNAIL --}}
                                <td>
                                    @if ($item->thumbnail)
                                        <img src="{{ Storage::url($item->thumbnail) }}" width="70">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>

                                {{-- DATA --}}
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->location }}</td>
                                <td>{{ $item->start_time }} - {{ $item->end_time }}</td>
                                <!-- <td>{{ $item->quota }} Orang</td> -->
                                <td>Rp {{ number_format($item->base_price) }}</td>

                                {{-- STATUS --}}
                                <td>
                                    @if ($item->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td>
                                    <a href="{{ route('tour-packages.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('tour-packages.destroy', $item->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')

                                        <button onclick="return confirm('Hapus paket ini?')" class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection
