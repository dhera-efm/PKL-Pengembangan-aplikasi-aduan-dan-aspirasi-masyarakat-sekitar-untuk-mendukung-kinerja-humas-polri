<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    echo "<div class='alert alert-danger'>Akses ditolak! Halaman ini hanya untuk pengguna.</div>";
    exit;
}

$id_user = intval($_SESSION['id_user']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_foto_profil'])) {
    if (!isset($_FILES['foto_profil']) || $_FILES['foto_profil']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Upload foto gagal.'];
        header('Location: user');
        exit;
    }

    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    $originalName = $_FILES['foto_profil']['name'];
    $tmpName = $_FILES['foto_profil']['tmp_name'];
    $size = (int) $_FILES['foto_profil']['size'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt, true)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.'];
        header('Location: user');
        exit;
    }

    if ($size > $maxSize) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Ukuran foto maksimal 2 MB.'];
        header('Location: user');
        exit;
    }

    $checkImage = @getimagesize($tmpName);
    if ($checkImage === false) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'File yang diunggah bukan gambar valid.'];
        header('Location: user');
        exit;
    }

    $newFileName = 'pp_' . $id_user . '_' . time() . '.' . $ext;
    $targetPath = __DIR__ . '/../assets/img/' . $newFileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan foto profil.'];
        header('Location: user');
        exit;
    }

    $safeName = mysqli_real_escape_string($conn, $newFileName);
    $ok = mysqli_query($conn, "UPDATE users SET foto_profil='$safeName' WHERE id_user='$id_user'");

    if ($ok) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Foto profil berhasil diperbarui.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Foto tersimpan tetapi gagal update database.'];
    }

    header('Location: user');
    exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_user'"));


?>


<div class="container mt-4">

 
  <div class="card shadow-sm mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <?php if (!empty($user['foto_profil'])): ?>
          <img src="assets/img/<?= htmlspecialchars($user['foto_profil']) ?>" 
               class="rounded-circle me-3 shadow" width="60" height="60" style="object-fit: cover;">
        <?php else: ?>
          <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3 shadow"
               style="width:60px;height:60px;font-size:1.5rem;">
            <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
          </div>
        <?php endif; ?>
        <div>
          <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['nama_lengkap']) ?></h5>
          <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
        </div>
      </div>
      <form method="post" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
        <input type="file" name="foto_profil" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp" required>
        <button type="submit" name="update_foto_profil" class="btn btn-sm btn-primary">Ubah Foto Profile</button>
      </form>
    </div>
  </div>


 
  <ul class="nav nav-tabs mb-3" id="userTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="pengaduan-tab" data-bs-toggle="tab" data-bs-target="#pengaduan" type="button" role="tab">
        Pengaduan
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback" type="button" role="tab">
        Feedback Saya
      </button>
    </li>

  </ul>

 
  <div class="tab-content" id="userTabContent">


    <div class="tab-pane fade show active" id="pengaduan" role="tabpanel" aria-labelledby="pengaduan-tab">

      <?php
      $q = mysqli_query($conn, "
        SELECT p.*, k.nama_kategori
        FROM pengaduan p
        JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.id_user = $id_user
        ORDER BY p.created_at DESC
      ");
      ?>

      <h5 class="mb-3">Riwayat Pengaduan Saya</h5>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Kategori</th>
              <th>Judul</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($q)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><span class="badge bg-info"><?= htmlspecialchars($row['status']) ?></span></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                  <?php if ($row['status'] == 'Selesai'): ?>
                    <button class="btn btn-sm btn-warning btnFeedback" 
                      data-id="<?= $row['id_pengaduan'] ?>">
                      <i class="bi bi-star"></i> Feedback
                    </button>
                  <?php else: ?>
                    <span class="text-muted">Menunggu selesai</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    </div>

    <div class="tab-pane fade" id="feedback" role="tabpanel" aria-labelledby="feedback-tab">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="mb-3">Feedback Anda</h5>

          <?php
          $feedbacks = mysqli_query($conn, "
            SELECT f.*, p.judul 
            FROM feedback f
            JOIN pengaduan p ON f.id_pengaduan = p.id_pengaduan
            WHERE f.id_user = '$id_user'
            ORDER BY f.tanggal DESC
          ");

          if (mysqli_num_rows($feedbacks) > 0):
            while ($f = mysqli_fetch_assoc($feedbacks)): ?>
              <div class="border rounded p-3 mb-3 bg-light">
                <h6 class="fw-bold"><?= htmlspecialchars($f['judul']) ?></h6>
                <div class="text-warning mb-1"><?= str_repeat('⭐', $f['rating']) ?></div>
                <p class="text-muted fst-italic mb-0">"<?= htmlspecialchars($f['komentar']) ?>"</p>
                <small class="text-muted"><?= date('d M Y', strtotime($f['tanggal'])) ?></small>
              </div>
            <?php endwhile;
          else: ?>
            <p class="text-muted">Belum ada feedback yang Anda kirim.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

</div>


<div class="modal fade" id="modalFeedback" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formFeedback" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Beri Penilaian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_pengaduan" id="id_pengaduan">

        <div class="mb-3">
          <label>Rating</label>
          <select name="rating" class="form-select" required>
            <option value="">Pilih...</option>
            <option value="1">⭐ 1</option>
            <option value="2">⭐⭐ 2</option>
            <option value="3">⭐⭐⭐ 3</option>
            <option value="4">⭐⭐⭐⭐ 4</option>
            <option value="5">⭐⭐⭐⭐⭐ 5</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Komentar</label>
          <textarea name="komentar" class="form-control" rows="3" placeholder="Tuliskan kesan atau saran..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kirim</button>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.btnFeedback').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('id_pengaduan').value = btn.dataset.id;
    new bootstrap.Modal(document.getElementById('modalFeedback')).show();
  });
});

document.getElementById('formFeedback').addEventListener('submit', e => {
  e.preventDefault();
  fetch('views/simpan_feedback.php', {
    method: 'POST',
    body: new FormData(e.target)
  })
  .then(r => r.text())
  .then(msg => {
    alert(msg);
    bootstrap.Modal.getInstance(document.getElementById('modalFeedback')).hide();
  });
});
</script>
