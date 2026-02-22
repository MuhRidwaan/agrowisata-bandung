@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Area Data</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Areas</li>
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

                                <h3 class="card-title mb-2">Area List</h3>

                                <div class="d-flex align-items-center">

                                    <!-- ADD -->
                                    <a href="{{ route('areas.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Add Area
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
                                        <th>Area Name</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($areas as $key => $area)
                                        <tr>
                                            <td>
                                                {{ ($areas->currentPage() - 1) * $areas->perPage() + $loop->iteration }}
                                            </td>

                                            <td>{{ $area->name }}</td>

                                            <td>

                                                <!-- EDIT -->
                                                <a href="{{ route('areas.edit', $area->id) }}"
                                                    class="btn btn-warning btn-sm"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- DELETE -->
                                                <form action="{{ route('areas.destroy', $area->id) }}" method="POST"
                                                    style="display:inline-block" class="form-delete">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>

                                                </form>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                No area data available
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                            <!-- PAGINATION -->
                            <div class="mt-3">
                                {{ $areas->links() }}
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- ALERT SUCCESS --}}
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

    {{-- DELETE CONFIRM --}}
    <script>
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This data will be permanently deleted",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
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