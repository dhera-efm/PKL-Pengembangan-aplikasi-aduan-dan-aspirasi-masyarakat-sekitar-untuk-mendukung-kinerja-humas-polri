<?php
// pastikan hanya admin
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
  echo "<div class='alert alert-danger'>Akses ditolak!</div>"; exit;
}

// Tambah lokasi
if(isset($_POST['tambah'])){
  $nama = mysqli_real_escape_string($conn, $_POST['nama_lokasi']);
  mysqli_query($conn,"INSERT INTO lokasi(nama_lokasi) VALUES('$nama')");
  echo "<div class='alert alert-success'>Lokasi berhasil ditambahkan.</div>";
}

// Hapus lokasi
if(isset($_GET['hapus'])){
  $id = intval($_GET['hapus']);
  mysqli_query($conn,"DELETE FROM lokasi WHERE id_lokasi=$id");
  echo "<div class='alert alert-warning'>Lokasi berhasil dihapus.</div>";
}

// Update lokasi
if(isset($_POST['update'])){
  $id   = intval($_POST['id_lokasi']);
  $nama = mysqli_real_escape_string($conn, $_POST['nama_lokasi']);
  mysqli_query($conn,"UPDATE lokasi SET nama_lokasi='$nama' WHERE id_lokasi=$id");
  echo "<div class='alert alert-info'>Lokasi berhasil diperbarui.</div>";
}

// Ambil data lokasi
$loks = mysqli_query($conn,"SELECT * FROM lokasi ORDER BY id_lokasi ASC");
?>

<h3>Kelola Lokasi</h3>
<a href="admin" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

<!-- Form tambah lokasi -->
<form method="post" class="mb-4">
  <div class="input-group">
    <input type="text" name="nama_lokasi" class="form-control" placeholder="Nama lokasi baru" required>
    <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
  </div>
</form>

<!-- Tabel lokasi -->
<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th width="50">No</th>
      <th>Nama Lokasi</th>
      <th width="150">Aksi</th>
    </tr>
  </thead>
  <tbody>
  <?php $no=1; while($row=mysqli_fetch_assoc($loks)): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($row['nama_lokasi']) ?></td>
      <td>
        <!-- Tombol Edit (modal trigger) -->
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id_lokasi']?>">Edit</button>
        <a href="lokasi?hapus=<?= $row['id_lokasi']?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus lokasi ini?')">Hapus</a>
      </td>
    </tr>

    <!-- Modal Edit -->
    <div class="modal fade" id="edit<?= $row['id_lokasi']?>" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post">
            <div class="modal-header">
              <h5 class="modal-title">Edit Lokasi</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id_lokasi" value="<?= $row['id_lokasi']?>">
              <input type="text" name="nama_lokasi" class="form-control" value="<?= htmlspecialchars($row['nama_lokasi'])?>" required>
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
