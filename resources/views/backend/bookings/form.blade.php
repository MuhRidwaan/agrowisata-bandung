@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($booking) ? 'Edit Booking' : 'Tambah Booking' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Data Booking</a></li>
                        <li class="breadcrumb-item active">{{ isset($booking) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ isset($booking) ? 'Form Edit Booking' : 'Form Tambah Booking' }}</h3>
                        </div>

                        <form
                            action="{{ isset($booking) ? route('bookings.update', $booking->id) : route('bookings.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($booking))
                                @method('PUT')
                            @endif

                            <div class="card-body">

                                <div class="form-group">
                                    <label>Paket Tour</label>
                                    <select name="paket_tour_id" class="form-control" required>
                                        <option value="">-- Pilih Paket Tour --</option>
                                        @foreach ($pakets as $paket)
                                            <option value="{{ $paket->id }}"
                                                {{ (isset($booking) && $booking->paket_tour_id == $paket->id) || old('paket_tour_id') == $paket->id ? 'selected' : '' }}>
                                                {{ $paket->nama_paket }} (Rp
                                                {{ number_format($paket->harga_paket, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Jumlah Peserta</label>
                                    <input type="number" name="jumlah_peserta" class="form-control"
                                        value="{{ old('jumlah_peserta', $booking->jumlah_peserta ?? '') }}"
                                        placeholder="Masukkan jumlah peserta" min="1" required>
                                </div>

                                @if (isset($booking))
                                    <div class="form-group">
                                        <label>Status Booking</label>
                                        <select name="status" class="form-control" required>
                                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="confirmed"
                                                {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="cancelled"
                                                {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                @endif

                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary">
                                    {{ isset($booking) ? 'Update' : 'Simpan' }}
                                </button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
