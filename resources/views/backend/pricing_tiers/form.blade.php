@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">

        <h4 class="mb-3">{{ isset($tier) ? 'Edit Pricing Tier' : 'Tambah Pricing Tier' }}</h4>

        <div class="card">
            <div class="card-body">

                <form method="POST"
                    action="{{ isset($tier) ? route('pricing-tiers.update', $tier->id) : route('pricing-tiers.store') }}">
                    @csrf
                    @if (isset($tier))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label>Paket Tour</label>
                        <select name="tour_package_id" class="form-control" required>
                            <option value="">-- pilih paket --</option>
                            @foreach ($packages as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('tour_package_id', $tier->tour_package_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $tier->name ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="price" class="form-control"
                            value="{{ old('price', $tier->price ?? '') }}" required>
                    </div>

                    <button class="btn btn-success">Simpan</button>
                    <a href="{{ route('pricing-tiers.index') }}" class="btn btn-secondary">Kembali</a>

                </form>

            </div>
        </div>

    </div>
@endsection
