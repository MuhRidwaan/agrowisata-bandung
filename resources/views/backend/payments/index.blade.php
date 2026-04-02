@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Payment Data</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payment Data</li>
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
                                <h3 class="card-title mb-2">Incoming Payment List</h3>
                                <div class="d-flex align-items-center">
                                    <form action="{{ route('payments.index') }}" method="GET">
                                        <div class="input-group input-group-sm" style="width:250px;">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search code/user..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default"><i
                                                        class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- BODY -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Booking Code</th>
                                        <th>Customer Name</th>
                                        <th>Total Price</th>
                                        <th class="text-center">Method</th>
                                        <th class="text-center">Status</th>
                                        <th width="22%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                                            </td>
                                            <td><strong>{{ $payment->booking->booking_code ?? '-' }}</strong></td>
                                            <td>
                                                {{ $payment->booking->customer_name ?? ($payment->booking->user->name ?? '-') }}
                                                <br>
                                                <small
                                                    class="text-muted">{{ $payment->booking->customer_phone ?? '' }}</small>
                                            </td>
                                            <td>Rp {{ number_format($payment->booking->total_price ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if (($payment->payment_method ?? null) === 'manual_transfer')
                                                    <span class="badge badge-info">Manual Transfer</span>
                                                @elseif (($payment->payment_method ?? null) === 'midtrans' || $payment->snap_token)
                                                    <span class="badge badge-primary">Midtrans</span>
                                                @else
                                                    <span class="badge badge-secondary">-</span>
                                                @endif
                                            </td>

                                            <!-- UPDATE STATUS BADGE -->
                                            <td class="text-center">
                                                @if ($payment->status == 'success')
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i>
                                                        Paid</span>
                                                @elseif ($payment->status == 'failed')
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i>
                                                        Expired / Failed</span>
                                                @elseif ($payment->status == 'revision')
                                                    <span class="badge badge-warning"><i class="fas fa-redo"></i>
                                                        Perlu Revisi</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Waiting
                                                        Payment</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $isManual = ($payment->payment_method ?? null) === 'manual_transfer';
                                                    $isMidtrans = !$isManual && $payment->snap_token;
                                                @endphp

                                                @if ($payment->status == 'pending')

                                                    {{-- Tombol Pay: hanya untuk Midtrans --}}
                                                    @if ($isMidtrans)
                                                        <button class="btn btn-primary btn-sm btn-pay mb-1"
                                                            data-token="{{ $payment->snap_token }}"
                                                            data-url="{{ route('payments.paid', $payment->id) }}"
                                                            title="Pay with Midtrans">
                                                            <i class="fas fa-money-bill-wave"></i> Pay
                                                        </button>
                                                    @endif

                                                    {{-- Tombol Mark Paid --}}
                                                    <form action="{{ route('payments.paid', $payment->id) }}"
                                                        method="POST" style="display:inline-block" class="form-mark-paid">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm mb-1"
                                                            title="Mark as Paid">
                                                            <i class="fas fa-check"></i> Mark Paid
                                                        </button>
                                                    </form>

                                                    {{-- Tombol Cancel --}}
                                                    <form action="{{ route('payments.cancel', $payment->id) }}"
                                                        method="POST" style="display:inline-block" class="form-cancel"
                                                        data-method="{{ $isManual ? 'manual' : 'midtrans' }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm mb-1"
                                                            title="Cancel / Batalkan">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>

                                                    {{-- Tombol Revisi: hanya untuk manual transfer yang sudah upload --}}
                                                    @if ($isManual && $payment->transfer_proof)
                                                        <button type="button"
                                                            class="btn btn-warning btn-sm mb-1"
                                                            data-toggle="modal"
                                                            data-target="#revisionModal{{ $payment->id }}"
                                                            title="Minta Revisi Bukti">
                                                            <i class="fas fa-redo"></i> Revisi
                                                        </button>
                                                    @endif

                                                    {{-- Tombol Bukti: hanya untuk manual transfer yang sudah upload --}}
                                                    @if ($isManual && $payment->transfer_proof)
                                                        <button type="button"
                                                            class="btn btn-info btn-sm mb-1"
                                                            data-toggle="modal"
                                                            data-target="#proofModal{{ $payment->id }}"
                                                            title="Lihat Bukti Transfer">
                                                            <i class="fas fa-image"></i> Bukti
                                                        </button>
                                                    @endif

                                                @elseif ($payment->status == 'success')
                                                    <span class="text-success font-weight-bold mr-2">
                                                        <i class="fas fa-check"></i> Paid
                                                    </span>
                                                    <a href="{{ route('payments.invoice', $payment->id) }}"
                                                        class="btn btn-info btn-sm mb-1" title="Print Invoice">
                                                        <i class="fas fa-print"></i> Invoice
                                                    </a>
                                                    {{-- Bukti transfer tetap bisa dilihat meski sudah paid --}}
                                                    @if ($isManual && $payment->transfer_proof)
                                                        <button type="button"
                                                            class="btn btn-secondary btn-sm mb-1"
                                                            data-toggle="modal"
                                                            data-target="#proofModal{{ $payment->id }}"
                                                            title="Lihat Bukti Transfer">
                                                            <i class="fas fa-image"></i> Bukti
                                                        </button>
                                                    @endif

                                                @elseif ($payment->status == 'failed')
                                                    <span class="text-danger font-weight-bold mr-2">
                                                        <i class="fas fa-ban"></i> Cancelled
                                                    </span>
                                                    <form action="{{ route('payments.paid', $payment->id) }}"
                                                        method="POST" style="display:inline-block" class="form-mark-paid">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm mb-1"
                                                            title="Mark as Paid">
                                                            <i class="fas fa-check"></i> Mark Paid
                                                        </button>
                                                    </form>
                                                    @if ($isManual && $payment->transfer_proof)
                                                        <button type="button"
                                                            class="btn btn-secondary btn-sm mb-1"
                                                            data-toggle="modal"
                                                            data-target="#proofModal{{ $payment->id }}"
                                                            title="Lihat Bukti Transfer">
                                                            <i class="fas fa-image"></i> Bukti
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-money-check-alt fa-3x text-muted mb-2"></i><br>
                                                No payment data yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- PAGINATION -->
                            <div class="mt-4">
                                {{ $payments->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LOAD MIDTRANS SANDBOX SCRIPT -->
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
        data-client-key="{{ config('midtrans.client_key') }}">
</script>

    <!-- SWEETALERT LOGIC & MIDTRANS POP-UP -->
    <script>
        document.querySelectorAll('.btn-pay').forEach(button => {
            button.addEventListener('click', function() {
                var snapToken = this.getAttribute('data-token');
                var paymentUrl = this.getAttribute('data-url');

                if (!snapToken) {
                    Swal.fire('Error', 'Payment token not found!', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Continue Payment?',
                    text: "You will be directed to the secure Midtrans page to pay.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Pay Now!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.snap.pay(snapToken, {
                            onSuccess: function(result) {
                                Swal.fire('Success!',
                                    'Payment successful! Processing data...',
                                    'success');
                                var form = document.createElement('form');
                                form.method = 'POST';
                                form.action = paymentUrl;
                                var csrf = document.createElement('input');
                                csrf.type = 'hidden';
                                csrf.name = '_token';
                                csrf.value = '{{ csrf_token() }}';
                                form.appendChild(csrf);
                                document.body.appendChild(form);
                                form.submit();
                            },
                            onPending: function(result) {
                                Swal.fire('Waiting',
                                    'Please complete your payment.', 'info');
                            },
                            onError: function(result) {
                                Swal.fire('Failed',
                                    'Payment failed, please try again.', 'error');
                            },
                            onClose: function() {
                                Swal.fire('Closed',
                                    'You closed the screen before completing it.',
                                    'warning');
                            }
                        });
                    }
                });
            });
        });

        // SCRIPT FOR MANUAL CANCEL BUTTON CONFIRMATION
        document.querySelectorAll('.form-cancel').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const method = this.getAttribute('data-method');
                const isManual = method === 'manual';
                Swal.fire({
                    title: isManual ? 'Batalkan Pembayaran?' : 'Cancel Payment?',
                    text: isManual
                        ? 'Booking ini akan dibatalkan. Pastikan customer belum melakukan transfer.'
                        : 'If the transaction in Midtrans has Expired, click Yes to cancel this order.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: isManual ? 'Ya, Batalkan!' : 'Yes, Cancel!',
                    cancelButtonText: 'Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    <!-- MODAL REVISI BUKTI TRANSFER -->
    @foreach ($payments as $payment)
        @if (($payment->payment_method ?? null) === 'manual_transfer' && $payment->transfer_proof)
            <div class="modal fade" id="revisionModal{{ $payment->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-redo text-warning"></i> Minta Revisi — {{ $payment->booking->booking_code ?? '-' }}</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="{{ route('payments.revision', $payment->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p class="text-muted small mb-3">Bukti transfer akan direset. Customer akan diminta upload ulang dengan catatan dari Anda.</p>
                                <div class="form-group">
                                    <label class="font-weight-bold">Catatan untuk Customer <span class="text-danger">*</span></label>
                                    <textarea name="admin_note" class="form-control" rows="3" required
                                        placeholder="cth: Transfer kurang Rp50.000, mohon transfer ulang sesuai total tagihan."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-paper-plane"></i> Kirim Permintaan Revisi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- MODAL BUKTI TRANSFER -->
    @foreach ($payments as $payment)
        @if ($payment->transfer_proof)
            <div class="modal fade" id="proofModal{{ $payment->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Bukti Transfer — {{ $payment->booking->booking_code ?? '-' }}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ storage_asset_url($payment->transfer_proof) }}"
                                alt="Bukti Transfer"
                                class="img-fluid rounded"
                                style="max-height: 400px; object-fit: contain;">
                            <p class="small text-muted mt-2">
                                Diunggah: {{ $payment->transfer_proof_uploaded_at ? \Carbon\Carbon::parse($payment->transfer_proof_uploaded_at)->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ storage_asset_url($payment->transfer_proof) }}"
                                target="_blank" class="btn btn-secondary btn-sm">
                                <i class="fas fa-external-link-alt"></i> Buka di Tab Baru
                            </a>
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection
