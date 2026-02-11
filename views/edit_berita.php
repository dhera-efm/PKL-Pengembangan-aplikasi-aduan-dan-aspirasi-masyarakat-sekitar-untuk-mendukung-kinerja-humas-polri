<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak! Hanya admin yang bisa masuk.</div>";
    exit;
}

// Validasi ID berita
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Berita tidak valid.</div>";
    exit;
}

$id = intval($_GET['id']);
$berita = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM berita WHERE id_berita = $id"));

if (!$berita) {
    echo "<div class='alert alert-danger'>Data berita tidak ditemukan.</div>";
    exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul   = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi     = mysqli_real_escape_string($conn, $_POST['isi']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $tanggal = date('Y-m-d');

    $gambar = $berita['gambar']; // default pakai gambar lama

    // Jika ada gambar baru diupload
    if (!empty($_FILES['gambar']['name'])) {
        $fileTmp  = $_FILES['gambar']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['gambar']['name']);
        $target   = "assets/img/" . $fileName;

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($fileTmp, $target)) {
            $gambar = $fileName;
            // Hapus gambar lama (jika ada)
            if (!empty($berita['gambar']) && file_exists("assets/img/" . $berita['gambar'])) {
                unlink("assets/img/" . $berita['gambar']);
            }
        }
    }

    // Update ke database
    $update = mysqli_query($conn, "
        UPDATE berita SET 
            judul = '$judul',
            isi = '$isi',
            penulis = '$penulis',
            tanggal = '$tanggal',
            gambar = '$gambar'
        WHERE id_berita = $id
    ");

    if ($update) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Berita berhasil diperbarui.'];
        header('Location: index.php?page=berita');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui berita.</div>";
    }
}
?>

<div class="container mt-4 mb-5">
  <a href="index.php?page=berita" class="btn btn-secondary mb-3">&larr; Kembali ke Daftar Berita</a>

  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">Edit Berita</h3>

      <form method="post" enctype="multipart/form-data">
        <!-- Judul -->
        <div class="mb-3">
          <label class="form-label">Judul Berita</label>
          <input type="text" name="judul" class="form-control" 
                 value="<?= htmlspecialchars($berita['judul']) ?>" required>
        </div>

        <!-- Penulis -->
        <div class="mb-3">
          <label class="form-label">Penulis</label>
          <input type="text" name="penulis" class="form-control"
                 value="<?= htmlspecialchars($berita['penulis']) ?>" required>
        </div>

        <!-- Isi Berita -->
        <div class="mb-3">
          <label class="form-label">Isi Berita</label>
          <textarea name="isi" class="form-control" rows="6" required><?= htmlspecialchars($berita['isi']) ?></textarea>
        </div>

        <!-- Gambar -->
        <div class="mb-3">
          <label class="form-label">Gambar Berita (opsional)</label>
          <input type="file" name="gambar" class="form-control">
          <?php if (!empty($berita['gambar'])): ?>
            <div class="mt-3">
              <p class="mb-1"><strong>Gambar Saat Ini:</strong></p>
              <img src="assets/img/<?= htmlspecialchars($berita['gambar']) ?>" 
                   alt="Gambar Berita" class="img-fluid rounded shadow-sm" style="max-width: 300px;">
            </div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</div>
