@extends('backend..main_dashboard')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $edit ? 'Edit' : 'Add' }} Available Date</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('tanggal-available.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="POST"
                    action="{{ $edit ? route('tanggal-available.update', $tanggalAvailable) : route('tanggal-available.store') }}">
                    @csrf
                    @if ($edit)
                        @method('PUT')
                    @endif
                    <div class="form-group">
                        <label for="paket_tour_id">Tour Package</label>
                        <select name="paket_tour_id" id="paket_tour_id" class="form-control" required>
                            <option value="">-- Select Tour Package --</option>
                            @foreach ($paketTours as $paket)
                                <option value="{{ $paket->id }}"
                                    {{ old('paket_tour_id', $tanggalAvailable->paket_tour_id) == $paket->id ? 'selected' : '' }}>
                                    {{ $paket->nama_paket }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Date</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                            value="{{ old('tanggal', $tanggalAvailable->tanggal) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="kuota">Quota</label>
                        <input type="number" name="kuota" id="kuota" class="form-control" min="1"
                            value="{{ old('kuota', $tanggalAvailable->kuota) }}" required placeholder="Minimum 1">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="aktif"
                                {{ old('status', $tanggalAvailable->status) == 'aktif' ? 'selected' : '' }}>Active</option>
                            <option value="nonaktif"
                                {{ old('status', $tanggalAvailable->status) == 'nonaktif' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ $edit ? 'Update' : 'Save' }}</button>
                    <a href="{{ route('tanggal-available.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection