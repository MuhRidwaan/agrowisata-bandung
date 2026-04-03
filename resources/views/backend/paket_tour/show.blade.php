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

                    <dt class="col-sm-3">Minimum Person</dt>
                    <dd class="col-sm-9">
                        @if ($paketTour->has_minimum_person && $paketTour->minimum_person)
                            {{ number_format($paketTour->minimum_person, 0, ',', '.') }} orang
                        @else
                            <span class="text-muted">Tidak diatur</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Bundling Price</dt>
                    <dd class="col-sm-9">
                        @if ($paketTour->bundlings->count())
                            @foreach ($paketTour->bundlings as $bundling)
                                <div class="mb-2">
                                    <span class="badge badge-success">
                                        Rp {{ number_format($bundling->bundle_price, 0, ',', '.') }}
                                    </span>
                                    @if($bundling->label)
                                        <span class="text-muted small ml-1">{{ $bundling->label }}</span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Bundling People</dt>
                    <dd class="col-sm-9">
                        @if ($paketTour->bundlings->count())
                            @foreach ($paketTour->bundlings as $bundling)
                                <div class="mb-2">
                                    <span class="badge badge-info">
                                        {{ number_format($bundling->people_count, 0, ',', '.') }} orang
                                    </span>
                                    @if($bundling->description)
                                        <div class="small text-muted mt-1">{{ $bundling->description }}</div>
                                    @endif
                                    @if($bundling->photos->count())
                                        <div class="row mt-2">
                                            @foreach($bundling->photos as $photo)
                                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                    <img src="{{ $photo->photo_url }}"
                                                        alt="Bundling Photo"
                                                        class="img-fluid rounded border"
                                                        style="height: 110px; width: 100%; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
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

                    <dt class="col-sm-3">Facilities</dt>
                    <dd class="col-sm-9">
                        @if (is_array($paketTour->facilities) && count(array_filter($paketTour->facilities)))
                            @foreach ($paketTour->facilities as $item)
                                @if (filled($item))
                                    <span class="badge badge-success mb-1">{{ $item }}</span><br>
                                @endif
                            @endforeach
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
