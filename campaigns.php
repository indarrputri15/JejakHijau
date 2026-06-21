<?php
//campaigns.php - Halaman untuk menampilkan daftar campaign yang sedang berlangsung
require_once 'config.php';
session_start();

$query = "SELECT c.*, u.nama_lengkap FROM campaigns c
          JOIN users u ON c.user_id = u.id
          WHERE c.status = 'approved'
          ORDER BY c.created_at DESC";

$result = $conn->query($query);
$campaigns = $result->fetch_all(MYSQLI_ASSOC);

function calculatePercentage($terkumpul, $target) {
    if ($target == 0) return 0;
    return round(($terkumpul / $target) * 100, 100);
}
?>

<!DOCTYPE php>
<php lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campaign | JejakHijau</title>

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
  <a href="index.php" class="navbar-logo">Jejak<span>Hijau</span></a>
  <ul class="ul-navbar">
    <li><a href="index.php#beranda">BERANDA</a></li>
    <li><a href="index.php#dampak">DAMPAK</a></li>
    <li><a href="index.php#tentang">TENTANG</a></li>
    <li><a href="bantuan.php">BANTUAN</a></li>
    <li><a href="campaigns.php" class="active">CAMPAIGN</a></li>
  </ul>
  <div class="navbar-extra">
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Jika sudah login -->
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
    <?php else: ?>
      <!-- Jika belum login: -->
      <a href="login.php">MASUK</a> 
    <?php endif; ?>
  </div>
</nav>
<!-- NAVBAR END -->


<!-- HEADER -->
<section class="header-kontribusi">
  <span>Halaman Campaign</span>
  <h1>Daftar Campaign yang Sedang Berlangsung 🌱</h1>
  <p>Pilih campaign yang ingin Anda bantu dengan donasi Anda.</p>
</section>
<!-- HEADER END -->

<!-- CAMPAIGNS LIST -->
<section class="panel-section">
  <div class="campaign-container">

      <?php if (count($campaigns) > 0): ?>
        <?php foreach ($campaigns as $campaign) :
          $percentage = calculatePercentage($campaign['dana_terkumpul'], $campaign['target_dana']);
        ?>
    <!-- CAMPAIGN CARD -->
    <div class="campaign-card">
      <div class="campaign-image">
        <?php if (!empty($campaign['gambar_sampul']) && file_exists('uploads/' . $campaign['gambar_sampul'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($campaign['gambar_sampul']); ?>" alt="Campaign">
        <?php else: ?>
          <img src="assets/nanam.jpeg" alt="Campaign">
        <?php endif; ?>
      </div>

      <div class="campaign-content">
        <span>Campaign Aktif</span>
        <h3><?php echo htmlspecialchars($campaign['judul_campaign']); ?></h3>
        <p><?php echo substr(htmlspecialchars($campaign['deskripsi']), 0, 100) . '...'; ?></p>
        
        <div class="campaign-progress">
          <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
          </div>
          <small>Rp<?php echo number_format($campaign['dana_terkumpul'], 0, ',', '.'); ?> / Rp<?php echo number_format($campaign['target_dana'], 0, ',', '.'); ?> (<?php echo $percentage; ?>%)
        </small>
        </div>

        <a href="campaign-detail.php?id=<?php echo $campaign['id']; ?>" class="btn detail-btn">Lihat Detail</a>
      </div>
    </div>
        <?php endforeach; ?>
      
        <?php else: ?>
          <div class="campaigns-empty">
            <p>Belum ada campaign yang aktif saat ini.</p>
          </div>
        <?php endif; ?>
    
    <!-- CREATE CAMPAIGN BOX -->
    <div class="campaign-card">
      <div class="campaign-image">
        <img src="assets/nanam.jpeg" alt="Campaign">
      </div>
      <div class="campaign-content">
        <span>Campaign Aktif</span>
        <h3>Penanaman Mangrove Sulawesi</h3>
        <p>Program penyelamatan ekosistem pesisir melalui penanaman pohon mangrove...</p>
        <div class="campaign-progress">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 40%"></div>
          </div>
          <small>Rp2.000.000 / Rp5.000.000 (40%)</small>
        </div>
        <a href="campaign-detail.php" class="btn detail-btn">Lihat Detail</a>
      </div>
    </div>

    <!-- EMPTY STATE — dipindahkan dari inline style -->
    <!-- PHP: if (count($campaigns) === 0) -->
    <!--
    <div class="campaigns-empty">
      <p>Belum ada campaign yang aktif saat ini.</p>
    </div>
    -->

    <!-- CAMPAIGN CREATE BOX -->
    <div class="campaign-card campaign-create-box">
      <div class="campaign-create-inner">
        <h3>Miliki Campaign Anda Sendiri</h3>
        <p>
          Apakah Anda memiliki lokasi yang butuh penghijauan?
          Kami bisa membantu merealisasikannya bersama komunitas.
        </p>
        <!-- PHP: if (isset($_SESSION['user_id'])) -->
        <a href="create-campaign.php" class="btn-primary">Ajukan Campaign</a>
        <!-- Else: <a href="login.php" class="btn-primary">Login untuk Membuat Campaign</a> -->
      </div>
    </div>

  </div>
</section>
<!-- CAMPAIGNS LIST END -->


<!-- FOOTER -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-logo">
      <h2>JejakHijau</h2>
      <p>Langkah kecilmu, dampak besar bagi bumi 🌏</p>
    </div>
    <div class="footer-menu">
      <h3>Menu</h3>
      <a href="index.php">Beranda</a>
      <a href="campaigns.php">Campaign</a>
      <a href="bantuan.php">Bantuan</a>
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
