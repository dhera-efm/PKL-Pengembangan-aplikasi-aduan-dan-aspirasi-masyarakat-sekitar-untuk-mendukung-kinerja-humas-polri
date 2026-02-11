<?php
$id_admin = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pass_lama = md5($_POST['password_lama']);
    $pass_baru = md5($_POST['password_baru']);

    $cek = mysqli_query($conn, "SELECT * FROM users 
                                WHERE id_user=$id_admin 
                                AND password='$pass_lama'");

    if (mysqli_num_rows($cek) == 0) {
        $error = "Password lama salah!";
    } else {
        mysqli_query($conn, "UPDATE users 
                             SET password='$pass_baru' 
                             WHERE id_user=$id_admin");
        mysqli_query($conn, 
          "INSERT INTO admin_log (id_admin, aktivitas, waktu)
           VALUES ($id_admin, 'Mengubah password admin', NOW())");

        $success = "Password berhasil diperbarui.";
    }
}
?>

<h4>Ganti Password</h4>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if(isset($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label>Password Lama</label>
        <input type="password" name="password_lama" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password Baru</label>
        <input type="password" name="password_baru" class="form-control" required>
    </div>

    <button class="btn btn-primary">Simpan</button>
</form>