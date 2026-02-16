// Booking Wizard - Multi-step booking system
'use strict';

let currentStep = 1;
let participantCount = 1;
let bookingCode = '';

const config = window.BOOKING_CONFIG || {
  name: 'AgroBandung', location: 'Bandung',
  prices: [50000, 45000, 40000], serviceFee: 2500
};

document.addEventListener('DOMContentLoaded', function() {
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  const dateInput = document.getElementById('visitDate');
  if (dateInput) {
    dateInput.min = tomorrow.toISOString().split('T')[0];
    dateInput.addEventListener('change', updateSummary);
  }
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
  if (!validateStep(currentStep)) return;
  if (currentStep < 4) {
    currentStep++;
    showStep(currentStep);
    updateStepper();
    updateNavButtons();
    if (currentStep === 4) startPaymentProcess();
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

function getPricePerPerson() {
  if (participantCount >= 10) return config.prices[2];
  if (participantCount >= 5) return config.prices[1];
  return config.prices[0];
}

function updatePriceTiers() {
  var tiers = document.querySelectorAll('.price-tier-card');
  tiers.forEach(function(t) { t.classList.remove('active'); });
  if (participantCount >= 10 && tiers[2]) tiers[2].classList.add('active');
  else if (participantCount >= 5 && tiers[1]) tiers[1].classList.add('active');
  else if (tiers[0]) tiers[0].classList.add('active');
}

function updateSummary() {
  var dateInput = document.getElementById('visitDate');
  if (dateInput && dateInput.value) {
    var date = new Date(dateInput.value + 'T00:00:00');
    var el = document.getElementById('summaryDate');
    if (el) el.textContent = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
  }
  var price = getPricePerPerson();
  var subtotal = participantCount * price;
  var total = subtotal + config.serviceFee;

  var pl = document.getElementById('summaryPriceLabel');
  if (pl) pl.textContent = formatCurrency(price) + ' \u00d7 ' + participantCount;
  var st = document.getElementById('summarySubtotal');
  if (st) st.textContent = formatCurrency(subtotal);
  var sf = document.getElementById('summaryServiceFee');
  if (sf) sf.textContent = formatCurrency(config.serviceFee);
  var tp = document.getElementById('totalPrice');
  if (tp) tp.textContent = formatCurrency(total);
}

function formatCurrency(amount) {
  return 'Rp' + amount.toLocaleString('id-ID');
}

function generateBookingCode() {
  var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  var code = 'AGR-';
  for (var i = 0; i < 6; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
  return code;
}

function startPaymentProcess() {
  bookingCode = generateBookingCode();
  var radio = document.querySelector('input[name="payment"]:checked');
  var names = { transfer: 'Transfer Bank', ewallet: 'E-Wallet', qris: 'QRIS' };
  document.getElementById('bookingCode').textContent = bookingCode;
  document.getElementById('paymentMethodName').textContent = names[radio ? radio.value : ''] || '-';
  var total = (participantCount * getPricePerPerson()) + config.serviceFee;
  document.getElementById('waitingTotal').textContent = formatCurrency(total);
  document.getElementById('paymentWaiting').classList.remove('d-none');
  document.getElementById('paymentSuccess').classList.add('d-none');
}

function confirmPayment() {
  document.getElementById('paymentWaiting').classList.add('d-none');
  document.getElementById('paymentSuccess').classList.remove('d-none');
  document.getElementById('successBookingCode').textContent = bookingCode;
  document.getElementById('successDestination').textContent = config.name;
  var dateInput = document.getElementById('visitDate');
  if (dateInput && dateInput.value) {
    var d = new Date(dateInput.value + 'T00:00:00');
    document.getElementById('successDate').textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  }
  var nameInput = document.querySelector('.participant-card input[type="text"]');
  document.getElementById('successParticipantLabel').textContent = 'Peserta (' + participantCount + ' orang)';
  document.getElementById('successParticipantName').textContent = nameInput ? nameInput.value : '-';
  var total = (participantCount * getPricePerPerson()) + config.serviceFee;
  document.getElementById('successTotal').textContent = formatCurrency(total);

  // Generate QR Code with booking details
  var qrContainer = document.getElementById('qrCodeContainer');
  if (!qrContainer) {
    var dateEl = document.getElementById('successDate');
    var qrData = 'AGROBANDUNG BOOKING\n'
      + 'Kode: ' + bookingCode + '\n'
      + 'Destinasi: ' + config.name + '\n'
      + 'Lokasi: ' + config.location + '\n'
      + 'Tanggal: ' + (dateEl ? dateEl.textContent : '-') + '\n'
      + 'Peserta: ' + participantCount + ' orang\n'
      + 'Total: ' + formatCurrency(total);

    var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&format=svg&data=' + encodeURIComponent(qrData);

    qrContainer = document.createElement('div');
    qrContainer.id = 'qrCodeContainer';
    qrContainer.className = 'mb-4 mx-auto text-center';
    qrContainer.style.maxWidth = '400px';
    qrContainer.innerHTML = '<div class="border rounded-3 p-3 d-inline-block bg-white">'
      + '<img src="' + qrUrl + '" alt="QR Code Booking ' + bookingCode + '" width="180" height="180" class="mb-2" id="qrCodeImg">'
      + '<p class="text-muted small mb-0"><i class="bi bi-qr-code me-1"></i>Scan untuk menyimpan detail booking</p>'
      + '</div>';

    // Insert after the booking details card (before Total Bayar)
    var totalRow = document.querySelector('#paymentSuccess .d-flex.align-items-center.justify-content-between.mb-4');
    if (totalRow) {
      totalRow.parentNode.insertBefore(qrContainer, totalRow);
    }
  }

  // Inject WhatsApp button after successful payment
  if (config.waNumber && config.waContact) {
    var waContainer = document.getElementById('waSuccessContainer');
    if (!waContainer) {
      waContainer = document.createElement('div');
      waContainer.id = 'waSuccessContainer';
      waContainer.className = 'mt-3 mx-auto';
      waContainer.style.maxWidth = '400px';
      var waMsg = encodeURIComponent('Halo ' + config.waContact + ', saya sudah melakukan pemesanan ' + config.name + ' dengan kode ' + bookingCode + '. Mohon konfirmasinya. Terima kasih!');
      waContainer.innerHTML = '<a href="https://wa.me/' + config.waNumber + '?text=' + waMsg + '" target="_blank" class="btn btn-whatsapp w-100 d-flex align-items-center justify-content-center gap-2">'
        + '<i class="bi bi-whatsapp"></i> Hubungi ' + config.waContact
        + '</a>'
        + '<p class="text-muted small text-center mt-2 mb-0">Konfirmasi pembayaran via WhatsApp</p>';
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
