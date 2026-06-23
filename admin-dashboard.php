<?php

require_once 'config.php';
require_once 'session-check.php';

// Check if admin is logged in
checkAdminLogin();

$error = '';
$success = '';

// Handle campaign actions (approve, reject, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id = isset($_POST['campaign_id']) ? intval($_POST['campaign_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($campaign_id > 0) {
        switch ($action) {
            case 'approve':
                $stmt = $conn->prepare("UPDATE campaigns SET status = 'approved', updated_at = NOW() WHERE id = ?");
                $stmt->bind_param("i", $campaign_id);
                if ($stmt->execute()) {
                    $success = "Campaign berhasil disetujui!";
                } else {
                    $error = "Gagal menyetujui campaign.";
                }
                $stmt->close();
                break;

            case 'reject':
                $stmt = $conn->prepare("UPDATE campaigns SET status = 'rejected', updated_at = NOW() WHERE id = ?");
                $stmt->bind_param("i", $campaign_id);
                if ($stmt->execute()) {
                    $success = "Campaign berhasil ditolak.";
                } else {
                    $error = "Gagal menolak campaign.";
                }
                $stmt->close();
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM campaigns WHERE id = ?");
                $stmt->bind_param("i", $campaign_id);
                if ($stmt->execute()) {
                    $success = "Campaign berhasil dihapus.";
                } else {
                    $error = "Gagal menghapus campaign.";
                }
                $stmt->close();
                break;
        }
    }
}

// Fetch all users
$all_users = [];
$user_result = $conn->query("SELECT id, nama_lengkap, email, no_hp, provinsi, created_at FROM users ORDER BY created_at DESC");
if ($user_result) {
    while ($row = $user_result->fetch_assoc()) {
        $all_users[] = $row;
    }
}

// Fetch pending campaigns (belum diverifikasi)
$pending_campaigns = [];
$pending_result = $conn->query("
    SELECT c.id, c.judul, c.user_id, u.nama_lengkap, c.target_dana, c.lokasi, c.created_at, c.status
    FROM campaigns c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.status = 'pending'
    ORDER BY c.created_at DESC
");
if ($pending_result) {
    while ($row = $pending_result->fetch_assoc()) {
        $pending_campaigns[] = $row;
    }
}

// Fetch approved campaigns
$approved_campaigns = [];
$approved_result = $conn->query("
    SELECT c.id, c.judul, c.user_id, u.nama_lengkap, c.target_dana, c.dana_terkumpul, c.lokasi, c.created_at, c.status
    FROM campaigns c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.status = 'approved'
    ORDER BY c.created_at DESC
");
if ($approved_result) {
    while ($row = $approved_result->fetch_assoc()) {
        $approved_campaigns[] = $row;
    }
}

// Count campaigns
$total_users = count($all_users);
$total_pending = count($pending_campaigns);
$total_approved = count($approved_campaigns);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | JejakHijau</title>

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
    <li><a href="#users-section">KELOLA USER</a></li>
    <li><a href="#campaigns-section">VERIFIKASI CAMPAIGN</a></li>
    <li><a href="#approved-campaigns-section">CAMPAIGN AKTIF</a></li>
  </ul>
  <div class="admin-right">
    <a href="logout.php">Logout</a>
  </div>
</nav>
<!-- ADMIN NAVBAR END -->


<!-- ADMIN HEADER -->
<section class="admin-header">
  <span>Admin Panel</span>
  <h1>Dashboard Admin JejakHijau</h1>
  <p>Kelola campaign dan user dari pengguna</p>
</section>
<!-- ADMIN HEADER END -->


<!-- MESSAGES -->
<?php if (!empty($success)): ?>
  <div class="admin-msg-wrapper">
    <div class="msg-success" id="admin-msg-success">
      <?php echo htmlspecialchars($success); ?>
    </div>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div class="admin-msg-wrapper">
    <div class="msg-error" id="admin-msg-error">
      <?php echo htmlspecialchars($error); ?>
    </div>
  </div>
<?php endif; ?>
<!-- MESSAGES END -->


<!-- USERS LIST TABLE SECTION -->
<section class="admin-section admin-section--pt" id="users-section">
  <div class="section-title">
    <h2>Daftar User (<?php echo $total_users; ?>)</h2>
  </div>

  <?php if (count($all_users) > 0): ?>
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Provinsi</th>
            <th>Terdaftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($all_users as $index => $user): ?>
            <tr class="user-row" data-user-id="<?php echo $user['id']; ?>">
              <td><?php echo $index + 1; ?></td>
              <td class="user-name"><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['no_hp'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($user['provinsi'] ?? '-'); ?></td>
              <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
              <td>
                <a href="users-detail.php?id=<?php echo $user['id']; ?>" class="btn-detail btn-detail--sm">Lihat Detail</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="admin-empty-state">
      <p>Tidak ada user terdaftar</p>
    </div>
  <?php endif; ?>

</section>
<!-- USERS LIST TABLE END -->


<!-- PENDING CAMPAIGNS SECTION -->
<section class="admin-section admin-section--pt" id="campaigns-section">
  <div class="section-title">
    <h2>Verifikasi Campaign (<?php echo $total_pending; ?> menunggu)</h2>
  </div>

  <?php if (count($pending_campaigns) > 0): ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Judul Campaign</th>
          <th>Pembuat</th>
          <th>Target Dana</th>
          <th>Lokasi</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pending_campaigns as $index => $campaign): ?>
          <tr>
            <td><?php echo $index + 1; ?></td>
            <td><?php echo htmlspecialchars($campaign['judul']); ?></td>
            <td><?php echo htmlspecialchars($campaign['nama_lengkap'] ?? 'Unknown'); ?></td>
            <td>Rp<?php echo number_format($campaign['target_dana'], 0, ',', '.'); ?></td>
            <td><?php echo htmlspecialchars($campaign['lokasi']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($campaign['created_at'])); ?></td>
            <td>
              <form method="POST" class="form-inline">
                <input type="hidden" name="campaign_id" value="<?php echo $campaign['id']; ?>">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn-approve">Setujui</button>
              </form>
              <form method="POST" class="form-inline">
                <input type="hidden" name="campaign_id" value="<?php echo $campaign['id']; ?>">
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn-reject">Tolak</button>
              </form>
              <form method="POST" class="form-inline">
                <input type="hidden" name="campaign_id" value="<?php echo $campaign['id']; ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus campaign ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="admin-empty-state">
      <p>Tidak ada campaign yang menunggu verifikasi</p>
    </div>
  <?php endif; ?>

</section>
<!-- PENDING CAMPAIGNS SECTION END -->


<!-- APPROVED CAMPAIGNS SECTION -->
<section class="admin-section admin-section--pt" id="approved-campaigns-section">
  <div class="section-title">
    <h2>Campaign Aktif (<?php echo $total_approved; ?>)</h2>
  </div>

  <?php if (count($approved_campaigns) > 0): ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Judul Campaign</th>
          <th>Pembuat</th>
          <th>Dana Terkumpul</th>
          <th>Target</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($approved_campaigns as $index => $campaign): ?>
          <tr>
            <td><?php echo $index + 1; ?></td>
            <td><?php echo htmlspecialchars($campaign['judul']); ?></td>
            <td><?php echo htmlspecialchars($campaign['nama_lengkap'] ?? 'Unknown'); ?></td>
            <td><strong>Rp<?php echo number_format($campaign['dana_terkumpul'] ?? 0, 0, ',', '.'); ?></strong></td>
            <td>Rp<?php echo number_format($campaign['target_dana'], 0, ',', '.'); ?></td>
            <td>
              <span class="status-badge status-active">Aktif</span>
            </td>
            <td>
              <form method="POST" class="form-inline">
                <input type="hidden" name="campaign_id" value="<?php echo $campaign['id']; ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus campaign ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="admin-empty-state">
      <p>Tidak ada campaign aktif</p>
    </div>
  <?php endif; ?>

</section>
<!-- APPROVED CAMPAIGNS SECTION END -->


<!-- JS -->
<script src="main.js"></script>

</body>
</html>