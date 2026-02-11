<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  echo "<div class='alert alert-danger'>Akses ditolak!</div>"; exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "<div class='alert alert-danger'>ID lokasi tidak valid.</div>"; exit;
}

$id = intval($_GET['id']);
$lok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lokasi WHERE id_lokasi=$id"));

if (!$lok) {
  echo "<div class='alert alert-danger'>Data lokasi tidak ditemukan.</div>"; exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_lokasi']);
  $ok = mysqli_query($conn, "UPDATE lokasi SET nama_lokasi='$nama' WHERE id_lokasi=$id");
  if ($ok) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Lokasi berhasil diperbarui.'];
  } else {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui lokasi.'];
  }
  header('Location: index.php?page=admin#lokasi');
  exit;
}
?>
<h3>Edit Lokasi</h3>
<form method="post">
  <div class="mb-3">
    <label>Nama Lokasi</label>
    <input type="text" name="nama_lokasi" value="<?= $lok['nama_lokasi']?>" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
  <a href="index.php?page=admin#lokasi" class="btn btn-secondary">Kembali</a>
</form>
