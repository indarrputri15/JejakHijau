// main.js — JejakHijau 
// Shared script: navbar hamburger, profile dropdown
// Dipakai di: semua halaman

document.addEventListener('DOMContentLoaded', function () {
  
  // Replace feather icons
  if (typeof feather !== 'undefined') {
    feather.replace();
  }


  // HAMBURGER MENU
  const hamburger = document.querySelector('#hamburger-menu');
  const menuBtn = document.querySelector('#menu-btn');
  const ulNavbar = document.querySelector('.ul-navbar');

  // Only initialize if elements exist
  if (hamburger || menuBtn) {
    if (!ulNavbar) {
      console.warn('⚠️ .ul-navbar tidak ditemukan di DOM');
      return;
    }

    function toggleMenu(e) {
      e.preventDefault();
      const isActive = ulNavbar.classList.contains('active');
      
      // Toggle class
      ulNavbar.classList.toggle('active');
      
      // Update aria-expanded untuk accessibility
      const expandedState = ulNavbar.classList.contains('active');
      if (hamburger) hamburger.setAttribute('aria-expanded', expandedState);
      if (menuBtn) menuBtn.setAttribute('aria-expanded', expandedState);
    }

    // Set initial aria-expanded state
    if (hamburger) {
      hamburger.setAttribute('aria-expanded', 'false');
      hamburger.setAttribute('aria-label', 'Toggle navigation menu');
      hamburger.addEventListener('click', toggleMenu);
    }
    
    if (menuBtn) {
      menuBtn.setAttribute('aria-expanded', 'false');
      menuBtn.setAttribute('aria-label', 'Toggle navigation menu');
      menuBtn.addEventListener('click', toggleMenu);
    }

    // Close menu when clicking on navbar links
    const navLinks = ulNavbar.querySelectorAll('a');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        ulNavbar.classList.remove('active');
        if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
      });
    });
  }


  // PROFILE DROPDOWN
  const profileBtn = document.querySelector('#profile-btn');
  const dropdownProfile = document.querySelector('#dropdown-profile');
  const profileMenu = document.querySelector('#profile-menu');

  if (profileBtn && dropdownProfile) {
    if (!profileMenu) {
      console.warn('⚠️ #profile-menu tidak ditemukan di DOM');
      return;
    }

    // Initial state
    profileBtn.setAttribute('aria-expanded', 'false');
    profileBtn.setAttribute('aria-label', 'Toggle profile menu');
    dropdownProfile.setAttribute('aria-hidden', 'true');

    // Toggle dropdown saat button diklik
    profileBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      
      const isOpen = dropdownProfile.getAttribute('aria-hidden') === 'false';
      const newState = !isOpen;
      
      // Update visibility
      dropdownProfile.setAttribute('aria-hidden', String(!newState));
      profileBtn.setAttribute('aria-expanded', String(newState));
      
      // Update display
      dropdownProfile.style.display = newState ? 'block' : 'none';
    });

    // Close dropdown saat klik di luar
    document.addEventListener('click', function (e) {
      // Gunakan closest() untuk better event delegation
      if (!e.target.closest('#profile-menu')) {
        dropdownProfile.style.display = 'none';
        dropdownProfile.setAttribute('aria-hidden', 'true');
        profileBtn.setAttribute('aria-expanded', 'false');
      }
    });

    // Close dropdown saat tekan Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        dropdownProfile.style.display = 'none';
        dropdownProfile.setAttribute('aria-hidden', 'true');
        profileBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

 
  // SMOOTH SCROLL untuk anchor links 
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      
      // Skip jika hanya '#' atau tidak ada target
      if (href === '#' || !href.startsWith('#')) return;
      
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

});