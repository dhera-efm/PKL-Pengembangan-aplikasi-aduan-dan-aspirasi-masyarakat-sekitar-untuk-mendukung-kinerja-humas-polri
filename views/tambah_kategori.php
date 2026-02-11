<?php
// pastikan hanya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  echo "<div class='alert alert-danger'>Akses ditolak!</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $nama = trim($_POST['nama_kategori']);
  if ($nama == '') {
    echo "<div class='alert alert-danger'>Nama kategori tidak boleh kosong.</div>";
  } else {
    $namaSafe = mysqli_real_escape_string($conn, $nama);
    $ok = mysqli_query($conn, "INSERT INTO kategori(nama_kategori) VALUES('$namaSafe')");
    if ($ok) {
      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori berhasil ditambahkan.'];
    } else {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menambahkan kategori.'];
    }
    header('Location: index.php?page=admin#kategori');
    exit;
  }
}
?>

<h3>Tambah Kategori</h3>
<form method="post">
  <div class="mb-3">
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-success">Simpan</button>
  <a href="index.php?page=admin#kategori" class="btn btn-secondary">Kembali</a>
</form>
