<?php
// pastikan hanya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  echo "<div class='alert alert-danger'>Akses ditolak!</div>"; exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_lokasi']);
  $ok = mysqli_query($conn, "INSERT INTO lokasi(nama_lokasi) VALUES('$nama')");
  if ($ok) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Lokasi berhasil ditambahkan.'];
  } else {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menambahkan lokasi.'];
  }
  header('Location: index.php?page=admin#lokasi');
  exit;
}
?>

<h3>Tambah Lokasi</h3>
<form method="post">
  <div class="mb-3">
    <label>Nama Lokasi</label>
    <input type="text" name="nama_lokasi" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-success">Simpan</button>
  <a href="index.php?page=admin#lokasi" class="btn btn-secondary">Kembali</a>
</form>
