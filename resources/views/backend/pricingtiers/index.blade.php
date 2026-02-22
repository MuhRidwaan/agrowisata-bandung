@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Pricing Tier</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Pricing Tier</li>
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

                            <h3 class="card-title mb-2">Pricing Tier Data</h3>

                            <div class="d-flex align-items-center">
                                <a href="{{ route('pricingtiers.create') }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Pricing Tier
                                </a>
                            </div>

                        </div>
                    </div>
                    <!-- END HEADER -->

                    <div class="card-body">
                        <table class="table table-bordered table-hover">

                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tour Package</th>
                                    <th>Category Name</th>
                                    <th>Price</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($tiers as $key => $tier)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $tier->tourPackage->title ?? '-' }}</td>
                                        <td>{{ $tier->name }}</td>
                                        <td>Rp {{ number_format($tier->price, 0, ',', '.') }}</td>
                                        <td>
                                            <!-- EDIT -->
                                            <a href="{{ route('pricingtiers.edit', $tier->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- DELETE -->
                                            <form action="{{ route('pricingtiers.destroy', $tier->id) }}"
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
                                            No pricing tier data available.
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
            text: '{{ session('success') }}'
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
    </script>
@endif

<script>
    document.querySelectorAll('.form-delete').forEach(form => {
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

@endsection