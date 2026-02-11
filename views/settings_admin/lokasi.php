<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("<div class='alert alert-danger'>Akses ditolak!</div>");
}
if (isset($_POST['tambah_lokasi'])) {
    $nama = trim($_POST['nama_lokasi']);
    if ($nama == "") {
        $error = "Nama lokasi tidak boleh kosong!";
    } else {
        $namaSafe = mysqli_real_escape_string($conn, $nama);
        mysqli_query($conn, "INSERT INTO lokasi (nama_lokasi) VALUES ('$namaSafe')");
        $success = "Lokasi berhasil ditambahkan!";
    }
}
if (isset($_POST['edit_lokasi'])) {
    $id  = intval($_POST['id_lokasi']);
    $nama = trim($_POST['nama_lokasi']);

    if ($nama == "") {
        $error = "Nama lokasi tidak boleh kosong!";
    } else {
        $namaSafe = mysqli_real_escape_string($conn, $nama);
        mysqli_query($conn, "
            UPDATE lokasi 
            SET nama_lokasi='$namaSafe'
            WHERE id_lokasi=$id
        ");
        $success = "Lokasi berhasil diperbarui!";
    }
}
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM lokasi WHERE id_lokasi=$id");
    $success = "Lokasi berhasil dihapus!";
}
$lokasi = mysqli_query($conn, "SELECT * FROM lokasi ORDER BY id_lokasi DESC");
?>

<h4 class="mb-3">Kelola Lokasi</h4>

<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white">
        Tambah Lokasi Baru
    </div>
    <div class="card-body">

        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Lokasi</label>
                    <input type="text" class="form-control" name="nama_lokasi" required>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="tambah_lokasi" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        Daftar Lokasi
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0 text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Lokasi</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;
                    while ($l = mysqli_fetch_assoc($lokasi)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($l['nama_lokasi']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEdit<?= $l['id_lokasi'] ?>">
                                Edit
                            </button>
                            <a href="settings?menu=lokasi&hapus=<?= $l['id_lokasi'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Hapus lokasi ini?')">
                                Hapus
                            </a>

                        </td>
                    </tr>
                    <div class="modal fade" id="modalEdit<?= $l['id_lokasi'] ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">

                          <div class="modal-header bg-warning">
                            <h5 class="modal-title">Edit Lokasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>

                          <form method="POST">
                              <div class="modal-body">
                                  <input type="hidden" name="id_lokasi" value="<?= $l['id_lokasi'] ?>">

                                  <label class="form-label fw-bold">Nama Lokasi</label>
                                  <input type="text" name="nama_lokasi" 
                                         class="form-control" 
                                         value="<?= htmlspecialchars($l['nama_lokasi']) ?>" required>
                              </div>

                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                  <button type="submit" name="edit_lokasi" class="btn btn-warning">Simpan</button>
                              </div>
                          </form>

                        </div>
                      </div>
                    </div>

                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>