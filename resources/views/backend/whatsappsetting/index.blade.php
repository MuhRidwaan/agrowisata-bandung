@extends('backend.main_dashboard')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>WhatsApp Settings</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">WhatsApp Settings</li>
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

                            <h3 class="card-title mb-2">WhatsApp List</h3>

                            <div>
                                <a href="{{ route('whatsappsetting.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Data
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
                                    <th>Vendor</th>
                                    <th>WhatsApp Number</th>
                                    <th>Template</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($settings as $setting)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <!-- VENDOR -->
                                        <td>{{ $setting->vendor->name ?? '-' }}</td>

                                        <!-- PHONE -->
                                        <td>
                                            {{ \Illuminate\Support\Str::startsWith($setting->phone_number, '+') 
                                                ? $setting->phone_number 
                                                : '+' . $setting->phone_number }}
                                        </td>

                                        <!-- TEMPLATE -->
                                        <td>
                                            {{ \Illuminate\Support\Str::limit($setting->message_template, 50) }}
                                        </td>

                                        <td>

                                            <!-- WHATSAPP AUTO CHAT -->
                                            @php
                                                $phone = preg_replace('/[^0-9]/', '', $setting->phone_number);

                                                if (str_starts_with($phone, '0')) {
                                                    $phone = '62' . substr($phone, 1);
                                                }

                                                $message = str_replace(
                                                    '{nama_vendor}',
                                                    $setting->vendor->name ?? '-',
                                                    $setting->message_template
                                                );
                                            @endphp

                                            <a href="https://wa.me/{{ $phone }}?text={{ urlencode($message) }}"
                                                target="_blank"
                                                class="btn btn-success btn-sm"
                                                title="Open WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>

                                            <!-- EDIT -->
                                            <a href="{{ route('whatsappsetting.edit', $setting->id) }}"
                                                class="btn btn-warning btn-sm"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- DELETE -->
                                            <form action="{{ route('whatsappsetting.destroy', $setting->id) }}"
                                                method="POST"
                                                style="display:inline-block"
                                                class="form-delete">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>

                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No WhatsApp data available
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