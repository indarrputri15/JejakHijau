<?php
/**
 * JejakHijau - Create Campaign
 * GET dan POST untuk membuat campaign baru
 */

require_once 'config.php';
require_once 'session-check.php';

// Check if user is logged in
checkUserLogin();

$user_id = getCurrentUserId();
$error = '';
$success = '';

// Handle campaign creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_campaign = trim($_POST['judul_campaign'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $target_dana = intval($_POST['target_dana'] ?? 0);
    
    // Validation
    if (empty($judul_campaign) || empty($deskripsi) || empty($lokasi) || $target_dana < 100000) {
        $error = "Semua field harus diisi dan target dana minimal Rp100.000.";
    } elseif (!isset($_FILES['gambar_sampul']) || $_FILES['gambar_sampul']['error'] !== UPLOAD_ERR_OK) {
        $error = "Silakan upload gambar sampul campaign.";
    } else {
        // Validate image file
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_ext = strtolower(pathinfo($_FILES['gambar_sampul']['name'], PATHINFO_EXTENSION));
        $file_size = $_FILES['gambar_sampul']['size'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error = "Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.";
        } elseif ($file_size > $max_file_size) {
            $error = "Ukuran gambar terlalu besar. Maksimal 5MB.";
        } else {
            // Create upload directory if not exists
            if (!is_dir($upload_dir_campaigns)) {
                mkdir($upload_dir_campaigns, 0755, true);
            }
            
            // Generate unique filename
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir_campaigns . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['gambar_sampul']['tmp_name'], $file_path)) {
                // Insert campaign into database
                $created_at = date('Y-m-d');
                $updated_at = date('Y-m-d');
                $status = 'pending';
                $dana_terkumpul = 0;
                
                $stmt = $conn->prepare("INSERT INTO campaigns (user_id, judul_campaign, deskripsi, target_dana, dana_terkumpul, gambar_sampul, status, lokasi, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isddsdsss", $user_id, $judul_campaign, $deskripsi, $target_dana, $dana_terkumpul, $file_path, $status, $lokasi, $created_at, $updated_at);
                
                if ($stmt->execute()) {
                    $success = "Campaign berhasil diajukan! Admin akan memverifikasinya.";
                } else {
                    $error = "Terjadi kesalahan saat menyimpan campaign.";
                    // Delete uploaded file if database insert fails
                    unlink($file_path);
                }
                $stmt->close();
            } else {
                $error = "Gagal mengunggah gambar. Silakan coba lagi.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Campaign | JejakHijau</title>

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
<section class="header-kontribusi">
    <span>Buat Campaign</span>
    <h1>Ajukan Campaign Penghijauan Anda 🌱</h1>
    <p>Campaign Anda akan diverifikasi admin sebelum ditampilkan ke publik</p>
</section>
<!-- HEADER END -->


<!-- FORM SECTION -->
<section class="form-section">
    <div class="form-container form-container--md">

        <?php if (!empty($error)): ?>
            <div class="msg-error" id="campaign-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="msg-success" id="campaign-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <h2>Form Campaign Baru</h2>

        <form method="POST" action="create-campaign.php" enctype="multipart/form-data" id="form-campaign">
            <table class="form-table">
                <tr>
                    <td>Judul Campaign</td>
                    <td>
                        <input type="text" name="judul_campaign" placeholder="Contoh: Reboisasi Hutan Aceh" required value="<?php echo isset($_POST['judul_campaign']) ? htmlspecialchars($_POST['judul_campaign']) : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>Deskripsi</td>
                    <td>
                        <textarea name="deskripsi" rows="5" placeholder="Jelaskan campaign kamu secara detail..." required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Lokasi Campaign</td>
                    <td>
                        <input type="text" name="lokasi" placeholder="Provinsi / Kota" required value="<?php echo isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>Target Dana</td>
                    <td>
                        <input type="number" name="target_dana" placeholder="Minimal 100.000" min="100000" step="100000" required value="<?php echo isset($_POST['target_dana']) ? htmlspecialchars($_POST['target_dana']) : ''; ?>">
                        <small class="upload-info">Minimal Rp100.000</small>
                    </td>
                </tr>
                <tr>
                    <td>Gambar Sampul Campaign</td>
                    <td>
                        <input type="file" name="gambar_sampul" accept="image/*" required>
                        <small class="upload-info">Format: JPG, PNG, GIF, WebP. Maksimal 5MB</small>
                    </td>
                </tr>
            </table>

            <div class="btn-area">
                <a href="campaigns.php" class="btn-kirim btn-kirim--secondary">Kembali</a>
                <button type="submit" class="btn-kirim">Ajukan Campaign</button>
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


<!-- JS -->
<script src="main.js"></script>
<script src="validation.js"></script>

</body>
</html>
