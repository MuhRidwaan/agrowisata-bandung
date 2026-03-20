<!-- Footer -->
<footer id="kontak" class="footer-agro py-5">
    <div class="container">
        <div class="row g-4 ">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-0 mb-2">
                    <img src="{{ setting_asset_url('app_logo') }}" alt="AgroBandung Logo" style="height:90px; width:auto;">
                    <i class="bi bi-leaf text-accent"></i>
                    <span class="font-display fw-bold fs-5">{{ get_setting('app_name', 'AgroBandung') }}</span>
                </div>
                <p class="text-white-50 small">
                    Platform pemesanan tiket wisata agro terbaik di Bandung.
                </p>
            </div>
            <div class="col-md-3">
                <h5 class="font-display fw-semibold mb-3">Kontak</h5>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="bi bi-telephone"></i>
                        <span>{{ get_setting('contact_phone','+62 856 2455 4616') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="bi bi-envelope"></i>
                        <span>{{ get_setting('contact_email','agrotourisminbandung@gmail.com') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ get_setting('contact_address','Bandung, Jawa Barat') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 ms-md-auto">
                <h5 class="font-display fw-semibold mb-3">Jam Operasional</h5>
                <div class="text-white-50 small">
                    <p class="mb-1">
                        {{ get_setting('weekday_hours','Senin - Jumat: 08:00 - 18:00') }}
                    </p>
                    <p class="mb-0">
                        {{ get_setting('weekend_hours','Sabtu - Minggu: 07:00 - 19:00') }}
                    </p>
                </div>
            </div>
        </div>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
        <p class="text-center text-white-50 small mb-0">
            © {{ date('Y') }} {{ get_setting('app_name','AgroBandung') }}. All rights reserved.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@if(isset($paket))
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    window.BOOKING_CONFIG = {
        name: '{{ $paket->nama_paket ?? "AgroBandung" }}',
        location: '{{ $paket->vendor->area->name ?? "Bandung" }}',
        basePrice: {{ $paket->harga_paket ?? 0 }},
        pricingRules: @json($paket->pricingRules ?? []),
        waNumber: '{{ preg_replace("/[^0-9]/", "", $paket->vendor->whatsappsetting->phone_number ?? "") }}',
        waContact: '{{ $paket->vendor->name ?? "Admin" }}',
        storeUrl: '{{ route("booking.store") }}',
        csrfToken: '{{ csrf_token() }}',
        invoiceUrl: '{{ url("/pembayaran/invoice") }}',
        invoiceEmailUrl: '{{ url("/pembayaran/invoice-email") }}',
        resumeBaseUrl: '{{ url("/pembayaran/lanjut") }}'
    };
</script>
<script src="{{ asset('frontend/js/booking.js') }}?v={{ time() }}"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    var raw = localStorage.getItem('last_pending_booking');
    if (!raw) return;

    var data;
    try {
        data = JSON.parse(raw);
    } catch (e) {
        localStorage.removeItem('last_pending_booking');
        return;
    }

    if (!data || !data.booking_code) {
        localStorage.removeItem('last_pending_booking');
        return;
    }

    fetch(@json(url('/pembayaran/status')) + '/' + encodeURIComponent(data.booking_code), {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(function (response) {
        if (!response.ok) {
            throw new Error('Invalid pending booking state');
        }
        return response.json();
    })
    .then(function (result) {
        if (!result || !result.active || !result.booking_code || !result.resume_url) {
            localStorage.removeItem('last_pending_booking');
            return;
        }

        localStorage.setItem('last_pending_booking', JSON.stringify({
            booking_code: result.booking_code,
            total_price: result.total_price || 0,
            resume_url: result.resume_url,
            saved_at: new Date().toISOString()
        }));

        var wrapper = document.createElement('div');
        wrapper.style.position = 'fixed';
        wrapper.style.right = '16px';
        wrapper.style.bottom = '16px';
        wrapper.style.zIndex = '2000';
        wrapper.style.maxWidth = '340px';
        wrapper.innerHTML =
            '<div style="background:#fff;border:1px solid #dee2e6;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.12);padding:12px 14px;">'
          +   '<div style="font-size:13px;color:#6c757d;">Pembayaran belum selesai</div>'
          +   '<div style="font-weight:700;margin:2px 0 10px;">Kode: ' + result.booking_code + '</div>'
          +   '<a href="' + result.resume_url + '" style="display:inline-block;background:#198754;color:#fff;text-decoration:none;padding:8px 12px;border-radius:8px;font-size:13px;">Lanjutkan Pembayaran</a>'
          +   '<button type="button" id="dismissPendingBooking" style="margin-left:8px;border:none;background:transparent;color:#6c757d;font-size:13px;">Tutup</button>'
          + '</div>';
        document.body.appendChild(wrapper);

        var dismissBtn = document.getElementById('dismissPendingBooking');
        if (dismissBtn) {
            dismissBtn.addEventListener('click', function () {
                localStorage.removeItem('last_pending_booking');
                wrapper.remove();
            });
        }
    })
    .catch(function () {
        localStorage.removeItem('last_pending_booking');
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var pills = document.querySelectorAll('.region-pill');
        var cards = document.querySelectorAll('.destination-card');
        var searchInput = document.getElementById('searchInput');
        var searchClear = document.getElementById('searchClear');
        var noResults = document.getElementById('noResults');
        var activeFilter = 'semua';

        function filterCards() {
            var query = searchInput.value.toLowerCase().trim();
            var visibleCount = 0;

            // Show/hide clear button
            if (query.length > 0) {
                searchClear.classList.remove('d-none');
            } else {
                searchClear.classList.add('d-none');
            }

            cards.forEach(function(card) {
                var region = card.dataset.region;
                var name = card.dataset.name || '';
                var cardText = card.textContent.toLowerCase();
                var matchRegion = (activeFilter === 'semua' || region === activeFilter);
                var matchSearch = (!query || cardText.indexOf(query) !== -1);

                if (matchRegion && matchSearch) {
                    card.style.display = '';
                    card.style.animation = 'none';
                    card.offsetHeight;
                    card.style.animation = 'regionFadeIn 0.4s ease forwards';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }

        // Region filter click
        pills.forEach(function(pill) {
            pill.addEventListener('click', function() {
                activeFilter = this.dataset.filter;
                pills.forEach(function(p) {
                    p.classList.remove('active');
                    p.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                filterCards();
            });
        });

        // Search input
        var debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(filterCards, 200);
        });

        // Clear search
        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            searchClear.classList.add('d-none');
            filterCards();
            searchInput.focus();
        });
    });
</script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ================= GLOBAL SWEET ALERT ================= --}}
@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        confirmButtonColor: '#2f6d4f',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        backdrop: 'rgba(0,0,0,0.4)'
    });
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "{{ session('error') }}",
        confirmButtonColor: '#d33'
    });
});
</script>
@endif

</body>

</html>
