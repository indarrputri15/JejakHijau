<?php

require_once 'config.php';
require_once 'session-check.php';

// Redirect jika admin sudah login
redirectIfLoggedIn('admin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Check admin credentials from config
        if ($email === $admin_email && password_verify($password, $admin_password_hash)) {
            // Login successful
            $_SESSION['admin_id'] = 'admin_' . md5($admin_email);
            $_SESSION['admin_email'] = $admin_email;
            
            // Langsung redirect tanpa pesan
            header("Location: admin-dashboard.php");
            exit();
        } else {
            $error = "Email atau password admin salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | JejakHijau</title>

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
</nav>
<!-- NAVBAR END -->


<!-- ADMIN LOGIN SECTION -->
<section class="login-section">
  <div class="login-container">

    <!-- LEFT -->
    <div class="login-left">
      <span>Admin Panel</span>
      <h1>Login Admin JejakHijau 🌱</h1>
      <p>
        Kelola campaign, verifikasi donasi,
        dan pantau sistem JejakHijau.
      </p>
    </div>

    <!-- RIGHT -->
    <div class="login-right">
      <h2>Login Admin</h2>

      <?php if (!empty($error)): ?>
        <div class="msg-error" id="admin-login-error">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="admin-login.php">
        <div class="input-group">
          <label>Email Admin</label>
          <input type="email" name="email" placeholder="Masukkan email admin" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit" class="btn-auth">Login</button>
      </form>

      <div class="bottom-text bottom-text--sm">
        <p>Email: <strong>admin@jejakhijau.com</strong></p>
        <p>Password: <strong>admin123</strong></p>
      </div>
    </div>

  </div>
</section>
<!-- ADMIN LOGIN SECTION END -->


<!-- JS -->
<script src="main.js"></script>

</body>
</html>