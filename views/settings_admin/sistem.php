<?php
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    die("<div class='alert alert-danger'>Akses ditolak!</div>");
}


if(isset($_POST['save_system'])){

    function saveSetting($key, $value){
        global $conn;
        $key   = mysqli_real_escape_string($conn, $key);
        $value = mysqli_real_escape_string($conn, $value);

        mysqli_query($conn, "
            REPLACE INTO system_settings (`key`, `value`)
            VALUES ('$key', '$value')
        ");
    }

    saveSetting('site_name',       $_POST['site_name']);
    saveSetting('email_service',   $_POST['email_service']);
    saveSetting('whatsapp',        $_POST['whatsapp']);
    saveSetting('address',         $_POST['address']);
    saveSetting('smtp_host',       $_POST['smtp_host']);
    saveSetting('smtp_user',       $_POST['smtp_user']);
    saveSetting('smtp_pass',       $_POST['smtp_pass']);
    saveSetting('theme',           $_POST['theme']);


    if (!empty($_FILES['site_logo']['name'])) {
        $ext = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
        $file = "logo_" . time() . "." . $ext;

        move_uploaded_file($_FILES['site_logo']['tmp_name'], "assets/img/$file");
        saveSetting('site_logo', $file);
    }

    saveSetting('updated_at', date("Y-m-d H:i:s"));

    echo "<script>
            alert('Pengaturan sistem berhasil disimpan!');
            window.location='settings?menu=sistem';
          </script>";
    exit;
}


$set = [];
$q = mysqli_query($conn, "SELECT * FROM system_settings");
while ($row = mysqli_fetch_assoc($q)) {
    $set[$row['key']] = $row['value'];
}


function getSet($key, $default='') {
    global $set;
    return $set[$key] ?? $default;
}

?>

<h4 class="mb-4">Pengaturan Sistem</h4>

<form method="POST" enctype="multipart/form-data">

<div class="row">

    <div class="col-md-6">
        <div class="mb-3">
            <label>Nama Sistem</label>
            <input type="text" name="site_name" class="form-control"
                   value="<?= getSet('site_name') ?>" required>
        </div>

        <div class="mb-3">
            <label>Email Layanan</label>
            <input type="email" name="email_service" class="form-control"
                   value="<?= getSet('email_service') ?>">
        </div>

        <div class="mb-3">
            <label>No WhatsApp Layanan</label>
            <input type="text" name="whatsapp" class="form-control"
                   value="<?= getSet('whatsapp') ?>">
        </div>

        <div class="mb-3">
            <label>Alamat Instansi</label>
            <textarea name="address" class="form-control" rows="2"><?= getSet('address') ?></textarea>
        </div>

        <div class="mb-3">
            <label>Tema Dashboard</label>
            <select name="theme" class="form-select">
                <option value="light" <?= getSet('theme')=="light"?"selected":"" ?>>Light</option>
                <option value="dark"  <?= getSet('theme')=="dark" ?"selected":"" ?>>Dark</option>
            </select>
        </div>
    </div>


    <div class="col-md-6">

        <div class="mb-3">
            <label>Host SMTP</label>
            <input type="text" name="smtp_host" class="form-control"
                   value="<?= getSet('smtp_host') ?>">
        </div>

        <div class="mb-3">
            <label>User SMTP</label>
            <input type="text" name="smtp_user" class="form-control"
                   value="<?= getSet('smtp_user') ?>">
        </div>

        <div class="mb-3">
            <label>Password SMTP</label>
            <input type="password" name="smtp_pass" class="form-control"
                   value="<?= getSet('smtp_pass') ?>">
        </div>

        <div class="mb-3">
            <label>Logo Sistem</label>
            <input type="file" name="site_logo" class="form-control">
        </div>

        <div class="mb-3">
            <label>Preview Logo</label><br>
            <?php if (getSet('site_logo')): ?>
                <img src="assets/img/<?= getSet('site_logo') ?>"
                     alt="logo" style="width:120px; border:1px solid #ccc; padding:5px;">
            <?php else: ?>
                <p class="text-muted small">Belum ada logo.</p>
            <?php endif; ?>
        </div>

    </div>

</div>

<button class="btn btn-primary mt-3" name="save_system">
    Simpan Pengaturan
</button>

</form>

    