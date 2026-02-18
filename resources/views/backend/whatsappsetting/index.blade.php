@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid pl-3">

        <h1 class="mb-3">Setting WhatsApp</h1>

        <div class="mb-3">
            <a href="{{ route('whatsappsetting.create') }}" class="btn btn-primary">
                + Tambah Data
            </a>
        </div>

        <div class="bg-white p-2 rounded shadow-sm">

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nomor WA</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($settings as $setting)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $setting->phone_number }}</td>

                            <td>{{ $setting->message_template }}</td>

                            <td>
                                @if ($setting->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Non Aktif</span>
                                @endif
                            </td>

                            <td>
                                <!-- EDIT -->
                                <a href="{{ route('whatsappsetting.edit', $setting->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <!-- DELETE -->
                                <form action="{{ route('whatsappsetting.destroy', $setting->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin mau hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Belum ada data WhatsApp
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </div>
@endsection
