@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($booking) ? 'Edit Booking' : 'Add New Booking' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Booking Data</a></li>
                        <li class="breadcrumb-item active">{{ isset($booking) ? 'Edit' : 'Add' }}</li>
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
                            <h3 class="card-title">{{ isset($booking) ? 'Edit Booking Form' : 'Add Booking Form' }}</h3>
                        </div>

                        <form
                            action="{{ isset($booking) ? route('bookings.update', $booking->id) : route('bookings.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($booking))
                                @method('PUT')
                            @endif

                            <div class="card-body">

                                <h5 class="text-primary border-bottom pb-2 mb-3">Package Options</h5>

                                <div class="form-group">
                                    <label>Tour Package</label>
                                    <select name="paket_tour_id" class="form-control" required>
                                        <option value="">-- Select Tour Package --</option>
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
                                    <label>Number of Participants</label>
                                    <input type="number" name="jumlah_peserta" class="form-control"
                                        value="{{ old('jumlah_peserta', $booking->jumlah_peserta ?? '') }}"
                                        placeholder="Enter number of participants (Min: 1)" min="1" required>
                                </div>

                                <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">Customer Information</h5>

                                <div class="form-group">
                                    <label>Full Name (as per ID)</label>
                                    <input type="text" name="customer_name" class="form-control"
                                        value="{{ old('customer_name', $booking->customer_name ?? auth()->user()->name) }}"
                                        placeholder="Enter full name" required>
                                </div>

                                <div class="form-group">
                                    <label>Customer Email</label>
                                    <input type="email" name="customer_email" class="form-control"
                                        value="{{ old('customer_email', $booking->customer_email ?? auth()->user()->email) }}"
                                        placeholder="Enter active email" required>
                                </div>

                                <div class="form-group">
                                    <label>WhatsApp / Phone Number</label>
                                    <input type="text" name="customer_phone" class="form-control"
                                        value="{{ old('customer_phone', $booking->customer_phone ?? '') }}"
                                        placeholder="Example: 081234567890" required>
                                </div>

                                @if (isset($booking))
                                    <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">Transaction Status</h5>
                                    <div class="form-group">
                                        <label>Booking Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="paid" {{ $booking->status == 'paid' ? 'selected' : '' }}>Paid
                                            </option>
                                            <option value="confirmed"
                                                {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="cancelled"
                                                {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                @endif

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    {{ isset($booking) ? 'Update Data' : 'Save & Proceed to Payment' }}
                                </button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                                    Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
