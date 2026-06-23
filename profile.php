<?php
/**
 * JejakHijau - User Profile
 * Menampilkan profil user yang sedang login
 */

require_once 'config.php';
require_once 'session-check.php';

// Check if user is logged in
checkUserLogin();

$is_logged_in = isUserLoggedIn();
$user_id = getCurrentUserId();

// Get user data
$user = null;
$stmt = $conn->prepare("SELECT id, nama_lengkap, email, no_hp, provinsi, alamat, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
}
$stmt->close();

if (!$user) {
    header("Location: logout.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | JejakHijau</title>

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

        <a href="#" id="hamburger-menu">
            <i data-feather="menu"></i>
        </a>
    </div>
</nav>
<!-- NAVBAR END -->


<!-- HEADER -->
<section class="header-bantuan">
    <h1>Profil Anda</h1>
</section>
<!-- HEADER END -->


<!-- PROFILE CONTENT -->
<section class="form-section">
    <div class="form-container form-container--md">

        <!-- Pesan Success -->
        <div class="msg-success" id="profile-success" style="display:none;">
            Profil berhasil diperbarui!
        </div>

        <!-- ============ VIEW MODE ============ -->
        <!-- Menampilkan tabel data profil -->
        <h2>Data Profil</h2>
        <table class="form-table">
            <tr>
                <td>Nama Lengkap</td>
                <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <td>No. Handphone</td>
                <td><?php echo htmlspecialchars($user['no_hp']); ?></td>
            </tr>
            <tr>
                <td>Provinsi</td>
                <td><?php echo htmlspecialchars($user['provinsi']); ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td><?php echo htmlspecialchars($user['alamat']); ?></td>
            </tr>
            <tr>
                <td>Terdaftar Sejak</td>
                <td><?php echo date('d F Y', strtotime($user['created_at'])); ?></td>
            </tr>
        </table>
        <div class="btn-area">
            <a href="edit-profile.php" class="btn-kirim">Edit Profil</a>
        </div>

    </div>
</section>
<!-- PROFILE CONTENT END -->


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
<script src="validation.js"></script>

</body>
</html>
