// main.js — JejakHijau
// Shared script: navbar hamburger, profile dropdown
// Dipakai di: semua halaman

document.addEventListener('DOMContentLoaded', function () {

  feather.replace();

  // ── Hamburger Menu (index.php, bantuan.php) ──────────────────
  const hamburger = document.querySelector('#hamburger-menu');
  const menuBtn   = document.querySelector('#menu-btn');
  const ulNavbar  = document.querySelector('.ul-navbar');

  function toggleMenu(e) {
    e.preventDefault();
    if (ulNavbar) ulNavbar.classList.toggle('active');
  }

  if (hamburger) hamburger.addEventListener('click', toggleMenu);
  if (menuBtn)   menuBtn.addEventListener('click', toggleMenu);

  // ── Profile Dropdown ──────────────────────────────────────────
  const profileBtn     = document.querySelector('#profile-btn');
  const dropdownProfile = document.querySelector('#dropdown-profile');

  if (profileBtn && dropdownProfile) {
    profileBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = dropdownProfile.style.display === 'block';
      dropdownProfile.style.display = isOpen ? 'none' : 'block';
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.profile-menu')) {
        dropdownProfile.style.display = 'none';
      }
    });
  }

});
