<?php
/**
 * JejakHijau - Donation Form
 * GET dan POST untuk donasi campaign
 */

require_once 'config.php';
require_once 'session-check.php';

// Check if user is logged in
checkUserLogin();

$user_id = getCurrentUserId();
$campaign_id = intval($_GET['campaign_id'] ?? 0);
$error = '';
$success = '';

if ($campaign_id <= 0) {
    header("Location: campaigns.php");
    exit();
}

// Get campaign data
$campaign = null;
$stmt = $conn->prepare("SELECT id, judul_campaign, lokasi, dana_terkumpul FROM campaigns WHERE id = ? AND status = 'approved'");
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

// Handle donation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nominal = intval($_POST['nominal'] ?? 0);
    $pesan = trim($_POST['pesan'] ?? '');
    
    // Validation
    if ($nominal < 10000) {
        $error = "Nominal donasi minimal Rp10.000.";
    } elseif (!isset($_FILES['bukti_transfer']) || $_FILES['bukti_transfer']['error'] !== UPLOAD_ERR_OK) {
        $error = "Silakan upload bukti transfer.";
    } else {
        // Validate image file
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION));
        $file_size = $_FILES['bukti_transfer']['size'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error = "Format bukti transfer harus JPG atau PNG.";
        } elseif ($file_size > $max_file_size) {
            $error = "Ukuran file terlalu besar. Maksimal 5MB.";
        } else {
            // Create upload directory if not exists
            if (!is_dir($upload_dir_donations)) {
                mkdir($upload_dir_donations, 0755, true);
            }
            
            // Generate unique filename
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir_donations . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $file_path)) {
                // Insert donation into database
                $created_at = date('Y-m-d');
                $status = 'success';
                
                $stmt = $conn->prepare("INSERT INTO donations (campaign_id, user_id, nominal, pesan, bukti_transfer, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iidssss", $campaign_id, $user_id, $nominal, $pesan, $file_path, $status, $created_at);
                
                if ($stmt->execute()) {
                    // Update campaign dana_terkumpul
                    $stmt = $conn->prepare("UPDATE campaigns SET dana_terkumpul = dana_terkumpul + ?, updated_at = ? WHERE id = ?");
                    $stmt->bind_param("dsi", $nominal, $created_at, $campaign_id);
                    $stmt->execute();
                    $stmt->close();
                    
                    $success = "Donasi berhasil dikirim! Terima kasih atas kontribusi Anda.";
                } else {
                    $error = "Terjadi kesalahan saat memproses donasi.";
                    // Delete uploaded file if database insert fails
                    unlink($file_path);
                }
            } else {
                $error = "Gagal mengunggah bukti transfer. Silakan coba lagi.";
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
    <title>Form Donasi | JejakHijau</title>

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
    <span>Form Donasi</span>
    <h1>Donasi untuk Campaign 🌱</h1>
    <p>Setiap Rp 10.000 = 1 pohon yang ditanam</p>
</section>
<!-- HEADER END -->


<!-- FORM SECTION -->
<section class="form-section">
    <div class="form-container form-container--md">

        <?php if (!empty($error)): ?>
            <div class="msg-error" id="donasi-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="msg-success" id="donasi-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <h2>Informasi Campaign</h2>
        <table class="form-table">
            <tr>
                <td>Judul Campaign</td>
                <td><?php echo htmlspecialchars($campaign['judul_campaign']); ?></td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td><?php echo htmlspecialchars($campaign['lokasi']); ?></td>
            </tr>
            <tr>
                <td>Dana Terkumpul</td>
                <td>Rp<?php echo number_format($campaign['dana_terkumpul'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <!-- Info Rekening -->
        <div class="rekening-info">
            <strong>Silakan transfer ke rekening berikut:</strong><br><br>
            Bank BCA<br>
            No. Rekening: <strong>1234567890</strong><br>
            Atas Nama: <strong>Jejak Hijau</strong><br><br>
            Setelah melakukan transfer, unggah bukti transfer pada form di bawah ini.
        </div>

        <form method="POST" action="donation.php?campaign_id=<?php echo $campaign_id; ?>" id="form-donasi" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <td>Nominal Donasi</td>
                    <td>
                        <input type="number" name="nominal" id="nominal" placeholder="Minimal 10.000" min="10000" step="1000" required value="<?php echo isset($_POST['nominal']) ? htmlspecialchars($_POST['nominal']) : ''; ?>">
                        <div class="estimasi-note">
                            🌱 <strong>Estimasi:</strong> <span id="estimasi-pohon">0</span> pohon akan ditanam
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Bukti Transfer</td>
                    <td>
                        <input type="file" name="bukti_transfer" id="bukti_transfer" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                        <small class="upload-info">Format yang diperbolehkan: JPG, JPEG, PNG</small>
                    </td>
                </tr>
                <tr>
                    <td>Pesan Dukungan</td>
                    <td>
                        <textarea name="pesan" id="pesan" rows="4" placeholder="Pesan dukunganmu (opsional)..."><?php echo isset($_POST['pesan']) ? htmlspecialchars($_POST['pesan']) : ''; ?></textarea>
                    </td>
                </tr>
            </table>

            <div class="btn-area">
                <a href="campaign-detail.php?id=<?php echo $campaign_id; ?>" class="btn-kirim btn-kirim--secondary">Kembali</a>
                <button type="submit" class="btn-kirim">Kirim Donasi</button>
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
<script>
    // Update estimasi pohon saat nominal berubah
    document.getElementById('nominal').addEventListener('input', updateEstimasi);
    // Initial calculation
    updateEstimasi();
</script>

</body>
</html>
