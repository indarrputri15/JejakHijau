// validation.js - Client-side form validation 
// UTILITY FUNCTIONS - Safe DOM Selectors

//   Safe querySelector yang mengembalikan null tanpa warning
function safeQuerySelector(selector, context = document) {
  try {
    return context.querySelector(selector);
  } catch (e) {
    console.warn(`Invalid selector: ${selector}`);
    return null;
  }
}


//  Safe querySelectorAll
function safeQuerySelectorAll(selector, context = document) {
  try {
    return context.querySelectorAll(selector);
  } catch (e) {
    console.warn(`Invalid selector: ${selector}`);
    return [];
  }
}


//  Get input value dengan error handling
function getInputValue(selector) {
  const input = safeQuerySelector(selector);
  if (!input) {
    console.warn(`Input not found: ${selector}`);
    return '';
  }
  return input.value.trim();
}


//  Set input value dengan error handling
function setInputValue(selector, value) {
  const input = safeQuerySelector(selector);
  if (!input) {
    console.warn(`Input not found: ${selector}`);
    return false;
  }
  input.value = value;
  return true;
}


// POPUP/NOTIFICATION SYSTEM

//   Show success notification popup
function showSuccessPopup(message, duration = 2000) {
  // Remove existing popups
  const existingPopups = document.querySelectorAll('[data-popup="success"]');
  existingPopups.forEach(popup => popup.remove());

  const popup = document.createElement('div');
  popup.setAttribute('data-popup', 'success');
  popup.setAttribute('role', 'alert');
  popup.setAttribute('aria-live', 'polite');
  
  popup.style.cssText = `
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #eafaf1;
    border: 2px solid #27ae60;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 9999;
    text-align: center;
    max-width: 400px;
    animation: slideDown 0.3s ease-out;
  `;
  
  popup.innerHTML = `
    <h3 style="color: #27ae60; margin-bottom: 15px; margin-top: 0;">✓ ${escapeHtml(message)}</h3>
    <p style="color: #1e8449; font-size: 14px; margin: 0;">Mengalihkan dalam 2 detik...</p>
  `;
  
  document.body.appendChild(popup);
  
  // Auto remove setelah duration
  setTimeout(() => {
    popup.remove();
  }, duration);
}

// Error message

function showErrorPopup(message) {
  // Remove existing error popups
  const existingPopups = document.querySelectorAll('[data-popup="error"]');
  existingPopups.forEach(popup => popup.remove());

  const popup = document.createElement('div');
  popup.setAttribute('data-popup', 'error');
  popup.setAttribute('role', 'alert');
  popup.setAttribute('aria-live', 'assertive');
  
  popup.style.cssText = `
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #ffebee;
    border: 2px solid #e74c3c;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 9999;
    text-align: center;
    max-width: 400px;
    animation: slideDown 0.3s ease-out;
  `;
  
  popup.innerHTML = `
    <h3 style="color: #e74c3c; margin-bottom: 15px; margin-top: 0;">✗ ${escapeHtml(message)}</h3>
    <button id="close-error-popup" style="
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
      font-weight: 500;
    ">Tutup</button>
  `;
  
  document.body.appendChild(popup);
  
  // Close button handler
  const closeBtn = popup.querySelector('#close-error-popup');
  if (closeBtn) {
    closeBtn.addEventListener('click', () => popup.remove());
  }
}


// Simple inline error message (di dalam form)

function showInlineError(selector, message) {
  const errorEl = safeQuerySelector(selector);
  if (!errorEl) {
    console.warn(`⚠️ Error element not found: ${selector}`);
    return;
  }
  
  errorEl.textContent = message;
  errorEl.style.display = 'block';
}

//  Element selector

function hideInlineError(selector) {
  const errorEl = safeQuerySelector(selector);
  if (!errorEl) return;
  
  errorEl.textContent = '';
  errorEl.style.display = 'none';
}

//  Escape HTML untuk prevent XSS
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, m => map[m]);
}


// VALIDATION FUNCTIONS

//   Validasi format email
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}


//  Validasi password strength (min 6 karakter)

function isValidPassword(password) {
  return password.length >= 6;
}


//   Validasi nomor HP Indonesia


function isValidPhoneNumber(phone) {
  const phoneRegex = /^(\+62|0)[0-9]{9,12}$/;
  return phoneRegex.test(phone.replace(/\s/g, ''));
}

// FORM-SPECIFIC VALIDATIONS

//   Validasi login form
function validateLoginForm() {
  const email = getInputValue('input[name="email"]');
  const password = getInputValue('input[name="password"]');
  
  if (!email || !password) {
    showErrorPopup('Email dan password tidak boleh kosong!');
    return false;
  }
  
  if (!isValidEmail(email)) {
    showErrorPopup('Format email tidak valid!');
    return false;
  }
  
  return true;
}

// Validasi signup form
 
function validateSignupForm() {
  const nama = getInputValue('input[name="nama_lengkap"]');
  const email = getInputValue('input[name="email"]');
  const noHp = getInputValue('input[name="no_hp"]');
  const provinsi = getInputValue('input[name="provinsi"]');
  const alamat = getInputValue('textarea[name="alamat"]');
  const password = getInputValue('input[name="password"]');
  const passwordConfirm = getInputValue('input[name="password_confirm"]');
  
  if (!nama || !email || !noHp || !provinsi || !alamat || !password) {
    showErrorPopup('Semua field harus diisi!');
    return false;
  }
  
  if (!isValidEmail(email)) {
    showErrorPopup('Format email tidak valid!');
    return false;
  }
  
  if (!isValidPhoneNumber(noHp)) {
    showErrorPopup('Format nomor HP tidak valid! (Contoh: 08xxxxxxxxxx)');
    return false;
  }
  
  if (!isValidPassword(password)) {
    showErrorPopup('Password minimal 6 karakter!');
    return false;
  }
  
  if (password !== passwordConfirm) {
    showErrorPopup('Password dan konfirmasi password tidak cocok!');
    return false;
  }
  
  return true;
}

/**
 * Validasi donation form
 */
function validateDonationForm() {
  const nominal = parseInt(getInputValue('input[name="nominal"]')) || 0;
  const buktiInput = safeQuerySelector('input[name="bukti_transfer"]');
  
  if (nominal < 10000) {
    showErrorPopup('Nominal donasi minimal Rp10.000!');
    return false;
  }
  
  if (!buktiInput || buktiInput.files.length === 0) {
    showErrorPopup('Silakan upload bukti transfer!');
    return false;
  }
  
  const ext = buktiInput.files[0].name.split('.').pop().toLowerCase();
  if (!['jpg', 'jpeg', 'png'].includes(ext)) {
    showErrorPopup('Format bukti transfer harus JPG atau PNG!');
    return false;
  }
  
  return true;
}

//  Validasi create campaign form

function validateCreateCampaignForm() {
  const judul = getInputValue('input[name="judul_campaign"]');
  const deskripsi = getInputValue('textarea[name="deskripsi"]');
  const lokasi = getInputValue('input[name="lokasi"]');
  const target = parseInt(getInputValue('input[name="target_dana"]')) || 0;
  const gambarInput = safeQuerySelector('input[name="gambar_sampul"]');
  const gambar = gambarInput ? gambarInput.files.length : 0;
  
  if (!judul || !deskripsi || !lokasi) {
    showErrorPopup('Semua field harus diisi!');
    return false;
  }
  
  if (target < 100000) {
    showErrorPopup('Target dana minimal Rp100.000!');
    return false;
  }
  
  if (gambar === 0) {
    showErrorPopup('Gambar sampul harus diunggah!');
    return false;
  }
  
  return true;
}

//  Validasi profile edit form
//  @returns {boolean}
function validateProfileForm() {
  const nama = getInputValue('input[name="nama_lengkap"]');
  const noHp = getInputValue('input[name="no_hp"]');
  const provinsi = getInputValue('input[name="provinsi"]');
  const alamat = getInputValue('textarea[name="alamat"]');
  
  if (!nama || !noHp || !provinsi || !alamat) {
    showErrorPopup('Semua field harus diisi!');
    return false;
  }
  
  if (!isValidPhoneNumber(noHp)) {
    showErrorPopup('Format nomor HP tidak valid! (Contoh: 08xxxxxxxxxx)');
    return false;
  }
  
  return true;
}

// CURRENCY & FORMATTING

//  Format angka ke currency IDR
function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
}


//  Set nominal input dengan format
function setNominal(amount) {
  setInputValue('input[name="nominal"]', amount.toString());
  updateEstimasi();
}

/**
 * Update estimasi pohon (setiap Rp10.000 = 1 pohon)
 * Dipanggil saat input nominal berubah
 */
function updateEstimasi() {
  const nominalInput = safeQuerySelector('input[name="nominal"]');
  const estimasiElement = safeQuerySelector('[data-estimasi="pohon"]') || 
                          safeQuerySelector('#estimasi-pohon');
  
  if (!nominalInput || !estimasiElement) {
    return;
  }
  
  const nominal = parseInt(nominalInput.value) || 0;
  const pohon = Math.floor(nominal / 10000);
  estimasiElement.textContent = pohon;
}

// FORM INITIALIZATION

function initializeFormValidations() {
  
  // ── LOGIN FORM ──────────────────────────────────────────
  const loginForm = safeQuerySelector('#form-login');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (validateLoginForm()) {
        showSuccessPopup('Login berhasil!', 1500);
        setTimeout(() => {
          window.location.href = 'index.html';
        }, 1500);
      }
    });
  }

  // SIGNUP FORM 
  const signupForm = safeQuerySelector('#form-signup');
  if (signupForm) {
    signupForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (validateSignupForm()) {
        showSuccessPopup('Akun berhasil dibuat!', 2000);
        setTimeout(() => {
          window.location.href = 'login.html';
        }, 2000);
      }
    });
  }

  // DONATION FORM 
  const donationForm = safeQuerySelector('#form-donasi');
  if (donationForm) {
    // Form submit handler
    donationForm.addEventListener('submit', function(e) {
      if (!validateDonationForm()) {
        e.preventDefault();
      }
    });
    
    // Update estimasi saat input nominal berubah
    const nominalInput = safeQuerySelector('input[name="nominal"]');
    if (nominalInput) {
      nominalInput.addEventListener('input', updateEstimasi);
      
      // Initial calculation
      updateEstimasi();
    }
  }

  // CAMPAIGN FORM 
  const campaignForm = safeQuerySelector('#form-campaign');
  if (campaignForm) {
    campaignForm.addEventListener('submit', function(e) {
      if (!validateCreateCampaignForm()) {
        e.preventDefault();
      }
    });
  }

  // PROFILE FORM 
  const profileForm = safeQuerySelector('#form-profile');
  if (profileForm) {
    profileForm.addEventListener('submit', function(e) {
      if (!validateProfileForm()) {
        e.preventDefault();
      }
    });
  }
}

// DOM 
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeFormValidations);
} else {
  // DOM already loaded
  initializeFormValidations();
}