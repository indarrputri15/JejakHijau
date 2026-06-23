<?php
/**
 * JejakHijau - User Registration
 * POST: signup.html form
 */

require_once 'config.php';
require_once 'session-check.php';

// Redirect if already logged in
redirectIfLoggedIn('user');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $provinsi = trim($_POST['provinsi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($nama_lengkap) || empty($email) || empty($no_hp) || empty($provinsi) || empty($alamat) || empty($password)) {
        $error = "Semua field harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $password_confirm) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } elseif (!preg_match('/^(\+62|0)[0-9]{9,12}$/', str_replace(' ', '', $no_hp))) {
        $error = "Format nomor HP tidak valid.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $created_at = date('Y-m-d');

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, no_hp, provinsi, alamat, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $nama_lengkap, $email, $no_hp, $provinsi, $alamat, $hashed_password, $created_at, $created_at);

            if ($stmt->execute()) {
                $success = "Akun berhasil dibuat. Silakan login.";
                header("Refresh: 2; url=login.php");
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
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

            <?php if (!empty($error)): ?>
                <div class="msg-error" id="signup-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="msg-success" id="signup-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="signup.php" id="form-signup">
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" required value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>">
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Masukkan email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="input-group">
                    <label>No. Handphone</label>
                    <input type="tel" name="no_hp" placeholder="Masukkan nomor handphone" required value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>">
                </div>

                <div class="input-group">
                    <label>Provinsi</label>
                    <input type="text" name="provinsi" placeholder="Masukkan provinsi" required value="<?php echo isset($_POST['provinsi']) ? htmlspecialchars($_POST['provinsi']) : ''; ?>">
                </div>

                <div class="input-group">
                    <label>Alamat</label>
                    <textarea name="alamat" placeholder="Masukkan alamat lengkap" rows="3" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
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
<!-- SIGNUP SECTION END -->


<!-- JS -->
<script src="main.js"></script>
<script src="validation.js"></script>

</body>
</html>
