<?php
// profile.php - User Profile Page
require_once 'config.php';
require_once 'session-check.php';

checkUserLogin();

$error = '';
$success = '';
$edit_mode = isset($_GET['edit']) && $_GET['edit'] === '1';

// Get user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $edit_mode) {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $provinsi = trim($_POST['provinsi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    
    if (empty($nama_lengkap) || empty($no_hp) || empty($provinsi) || empty($alamat)) {
        $error = "Semua field harus diisi!";
    } else {
        $update_query = "UPDATE users SET nama_lengkap = ?, no_hp = ?, provinsi = ?, alamat = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssi", $nama_lengkap, $no_hp, $provinsi, $alamat, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "Profil berhasil diperbarui!";
            $_SESSION['user_name'] = $nama_lengkap;
            // Refresh data
            $query = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = "Terjadi kesalahan saat mengupdate profil!";
        }
        
        $update_stmt->close();
    }
}
?>

<!DOCTYPE php>
<php lang="id">
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
        <a href="profil.php">Profil Saya</a>
        <a href="edit-profil.php">Edit Profil</a>
        <a href="#">Logout</a>
      </div>
    </div>
  </div>
</nav>
<!-- NAVBAR END -->


<!-- HEADER -->
<section class="header-bantuan">
  <span>Halaman Profil</span>
  <h1>Profil Anda</h1>
  <p>Kelola data profil Anda di sini</p>
</section>
<!-- HEADER END -->


<!-- PROFILE CONTENT -->
<section class="form-section">
  <div class="form-container form-container--md">

    <!-- Pesan Error -->
    <?php if (!empty($error)): ?>
    <div class="msg-error" id="profile-error" style="display:none;">
      <?php echo phpspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <!-- Pesan Sukses -->
    <?php if (!empty($success)): ?>
    <div class="msg-success" id="profile-success" style="display:none;">
      <?php echo phpspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <!-- Menampilkan tabel data profil -->
    <h2>Data Profil</h2>
    <table class="form-table">
      <tr>
        <td>Nama Lengkap</td>
        <td><?php echo phpspecialchars($user['nama_lengkap']) ?></td>
      </tr>
      <tr>
        <td>Email</td>
        <td><?php echo phpspecialchars($user['email']); ?></td>
      </tr>
      <tr>
        <td>No. Handphone</td>
        <td><?php echo phpspecialchars($user['no_hp']); ?></td>
      </tr>
      <tr>
        <td>Provinsi</td>
        <td><?php echo phpspecialchars($user['provinsi']); ?></td>
      </tr>
      <tr>
        <td>Alamat</td>
        <td><?php echo phpspecialchars($user['alamat']); ?></td>
      </tr>
      <tr>
        <td>Terdaftar Sejak</td>
        <td><?php echo date('d F Y H:i', strtotime($user['created_at'])) ?></td>
      </tr>
    </table>
    <div class="btn-area">
      <a href="edit-profil.php" class="btn-kirim">Edit Profil</a>
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
</php>