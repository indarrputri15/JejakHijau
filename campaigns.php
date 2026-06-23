<?php

require_once 'config.php';
require_once 'session-check.php';

$is_logged_in = isUserLoggedIn();

// Get all approved campaigns
$campaigns = [];
$result = $conn->query("SELECT id, judul_campaign, deskripsi, gambar_sampul, target_dana, dana_terkumpul FROM campaigns WHERE status = 'approved' ORDER BY created_at DESC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $campaigns[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
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
        <li><a href="campaigns.php" class="active">CAMPAIGN</a></li>
    </ul>
    <div class="navbar-extra">
        <?php if ($is_logged_in): ?>
            <!-- Jika user sudah login -->
            <div class="profile-menu" id="profile-menu">
                <button class="profile-btn" id="profile-btn">
                    <i data-feather="user"></i>
                </button>
                <div class="dropdown-profile" id="dropdown-profile">
                    <a href="profile.php">Profil Saya</a>
                    <a href="create-campaign.php">Buat Campaign</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Jika belum login -->
            <div class="profile-menu" id="profile-menu">
                <button class="profile-btn" id="profile-btn">
                    <i data-feather="user"></i>
                </button>
                <div class="dropdown-profile" id="dropdown-profile">
                    <a href="login.php">Masuk</a>
                </div>
            </div>
        <?php endif; ?>

        <a href="#" id="hamburger-menu">
            <i data-feather="menu"></i>
        </a>
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

<section class="panel-section">
    <div class="campaign-container">

        <?php if (count($campaigns) > 0): ?>
            <?php foreach ($campaigns as $campaign): ?>
                <div class="campaign-card">
                    <div class="campaign-image">
                        <img src="<?php echo htmlspecialchars($campaign['gambar_sampul']); ?>" alt="<?php echo htmlspecialchars($campaign['judul_campaign']); ?>">
                    </div>
                    <div class="campaign-content">
                        <span>Campaign Aktif</span>
                        <h3><?php echo htmlspecialchars($campaign['judul_campaign']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($campaign['deskripsi'], 0, 100)) . '...'; ?></p>
                        <div class="campaign-progress">
                            <div class="progress-bar">
                                <?php 
                                $percentage = $campaign['dana_terkumpul'] > 0 ? ($campaign['dana_terkumpul'] / $campaign['target_dana']) * 100 : 0;
                                $percentage = min($percentage, 100);
                                ?>
                                <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <small>Rp<?php echo number_format($campaign['dana_terkumpul'], 0, ',', '.'); ?> / Rp<?php echo number_format($campaign['target_dana'], 0, ',', '.'); ?> (<?php echo round($percentage); ?>%)</small>
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

        <!-- CAMPAIGN CREATE BOX -->
        <div class="campaign-card campaign-create-box">
            <div class="campaign-create-inner">
                <h3>Miliki Campaign Anda Sendiri</h3>
                <p>
                    Apakah Anda memiliki lokasi yang butuh penghijauan?
                    Kami bisa membantu merealisasikannya bersama komunitas.
                </p>
                <?php if ($is_logged_in): ?>
                    <a href="create-campaign.php" class="btn-primary">Ajukan Campaign</a>
                <?php else: ?>
                    <a href="login.php" class="btn-primary">Login untuk Membuat Campaign</a>
                <?php endif; ?>
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
</html>
