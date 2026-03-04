// Booking Wizard - Multi-step booking system with Midtrans Integration
'use strict';

let currentStep = 1;
let participantCount = 1;
let bookingCode = '';
let isProcessing = false;

const config = window.BOOKING_CONFIG || {
  name: 'AgroBandung', location: 'Bandung',
  basePrice: 50000, pricingRules: [],
  storeUrl: '', csrfToken: '', invoiceUrl: ''
};

function formatCurrency(amount) {
  return 'Rp' + Math.round(amount).toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function() {
  initCalendar();
  initPaymentOptions();
  updateSummary();
  updateStepper();
  updateNavButtons();
});

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
    var cards = document.querySelectorAll('.participant-card');
    for (var i = 0; i < cards.length; i++) {
      var name = cards[i].querySelector('input[type="text"]');
      if (!name || !name.value.trim()) {
        showToast('Silakan isi nama lengkap Peserta ' + (i + 1) + '!');
        if (name) name.focus();
        return false;
      }
    }
    var phone = document.querySelector('.participant-card input[type="tel"]');
    var email = document.querySelector('.participant-card input[type="email"]');
    if (!phone || !phone.value.trim()) { showToast('Silakan isi nomor telepon!'); if (phone) phone.focus(); return false; }
    if (!email || !email.value.trim()) { showToast('Silakan isi email!'); if (email) email.focus(); return false; }
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

function addParticipant() {
  participantCount++;
  var list = document.getElementById('participantsList');
  var card = document.createElement('div');
  card.className = 'participant-card mb-3';
  card.dataset.participant = participantCount;
  card.innerHTML = '<div class="d-flex align-items-center justify-content-between mb-3">'
    + '<p class="fw-semibold mb-0">Peserta ' + participantCount + '</p>'
    + '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeParticipant(this)"><i class="bi bi-trash"></i></button></div>'
    + '<div class="mb-3"><label class="form-label small fw-medium">Nama Lengkap</label>'
    + '<input type="text" class="form-control" placeholder="Masukkan nama lengkap" required></div>';
  list.appendChild(card);
  document.getElementById('participantTotal').textContent = participantCount;
  updateSummary();
  updatePriceTiers();
}

function removeParticipant(btn) {
  if (participantCount <= 1) return;
  btn.closest('.participant-card').remove();
  participantCount--;
  document.querySelectorAll('.participant-card').forEach(function(card, i) {
    card.dataset.participant = i + 1;
    card.querySelector('.fw-semibold').textContent = 'Peserta ' + (i + 1);
  });
  document.getElementById('participantTotal').textContent = participantCount;
  updateSummary();
  updatePriceTiers();
}

function getPriceCalculation() {
  const totalBase = participantCount * config.basePrice;
  let discount = 0;
  let appliedRule = null;

  if (config.pricingRules && config.pricingRules.length > 0) {
    appliedRule = config.pricingRules.find(rule => {
      const min = parseInt(rule.min_pax);
      const max = rule.max_pax ? parseInt(rule.max_pax) : Infinity;
      return participantCount >= min && participantCount <= max;
    });

    if (appliedRule) {
      if (appliedRule.discount_type === 'percent') {
        discount = (totalBase * parseFloat(appliedRule.discount_value)) / 100;
      } else if (appliedRule.discount_type === 'nominal') {
        discount = parseFloat(appliedRule.discount_value);
      }
    }
  }

  return {
    totalBase,
    discount,
    totalPrice: totalBase - discount,
    appliedRule
  };
}

function updatePriceTiers() {
  var tiers = document.querySelectorAll('.price-tier-card');
  tiers.forEach(function(t) { t.classList.remove('active'); });
  
  const calc = getPriceCalculation();
  if (calc.appliedRule) {
    const activeTier = Array.from(tiers).find(t => 
      parseInt(t.dataset.min) === parseInt(calc.appliedRule.min_pax)
    );
    if (activeTier) activeTier.classList.add('active');
  }
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
  if (pl) pl.textContent = formatCurrency(config.basePrice) + ' \u00d7 ' + participantCount;
  
  var st = document.getElementById('summarySubtotal');
  if (st) st.textContent = formatCurrency(calc.totalBase);
  
  // Update Discount if exists
  let discEl = document.getElementById('summaryDiscount');
  if (calc.discount > 0) {
    if (!discEl) {
      discEl = document.createElement('div');
      discEl.id = 'summaryDiscount';
      discEl.className = 'd-flex justify-content-between small text-danger mb-1';
      const subtotalEl = document.getElementById('summarySubtotal').parentElement;
      subtotalEl.parentNode.insertBefore(discEl, subtotalEl.nextSibling);
    }
    discEl.innerHTML = '<span>Diskon</span><span>-' + formatCurrency(calc.discount) + '</span>';
  } else if (discEl) {
    discEl.remove();
  }

  var tp = document.getElementById('totalPrice');
  if (tp) tp.textContent = formatCurrency(calc.totalPrice);
}

// ================= SUBMIT BOOKING TO BACKEND =================
function submitBooking() {
  if (isProcessing) return;
  isProcessing = true;

  var btnNext = document.getElementById('btnNext');
  btnNext.disabled = true;
  btnNext.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

  // Gather form data
  var nameInput = document.querySelector('.participant-card input[type="text"]');
  var phoneInput = document.querySelector('.participant-card input[type="tel"]');
  var emailInput = document.querySelector('.participant-card input[type="email"]');
  var dateInput = document.getElementById('visitDate');
  var paketId = document.getElementById('paketTourId').value;

  var payload = {
    paket_tour_id: paketId,
    jumlah_peserta: participantCount,
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
    // Show success directly
    document.getElementById('paymentWaiting').classList.add('d-none');
    document.getElementById('paymentSuccess').classList.remove('d-none');
    showSuccessDetails(bookingData);
  } else {
    // Show waiting payment (pending or closed popup)
    document.getElementById('paymentWaiting').classList.remove('d-none');
    document.getElementById('paymentSuccess').classList.add('d-none');
    document.getElementById('bookingCode').textContent = bookingData.booking_code;
    document.getElementById('paymentMethodName').textContent = 'Midtrans';
    document.getElementById('waitingTotal').textContent = formatCurrency(bookingData.total_price);
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

  var nameInput = document.querySelector('.participant-card input[type="text"]');
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

function confirmPayment() {
  // User clicked "Saya Sudah Bayar" - show success
  document.getElementById('paymentWaiting').classList.add('d-none');
  document.getElementById('paymentSuccess').classList.remove('d-none');

  showSuccessDetails({ booking_code: bookingCode, total_price: getPriceCalculation().totalPrice });
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
}
