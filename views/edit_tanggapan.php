<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "<div class='alert alert-danger'>ID tanggapan tidak valid.</div>"; exit;
}
$id = intval($_GET['id']);
$t = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tanggapan WHERE id_tanggapan=$id"));
if (!$t) {
  echo "<div class='alert alert-danger'>Data tanggapan tidak ditemukan.</div>"; exit;
}
?>
<h3>Edit Tanggapan</h3>
<form method="post">
  <div class="mb-3">
    <label>Isi Tanggapan</label>
    <textarea name="isi" class="form-control" required><?= htmlspecialchars($t['isi_tanggapan']) ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $isi = mysqli_real_escape_string($conn, $_POST['isi']);
  $ok = mysqli_query($conn, "UPDATE tanggapan SET isi_tanggapan='$isi' WHERE id_tanggapan=$id");
  if ($ok) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tanggapan berhasil diupdate.'];
  } else {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui tanggapan.'];
  }
  header('Location: index.php?page=admin');
  exit;
}
?>
