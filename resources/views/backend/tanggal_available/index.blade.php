@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>Available Date</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Available Date
                    </li>
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

                    <!-- HEADER -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h3 class="card-title mb-2">
                                Available Date
                            </h3>

                            <div class="d-flex gap-2 flex-wrap">
                                {{-- Download Template --}}
                                <a href="{{ route('tanggal-available.download-template') }}"
                                   class="btn btn-outline-secondary btn-sm mr-2">
                                    <i class="fas fa-file-download"></i> Template
                                </a>

                                {{-- Export --}}
                                <a href="{{ route('tanggal-available.export', request()->query()) }}"
                                   class="btn btn-success btn-sm mr-2">
                                    <i class="fas fa-file-excel"></i> Export
                                </a>

                                {{-- Import Button (trigger modal) --}}
                                <button type="button"
                                        class="btn btn-info btn-sm mr-2"
                                        data-toggle="modal"
                                        data-target="#importModal">
                                    <i class="fas fa-file-upload"></i> Import
                                </button>

                                <a href="{{ route('tanggal-available.create') }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Date
                                </a>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Template import sekarang sudah dilengkapi sheet <strong>Referensi Paket Tour</strong> berisi ID dan nama paket yang valid agar input data tidak membingungkan.
                        </small>
                    </div>
                    <!-- END HEADER -->

                    <div class="card-body">

                        {{-- DATE FILTER --}}
                        <form method="GET" action="{{ route('tanggal-available.index') }}" class="mb-3 d-flex align-items-center flex-wrap">
                            <label class="mr-2 mb-0">Filter tanggal:</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control mr-2" style="width:auto;">
                            <span class="mx-1">s/d</span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control mr-2" style="width:auto;">
                            <button type="submit" class="btn btn-secondary btn-sm mr-2">Filter</button>
                            @if(request('date_from') || request('date_to'))
                                <a href="{{ route('tanggal-available.index') }}" class="btn btn-link btn-sm">Reset</a>
                            @endif
                        </form>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tour Package</th>
                                    <th>Date</th>
                                    <th>Quota</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($tanggalAvailables as $item)
                                    <tr>
                                        <td>{{ $tanggalAvailables->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->paketTour->nama_paket ?? '-' }}</td>
                                        <td>{{ $item->tanggal }}</td>
                                        <td>{{ $item->kuota }}</td>
                                        <td>
                                            <span class="badge badge-{{ $item->status == 'aktif' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>

                                            <!-- EDIT -->
                                            <a href="{{ route('tanggal-available.edit', $item) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> 
                                            </a>

                                            <!-- DELETE -->
                                            <form action="{{ route('tanggal-available.destroy', $item) }}"
                                                  method="POST"
                                                  style="display:inline-block"
                                                  class="form-delete">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> 
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>

                    <div class="card-footer clearfix">
                        {{ $tanggalAvailables->links() }}
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>


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

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
    });
</script>
@endif


<script>
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure you want to delete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

{{-- IMPORT MODAL --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('tanggal-available.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Available Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Upload File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
                        <small class="form-text text-muted">
                            Format: .xlsx, .xls, atau .csv (max 2MB).
                            <a href="{{ route('tanggal-available.download-template') }}">Download template di sini</a>.
                            Gunakan sheet <strong>Referensi Paket Tour</strong> untuk melihat acuan <code>paket_tour_id</code> dan <code>nama_paket</code> yang valid.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-file-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
