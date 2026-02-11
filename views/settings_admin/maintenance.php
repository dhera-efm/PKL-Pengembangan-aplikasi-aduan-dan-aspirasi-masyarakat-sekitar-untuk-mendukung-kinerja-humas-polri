<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='alert alert-danger'>Akses ditolak!</div>");
}

$q = mysqli_query($conn, "SELECT * FROM system_settings");
$settings = [];
while ($s = mysqli_fetch_assoc($q)) {
    $settings[$s['key']] = $s['value'];
}

$maintenance = $settings['maintenance_mode'] ?? "0";
$title = $settings['maintenance_title'] ?? "Website Dalam Perawatan";
$msg   = $settings['maintenance_msg'] ?? "Saat ini website sedang dalam pemeliharaan.";

if (isset($_POST['save'])) {

    $newMode  = isset($_POST['maintenance_mode']) ? "1" : "0";
    $newTitle = mysqli_real_escape_string($conn, $_POST['title']);
    $newMsg   = mysqli_real_escape_string($conn, $_POST['message']);
    $ok1 = mysqli_query($conn, "UPDATE system_settings SET value='$newMode' WHERE `key`='maintenance_mode'");
    $ok2 = mysqli_query($conn, "UPDATE system_settings SET value='$newTitle' WHERE `key`='maintenance_title'");
    $ok3 = mysqli_query($conn, "UPDATE system_settings SET value='$newMsg' WHERE `key`='maintenance_msg'");

    if ($ok1 && $ok2 && $ok3) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Pengaturan Maintenance berhasil disimpan.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan pengaturan Maintenance.'];
    }
    header('Location: index.php?page=settings&menu=maintenance');
    exit;
}
?>

<h4 class="mb-3">Mode Maintenance</h4>

<form method="post">

    <div class="form-check form-switch mb-3">
        <input type="checkbox" class="form-check-input" id="mode" name="maintenance_mode"
               <?= $maintenance == "1" ? "checked" : "" ?>>
        <label class="form-check-label" for="mode">
            <strong>Aktifkan Mode Maintenance</strong>
        </label>
    </div>

    <div class="mb-3">
        <label>Judul Halaman Maintenance</label>
        <input type="text" name="title" class="form-control"
               value="<?= htmlspecialchars($title) ?>" required>
    </div>

    <div class="mb-3">
        <label>Pesan Maintenance</label>
        <textarea name="message" class="form-control" rows="4" required><?= htmlspecialchars($msg) ?></textarea>
    </div>

    <button name="save" class="btn btn-primary">Simpan Pengaturan</button>

</form>
