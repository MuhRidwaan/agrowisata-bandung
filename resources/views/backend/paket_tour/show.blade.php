@extends('backend.main_dashboard')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tour Package Detail</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('paket-tours.index') }}">Tour Package Data</a>
                    </li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">Detail Tour Package</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $paketTour->deskripsi }}</dd>

                    <dt class="col-sm-3">Price</dt>
                    <dd class="col-sm-9">
                        @if ($paketTour->harga_paket)
                            Rp {{ number_format($paketTour->harga_paket, 0, ',', '.') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Bundling Price</dt>
                    <dd class="col-sm-9">
                        @if ($paketTour->is_bundling_available && $paketTour->harga_bundling)
                            <span class="badge badge-success">
                                Rp {{ number_format($paketTour->harga_bundling, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Activities</dt>
                    <dd class="col-sm-9">
                        @if (is_array($paketTour->aktivitas))
                            @foreach ($paketTour->aktivitas as $item)
                                <span class="badge badge-info mb-1">{{ $item }}</span><br>
                            @endforeach
                        @elseif ($paketTour->aktivitas)
                            {{ $paketTour->aktivitas }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Available Dates</dt>
                    <dd class="col-sm-9">
                        @if ($paketTour->tanggalAvailables && $paketTour->tanggalAvailables->count())
                            @foreach ($paketTour->tanggalAvailables as $tgl)
                                <span class="badge badge-info mb-1">{{ $tgl->tanggal }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Quota (from Available Dates)</dt>
                    <dd class="col-sm-9">
                        @if ($paketTour->tanggalAvailables && $paketTour->tanggalAvailables->count())
                            {{ $paketTour->tanggalAvailables->sum('kuota') }}
                            <small class="text-muted">(total dari {{ $paketTour->tanggalAvailables->count() }} tanggal)</small>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </dd>
                </dl>
                <a href="{{ route('paket-tours.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</section>
@endsection
