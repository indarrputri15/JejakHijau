<?php
require_once 'config.php';
require_once 'session-check.php';

redirectIfLoggedIn('user'); 
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi.";
    } else {
        // Cek user di database
        $stmt = $conn->prepare("SELECT id, nama_lengkap, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login sukses
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nama_lengkap'];
                $_SESSION['user_email'] = $user['email'];
                
                $success = "Login berhasil. Mengalihkan...";
                header("Refresh: 2; url=profile.php");
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
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
  <title>Login | JejakHijau</title>

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


<!-- LOGIN SECTION -->
<section class="login-section">
  <div class="login-container">

    <!-- LEFT -->
    <div class="login-left">
      <span>Selamat Datang</span>
      <h1>Login Ke JejakHijau 🌱</h1>
      <p>
        Masuk untuk mulai berdonasi,
        membuat campaign,
        atau mengelola sistem.
      </p>
    </div>

    <!-- RIGHT -->
    <div class="login-right">
      <h2>Login Account</h2>

         <!-- PHP untuk menampilkan pesan error dari proses login -->
        <?php if (!empty($error)) : ?>
          <div class="msg-error" id="login-error" style="display:none;">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <!-- Sukses login -->
        <?php if (!empty($success)) : ?>
          <div class="msg-success" id="login-success" style="display:none;">
            <?php echo htmlspecialchars($success); ?>
          </div>
        <?php endif; ?>

       <form method="POST" action="login_process.php" id="form-login">
        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Masukkan email" required>
        </div>
        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit" class="btn-auth">Login</button>
      </form>

      <div class="bottom-text">Belum punya akun?<a href="signup.html">Daftar sekarang</a></div>
    </div>

  </div>
</section>
<!-- LOGIN SECTION END -->


<!-- JS -->
<script src="main.js"></script>
<script src="validation.js"></script>

</body>
</html>
