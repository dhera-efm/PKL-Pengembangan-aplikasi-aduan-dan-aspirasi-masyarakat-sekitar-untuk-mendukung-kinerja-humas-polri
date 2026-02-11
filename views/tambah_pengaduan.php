<?php
// Pastikan user login
if (!isset($_SESSION['id_user'])) {
    $_SESSION['alert'] = "Silakan login terlebih dahulu untuk membuat pengaduan.";;
    header("Location: home");
    exit;
}

// Proses tambah pengaduan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul   = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi     = mysqli_real_escape_string($conn, $_POST['isi']);
    $lokasi  = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $kat     = (int)$_POST['kategori'];
    $id_user = $_SESSION['id_user'];
    $status  = 'Menunggu';
    $bukti_file = null;

    // Upload file jika ada
    if (isset($_FILES['bukti_file']) && $_FILES['bukti_file']['error'] === 0) {
        $targetDir = "uploads/";
        $fileName  = time() . "_" . basename($_FILES["bukti_file"]["name"]);
        $targetPath = $targetDir . $fileName;
        $fileType  = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $fileSize  = $_FILES["bukti_file"]["size"];

        // Validasi tipe & ukuran file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'wav', 'mkv'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "<div class='alert alert-warning'>Tipe file tidak diizinkan. (Hanya foto, video, atau audio)</div>";
        } elseif ($fileSize > 20 * 1024 * 1024) { // 20MB
            echo "<div class='alert alert-warning'>Ukuran file terlalu besar! Maksimum 20MB.</div>";
        } else {
            if (move_uploaded_file($_FILES["bukti_file"]["tmp_name"], $targetPath)) {
                $bukti_file = $fileName;
            } else {
                echo "<div class='alert alert-danger'>Gagal mengupload file.</div>";
            }
        }
    }

    // Simpan ke database
    $stmt = $conn->prepare("
        INSERT INTO pengaduan (id_user, id_kategori, judul, isi_aduan, lokasi, bukti_file, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisssss", $id_user, $kat, $judul, $isi, $lokasi, $bukti_file, $status);
    $stmt->execute();

    echo "<div class='alert alert-success'>Pengaduan berhasil dikirim!</div>";
}
?>

<div class="container mt-4 mb-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">Buat Pengaduan</h3>

      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Judul</label>
          <input type="text" name="judul" class="form-control" required placeholder="Masukkan judul pengaduan">
        </div>

        <div class="mb-3">
          <label class="form-label">Isi Aduan</label>
          <textarea name="isi" class="form-control" rows="5" required placeholder="Tuliskan isi aduan dengan jelas..."></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Lokasi Kejadian</label>
          <select name="lokasi" class="form-select" required>
            <option value="">-- Pilih Lokasi --</option>
            <?php
            $lokasiQ = mysqli_query($conn, "SELECT * FROM lokasi ORDER BY nama_lokasi ASC");
            while ($lok = mysqli_fetch_assoc($lokasiQ)) {
                echo "<option value='" . htmlspecialchars($lok['nama_lokasi']) . "'>" . htmlspecialchars($lok['nama_lokasi']) . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Kategori</label>
          <select name="kategori" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            <?php
            $katQ = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
            while ($r = mysqli_fetch_assoc($katQ)) {
                echo "<option value='" . $r['id_kategori'] . "'>" . htmlspecialchars($r['nama_kategori']) . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Upload Bukti (Foto / Video / Audio)</label>
          <input type="file" name="bukti_file" class="form-control" accept="image/*,video/*,audio/*">
          <small class="text-muted">
            Format yang diperbolehkan: JPG, PNG, MP4, MP3, WAV, MKV (maks. 20MB)
          </small>
        </div>

        <div class="d-flex justify-content-between">
          <a href="home" class="btn btn-secondary">Kembali</a>
          <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
        </div>
      </form>
    </div>
  </div>
</div>
