<?php
/**
 * JejakHijau - User Login
 * POST: login form
 */

require_once 'config.php';
require_once 'session-check.php';

// Redirect if already logged in
redirectIfLoggedIn('user');

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
        // Check user in database
        $stmt = $conn->prepare("SELECT id, nama_lengkap, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login successful - langsung redirect tanpa pesan
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nama_lengkap'];
                $_SESSION['user_email'] = $user['email'];
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Email atau password salah.";
            }
        } else {
            $error = "Email atau password salah.";
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

            <?php if (!empty($error)): ?>
                <div class="msg-error" id="login-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" id="form-login">
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Masukkan email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-auth">Login</button>
            </form>

            <div class="bottom-text">Belum punya akun?<a href="signup.php">Daftar sekarang</a></div>
        </div>

    </div>
</section>
<!-- LOGIN SECTION END -->


<!-- JS -->
<script src="main.js"></script>
<script src="validation.js"></script>

</body>
</html>