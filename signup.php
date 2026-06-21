<?php
// signup.php
require_once 'config.php';
require_once 'session-check.php';

redirectIfLoggedIn('user');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $provinsi = trim($_POST['provinsi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');
    // Validasi input
    if (empty($nama_lengkap) || empty($email) || empty($no_hp) || empty($provinsi) || empty($alamat) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $password_confirm) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Check apakah email sudah terdaftar
        $check_query = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email sudah terdaftar! Gunakan email lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert ke database
            $insert_query = "INSERT INTO users (nama_lengkap, email, no_hp, provinsi, alamat, password) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            
            if ($insert_stmt) {
                $insert_stmt->bind_param("ssssss", $nama_lengkap, $email, $no_hp, $provinsi, $alamat, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $success = "Akun berhasil dibuat! Silahkan login.";
                    header("Refresh: 2; url=login.php");
                } else {
                    $error = "Terjadi kesalahan saat membuat akun!";
                }
                
                $insert_stmt->close();
            } else {
                $error = "Terjadi kesalahan pada query!";
            }
        }
        
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | JejakHijau</title>

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
  <a href="index.html" class="navbar-logo">Jejak<span>Hijau</span></a>
</nav>
<!-- NAVBAR END -->


<!-- SIGNUP SECTION -->
<section class="login-section">
  <div class="login-container">

    <!-- LEFT -->
    <div class="login-left">
      <span>Bergabung Sekarang</span>
      <h1>Jadi Bagian Dari JejakHijau 🌱</h1>
      <p>
        Buat akun untuk mulai berdonasi
        dan membantu penghijauan bumi.
      </p>
    </div>

    <!-- RIGHT -->
    <div class="login-right login-right--lg">
      <h2>Create Account</h2>

      <!-- Error Message -->
      <?php if (!empty($error)): ?>
      <div class="msg-error msg--sm" id="signup-error" style="display:none;">
        <?php echo htmlspecialchars($error); ?> Pesan error dari server.
      </div>
      <?php endif; ?>

      <!-- Success Message -->
      <?php if (!empty($success)): ?>
      <div class="msg-success msg--sm" id="signup-success" style="display:none;">
        <?php echo htmlspecialchars($success); ?> Akun berhasil dibuat! Silahkan login.
      </div>
      <?php endif; ?>

      <form method="POST" action="" id="form-signup">

        <div class="input-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Masukkan email" required>
          <small class="email-error" id="email-error">Email sudah terdaftar</small>
        </div>

        <div class="input-group">
          <label>No. Handphone</label>
          <input type="tel" name="no_hp" placeholder="Masukkan nomor handphone" required>
        </div>

        <div class="input-group">
          <label>Provinsi</label>
          <input type="text" name="provinsi" placeholder="Masukkan provinsi" required>
        </div>

        <div class="input-group">
          <label>Alamat</label>
          <textarea name="alamat" placeholder="Masukkan alamat lengkap" rows="3" required></textarea>
        </div>

        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Masukkan password (min 6 karakter)" required>
        </div>

        <div class="input-group">
          <label>Konfirmasi Password</label>
          <input type="password" name="password_confirm" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn-auth">Sign Up</button>
      </form>

      <div class="bottom-text bottom-text--sm">
        Sudah punya akun?
        <a href="login.php">Login sekarang</a>
      </div>
    </div>

  </div>
</section>

<script src="main.js"></script>
<script src="validation.js"></script>

</body>
</html>
