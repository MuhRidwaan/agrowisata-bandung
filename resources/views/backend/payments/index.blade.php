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

                                            <!-- UPDATE STATUS BADGE -->
                                            <td class="text-center">
                                                @if ($payment->status == 'success')
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i>
                                                        Paid</span>
                                                @elseif ($payment->status == 'failed')
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i>
                                                        Expired / Failed</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Waiting
                                                        Payment</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($payment->status == 'pending')
                                                    <!-- Tombol Bayar via Midtrans -->
                                                    <button class="btn btn-primary btn-sm btn-pay mb-1"
                                                        data-token="{{ $payment->snap_token }}"
                                                        data-url="{{ route('payments.paid', $payment->id) }}"
                                                        title="Pay with Midtrans">
                                                        <i class="fas fa-money-bill-wave"></i> Pay
                                                    </button>

                                                    <!-- Tombol Tandai Lunas (Manual) -->
                                                    <form action="{{ route('payments.paid', $payment->id) }}"
                                                        method="POST" style="display:inline-block" class="form-mark-paid">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm mb-1"
                                                            title="Mark as Paid Manually">
                                                            <i class="fas fa-check"></i> Mark Paid
                                                        </button>
                                                    </form>

                                                    <!-- Tombol Batalkan (Manual) -->
                                                    <form action="{{ route('payments.cancel', $payment->id) }}"
                                                        method="POST" style="display:inline-block" class="form-cancel">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm mb-1"
                                                            title="Cancel if Expired">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>
                                                @elseif ($payment->status == 'success')
                                                    <span class="text-success font-weight-bold mr-2"><i
                                                            class="fas fa-check"></i> Paid</span>
                                                    <!-- PRINT INVOICE BUTTON -->
                                                    <a href="{{ route('payments.invoice', $payment->id) }}"
                                                        class="btn btn-info btn-sm" title="Print Invoice">
                                                        <i class="fas fa-print"></i> Invoice
                                                    </a>
                                                @elseif ($payment->status == 'failed')
                                                    <span class="text-danger font-weight-bold mr-2"><i
                                                            class="fas fa-ban"></i>
                                                        Cancelled</span>
                                                    <!-- Tombol Tandai Lunas (Manual) untuk status Gagal -->
                                                    <form action="{{ route('payments.paid', $payment->id) }}"
                                                        method="POST" style="display:inline-block" class="form-mark-paid">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm mb-1"
                                                            title="Mark as Paid Manually">
                                                            <i class="fas fa-check"></i> Mark Paid
                                                        </button>
                                                    </form>
                                                @elseif ($payment->status == 'success')
                                                    <span class="text-success font-weight-bold mr-2"><i
                                                            class="fas fa-check"></i> Paid</span>

                                                    <a href="{{ route('payments.invoice', $payment->id) }}"
                                                        class="btn btn-info btn-sm mb-1" title="Print Invoice">
                                                        <i class="fas fa-print"></i> Invoice
                                                    </a>

                                                    {{-- <form action="{{ route('payments.send_email', $payment->id) }}"
                                                        method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-warning btn-sm mb-1 text-white"
                                                            title="Kirim Ulang Email Invoice">
                                                            <i class="fas fa-envelope"></i> Email
                                                        </button>
                                                    </form> --}}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
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
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
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
                Swal.fire({
                    title: 'Cancel Payment?',
                    text: "If the transaction in Midtrans has Expired, click Yes to cancel this order.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Cancel!',
                    cancelButtonText: 'Back'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
