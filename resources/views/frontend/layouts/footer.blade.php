<!-- Footer -->
<footer id="kontak" class="footer-agro pt-5 pb-4">
    <div class="container">
        <div class="row g-4">

            <!-- Brand -->
            <div class="col-12 col-md-5 col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ setting_asset_url('app_logo') }}"
                         alt="{{ get_setting('app_name', 'AgroBandung') }} Logo"
                         style="height:36px; width:auto; border-radius:6px; flex-shrink:0;">
                    <span class="font-display fw-bold fs-5 text-white">{{ get_setting('app_name', 'AgroBandung') }}</span>
                </div>
                <p class="text-white-50 small lh-lg mb-3" style="max-width:320px;">
                    Platform pemesanan tiket wisata agro terbaik di Bandung. Nikmati keindahan alam dan pengalaman agrikultur yang tak terlupakan.
                </p>
                @php
                    $socialLinks = [
                        'instagram' => get_setting('social_instagram', ''),
                        'facebook'  => get_setting('social_facebook', ''),
                        'youtube'   => get_setting('social_youtube', ''),
                    ];
                    $hasSocial = array_filter($socialLinks);
                @endphp
                @if($hasSocial)
                <div class="d-flex gap-2">
                    @if($socialLinks['instagram'])
                    <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener" class="footer-social-btn" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    @endif
                    @if($socialLinks['facebook'])
                    <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener" class="footer-social-btn" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    @endif
                    @if($socialLinks['youtube'])
                    <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener" class="footer-social-btn" aria-label="YouTube">
                        <i class="bi bi-youtube"></i>
                    </a>
                    @endif
                </div>
                @endif
            </div>

            <!-- Kontak -->
            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                <h5 class="footer-heading">Kontak</h5>
                <ul class="list-unstyled footer-list">
                    @if(get_setting('contact_phone'))
                    <li>
                        <a href="tel:{{ get_setting('contact_phone') }}" class="footer-link">
                            <i class="bi bi-telephone-fill footer-link-icon flex-shrink-0"></i>
                            <span>{{ get_setting('contact_phone', '+62 856 2455 4616') }}</span>
                        </a>
                    </li>
                    @endif
                    @if(get_setting('contact_email'))
                    <li>
                        <a href="mailto:{{ get_setting('contact_email') }}" class="footer-link footer-link-email">
                            <i class="bi bi-envelope-fill footer-link-icon flex-shrink-0"></i>
                            <span class="footer-email-text">{{ get_setting('contact_email', 'agrotourisminbandung@gmail.com') }}</span>
                        </a>
                    </li>
                    @endif
                    @if(get_setting('contact_address'))
                    <li>
                        <span class="footer-link">
                            <i class="bi bi-geo-alt-fill footer-link-icon flex-shrink-0"></i>
                            <span>{{ get_setting('contact_address', 'Bandung, Jawa Barat') }}</span>
                        </span>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Jam Operasional -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <h5 class="footer-heading">Jam Operasional</h5>
                <ul class="list-unstyled footer-list mb-3">
                    <li class="footer-link">
                        <i class="bi bi-clock-fill footer-link-icon flex-shrink-0"></i>
                        <span>{{ get_setting('weekday_hours', 'Senin - Jumat: 08:00 - 18:00') }}</span>
                    </li>
                    <li class="footer-link">
                        <i class="bi bi-clock-fill footer-link-icon flex-shrink-0"></i>
                        <span>{{ get_setting('weekend_hours', 'Sabtu - Minggu: 07:00 - 19:00') }}</span>
                    </li>
                </ul>

                @php
                    $waNumber = preg_replace('/[^0-9]/', '', get_setting('contact_phone', ''));
                @endphp
                @if($waNumber)
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                   class="footer-wa-btn">
                    <i class="bi bi-whatsapp"></i>
                    <span>Chat WhatsApp</span>
                </a>
                @endif
            </div>

        </div>

        <hr class="footer-divider my-4">

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
            <p class="text-white-50 small mb-0">
                © {{ date('Y') }} {{ get_setting('app_name', 'AgroBandung') }}. All rights reserved.
            </p>
            <a href="{{ route('home') }}" class="text-white-50 small text-decoration-none hover-white">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button -->
<button id="scrollToTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas" title="Kembali ke atas">
    <i class="bi bi-arrow-up"></i>
</button>

<style>
.footer-agro {
    background-color: var(--agro-text);
    color: rgba(255,255,255,0.75);
}

.footer-heading {
    color: #fff;
    font-family: var(--font-display);
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 1rem;
    letter-spacing: 0.02em;
}

.footer-list {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.footer-link {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 0.85rem;
    line-height: 1.5;
    transition: color 0.2s ease;
}

a.footer-link:hover {
    color: rgba(255,255,255,0.95);
}

.footer-link-icon {
    color: var(--agro-accent);
    flex-shrink: 0;
    margin-top: 2px;
    font-size: 0.8rem;
}

.footer-divider {
    border-color: rgba(255,255,255,0.1);
}

.footer-social-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-size: 1rem;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
}

.footer-social-btn:hover {
    background: var(--agro-accent);
    color: #fff;
    transform: translateY(-2px);
}

.hover-white:hover {
    color: #fff !important;
}

/* Scroll to top */
.scroll-top-btn {
    position: fixed;
    bottom: 24px;
    right: 20px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--agro-primary);
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(45, 106, 79, 0.35);
    opacity: 0;
    transform: translateY(12px);
    transition: opacity 0.3s ease, transform 0.3s ease, background-color 0.2s ease;
    z-index: 1050;
    pointer-events: none;
}

.scroll-top-btn.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

.scroll-top-btn:hover {
    background: var(--agro-primary-light);
}

@media (max-width: 575.98px) {
    .footer-agro {
        text-align: center;
    }
    .footer-list {
        align-items: center;
    }
    .footer-link {
        justify-content: center;
    }
    .footer-social-btn {
        margin: 0 auto;
    }
    .d-flex.gap-2.mt-3 {
        justify-content: center;
    }
    .scroll-top-btn {
        bottom: 16px;
        right: 16px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@if (isset($paket))
    @php
        $activeBundlings = ($paket->bundlings ?? collect())
            ->where('is_active', true)
            ->values()
            ->map(function ($bundling) {
                return [
                    'id' => $bundling->id,
                    'label' => $bundling->label,
                    'people_count' => $bundling->people_count,
                    'bundle_price' => $bundling->bundle_price,
                    'description' => $bundling->description,
                ];
            })
            ->all();
    @endphp
    @if (get_setting('enable_midtrans', 'true') === 'true' && filled(config('midtrans.client_key')) && ! str_starts_with((string) config('midtrans.client_key'), 'YOUR_'))
        <script
            src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
    <script>
        window.BOOKING_CONFIG = {
            name: '{{ $paket->nama_paket ?? 'AgroBandung' }}',
            location: '{{ $paket->vendor->area->name ?? 'Bandung' }}',
            basePrice: {{ $paket->harga_paket ?? 0 }},
            bundlings: @json($activeBundlings),
            pricingRules: @json($paket->pricingRules ?? []),
            manualPayment: @php
                $firstChannel = collect(json_decode(get_setting('manual_payment_channels', '[]'), true) ?? [])
                    ->where('is_active', true)
                    ->first();
                echo json_encode([
                    'bankName'      => $firstChannel['name'] ?? null,
                    'accountNumber' => $firstChannel['account_number'] ?? null,
                    'accountName'   => $firstChannel['account_name'] ?? null,
                    'instructions'  => $firstChannel['instructions'] ?? null,
                    'type'          => $firstChannel['type'] ?? 'bank_transfer',
                    'qrImage'       => !empty($firstChannel['qr_image']) ? storage_asset_url($firstChannel['qr_image']) : null,
                ]);
            @endphp,
            waNumber: '{{ preg_replace('/[^0-9]/', '', $paket->vendor->whatsappsetting->phone_number ?? '') }}',
            waContact: '{{ $paket->vendor->name ?? 'Admin' }}',
            storeUrl: '{{ route('booking.store') }}',
            csrfToken: '{{ csrf_token() }}',
            invoiceUrl: '{{ url('/pembayaran/invoice') }}',
            invoiceEmailUrl: '{{ url('/pembayaran/invoice-email') }}',
            resumeBaseUrl: '{{ url('/pembayaran/lanjut') }}'
        };
    </script>
    <script src="{{ asset('frontend/js/booking.js') }}?v={{ time() }}"></script>
@endif

<script>
// Scroll to top button
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('scrollToTopBtn');
    if (!btn) return;

    window.addEventListener('scroll', function () {
        if (window.scrollY > 400) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    }, { passive: true });

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>

<script>
// Pending booking notification
document.addEventListener('DOMContentLoaded', function() {
    // Gabungkan pending_bookings (array baru) + last_pending_booking (lama) jadi satu list
    var pendingList = [];

    try {
        var rawArr = localStorage.getItem('pending_bookings');
        if (rawArr) pendingList = JSON.parse(rawArr) || [];
    } catch(e) {}

    // Backward compat: cek last_pending_booking juga
    try {
        var rawSingle = localStorage.getItem('last_pending_booking');
        if (rawSingle) {
            var single = JSON.parse(rawSingle);
            if (single && single.booking_code) {
                var exists = pendingList.some(function(b) { return b.booking_code === single.booking_code; });
                if (!exists) pendingList.push(single);
            }
        }
    } catch(e) {}

    if (pendingList.length === 0) return;

    var statusUrl = @json(url('/pembayaran/status'));

    // Cek semua booking ke server secara paralel
    var checks = pendingList.map(function(booking) {
        return fetch(statusUrl + '/' + encodeURIComponent(booking.booking_code), {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(r) { return r.ok ? r.json() : null; })
        .then(function(result) {
            if (!result || !result.active) {
                // Booking sudah selesai, hapus dari storage
                return null;
            }
            return {
                booking_code: result.booking_code,
                total_price: result.total_price || booking.total_price || 0,
                resume_url: result.resume_url || booking.resume_url,
            };
        })
        .catch(function() { return null; });
    });

    Promise.all(checks).then(function(results) {
        var activeBookings = results.filter(function(r) { return r !== null; });

        // Update storage — hanya simpan yang masih aktif
        if (activeBookings.length > 0) {
            localStorage.setItem('pending_bookings', JSON.stringify(activeBookings));
            localStorage.setItem('last_pending_booking', JSON.stringify(activeBookings[activeBookings.length - 1]));
        } else {
            localStorage.removeItem('pending_bookings');
            localStorage.removeItem('last_pending_booking');
            return;
        }

        // Cek apakah sudah di-dismiss di sesi ini
        var dismissedKey = 'pending_notif_dismissed_v2';
        if (sessionStorage.getItem(dismissedKey) === 'true') return;

        renderPendingNotif(activeBookings, dismissedKey);
    });

    function renderPendingNotif(bookings, dismissedKey) {
        var existing = document.getElementById('pendingBookingNotif');
        if (existing) existing.remove();

        var wrapper = document.createElement('div');
        wrapper.id = 'pendingBookingNotif';
        wrapper.style.cssText = 'position:fixed;right:16px;bottom:80px;z-index:1049;max-width:320px;width:calc(100% - 32px);';

        var inner = '<div style="background:#fff;border:1px solid #dee2e6;border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.12);padding:14px 16px;">';

        // Header
        inner += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        inner += '<div style="display:flex;align-items:center;gap:8px;">';
        inner += '<span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;flex-shrink:0;"></span>';
        inner += '<span style="font-size:12px;color:#6c757d;font-weight:600;">Pembayaran Belum Selesai</span>';
        inner += '</div>';
        inner += '<button id="dismissPendingBooking" style="background:none;border:none;color:#adb5bd;font-size:16px;cursor:pointer;padding:0;line-height:1;" title="Tutup">&#x2715;</button>';
        inner += '</div>';

        if (bookings.length === 1) {
            // Single booking
            var b = bookings[0];
            inner += '<div style="font-size:13px;color:#1a2e23;margin-bottom:4px;font-weight:700;">' + b.booking_code + '</div>';
            if (b.paket_name) {
                inner += '<div style="font-size:11px;color:#6c757d;margin-bottom:10px;">' + b.paket_name + '</div>';
            } else {
                inner += '<div style="margin-bottom:10px;"></div>';
            }
            inner += '<a href="' + b.resume_url + '" style="display:flex;align-items:center;justify-content:center;background:#2d6a4f;color:#fff;text-decoration:none;padding:9px 12px;border-radius:8px;font-size:13px;font-weight:600;width:100%;">Lanjutkan Pembayaran</a>';
        } else {
            // Multiple bookings
            inner += '<div style="font-size:12px;color:#6c757d;margin-bottom:8px;">' + bookings.length + ' booking menunggu pembayaran:</div>';
            inner += '<div style="display:flex;flex-direction:column;gap:6px;max-height:160px;overflow-y:auto;">';
            bookings.forEach(function(b) {
                inner += '<a href="' + b.resume_url + '" style="display:flex;align-items:center;justify-content:space-between;background:#f8f9f7;border:1px solid #e9ecef;border-radius:8px;padding:8px 10px;text-decoration:none;color:#1a2e23;gap:8px;">';
                inner += '<div style="min-width:0;">';
                inner += '<div style="font-weight:700;font-size:12px;">' + b.booking_code + '</div>';
                if (b.paket_name) {
                    inner += '<div style="font-size:11px;color:#6c757d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + b.paket_name + '</div>';
                }
                inner += '</div>';
                inner += '<span style="color:#2d6a4f;font-weight:600;font-size:11px;flex-shrink:0;">Bayar &rsaquo;</span>';
                inner += '</a>';
            });
            inner += '</div>';
        }

        inner += '</div>';
        wrapper.innerHTML = inner;
        document.body.appendChild(wrapper);

        document.getElementById('dismissPendingBooking').addEventListener('click', function() {
            // Simpan di sessionStorage — muncul lagi setelah refresh
            sessionStorage.setItem(dismissedKey, 'true');
            wrapper.remove();
        });
    }
});
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        confirmButtonColor: '#2d6a4f',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
    });
});
</script>
@endif

@if (session('error'))
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "{{ session('error') }}",
        confirmButtonColor: '#d33'
    });
});
</script>
@endif

@stack('scripts')
</body>
</html>
