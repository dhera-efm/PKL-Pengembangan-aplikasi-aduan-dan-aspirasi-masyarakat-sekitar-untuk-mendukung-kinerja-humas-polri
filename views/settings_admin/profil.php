<?php
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak!</div>";
    exit;
}

$id_admin = $_SESSION['id_user'];
$admin = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT * FROM users WHERE id_user = $id_admin"
));
$detail = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM admin_detail WHERE id_admin = $id_admin"
));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    mysqli_query($conn, "
        UPDATE users 
        SET nama_lengkap='$nama', email='$email'
        WHERE id_user=$id_admin
    ");
    if (!empty($_FILES['foto']['name'])) {
        $fileName = time() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "assets/img/$fileName");

        mysqli_query($conn, "
            UPDATE users 
            SET foto_profil = '$fileName'
            WHERE id_user = $id_admin
        ");
    }
    $nip = $_POST['nip'] ?? '';
    $pangkat = $_POST['pangkat'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';

    mysqli_query($conn, "
        REPLACE INTO admin_detail(id_admin,nip,pangkat,jabatan)
        VALUES($id_admin,'$nip','$pangkat','$jabatan')
    ");

    echo "<div class='alert alert-success'>Profil berhasil diperbarui!</div>";
}
?>

<h4 class="mb-3">Profil Admin</h4>

<form method="post" enctype="multipart/form-data">

<div class="row">
    <div class="col-md-4 text-center">

        <?php if (!empty($admin['foto_profil'])): ?>
            <img src="assets/img/<?= $admin['foto_profil'] ?>" 
                 class="rounded-circle img-thumbnail mb-3" width="150">
        <?php else: ?>
            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center"
                 style="width:150px;height:150px;font-size:50px;">
                <?= strtoupper(substr($admin['nama_lengkap'],0,1)) ?>
            </div>
        <?php endif; ?>

        <input type="file" name="foto" class="form-control mt-2">

    </div>

    <div class="col-md-8">

        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" 
                   value="<?= $admin['nama_lengkap'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= $admin['email'] ?>" required>
        </div>

        <hr>

        <h6>Informasi Tambahan</h6>

        <div class="mb-3">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control"
                   value="<?= $detail['nip'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label>Pangkat</label>
            <input type="text" name="pangkat" class="form-control"
                   value="<?= $detail['pangkat'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label>Jabatan</label>
            <input type="text" name="jabatan" class="form-control"
                   value="<?= $detail['jabatan'] ?? '' ?>">
        </div>

        <button class="btn btn-primary mt-2">Simpan Perubahan</button>

    </div>
</div>

</form>