<?php

require_once 'config.php';
require_once 'session-check.php';

$is_logged_in = isUserLoggedIn();
$campaign_id = intval($_GET['id'] ?? 0);

if ($campaign_id <= 0) {
    header("Location: campaigns.php");
    exit();
}

// Get campaign data
$campaign = null;
$stmt = $conn->prepare("SELECT id, user_id, judul_campaign, deskripsi, gambar_sampul, target_dana, dana_terkumpul, lokasi, created_at FROM campaigns WHERE id = ? AND status = 'approved'");
$stmt->bind_param("i", $campaign_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $campaign = $result->fetch_assoc();
}
$stmt->close();

if (!$campaign) {
    header("Location: campaigns.php");
    exit();
}

// Get donations for this campaign
$donations = [];
$stmt = $conn->prepare("SELECT u.nama_lengkap, d.nominal, d.pesan, d.created_at FROM donations d JOIN users u ON d.user_id = u.id WHERE d.campaign_id = ? AND d.status = 'success' ORDER BY d.created_at DESC LIMIT 10");
$stmt->bind_param("i", $campaign_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $donations[] = $row;
}
$stmt->close();

$percentage = $campaign['dana_terkumpul'] > 0 ? ($campaign['dana_terkumpul'] / $campaign['target_dana']) * 100 : 0;
$percentage = min($percentage, 100);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($campaign['judul_campaign']); ?> | JejakHijau</title>

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
        <li><a href="campaigns.php">CAMPAIGN</a></li>
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


<!-- CAMPAIGN DETAIL CONTENT -->
<section class="campaign-page">
    <div class="campaign-container">

        <div class="campaign-detail">
            <!-- Campaign Image -->
            <div class="campaign-detail-image">
                <img src="<?php echo htmlspecialchars($campaign['gambar_sampul']); ?>" alt="<?php echo htmlspecialchars($campaign['judul_campaign']); ?>">
            </div>

            <!-- Campaign Info -->
            <div class="campaign-detail-content">
                <h2><?php echo htmlspecialchars($campaign['judul_campaign']); ?></h2>
                <p class="campaign-location"><i data-feather="map-pin"></i> <?php echo htmlspecialchars($campaign['lokasi']); ?></p>

                <!-- Progress -->
                <div class="campaign-progress">
                    <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                    </div>
                    <div class="progress-info">
                        <p><strong>Rp<?php echo number_format($campaign['dana_terkumpul'], 0, ',', '.'); ?></strong> dari <strong>Rp<?php echo number_format($campaign['target_dana'], 0, ',', '.'); ?></strong></p>
                        <p><strong><?php echo round($percentage); ?>%</strong> terkumpul</p>
                    </div>
                </div>

                <!-- Donation Button -->
                <?php if ($is_logged_in): ?>
                    <a href="donation.php?campaign_id=<?php echo $campaign_id; ?>" class="btn-primary">Donasi Sekarang</a>
                <?php else: ?>
                    <a href="login.php" class="btn-primary">Login untuk Donasi</a>
                <?php endif; ?>

                <!-- Description -->
                <div class="campaign-detail-description">
                    <h3>Deskripsi Campaign</h3>
                    <p><?php echo htmlspecialchars($campaign['deskripsi']); ?></p>
                </div>
            </div>
        </div>

        <!-- Donations -->
        <?php if (count($donations) > 0): ?>
            <div class="campaign-donations">
                <h3>Donasi Terbaru</h3>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th>Nama Donatur</th>
                            <th>Nominal</th>
                            <th>Pesan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donation['nama_lengkap']); ?></td>
                                <td>Rp<?php echo number_format($donation['nominal'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($donation['pesan'] ?? '-'); ?></td>
                                <td><?php echo date('d F Y', strtotime($donation['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</section>
<!-- CAMPAIGN DETAIL CONTENT END -->


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


<script src="main.js"></script>

</body>
</html>
