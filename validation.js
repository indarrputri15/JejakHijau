// validation.js - Client-side form validation

// validasi format email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// validasi password strength min 6 char
function isValidPassword(password) {
    return password.length >= 6;
}

// validasi no hp indo
function isValidPhoneNumber(phone) {
    const phoneRegex = /^(\+62|0)[0-9]{9,12}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

// validasi login form
function validateLoginForm() {
    const email = document.querySelector('input[name="email"]').value.trim();
    const password = document.querySelector('input[name="password"]').value.trim();
    if (!email || !password) {
        alert('Email dan password tidak boleh kosong!');
        return false;}
    
    if (!isValidEmail(email)) {
        alert('Format email tidak valid!');
        return false;}
    return true;
}

// mengarahkan dari login ke index.html
const loginForm = document.getElementById("form-login");

if (loginForm) {
    loginForm.addEventListener("submit", function(e){
        e.preventDefault();

        if (validateLoginForm()) {
            window.location.href = "index.html";
        }
    });
}

// validasi sign up form
function validateSignupForm() {
    const nama = document.querySelector('input[name="nama_lengkap"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const noHp = document.querySelector('input[name="no_hp"]').value.trim();
    const provinsi = document.querySelector('input[name="provinsi"]').value.trim();
    const alamat = document.querySelector('textarea[name="alamat"]').value.trim();
    const password = document.querySelector('input[name="password"]').value.trim();
    const passwordConfirm = document.querySelector('input[name="password_confirm"]').value.trim();
    
    if (!nama || !email || !noHp || !provinsi || !alamat || !password) {
        alert('Semua field harus diisi!');
        return false;
    }
    if (!isValidEmail(email)) {
        alert('Format email tidak valid!');
        return false;
    }
    
    if (!isValidPhoneNumber(noHp)) {
        alert('Format nomor HP tidak valid!');
        return false;
    }
    
    if (!isValidPassword(password)) {
        alert('Password minimal 6 karakter!');
        return false;
    }
    
    if (password !== passwordConfirm) {
        alert('Password dan konfirmasi password tidak cocok!');
        return false;
    }
    
    return true;
}

//ngarahin dari signup ke login
const signupForm = document.getElementById("form-signup");

if (signupForm) {
    signupForm.addEventListener("submit", function(e) {
        e.preventDefault();

        if (validateSignupForm()) {
            alert("Akun berhasil dibuat!");
            window.location.href = "login.html";
        }
    });
}

// validasi donation form
function validateDonationForm() {

    const nominal =
        parseInt(document.querySelector('input[name="nominal"]').value) || 0;

    const bukti =
        document.getElementById('bukti_transfer');

    if (nominal < 10000) {
        alert('Nominal donasi minimal Rp10.000!');
        return false;
    }

    if (!bukti || bukti.files.length === 0) {
        alert('Silakan upload bukti transfer!');
        return false;
    }

    const ext =
        bukti.files[0].name.split('.').pop().toLowerCase();

    if (!['jpg','jpeg','png'].includes(ext)) {
        alert('Format bukti transfer harus JPG atau PNG!');
        return false;
    }

    return true;
}

//  Validasi create campaign form
function validateCreateCampaignForm() {
    const judul = document.querySelector('input[name="judul_campaign"]').value.trim();
    const deskripsi = document.querySelector('textarea[name="deskripsi"]').value.trim();
    const lokasi = document.querySelector('input[name="lokasi"]').value.trim();
    const target = parseInt(document.querySelector('input[name="target_dana"]').value) || 0;
    const gambar = document.querySelector('input[name="gambar_sampul"]').files.length;
    
    if (!judul || !deskripsi || !lokasi) {
        alert('Semua field harus diisi!');
        return false;
    }
    
    if (target < 100000) {
        alert('Target dana minimal Rp100.000!');
        return false;
    }
    
    if (gambar === 0) {
        alert('Gambar sampul harus diunggah!');
        return false;
    }
    
    return true;
}

// validasi bantuan form
function validateHelpForm() {
    const nama = document.querySelector('input[name="nama_lengkap"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const noHp = document.querySelector('input[name="no_hp"]').value.trim();
    const kategori = document.querySelector('select[name="kategori"]').value;
    const pesan = document.querySelector('textarea[name="pesan"]').value.trim();
    
    if (!nama || !email || !noHp || !kategori || !pesan) {
        alert('Semua field harus diisi!');
        return false;
    }
    
    if (!isValidEmail(email)) {
        alert('Format email tidak valid!');
        return false;
    }
    
    if (!isValidPhoneNumber(noHp)) {
        alert('Format nomor HP tidak valid!');
        return false;
    }
    
    return true;
}

// validasi profile edit form
function validateProfileForm() {
    const nama = document.querySelector('input[name="nama_lengkap"]').value.trim();
    const noHp = document.querySelector('input[name="no_hp"]').value.trim();
    const provinsi = document.querySelector('input[name="provinsi"]').value.trim();
    const alamat = document.querySelector('textarea[name="alamat"]').value.trim();
    
    if (!nama || !noHp || !provinsi || !alamat) {
        alert('Semua field harus diisi!');
        return false;
    }
    
    if (!isValidPhoneNumber(noHp)) {
        alert('Format nomor HP tidak valid!');
        return false;
    }
    
    return true;
}

// format currency idr
function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
}

//Set nominal dengan format
function setNominal(amount) {
    const nominalInput = document.getElementById('nominal');
    if (nominalInput) {
        nominalInput.value = amount;
        // Trigger input event untuk update estimasi
        nominalInput.dispatchEvent(new Event('input'));
    }
}

//Update estimasi pohon (setiap Rp10.000 = 1 pohon)
function updateEstimasi() {
    const nominalInput = document.getElementById('nominal');
    const estimasiElement = document.getElementById('estimasi-pohon');
    
    if (nominalInput && estimasiElement) {
        const nominal = parseInt(nominalInput.value) || 0;
        const pohon = Math.floor(nominal / 10000);
        estimasiElement.textContent = pohon;
    }
}

//Show success popup
function showSuccessPopup(message) {
    const popup = document.createElement('div');
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
    `;
    
    popup.innerHTML = `
        <h3 style="color: #27ae60; margin-bottom: 15px;">✓ ${message}</h3>
        <p style="color: #1e8449; font-size: 14px;">Mengalihkan dalam 2 detik...</p>
    `;
    
    document.body.appendChild(popup);
}

//Show error popup
function showErrorPopup(message) {
    const popup = document.createElement('div');
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
    `;
    
    popup.innerHTML = `
        <h3 style="color: #e74c3c; margin-bottom: 15px;">✗ ${message}</h3>
        <button onclick="this.parentElement.remove()" style="
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        ">Tutup</button>
    `;
    
    document.body.appendChild(popup);
}

// Initialize all form validations
function initializeFormValidations() {
    
    // Donation form
    const donationForm = document.getElementById('form-donasi');
    if (donationForm) {
        donationForm.addEventListener('submit', function(e) {
            if (!validateDonationForm()) {
                e.preventDefault();
            }
        });
        
        // Update estimasi saat input berubah
        const nominalInput = document.getElementById('nominal');
        if (nominalInput) {
            nominalInput.addEventListener('input', updateEstimasi);
        }
    }
    
    // Campaign form
    const campaignForm = document.getElementById('form-campaign');
    if (campaignForm) {
        campaignForm.addEventListener('submit', function(e) {
            if (!validateCreateCampaignForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Help form
    const helpForm = document.getElementById('form-bantuan');
    if (helpForm) {
        helpForm.addEventListener('submit', function(e) {
            if (!validateHelpForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Profile form
    const profileForm = document.getElementById('form-profile');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            if (!validateProfileForm()) {
                e.preventDefault();
            }
        });
    }
}

// Run initializations when DOM is ready
document.addEventListener('DOMContentLoaded', initializeFormValidations);
