<?php
/**
 * JejakHijau - Admin User Detail
 * Menampilkan detail user, campaign, dan donasi
 */

require_once 'config.php';
require_once 'session-check.php';

// Check if admin is logged in
checkAdminLogin();

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    header("Location: admin-dashboard.php");
    exit();
}

// Fetch user details
$user = null;
$stmt = $conn->prepare("SELECT id, nama_lengkap, email, no_hp, provinsi, alamat, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    // User tidak ditemukan
    header("Location: admin-dashboard.php");
    exit();
}
$stmt->close();

// Fetch user's campaigns
$user_campaigns = [];
$camp_stmt = $conn->prepare("
    SELECT id, judul_campaign, target_dana, status, created_at
    FROM campaigns
    WHERE user_id = ?
    ORDER BY created_at DESC
");
if ($camp_stmt) {
    $camp_stmt->bind_param("i", $user_id);
    $camp_stmt->execute();
    $camp_result = $camp_stmt->get_result();
    while ($row = $camp_result->fetch_assoc()) {
        $user_campaigns[] = $row;
    }
    $camp_stmt->close();
}

// Fetch user's donations
$user_donations = [];
$don_stmt = $conn->prepare("
    SELECT d.id, c.judul_campaign, u.nama_lengkap as pembuat_campaign, d.nominal, d.pesan, d.created_at
    FROM donations d
    LEFT JOIN campaigns c ON d.campaign_id = c.id
    LEFT JOIN users u ON c.user_id = u.id
    WHERE d.user_id = ?
    ORDER BY d.created_at DESC
");
if ($don_stmt) {
    $don_stmt->bind_param("i", $user_id);
    $don_stmt->execute();
    $don_result = $don_stmt->get_result();
    while ($row = $don_result->fetch_assoc()) {
        $user_donations[] = $row;
    }
    $don_stmt->close();
}

// Count stats
$total_campaigns = count($user_campaigns);
$total_donations = count($user_donations);
$total_dana_donated = 0;
foreach ($user_donations as $donation) {
    $total_dana_donated += $donation['nominal'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail User | JejakHijau Admin</title>

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

<!-- ADMIN NAVBAR -->
<nav class="admin-navbar">
  <a href="index.php" class="navbar-logo">Jejak<span>Hijau</span></a>
  <ul class="ul-navbar">
    <li><a href="admin-dashboard.php#users-section">KELOLA USER</a></li>
    <li><a href="admin-dashboard.php#campaigns-section">VERIFIKASI CAMPAIGN</a></li>
    <li><a href="admin-dashboard.php#approved-campaigns-section">CAMPAIGN AKTIF</a></li>
  </ul>
  <div class="admin-right">
    <a href="logout.php">Logout</a>
  </div>
</nav>
<!-- ADMIN NAVBAR END -->


<!-- ADMIN HEADER -->
<section class="admin-header">
  <span>User Detail</span>
  <h1>Detail User</h1>
  <p>Informasi lengkap pengguna dan aktivitasnya</p>
</section>
<!-- ADMIN HEADER END -->


<!-- BACK BUTTON -->
<section class="admin-section admin-section--pt">
  <a href="admin-dashboard.php#users-section" class="btn-back">← Kembali ke Daftar User</a>
</section>
<!-- BACK BUTTON END -->


<!-- USER INFO SECTION -->
<section class="admin-section admin-section--pt">
  <div class="user-detail-card">
    <h2 class="user-detail-name"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h2>
    <table class="admin-table">
      <tbody>
        <tr>
          <th>Email</th>
          <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
        <tr>
          <th>No. Handphone</th>
          <td><?php echo htmlspecialchars($user['no_hp'] ?? '-'); ?></td>
        </tr>
        <tr>
          <th>Provinsi</th>
          <td><?php echo htmlspecialchars($user['provinsi'] ?? '-'); ?></td>
        </tr>
        <tr>
          <th>Alamat</th>
          <td><?php echo htmlspecialchars($user['alamat'] ?? '-'); ?></td>
        </tr>
        <tr>
          <th>Terdaftar</th>
          <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</section>
<!-- USER INFO SECTION END -->


<!-- USER'S CAMPAIGNS SECTION -->
<section class="admin-section admin-section--pt">
  <div class="section-title">
    <h2>Campaign yang dibuat (<?php echo $total_campaigns; ?>)</h2>
  </div>

  <?php if ($total_campaigns > 0): ?>
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Campaign</th>
            <th>Target Dana</th>
            <th>Status</th>
            <th>Dibuat</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($user_campaigns as $index => $campaign): ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($campaign['judul_campaign']); ?></td>
              <td>Rp<?php echo number_format($campaign['target_dana'], 0, ',', '.'); ?></td>
              <td>
                <?php
                  $status_class = '';
                  $status_text = '';
                  if ($campaign['status'] === 'approved') {
                    $status_class = 'status-badge status-active';
                    $status_text = 'Disetujui';
                  } elseif ($campaign['status'] === 'rejected') {
                    $status_class = 'status-badge status-rejected';
                    $status_text = 'Ditolak';
                  } else {
                    $status_class = 'status-badge status-pending';
                    $status_text = 'Menunggu Verifikasi';
                  }
                ?>
                <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
              </td>
              <td><?php echo date('d M Y', strtotime($campaign['created_at'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="admin-empty-state">
      <p>User belum membuat campaign apapun</p>
    </div>
  <?php endif; ?>
</section>
<!-- USER'S CAMPAIGNS SECTION END -->


<!-- USER'S DONATIONS SECTION -->
<section class="admin-section admin-section--pt">
  <div class="section-title">
    <h2>Donasi yang dilakukan (<?php echo $total_donations; ?>, Total: Rp<?php echo number_format($total_dana_donated, 0, ',', '.'); ?>)</h2>
  </div>

  <?php if ($total_donations > 0): ?>
    <div class="table-responsive">
      <table class="admin-table admin-table--donations">
        <thead>
          <tr>
            <th>No</th>
            <th>Campaign</th>
            <th>Pembuat Campaign</th>
            <th>Nominal</th>
            <th>Pesan</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($user_donations as $index => $donation): ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($donation['judul_campaign'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($donation['pembuat_campaign'] ?? '-'); ?></td>
              <td><strong>Rp<?php echo number_format($donation['nominal'], 0, ',', '.'); ?></strong></td>
              <td><em><?php echo htmlspecialchars($donation['pesan'] ?? '-'); ?></em></td>
              <td><?php echo date('d M Y', strtotime($donation['created_at'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="admin-empty-state">
      <p>User belum memberikan donasi apapun</p>
    </div>
  <?php endif; ?>

</section>
<!-- USER'S DONATIONS SECTION END -->


<!-- JS -->
<script src="main.js"></script>

</body>
</html>