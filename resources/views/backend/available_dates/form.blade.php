@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">{{ isset($date) ? 'Edit Tanggal Available' : 'Tambah Tanggal Available' }}</h4>
        <div class="card">
            <div class="card-body">
                <form method="POST"
                    action="{{ isset($date) ? route('available-dates.update', $date->id) : route('available-dates.store') }}">
                    @csrf
                    @if (isset($date))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label>Paket Tour</label>
                        <select name="tour_package_id" class="form-control" required>
                            <option value="">-- pilih paket --</option>
                            @foreach ($packages as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('tour_package_id', $date->tour_package_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control"
                            value="{{ old('date', $date->date ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Kuota</label>
                        <input type="number" name="quota" class="form-control" min="1"
                            value="{{ old('quota', $date->quota ?? '') }}" required>
                    </div>

                    @if (isset($date) && isset($date->booked))
                        <div class="mb-3">
                            <label>Booked</label>
                            <input type="number" class="form-control" value="{{ $date->booked }}" readonly>
                        </div>
                    @endif
                    <button class="btn btn-success">Simpan</button>
                    <a href="{{ route('available-dates.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection
