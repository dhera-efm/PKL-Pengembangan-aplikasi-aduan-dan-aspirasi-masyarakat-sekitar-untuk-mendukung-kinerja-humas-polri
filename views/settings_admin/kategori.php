<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("<div class='alert alert-danger'>Akses ditolak!</div>");
}
if (isset($_POST['tambah_kategori'])) {
    $nama = trim($_POST['nama_kategori']);
    if ($nama == "") {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        $namaSafe = mysqli_real_escape_string($conn, $nama);
        mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$namaSafe')");
        $success = "Kategori berhasil ditambahkan!";
    }
}
if (isset($_POST['edit_kategori'])) {
    $id  = intval($_POST['id_kategori']);
    $nama = trim($_POST['nama_kategori']);

    if ($nama == "") {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        $namaSafe = mysqli_real_escape_string($conn, $nama);
        mysqli_query($conn, "
            UPDATE kategori 
            SET nama_kategori='$namaSafe'
            WHERE id_kategori=$id
        ");
        $success = "Kategori berhasil diperbarui!";
    }
}
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $cek = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pengaduan WHERE id_kategori=$id"));
    if ($cek > 0) {
        $error = "Kategori tidak bisa dihapus karena sedang digunakan!";
    } else {
        mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori=$id");
        $success = "Kategori berhasil dihapus!";
    }
}
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
?>

<h4 class="mb-3">Kelola Kategori</h4>

<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white">
        Tambah Kategori Baru
    </div>
    <div class="card-body">

        <form method="POST">
            <div class="row">

                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Kategori</label>
                    <input type="text" class="form-control" name="nama_kategori" required>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="tambah_kategori" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        Daftar Kategori
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-bordered table-striped mb-0 text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Kategori</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;
                    while ($k = mysqli_fetch_assoc($kategori)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($k['nama_kategori']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditKat<?= $k['id_kategori'] ?>">
                                Edit
                            </button>
                            <a href="settings?menu=kategori&hapus=<?= $k['id_kategori'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Hapus kategori ini?')">
                               Hapus
                            </a>

                        </td>
                    </tr>
                    <div class="modal fade" id="modalEditKat<?= $k['id_kategori'] ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">

                          <div class="modal-header bg-warning">
                            <h5 class="modal-title">Edit Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>

                          <form method="POST">

                              <div class="modal-body">
                                  <input type="hidden" name="id_kategori" value="<?= $k['id_kategori'] ?>">

                                  <label class="form-label fw-bold">Nama Kategori</label>
                                  <input type="text" class="form-control" name="nama_kategori"
                                         value="<?= htmlspecialchars($k['nama_kategori']) ?>" required>
                              </div>

                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                  <button type="submit" name="edit_kategori" class="btn btn-warning">Simpan</button>
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
                    </div>