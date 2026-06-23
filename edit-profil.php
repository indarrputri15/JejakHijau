<?php

require_once 'config.php';
require_once 'session-check.php';

// Check if user is logged in
checkUserLogin();

$user_id = getCurrentUserId();
$error = '';
$success = '';

// Get current user data
$user = null;
$stmt = $conn->prepare("SELECT id, nama_lengkap, email, no_hp, provinsi, alamat FROM users WHERE id = ?");
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

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $provinsi = trim($_POST['provinsi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    // Validation
    if (empty($nama_lengkap) || empty($no_hp) || empty($provinsi) || empty($alamat)) {
        $error = "Semua field harus diisi.";
    } elseif (!preg_match('/^(\+62|0)[0-9]{9,12}$/', str_replace(' ', '', $no_hp))) {
        $error = "Format nomor HP tidak valid.";
    } else {
        // Update user
        $updated_at = date('Y-m-d');
        $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, no_hp = ?, provinsi = ?, alamat = ?, updated_at = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nama_lengkap, $no_hp, $provinsi, $alamat, $updated_at, $user_id);

        if ($stmt->execute()) {
            $success = "Profil berhasil diperbarui!";
            // Update session
            $_SESSION['user_name'] = $nama_lengkap;
            // Refresh user data
            $user['nama_lengkap'] = $nama_lengkap;
            $user['no_hp'] = $no_hp;
            $user['provinsi'] = $provinsi;
            $user['alamat'] = $alamat;
        } else {
            $error = "Terjadi kesalahan saat mengupdate profil.";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil | JejakHijau</title>

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
    <span>Edit Profil</span>
    <h1>Perbarui Data Profil Anda</h1>
    <p>Ubah informasi profil Anda</p>
</section>
<!-- HEADER END -->


<!-- FORM SECTION -->
<section class="form-section">
    <div class="form-container form-container--md">

        <?php if (!empty($error)): ?>
            <div class="msg-error" id="profile-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="msg-success" id="profile-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <h2>Form Edit Profil</h2>

        <form method="POST" action="edit-profile.php" id="form-profile">
            <table class="form-table">
                <tr>
                    <td>Nama Lengkap</td>
                    <td>
                        <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        <small>Email tidak dapat diubah</small>
                    </td>
                </tr>
                <tr>
                    <td>No. Handphone</td>
                    <td>
                        <input type="tel" name="no_hp" value="<?php echo htmlspecialchars($user['no_hp']); ?>" required>
                    </td>
                </tr>
                <tr>
                    <td>Provinsi</td>
                    <td>
                        <input type="text" name="provinsi" value="<?php echo htmlspecialchars($user['provinsi']); ?>" required>
                    </td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>
                        <textarea name="alamat" rows="3" required><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                    </td>
                </tr>
            </table>

            <div class="btn-area">
                <a href="profile.php" class="btn-kirim btn-kirim--secondary">Kembali</a>
                <button type="submit" class="btn-kirim">Simpan Perubahan</button>
            </div>
        </form>

    </div>
</section>
<!-- FORM SECTION END -->


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
