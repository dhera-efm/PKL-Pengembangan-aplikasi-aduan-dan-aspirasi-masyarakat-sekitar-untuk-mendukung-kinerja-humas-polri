<?php
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
  echo "<div class='alert alert-danger'>Akses ditolak!</div>"; exit;
}

if($_SERVER['REQUEST_METHOD']=="POST"){
  $judul = mysqli_real_escape_string($conn,$_POST['judul']);
  $isi = mysqli_real_escape_string($conn,$_POST['isi']);
  $penulis = $_SESSION['nama_lengkap'] ?? 'Admin';
  $tanggal = date('Y-m-d');
  
  // Upload gambar
  $gambar = null;
  if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0){
      $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
      $namaFile = time() . "_berita." . $ext;
      $uploadPath = "assets/img/" . $namaFile;
      move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath);
      $gambar = $namaFile;
  }

  $ok = mysqli_query($conn, "INSERT INTO berita(judul, isi, gambar, tanggal, penulis) 
                       VALUES('$judul','$isi','$gambar','$tanggal','$penulis')");
  if ($ok) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Berita berhasil ditambahkan.'];
  } else {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menambahkan berita.'];
  }
  header('Location: index.php?page=admin#berita');
  exit;
}
?>

<h3>Tambah Berita</h3>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label>Judul</label>
    <input type="text" name="judul" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Isi Berita</label>
    <textarea name="isi" class="form-control" rows="5" required></textarea>
  </div>
  <div class="mb-3">
    <label>Gambar (opsional)</label>
    <input type="file" name="gambar" class="form-control" accept="image/*">
  </div>
  <button type="submit" class="btn btn-success">Simpan</button>
  <a href="index.php?page=admin#berita" class="btn btn-secondary">Kembali</a>
</form>
