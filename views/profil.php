<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak!</div>";
    exit;
}

$id_admin = $_SESSION['id_user'];
$admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id_admin"));
?>
<a href="admin" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

<h3 class="mb-4">Profil Admin</h3>

<form method="post" class="card shadow-sm p-4">
  <div class="mb-3">
    <label class="form-label">Nama Lengkap</label>
    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($admin['nama_lengkap']) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Password Baru (kosongkan jika tidak diganti)</label>
    <input type="password" name="password" class="form-control" placeholder="••••••••">
  </div>

  <button type="submit" class="btn btn-primary">Update Profil</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (!empty($password)) {
        // Gunakan password_hash agar lebih aman
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $ok = mysqli_query($conn, "
            UPDATE users 
            SET nama_lengkap = '$nama', email = '$email', password = '$hashed' 
            WHERE id_user = $id_admin
        ");
    } else {
        $ok = mysqli_query($conn, "
            UPDATE users 
            SET nama_lengkap = '$nama', email = '$email' 
            WHERE id_user = $id_admin
        ");
    }

    if ($ok) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil berhasil diperbarui.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui profil.'];
    }
    header('Location: index.php?page=profil');
    exit;
}
?>
