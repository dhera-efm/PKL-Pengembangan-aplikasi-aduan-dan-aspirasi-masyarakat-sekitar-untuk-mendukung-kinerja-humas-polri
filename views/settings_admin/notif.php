<?php
$settings = [];
$res = mysqli_query($conn, "SELECT * FROM system_settings");
while ($s = mysqli_fetch_assoc($res)) {
    $settings[$s['key']] = $s['value'];
}

$notif_realtime = $settings['notif_realtime'] ?? '1';
$notif_email = $settings['notif_email'] ?? '0';
$email_from = $settings['email_from'] ?? '';
$smtp_host = $settings['smtp_host'] ?? '';
$smtp_port = $settings['smtp_port'] ?? '';
$smtp_user = $settings['smtp_user'] ?? '';
$smtp_pass = $settings['smtp_pass'] ?? '';

if (isset($_POST['save_notif'])) {

    function save($key, $val, $conn) {
        $val = mysqli_real_escape_string($conn, $val);
        mysqli_query($conn, "REPLACE INTO system_settings (`key`,`value`) VALUES ('$key', '$val')");
    }

    save("notif_realtime", $_POST['notif_realtime'], $conn);
    save("notif_email", $_POST['notif_email'], $conn);
    save("email_from", $_POST['email_from'], $conn);
    save("smtp_host", $_POST['smtp_host'], $conn);
    save("smtp_port", $_POST['smtp_port'], $conn);
    save("smtp_user", $_POST['smtp_user'], $conn);
    save("smtp_pass", $_POST['smtp_pass'], $conn);
    save("updated_at", date("Y-m-d H:i:s"), $conn);

    echo "<div class='alert alert-success'>Pengaturan berhasil disimpan!</div>";

    $notif_realtime = $_POST['notif_realtime'];
    $notif_email = $_POST['notif_email'];
    $email_from = $_POST['email_from'];
    $smtp_host = $_POST['smtp_host'];
    $smtp_port = $_POST['smtp_port'];
    $smtp_user = $_POST['smtp_user'];
    $smtp_pass = $_POST['smtp_pass'];
}
?>

<h4 class="mb-3">Notifikasi Realtime & Email</h4>

<form method="POST">

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong>Notifikasi Realtime</strong>
        </div>
        <div class="card-body">
            <label class="form-label">Status Notifikasi Realtime</label>
            <select name="notif_realtime" class="form-select">
                <option value="1" <?= $notif_realtime == '1' ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= $notif_realtime == '0' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
            <small class="text-muted">
                Notifikasi realtime akan memberi peringatan ketika ada laporan masuk tanpa reload halaman.
            </small>
        </div>
    </div>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <strong>Notifikasi Email</strong>
        </div>
        <div class="card-body">

            <label class="form-label">Status Notifikasi Email</label>
            <select name="notif_email" class="form-select mb-3">
                <option value="1" <?= $notif_email == '1' ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= $notif_email == '0' ? 'selected' : '' ?>>Nonaktif</option>
            </select>

            <div class="mb-3">
                <label class="form-label">Email Pengirim</label>
                <input type="email" name="email_from" class="form-control"
                       value="<?= htmlspecialchars($email_from) ?>">
            </div>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">SMTP Host</label>
                    <input type="text" name="smtp_host" class="form-control"
                           value="<?= htmlspecialchars($smtp_host) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">SMTP Port</label>
                    <input type="text" name="smtp_port" class="form-control"
                           value="<?= htmlspecialchars($smtp_port) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">SMTP User</label>
                    <input type="text" name="smtp_user" class="form-control"
                           value="<?= htmlspecialchars($smtp_user) ?>">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">SMTP Password</label>
                <input type="password" name="smtp_pass" class="form-control"
                       value="<?= htmlspecialchars($smtp_pass) ?>">
            </div>

            <small class="text-muted d-block mt-2">
                Gunakan SMTP Gmail / Outlook / perusahaan untuk mengirim email otomatis.
            </small>

        </div>
    </div>

    <button class="btn btn-primary" name="save_notif">Simpan Pengaturan</button>

</form>

<div class="mt-3 small text-muted">
    Terakhir diperbarui: <?= $settings['updated_at'] ?? 'Belum pernah' ?>
</div>