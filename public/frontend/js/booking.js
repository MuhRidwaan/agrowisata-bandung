// Booking Wizard - Multi-step booking system with Midtrans Integration
'use strict';

let currentStep = 1;
let participantCount = 1;
let bookingCode = '';
let isProcessing = false;

const config = window.BOOKING_CONFIG || {
  name: 'AgroBandung', location: 'Bandung',
  basePrice: 50000, bundlings: [], pricingRules: [],
  storeUrl: '', csrfToken: '', invoiceUrl: ''
};

function getResumeUrl(bookingCode) {
  if (!config.resumeBaseUrl || !bookingCode) return '';
  return config.resumeBaseUrl.replace(/\/$/, '') + '/' + encodeURIComponent(bookingCode);
}

function getInvoiceEmailUrl(bookingCode) {
  if (!config.invoiceEmailUrl || !bookingCode) return '';
  return config.invoiceEmailUrl.replace(/\/$/, '') + '/' + encodeURIComponent(bookingCode);
}

function savePendingBooking(bookingData) {
  if (!bookingData || !bookingData.booking_code) return;
  localStorage.setItem('last_pending_booking', JSON.stringify({
    booking_code: bookingData.booking_code,
    total_price: bookingData.total_price || 0,
    resume_url: getResumeUrl(bookingData.booking_code),
    saved_at: new Date().toISOString()
  }));
}

function clearPendingBookingIfMatch(bookingCode) {
  const raw = localStorage.getItem('last_pending_booking');
  if (!raw) return;
  try {
    const data = JSON.parse(raw);
    if (!bookingCode || data.booking_code === bookingCode) {
      localStorage.removeItem('last_pending_booking');
    }
  } catch (e) {
    localStorage.removeItem('last_pending_booking');
  }
}

function formatCurrency(amount) {
  return 'Rp' + Math.round(amount).toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function() {
  initCalendar();
  initPaymentOptions();
  initParticipantCountInput();
  updateSummary();
  updateStepper();
  updateNavButtons();
});

function initParticipantCountInput() {
  var input = document.getElementById('participantCountInput');
  if (!input) return;

  input.addEventListener('input', syncParticipantCountFromInput);
  input.addEventListener('change', syncParticipantCountFromInput);
  syncParticipantCountFromInput();
}

function isBundlingActive() {
  return !!document.querySelector('.bundling-card.active');
}

function getSelectedBundling() {
  var activeBundling = document.querySelector('.bundling-card.active');
  if (!activeBundling) {
    return null;
  }

  return {
    id: parseInt(activeBundling.dataset.id, 10) || null,
    label: activeBundling.dataset.label || 'Bundling',
    people_count: parseInt(activeBundling.dataset.people, 10) || 1,
    bundle_price: Number(activeBundling.dataset.price || 0)
  };
}

function getSelectedPricingRuleLimits() {
  var selectedBundling = getSelectedBundling();
  if (selectedBundling) {
    return { min: selectedBundling.people_count, max: selectedBundling.people_count };
  }

  var activeCard = document.querySelector('.discount-card.active');
  if (!activeCard) {
    return { min: 1, max: null };
  }

  var min = parseInt(activeCard.dataset.min, 10);
  var max = activeCard.dataset.max ? parseInt(activeCard.dataset.max, 10) : null;

  return {
    min: Number.isNaN(min) || min < 1 ? 1 : min,
    max: Number.isNaN(max) ? null : max
  };
}

function updateParticipantLimitButtons() {
  var input = document.getElementById('participantCountInput');
  if (!input) return;

  var minusBtn = input.previousElementSibling;
  var plusBtn = input.nextElementSibling;
  if (!minusBtn || !plusBtn) return;

  var limits = getSelectedPricingRuleLimits();
  var value = parseInt(input.value, 10) || limits.min;
  var remaining = getRemainingQuota();
  var effectiveMax = limits.max;

  if (remaining !== null && remaining > 0) {
    effectiveMax = effectiveMax === null ? remaining : Math.min(effectiveMax, remaining);
  }

  if (isBundlingActive()) {
    minusBtn.disabled = true;
    plusBtn.disabled = true;
    input.setAttribute('readonly', 'readonly');
    input.classList.add('bg-light');
  } else {
    minusBtn.disabled = value <= limits.min;
    plusBtn.disabled = effectiveMax !== null && value >= effectiveMax;
    input.removeAttribute('readonly');
    input.classList.remove('bg-light');
  }

  minusBtn.classList.toggle('opacity-50', minusBtn.disabled);
  plusBtn.classList.toggle('opacity-50', plusBtn.disabled);
}

function syncParticipantCountFromInput() {
  var input = document.getElementById('participantCountInput');
  if (!input) return;

  var limits = getSelectedPricingRuleLimits();
  var val = parseInt(input.value, 10);
  if (!val || val < limits.min) val = limits.min;

  if (limits.max !== null && val > limits.max) {
    val = limits.max;
  }

  var remaining = getRemainingQuota();
  if (remaining !== null && remaining > 0 && val > remaining) {
    val = remaining;
    showToast('Jumlah peserta melebihi sisa kuota. Disesuaikan ke ' + remaining + ' orang.');
  }

  if (val < limits.min) {
    val = limits.min;
  }

  participantCount = val;
  input.value = val;
  input.min = limits.min;
  if (limits.max !== null) input.max = limits.max;
  else input.removeAttribute('max');

  var totalEl = document.getElementById('participantTotal');
  if (totalEl) totalEl.textContent = participantCount;

  updateSummary();
  updatePriceTiers();
  updateParticipantLimitButtons();
}

function increaseParticipantCount() {
  var input = document.getElementById('participantCountInput');
  if (!input) return;
  var limits = getSelectedPricingRuleLimits();
  var val = parseInt(input.value, 10) || limits.min;
  var nextValue = val + 1;

  if (limits.max !== null && nextValue > limits.max) {
    nextValue = limits.max;
  }

  var remaining = getRemainingQuota();
  if (remaining !== null && remaining > 0 && nextValue > remaining) {
    nextValue = remaining;
  }

  input.value = Math.max(limits.min, nextValue);
  syncParticipantCountFromInput();
}

function decreaseParticipantCount() {
  var input = document.getElementById('participantCountInput');
  if (!input) return;
  var limits = getSelectedPricingRuleLimits();
  var val = parseInt(input.value, 10) || limits.min;
  input.value = Math.max(limits.min, val - 1);
  syncParticipantCountFromInput();
}

function getRemainingQuota() {
  var sisaInput = document.getElementById('visitDateSisa');
  if (!sisaInput || !sisaInput.value) return null;
  var remaining = parseInt(sisaInput.value, 10);
  return Number.isNaN(remaining) ? null : remaining;
}

function initPaymentOptions() {
  document.querySelectorAll('.payment-method-card input[type="radio"]').forEach(function(r) {
    r.addEventListener('change', function() {
      document.querySelectorAll('.payment-method-card').forEach(function(c) { c.classList.remove('selected'); });
      this.closest('.payment-method-card').classList.add('selected');
    });
  });
}

function nextStep() {
  if (isProcessing) return;
  if (!validateStep(currentStep)) return;

  // Step 3 → Step 4: Submit booking to backend & open Midtrans
  if (currentStep === 3) {
    submitBooking();
    return;
  }

  if (currentStep < 4) {
    currentStep++;
    showStep(currentStep);
    updateStepper();
    updateNavButtons();
  }
}

function prevStep() {
  if (currentStep > 1) {
    currentStep--;
    showStep(currentStep);
    updateStepper();
    updateNavButtons();
  }
}

function validateStep(step) {
  if (step === 1) {
    var dateInput = document.getElementById('visitDate');
    if (!dateInput || !dateInput.value) {
      showToast('Silakan pilih tanggal kunjungan!');
      if (dateInput) dateInput.focus();
      return false;
    }
    return true;
  }
  if (step === 2) {
    var name = document.getElementById('customerName');
    var phone = document.getElementById('customerPhone');
    var email = document.getElementById('customerEmail');
    var paxInput = document.getElementById('participantCountInput');

    if (!name || !name.value.trim()) { showToast('Silakan isi nama penanggung jawab!'); if (name) name.focus(); return false; }
    if (!phone || !phone.value.trim()) { showToast('Silakan isi nomor telepon!'); if (phone) phone.focus(); return false; }
    if (!email || !email.value.trim()) { showToast('Silakan isi email!'); if (email) email.focus(); return false; }
    if (!paxInput || !parseInt(paxInput.value, 10) || parseInt(paxInput.value, 10) < 1) {
      showToast('Jumlah peserta minimal 1 orang!');
      if (paxInput) paxInput.focus();
      return false;
    }

    var remaining = getRemainingQuota();
    if (remaining !== null && parseInt(paxInput.value, 10) > remaining) {
      showToast('Jumlah peserta melebihi sisa kuota (' + remaining + ' orang).');
      paxInput.focus();
      return false;
    }

    syncParticipantCountFromInput();
    return true;
  }
  if (step === 3) {
    if (!document.querySelector('input[name="payment"]:checked')) {
      showToast('Silakan pilih metode pembayaran!');
      return false;
    }
    return true;
  }
  return true;
}

function showToast(msg) {
  var existing = document.querySelector('.booking-toast');
  if (existing) existing.remove();
  var toast = document.createElement('div');
  toast.className = 'booking-toast';
  toast.innerHTML = '<i class="bi bi-exclamation-triangle"></i> ' + msg;
  document.body.appendChild(toast);
  setTimeout(function() { toast.classList.add('show'); }, 10);
  setTimeout(function() { toast.classList.remove('show'); setTimeout(function() { toast.remove(); }, 300); }, 3000);
}

function showStep(step) {
  document.querySelectorAll('.booking-step').forEach(function(s) { s.classList.add('d-none'); });
  var el = document.getElementById('bookingStep' + step);
  if (el) {
    el.classList.remove('d-none');
    el.style.animation = 'none';
    el.offsetHeight;
    el.style.animation = null;
  }
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateStepper() {
  var items = document.querySelectorAll('.booking-stepper-item');
  items.forEach(function(item, i) {
    var num = i + 1;
    var number = item.querySelector('.step-number');
    var check = item.querySelector('.step-check');
    item.classList.remove('active', 'completed');
    if (num < currentStep) {
      item.classList.add('completed');
      if (number) number.classList.add('d-none');
      if (check) check.classList.remove('d-none');
    } else if (num === currentStep) {
      item.classList.add('active');
      if (number) number.classList.remove('d-none');
      if (check) check.classList.add('d-none');
    } else {
      if (number) number.classList.remove('d-none');
      if (check) check.classList.add('d-none');
    }
  });
  document.querySelectorAll('.booking-stepper-line').forEach(function(line, i) {
    if (i + 1 < currentStep) line.classList.add('completed');
    else line.classList.remove('completed');
  });
}

function updateNavButtons() {
  var backCol = document.getElementById('btnBackCol');
  var nextCol = document.getElementById('btnNextCol');
  var nextBtn = document.getElementById('btnNext');
  var nav = document.getElementById('bookingNav');
  var sidebar = document.getElementById('orderSummaryCol');
  var mainCol = document.getElementById('mainContentCol');

  if (currentStep === 1) {
    backCol.classList.add('d-none');
    nextCol.className = 'col-12';
    nextBtn.textContent = 'Lanjutkan';
    nav.classList.remove('d-none');
    if (sidebar) sidebar.classList.remove('d-none');
    if (mainCol) mainCol.className = 'col-lg-8';
  } else if (currentStep === 2) {
    backCol.classList.remove('d-none');
    nextCol.className = 'col-6';
    nextBtn.textContent = 'Lanjutkan';
    nav.classList.remove('d-none');
    if (sidebar) sidebar.classList.remove('d-none');
    if (mainCol) mainCol.className = 'col-lg-8';
  } else if (currentStep === 3) {
    backCol.classList.remove('d-none');
    nextCol.className = 'col-6';
    nextBtn.textContent = 'Bayar Sekarang';
    nav.classList.remove('d-none');
    if (sidebar) sidebar.classList.remove('d-none');
    if (mainCol) mainCol.className = 'col-lg-8';
  } else {
    nav.classList.add('d-none');
    if (sidebar) sidebar.classList.add('d-none');
    if (mainCol) mainCol.className = 'col-lg-12';
  }
}

function getPriceCalculation() {
  const selectedBundling = getSelectedBundling();
  const totalBase = selectedBundling ? Number(selectedBundling.bundle_price || 0) : participantCount * config.basePrice;
  let discount = 0;
  let appliedRule = null;
  let appliedBundling = null;
  var activeCard = document.querySelector('.discount-card.active');

  if (selectedBundling) {
    appliedBundling = {
      bundling_id: selectedBundling.id,
      bundling_label: selectedBundling.label,
      bundling_people: selectedBundling.people_count,
      harga_bundling: Number(selectedBundling.bundle_price || 0)
    };
  } else if (activeCard) {
    appliedRule = {
      min_pax: activeCard.dataset.min,
      max_pax: activeCard.dataset.max,
      discount_type: activeCard.dataset.type,
      discount_value: activeCard.dataset.value
    };

    if (appliedRule.discount_type === 'percent') {
      discount = (totalBase * parseFloat(appliedRule.discount_value)) / 100;
    } else if (appliedRule.discount_type === 'nominal') {
      discount = parseFloat(appliedRule.discount_value);
    }
  }

  return {
    totalBase,
    discount,
    totalPrice: totalBase - discount,
    appliedRule,
    appliedBundling
  };
}

function updatePriceTiers() {
  var tiers = document.querySelectorAll('.price-tier-card');
  var activeDiscountTier = document.querySelector('.discount-card.active');
  var activeBundlingTier = document.querySelector('.bundling-card.active');
  tiers.forEach(function(t) {
    if (t !== activeDiscountTier && t !== activeBundlingTier) {
      t.classList.remove('active');
    }
  });
}

function getUmkmTotal() {
  var total = 0;

  document.querySelectorAll('.umkm-item').forEach(function(item) {
    var price = Number(item.dataset.price || 0);
    var id = item.dataset.id;
    var qtyEl = id ? document.getElementById('qty-' + id) : null;
    var qty = qtyEl ? parseInt(qtyEl.textContent, 10) || 0 : 0;

    if (qty > 0) {
      total += price * qty;
    }
  });

  return total;
}

function updateSummary() {
  var dateInput = document.getElementById('visitDate');
  var sisaInput = document.getElementById('visitDateSisa');
  if (dateInput && dateInput.value) {
    var date = new Date(dateInput.value + 'T00:00:00');
    var el = document.getElementById('summaryDate');
    var dd = String(date.getDate()).padStart(2, '0');
    var mm = String(date.getMonth() + 1).padStart(2, '0');
    var yyyy = date.getFullYear();
    if (el) el.textContent = dd + '/' + mm + '/' + yyyy;

    // Show sisa kuota
    var kuotaRow = document.getElementById('summaryKuotaRow');
    var kuotaEl = document.getElementById('summaryKuota');
    if (kuotaRow && kuotaEl && sisaInput && sisaInput.value) {
      kuotaRow.classList.remove('d-none');
      kuotaEl.textContent = sisaInput.value + ' orang';
    }
  } else {
    var el = document.getElementById('summaryDate');
    if (el) el.textContent = '-';
    var kuotaRow = document.getElementById('summaryKuotaRow');
    if (kuotaRow) kuotaRow.classList.add('d-none');
  }
  
  const calc = getPriceCalculation();

  var pl = document.getElementById('summaryPriceLabel');
  if (pl) {
    if (calc.appliedBundling) {
      pl.textContent = (calc.appliedBundling.bundling_label || 'Paket bundling') + ' \u00b7 ' + calc.appliedBundling.bundling_people + ' orang';
    } else {
      pl.textContent = formatCurrency(config.basePrice) + ' \u00d7 ' + participantCount;
    }
  }
  
  var st = document.getElementById('summarySubtotal');
  if (st) st.textContent = formatCurrency(calc.totalBase);
  
  var discountRow = document.getElementById('discountRow');
  if (discountRow) {
    if (calc.discount > 0) {
      discountRow.innerHTML = '<div class="d-flex justify-content-between align-items-center small mt-3 summary-discount-row"><span>Diskon</span><span class="fw-medium">-' + formatCurrency(calc.discount) + '</span></div>';
    } else {
      discountRow.innerHTML = '';
    }
  }

  var tp = document.getElementById('totalPrice');
  if (tp) tp.textContent = formatCurrency(calc.totalPrice + getUmkmTotal());
}

// ================= SUBMIT BOOKING TO BACKEND =================
function submitBooking() {
  if (isProcessing) return;
  isProcessing = true;

  var btnNext = document.getElementById('btnNext');
  btnNext.disabled = true;
  btnNext.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

  // Gather form data
  var nameInput = document.getElementById('customerName');
  var phoneInput = document.getElementById('customerPhone');
  var emailInput = document.getElementById('customerEmail');
  syncParticipantCountFromInput();
  var dateInput = document.getElementById('visitDate');
  var paketId = document.getElementById('paketTourId').value;

  var payload = {
    paket_tour_id: paketId,
    jumlah_peserta: participantCount,
    use_bundling: isBundlingActive(),
    bundling_id: getSelectedBundling() ? getSelectedBundling().id : null,
    customer_name: nameInput ? nameInput.value.trim() : '',
    customer_email: emailInput ? emailInput.value.trim() : '',
    customer_phone: phoneInput ? phoneInput.value.trim() : '',
    visit_date: dateInput ? dateInput.value : ''
  };

  fetch(config.storeUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': config.csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(function(response) {
    if (!response.ok) {
      return response.json().then(function(err) { throw err; });
    }
    return response.json();
  })
  .then(function(data) {
    if (data.success && data.snap_token) {
      bookingCode = data.booking_code;
      savePendingBooking(data);

      // Open Midtrans Snap popup
      window.snap.pay(data.snap_token, {
        onSuccess: function(result) {
          handlePaymentResult('success', data, result);
        },
        onPending: function(result) {
          handlePaymentResult('pending', data, result);
        },
        onError: function(result) {
          handlePaymentResult('error', data, result);
        },
        onClose: function() {
          // User closed the popup without completing payment
          handlePaymentResult('pending', data, null);
        }
      });
    } else {
      showToast('Gagal membuat booking. Silakan coba lagi.');
    }
  })
  .catch(function(err) {
    console.error('Booking error:', err);
    var msg = 'Terjadi kesalahan. Silakan coba lagi.';
    if (err && err.errors) {
      var firstKey = Object.keys(err.errors)[0];
      msg = err.errors[firstKey][0];
    } else if (err && err.message) {
      msg = err.message;
    }
    showToast(msg);
  })
  .finally(function() {
    isProcessing = false;
    btnNext.disabled = false;
    btnNext.innerHTML = 'Bayar Sekarang';
  });
}

function handlePaymentResult(status, bookingData, midtransResult) {
  // Move to step 4
  currentStep = 4;
  showStep(4);
  updateStepper();
  updateNavButtons();

  if (status === 'success') {
    clearPendingBookingIfMatch(bookingData.booking_code);
    var emailDispatchUrl = getInvoiceEmailUrl(bookingData.booking_code);

    if (emailDispatchUrl) {
      fetch(emailDispatchUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': config.csrfToken,
          'Accept': 'application/json'
        }
      }).catch(function(error) {
        console.error('Invoice email dispatch error:', error);
      }).finally(function() {
        window.location.href = config.invoiceUrl + '/' + bookingData.booking_code;
      });
      return;
    }

    window.location.href = config.invoiceUrl + '/' + bookingData.booking_code;
  } else {
    savePendingBooking(bookingData);

    // Show waiting payment (pending or closed popup)
    document.getElementById('paymentWaiting').classList.remove('d-none');
    document.getElementById('paymentSuccess').classList.add('d-none');
    document.getElementById('bookingCode').textContent = bookingData.booking_code;
    document.getElementById('paymentMethodName').textContent = 'Midtrans';
    document.getElementById('waitingTotal').textContent = formatCurrency(bookingData.total_price);

    var continueLink = document.getElementById('continuePaymentLink');
    if (continueLink) {
      continueLink.href = getResumeUrl(bookingData.booking_code);
      continueLink.classList.remove('d-none');
    }
  }
}

function showSuccessDetails(bookingData) {
  document.getElementById('successBookingCode').textContent = bookingData.booking_code;
  document.getElementById('successDestination').textContent = config.name;

  var dateInput = document.getElementById('visitDate');
  if (dateInput && dateInput.value) {
    var d = new Date(dateInput.value + 'T00:00:00');
    document.getElementById('successDate').textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  }

  var nameInput = document.getElementById('customerName');
  document.getElementById('successParticipantLabel').textContent = 'Peserta (' + participantCount + ' orang)';
  document.getElementById('successParticipantName').textContent = nameInput ? nameInput.value : '-';
  document.getElementById('successTotal').textContent = formatCurrency(bookingData.total_price);

  // Inject Invoice link
  var invoiceContainer = document.getElementById('invoiceLinkContainer');
  if (!invoiceContainer && config.invoiceUrl) {
    invoiceContainer = document.createElement('div');
    invoiceContainer.id = 'invoiceLinkContainer';
    invoiceContainer.className = 'mt-3 mx-auto';
    invoiceContainer.style.maxWidth = '400px';
    invoiceContainer.innerHTML = '<a href="' + config.invoiceUrl + '/' + bookingData.booking_code + '" target="_blank" class="btn btn-outline-primary w-100 mb-2">'
      + '<i class="bi bi-file-earmark-text me-2"></i>Lihat Invoice</a>';
    var homeBtn = document.querySelector('#paymentSuccess .btn-agro-primary');
    if (homeBtn) {
      homeBtn.parentNode.insertBefore(invoiceContainer, homeBtn);
    }
  }

  // Inject WhatsApp button
  if (config.waNumber && config.waContact) {
    var waContainer = document.getElementById('waSuccessContainer');
    if (!waContainer) {
      waContainer = document.createElement('div');
      waContainer.id = 'waSuccessContainer';
      waContainer.className = 'mt-2 mx-auto';
      waContainer.style.maxWidth = '400px';
      var waMsg = encodeURIComponent('Halo ' + config.waContact + ', saya sudah melakukan pemesanan ' + config.name + ' dengan kode ' + bookingData.booking_code + '. Mohon konfirmasinya. Terima kasih!');
      waContainer.innerHTML = '<a href="https://wa.me/' + config.waNumber + '?text=' + waMsg + '" target="_blank" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">'
        + '<i class="bi bi-whatsapp"></i> Hubungi ' + config.waContact
        + '</a>';
      var homeBtn = document.querySelector('#paymentSuccess .btn-agro-primary');
      if (homeBtn) {
        homeBtn.parentNode.insertBefore(waContainer, homeBtn);
      }
    }
  }
}

function copyBookingCode() {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(bookingCode);
  } else {
    var t = document.createElement('textarea');
    t.value = bookingCode;
    document.body.appendChild(t);
    t.select();
    document.execCommand('copy');
    document.body.removeChild(t);
  }
  var btn = event.currentTarget;
  var icon = btn.querySelector('i');
  if (icon) {
    icon.className = 'bi bi-check-lg text-success';
    setTimeout(function() { icon.className = 'bi bi-clipboard'; }, 2000);
  }
}

// ================= CUSTOM CALENDAR DATEPICKER =================
var calendarCurrentMonth = new Date().getMonth();
var calendarCurrentYear = new Date().getFullYear();
var calendarSelectedDate = null;
var availableDatesMap = {};

function initCalendar() {
  var dates = window.AVAILABLE_DATES || [];
  availableDatesMap = {};
  dates.forEach(function(d) {
    availableDatesMap[d.date] = { kuota: d.kuota, sisa: d.sisa };
  });

  // Set initial month to first available date if exists
  if (dates.length > 0) {
    var first = new Date(dates[0].date + 'T00:00:00');
    calendarCurrentMonth = first.getMonth();
    calendarCurrentYear = first.getFullYear();
  }

  renderCalendar();
}

function toggleCalendar() {
  var cal = document.getElementById('customCalendar');
  if (cal.classList.contains('d-none')) {
    cal.classList.remove('d-none');
    renderCalendar();
  } else {
    cal.classList.add('d-none');
  }
}

function calendarPrev() {
  calendarCurrentMonth--;
  if (calendarCurrentMonth < 0) {
    calendarCurrentMonth = 11;
    calendarCurrentYear--;
  }
  renderCalendar();
}

function calendarNext() {
  calendarCurrentMonth++;
  if (calendarCurrentMonth > 11) {
    calendarCurrentMonth = 0;
    calendarCurrentYear++;
  }
  renderCalendar();
}

function renderCalendar() {
  var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  var label = document.getElementById('calendarMonthYear');
  if (label) label.textContent = monthNames[calendarCurrentMonth] + ' ' + calendarCurrentYear;

  var body = document.getElementById('calendarBody');
  if (!body) return;
  body.innerHTML = '';

  var firstDay = new Date(calendarCurrentYear, calendarCurrentMonth, 1).getDay(); // 0=Sun
  var daysInMonth = new Date(calendarCurrentYear, calendarCurrentMonth + 1, 0).getDate();
  var today = new Date();
  today.setHours(0,0,0,0);

  // Empty cells for first row offset
  for (var i = 0; i < firstDay; i++) {
    var empty = document.createElement('div');
    empty.className = 'calendar-day empty';
    body.appendChild(empty);
  }

  for (var d = 1; d <= daysInMonth; d++) {
    var dateStr = calendarCurrentYear + '-' + String(calendarCurrentMonth + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
    var cellDate = new Date(calendarCurrentYear, calendarCurrentMonth, d);
    var cell = document.createElement('div');
    cell.className = 'calendar-day';
    
    var info = availableDatesMap[dateStr];
    var isPast = cellDate < today;

    if (info && !isPast) {
      if (info.sisa > 0) {
        // Available - clickable
        cell.classList.add('available');
        cell.innerHTML = '<span class="day-number">' + d + '</span><span class="day-quota">' + info.sisa + '</span>';
        cell.dataset.date = dateStr;
        cell.dataset.sisa = info.sisa;
        cell.addEventListener('click', function() {
          selectCalendarDate(this.dataset.date, this.dataset.sisa);
        });

        // Mark as selected if matches
        if (calendarSelectedDate === dateStr) {
          cell.classList.add('selected');
        }
      } else {
        // Full - disabled
        cell.classList.add('full');
        cell.innerHTML = '<span class="day-number">' + d + '</span><span class="day-quota">0</span>';
      }
    } else {
      // Not available or past
      cell.classList.add('disabled');
      cell.innerHTML = '<span class="day-number">' + d + '</span>';
    }

    body.appendChild(cell);
  }
}

function selectCalendarDate(dateStr, sisa) {
  calendarSelectedDate = dateStr;

  // Update hidden input
  var dateInput = document.getElementById('visitDate');
  var sisaInput = document.getElementById('visitDateSisa');
  if (dateInput) dateInput.value = dateStr;
  if (sisaInput) sisaInput.value = sisa;

  // Update display
  var parts = dateStr.split('-');
  var displayText = parts[2] + '/' + parts[1] + '/' + parts[0];
  var displayEl = document.getElementById('calendarInputDisplay');
  if (displayEl) {
    displayEl.textContent = displayText;
    displayEl.classList.remove('text-muted');
    displayEl.classList.add('text-dark', 'fw-medium');
  }

  // Re-render to show selected state
  renderCalendar();

  // Close calendar
  var cal = document.getElementById('customCalendar');
  if (cal) cal.classList.add('d-none');

  // Update summary
  updateSummary();
  syncParticipantCountFromInput();
}
