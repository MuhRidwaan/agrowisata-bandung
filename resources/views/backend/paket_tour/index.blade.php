@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tour Package Data</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Tour Package Data
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">
                    Tour Package Data
                </h3>

                <div class="ml-auto d-flex align-items-center gap-2">

                    <form method="GET" action="{{ route('paket-tours.index') }}" class="d-flex align-items-center mr-2">
                        @if(request('created_from'))<input type="hidden" name="created_from" value="{{ request('created_from') }}">@endif
                        @if(request('created_to'))<input type="hidden" name="created_to" value="{{ request('created_to') }}">@endif
                        <div class="input-group input-group-sm" style="width:220px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama paket...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('paket-tours.export') }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                    <a href="{{ route('paket-tours.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Tour Package
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('paket-tours.index') }}" class="mb-3 d-flex align-items-center flex-wrap">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    
                    @role('Super Admin')
                    <label class="mr-2 mb-0">Vendor:</label>
                    <select name="vendor_id" class="form-control mr-3 mb-1" style="width:auto;">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                    @endrole

                    <label class="mr-2 mb-0">Tanggal dibuat:</label>
                    <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control mr-2 mb-1" style="width:auto;">
                    <span class="mx-1">s/d</span>
                    <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control mr-2 mb-1" style="width:auto;">
                    <button type="submit" class="btn btn-secondary btn-sm mb-1">Filter</button>
                    @if(request('search') || request('created_from') || request('created_to') || request('vendor_id'))
                        <a href="{{ route('paket-tours.index') }}" class="btn btn-link btn-sm ml-2 mb-1">Reset</a>
                    @endif
                </form>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Package Name</th>
                            <!-- <th>Description</th> -->
                            <th style="min-width: 150px;">Operational Hours</th>
                            <th>Vendor</th>
                            <th class="text-right" style="min-width: 120px;">Price</th>
                            <th class="text-right" style="min-width: 140px;">Bundling Price</th>
                            <th class="text-right" style="min-width: 130px;">Bundling People</th>
                            <th style="min-width: 180px;">Bundling Photos</th>
                            <!-- <th>Available Dates</th> -->
                            <!-- <th>Quota</th> -->
                            <!-- <th>Activities</th> -->
                            <th style="min-width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paketTours as $paket)
                            <tr>
                                <td>{{ $loop->iteration + ($paketTours->currentPage() - 1) * $paketTours->perPage() }}</td>

                                <td>{{ $paket->nama_paket }}</td>

                                <!-- <td>{{ $paket->deskripsi }}</td> -->

                                <td class="operational-hours-cell">
                                    <span class="operational-hours-badge">
                                        {{ $paket->jam_operasional }}
                                    </span>
                                </td>

                                <td>
                                    {{ $paket->vendor->name ?? '-' }}
                                </td>

                                {{-- PRICE --}}
                                <td class="text-right" style="white-space: nowrap;">
                                    @if ($paket->harga_paket)
                                        <span class="d-inline-block">Rp {{ number_format($paket->harga_paket, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="text-right" style="white-space: nowrap;">
                                    @if ($paket->bundlings->count())
                                        @foreach ($paket->bundlings as $bundling)
                                            <div class="mb-2">
                                                <span class="badge badge-success">
                                                    Rp {{ number_format($bundling->bundle_price, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="text-right" style="white-space: nowrap;">
                                    @if ($paket->bundlings->count())
                                        @foreach ($paket->bundlings as $bundling)
                                            <div class="mb-2">
                                                <span class="badge badge-info">
                                                    {{ number_format($bundling->people_count, 0, ',', '.') }} orang
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="bundling-photos-cell">
                                    @if ($paket->bundlings->count())
                                        @php
                                            $allBundlingPhotos = $paket->bundlings
                                                ->flatMap(function ($bundling) {
                                                    return $bundling->photos;
                                                })
                                                ->values();
                                        @endphp

                                        @if ($allBundlingPhotos->count())
                                            <div class="bundling-photo-grid">
                                                @foreach ($allBundlingPhotos->take(4) as $photo)
                                                    <button type="button"
                                                        class="btn p-0 border-0 bg-transparent bundling-photo-trigger bundling-photo-thumb"
                                                        data-photo-url="{{ $photo->photo_url }}"
                                                        data-photo-label="{{ $paket->nama_paket }}">
                                                        <img
                                                            src="{{ $photo->photo_url }}"
                                                            alt="Bundling Photo"
                                                            class="rounded border bundling-photo-thumb-image">
                                                    </button>
                                                @endforeach

                                                @if ($allBundlingPhotos->count() > 4)
                                                    <div class="bundling-photo-more">
                                                        +{{ $allBundlingPhotos->count() - 4 }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">Belum ada foto</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- AVAILABLE DATES
                                <td>
                                    @if ($paket->tanggalAvailables && $paket->tanggalAvailables->count())
                                        @foreach ($paket->tanggalAvailables as $tgl)
                                            <span class="badge badge-info mb-1">
                                                {{ $tgl->tanggal }}
                                            </span>
                                            <br>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                --}}

                                <!--
                                <td>
                                    @if ($paket->tanggalAvailables && $paket->tanggalAvailables->count())
                                        {{ $paket->tanggalAvailables->sum('kuota') }}
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                -->

                                <!--
                                <td>
                                    @if (is_array($paket->aktivitas))
                                        @foreach ($paket->aktivitas as $item)
                                            <span class="badge badge-info mb-1">{{ $item }}</span><br>
                                        @endforeach
                                    @elseif ($paket->aktivitas)
                                        {{ $paket->aktivitas }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                -->

                                {{-- ACTION --}}
                                <td class="action-cell">
                                    <div class="action-group">
                                        <a href="{{ route('paket-tours.show', $paket->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('paket-tours.edit', $paket->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('paket-tours.destroy', $paket->id) }}" method="POST" class="form-delete mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">
                                    No tour package data available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="mt-3 d-flex justify-content-end">
                    {{ $paketTours->links() }}
                </div>
            </div>

        </div>
    </div>
</section>

{{-- SUCCESS ALERT --}}
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

<div class="modal fade" id="bundlingPhotoPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bundlingPhotoPreviewTitle">Preview Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="bundlingPhotoPreviewImage"
                    src=""
                    alt="Bundling Preview"
                    class="img-fluid rounded"
                    style="max-height: 70vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

{{-- DELETE CONFIRMATION (FIXED MULTIPLE FORMS) --}}
<script>
    document.querySelectorAll('.bundling-photo-trigger').forEach(function (button) {
        button.addEventListener('click', function () {
            const modalEl = document.getElementById('bundlingPhotoPreviewModal');
            const imageEl = document.getElementById('bundlingPhotoPreviewImage');
            const titleEl = document.getElementById('bundlingPhotoPreviewTitle');

            if (!modalEl || !imageEl) {
                return;
            }

            imageEl.src = this.dataset.photoUrl || '';
            titleEl.textContent = this.dataset.photoLabel || 'Preview Foto';

            if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                window.jQuery(modalEl).modal('show');
            }
        });
    });

    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete?',
                text: "Deleted data cannot be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

<style>
    .action-cell {
        min-width: 140px;
        vertical-align: middle;
    }

    .action-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .action-group .btn,
    .action-group .form-delete {
        margin: 0;
    }

    .operational-hours-cell {
        min-width: 150px;
        white-space: nowrap;
        vertical-align: middle;
    }

    .operational-hours-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: #f4f8f5;
        border: 1px solid #dbe8df;
        color: #274c3a;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    .bundling-photos-cell {
        min-width: 180px;
        vertical-align: top;
    }

    .bundling-photo-grid {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        min-width: max-content;
    }

    .bundling-photo-thumb {
        line-height: 0;
        border-radius: 0.35rem;
        overflow: hidden;
    }

    .bundling-photo-thumb-image {
        width: 42px;
        height: 42px;
        object-fit: cover;
        display: block;
    }

    .bundling-photo-more {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        border-radius: 0.35rem;
        background: #f8f9fa;
        color: #6c757d;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>

@endsection
