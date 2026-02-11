<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  echo "<div class='alert alert-danger'>Akses ditolak!</div>";
  exit;
}

$id = intval($_GET['id'] ?? 0);
$kat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori=$id"));

if (!$kat) {
  echo "<div class='alert alert-danger'>Kategori tidak ditemukan.</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $nama = trim($_POST['nama_kategori']);
  if ($nama == '') {
    echo "<div class='alert alert-danger'>Nama kategori tidak boleh kosong.</div>";
  } else {
    $namaSafe = mysqli_real_escape_string($conn, $nama);
    $ok = mysqli_query($conn, "UPDATE kategori SET nama_kategori='$namaSafe' WHERE id_kategori=$id");
    if ($ok) {
      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori berhasil diperbarui.'];
    } else {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui kategori.'];
    }
    header('Location: index.php?page=admin#kategori');
    exit;
  }
}
?>

<h3>Edit Kategori</h3>
<form method="post">
  <div class="mb-3">
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" value="<?= htmlspecialchars($kat['nama_kategori']) ?>" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
  <a href="index.php?page=admin#kategori" class="btn btn-secondary">Kembali</a>
</form>
