<?php
// pastikan hanya admin
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
  echo "<div class='alert alert-danger'>Akses ditolak!</div>"; exit;
}

// Tambah berita
if(isset($_POST['tambah'])){
  $judul = mysqli_real_escape_string($conn, $_POST['judul']);
  $isi   = mysqli_real_escape_string($conn, $_POST['isi']);
  $penulis = $_SESSION['id_user'];
  mysqli_query($conn,"INSERT INTO berita(judul,isi,penulis,tanggal) VALUES('$judul','$isi',$penulis,NOW())");
  echo "<div class='alert alert-success'>Berita berhasil ditambahkan.</div>";
}

// Hapus berita
if(isset($_GET['hapus'])){
  $id = intval($_GET['hapus']);
  mysqli_query($conn,"DELETE FROM berita WHERE id_berita=$id");
  echo "<div class='alert alert-warning'>Berita berhasil dihapus.</div>";
}

// Update berita
if(isset($_POST['update'])){
  $id    = intval($_POST['id_berita']);
  $judul = mysqli_real_escape_string($conn,$_POST['judul']);
  $isi   = mysqli_real_escape_string($conn,$_POST['isi']);
  mysqli_query($conn,"UPDATE berita SET judul='$judul', isi='$isi' WHERE id_berita=$id");
  echo "<div class='alert alert-info'>Berita berhasil diperbarui.</div>";
}

// Ambil data
$data = mysqli_query($conn,"SELECT b.*,u.nama_lengkap FROM berita b 
                            LEFT JOIN users u ON b.penulis=u.id_user ORDER BY tanggal DESC");
?>

<h3>Kelola Berita</h3>
<a href="admin" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

<!-- Tambah berita -->
<form method="post" class="mb-4">
  <div class="mb-2"><input type="text" name="judul" placeholder="Judul Berita" class="form-control" required></div>
  <div class="mb-2"><textarea name="isi" placeholder="Isi Berita" class="form-control" required></textarea></div>
  <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
</form>

<!-- List berita -->
<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>No</th><th>Judul</th><th>Isi</th><th>Penulis</th><th>Tanggal</th><th>Aksi</th>
    </tr>
  </thead>
  <tbody>
  <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($row['judul']) ?></td>
      <td><?= substr(strip_tags($row['isi']),0,50) ?>...</td>
      <td><?= $row['nama_lengkap'] ?? 'Admin' ?></td>
      <td><?= $row['tanggal'] ?></td>
      <td>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id_berita']?>">Edit</button>
        <a href="berita?hapus=<?= $row['id_berita']?>" onclick="return confirm('Yakin hapus berita?')" class="btn btn-sm btn-danger">Hapus</a>
      </td>
    </tr>

    <!-- Modal edit -->
    <div class="modal fade" id="edit<?= $row['id_berita']?>" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post">
            <div class="modal-header"><h5>Edit Berita</h5></div>
            <div class="modal-body">
              <input type="hidden" name="id_berita" value="<?= $row['id_berita']?>">
              <div class="mb-2"><input type="text" name="judul" value="<?= htmlspecialchars($row['judul'])?>" class="form-control"></div>
              <div class="mb-2"><textarea name="isi" class="form-control"><?= htmlspecialchars($row['isi'])?></textarea></div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="update" class="btn btn-primary">Simpan</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
  </tbody>
</table>
