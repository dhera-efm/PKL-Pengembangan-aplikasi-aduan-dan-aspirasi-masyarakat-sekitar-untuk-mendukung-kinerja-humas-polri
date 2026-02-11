<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='alert alert-danger'>Akses ditolak!</div>");
}

if (isset($_POST['clear_log'])) {
    $ok = mysqli_query($conn, "TRUNCATE admin_log");
    if ($ok) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Semua log aktivitas berhasil dihapus.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus log aktivitas.'];
    }
    header('Location: index.php?page=settings&menu=log');
    exit;
}

$filter_admin = $_GET['admin'] ?? '';
$filter_tgl    = $_GET['tanggal'] ?? '';

$where = "WHERE 1=1";

if ($filter_admin !== '') {
    $where .= " AND l.id_admin = " . intval($filter_admin);
}

if ($filter_tgl !== '') {
    $tgl = mysqli_real_escape_string($conn, $filter_tgl);
    $where .= " AND DATE(l.waktu) = '$tgl'";
}

$logQuery = mysqli_query($conn, "
    SELECT l.*, u.nama_lengkap 
    FROM admin_log l
    LEFT JOIN users u ON l.id_admin = u.id_user
    $where
    ORDER BY l.id DESC
");
?>

<h4 class="mb-3">Log Aktivitas Admin</h4>
<form class="row g-2 mb-3" method="GET">
    <input type="hidden" name="menu" value="log">

    <div class="col-md-4">
        <select name="admin" class="form-select">
            <option value="">Semua Admin</option>
            <?php
            $adminList = mysqli_query($conn, "SELECT id_user, nama_lengkap FROM users WHERE role='admin'");
            while ($a = mysqli_fetch_assoc($adminList)):
            ?>
                <option value="<?= $a['id_user'] ?>" <?= $filter_admin==$a['id_user']?'selected':'' ?>>
                    <?= htmlspecialchars($a['nama_lengkap']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <input type="date" name="tanggal" value="<?= $filter_tgl ?>" class="form-control">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">Filter</button>
    </div>
</form>
<form method="post" onsubmit="return confirm('Hapus semua log? Tindakan ini tidak bisa dibatalkan!')">
    <button name="clear_log" class="btn btn-danger mb-3">
        <i></i> Hapus Semua Log
    </button>
</form>
<div class="table-responsive">
<table class="table table-striped table-bordered align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Admin</th>
            <th>Aktivitas</th>
            <th>IP Address</th>
            <th>Device (User Agent)</th>
            <th>Waktu</th>
        </tr>
    </thead>

    <tbody>
        <?php 
        $no = 1;
        if (mysqli_num_rows($logQuery) == 0): ?>
            <tr>
                <td colspan="6" class="text-muted">Belum ada aktivitas tercatat.</td>
            </tr>

        <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($logQuery)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <?= $row['nama_lengkap'] ? htmlspecialchars($row['nama_lengkap']) : '<i>Admin Dihapus</i>' ?>
                    </td>
                    <td><?= htmlspecialchars($row['aktivitas']) ?></td>
                    <td><?= $row['ip_address'] ?: '-' ?></td>
                    <td class="text-start small" style="max-width:250px; word-wrap: break-word;">
                        <?= $row['user_agent'] ?: '-' ?>
                    </td>
                    <td><?= $row['waktu'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>
</div>
