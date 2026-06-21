<?php
// index.php
session_start();
?>

<!DOCTYPE php>
<php lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JejakHijau</title>

  <!-- FONT -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="style.css">

  <!-- ICON -->
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a href="index.php" class="navbar-logo">
    Jejak<span>Hijau</span>
  </a>

  <ul class="ul-navbar">
    <li><a href="#beranda">BERANDA</a></li>
    <li><a href="#dampak">DAMPAK</a></li>
    <li><a href="#tentang">TENTANG</a></li>
    <li><a href="campaigns.php">CAMPAIGN</a></li>
  </ul>

  <div class="navbar-extra">
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Jika user sudah login -->
    <div class="profile-menu" id="profile-menu">
      <button class="profile-btn" id="profile-btn">
        <i data-feather="user"></i>
      </button>
      <div class="dropdown-profile" id="dropdown-profile">
        <a href="profile.php">Profil Saya</a>
        <a href="create-campaign.php">Buat Campaign</a>
        <a href="#">Logout</a>
      </div>
    </div>

    <!-- Jika belum login: tampilkan link masuk -->
    <a href="login.php">MASUK</a>
    <?php endif; ?>

    <a href="#" id="hamburger-menu">
      <i data-feather="menu"></i>
    </a>
  </div>
</nav>
<!-- NAVBAR END -->


<!-- HERO -->
<section class="hero" id="beranda">
  <img src="assets/nanam.jpeg" class="hero-img" alt="Reboisasi Hutan">
  <article class="hero-text">
    <span>Campaign Aktif</span>
    <h1>Bergabung dalam Aksi Hijau untuk Masa Depan 🌳</h1>
    <p>Langkah kecilmu hari ini dapat memberikan dampak besar bagi bumi di masa depan.</p>
    <a href="campaigns.php" class="btn-primary">Ikut Sekarang</a>
  </article>
</section>
<!-- HERO END -->


<!-- DAMPAK -->
<section class="dampak" id="dampak">
  <h2>Dampak yang Sudah Kami Ciptakan</h2>
  <div class="dampak-container">
    <article class="card">
      <i data-feather="user-plus"></i>
      <h3>1.250+</h3>
      <p>Donatur Aktif</p>
    </article>
    <article class="card">
      <i data-feather="activity"></i>
      <h3>10.000+</h3>
      <p>Pohon Tertanam</p>
    </article>
    <article class="card">
      <i data-feather="map"></i>
      <h3>12 Ha</h3>
      <p>Lahan Terhijaukan</p>
    </article>
    <article class="card">
      <i data-feather="users"></i>
      <h3>40+</h3>
      <p>Mitra Kelompok Tani & Relawan</p>
    </article>
  </div>
</section>
<!-- DAMPAK END -->


<!-- TENTANG -->
<section class="tentang" id="tentang">
  <div class="tentang-container">

    <div class="tentang-image">
      <img src="assets/komunitas.jpg" alt="Komunitas JejakHijau" class="tentang-img">
    </div>

    <div class="tentang-content">
      <h2>Tentang JejakHijau</h2>

      <p>
        JejakHijau adalah platform digital yang menghubungkan masyarakat,
        komunitas, dan organisasi dalam upaya menjaga kelestarian lingkungan
        melalui donasi dan campaign penghijauan.
      </p>

      <p>
        Kami percaya bahwa perubahan besar dimulai dari langkah kecil.
        Dengan berpartisipasi dalam campaign yang tersedia, setiap orang dapat
        memberikan kontribusi nyata untuk membantu penanaman pohon, restorasi
        lahan hijau, pengelolaan sampah, serta berbagai program pelestarian
        lingkungan lainnya.
      </p>

      <p>
        Melalui JejakHijau, proses berdonasi menjadi lebih mudah, transparan,
        dan berdampak. Setiap campaign yang ditampilkan memiliki tujuan yang
        jelas sehingga para donatur dapat melihat bagaimana kontribusi mereka
        membantu menciptakan lingkungan yang lebih hijau dan berkelanjutan.
      </p>

      <div class="tentang-highlight">
        <div class="highlight-card">
          <h3>🌱 Misi Kami</h3>
          <p>
            Mendorong partisipasi masyarakat dalam aksi pelestarian lingkungan
            melalui teknologi yang mudah diakses.
          </p>
        </div>

        <div class="highlight-card">
          <h3>🌍 Visi Kami</h3>
          <p>
            Mewujudkan Indonesia yang lebih hijau, sehat, dan berkelanjutan
            melalui kolaborasi dan kepedulian bersama.
          </p>
        </div>
      </div>

      <a href="campaigns.php" class="btn-primary">
        Mulai Kontribusi
      </a>
    </div>

  </div>
</section>
<!-- TENTANG END -->


<!-- FOOTER -->
<footer class="footer" id="footer">
  <div class="footer-container">
    <div class="footer-logo">
      <h2>JejakHijau</h2>
      <p>Langkah kecilmu, dampak besar bagi bumi 🌏</p>
    </div>
    <div class="footer-menu">
      <h3>Menu</h3>
      <a href="index.php">Beranda</a>
      <a href="campaigns.php">Campaign</a>
    </div>
    <div class="footer-contact">
      <h3>Kontak</h3>
      <p>Email: jejakhijau@gmail.com</p>
      <p>Instagram: @jejakhijau</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 JejakHijau. All Rights Reserved.</p>
  </div>
</footer>
<!-- FOOTER END -->


<!-- JS -->
<script src="main.js"></script>

</body>
</php>
