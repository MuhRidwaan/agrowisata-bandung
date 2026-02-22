@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>Pricing Rule</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Pricing Rule
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
                                Pricing Rule Data
                            </h3>

                            <a href="{{ route('pricingrules.create') }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Rule
                            </a>
                        </div>
                    </div>
                    <!-- END HEADER -->

                    <div class="card-body table-responsive">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($rules as $item)
                                    <tr>
                                        <td>{{ $item->tourPackage->title ?? '-' }}</td>
                                        <td>{{ $item->min_pax }}</td>
                                        <td>{{ $item->max_pax }}</td>
                                        <td>{{ ucfirst($item->discount_type) }}</td>
                                        <td>
                                            {{ $item->discount_type == 'percent'
                                                ? $item->discount_value . '%'
                                                : 'Rp ' . number_format($item->discount_value, 0, ',', '.') }}
                                        </td>
                                        <td>

                                            <!-- EDIT -->
                                            <a href="{{ route('pricingrules.edit', $item->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- DELETE -->
                                            <form action="{{ route('pricingrules.destroy', $item->id) }}"
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
                                            No rules available
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


@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif


<script>
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Data will be permanently deleted!",
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

@endsection