@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">
        <br>
        <div class="d-flex justify-content-between mb-3">
            <h4>Gallery Foto Paket</h4>
            <a href="{{ route('tour-galleries.create') }}" class="btn btn-primary">Upload Foto</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width:40px">No </th>
                            <th>Paket Tour</th>
                            <th>Foto & Caption</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grouped = $photos->groupBy('tour_package_id'); @endphp
                        @forelse($grouped as $packageId => $items)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $items->first()->tourPackage->title ?? '-' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach ($items as $photo)
                                            <div class="text-center" style="width:120px;">
                                                <a href="{{ asset('storage/' . $photo->image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $photo->image) }}" alt=""
                                                        style="max-width:100px;max-height:80px;">
                                                </a>
                                                <div class="small mt-1">{{ $photo->caption }}</div>
                                                <form action="{{ route('tour-galleries.destroy', $photo->id) }}"
                                                    method="POST" onsubmit="return confirm('Yakin hapus foto ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-danger btn-sm mt-1">Hapus</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada foto</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
