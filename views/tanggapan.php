<?php
// Pastikan ada parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Pengaduan tidak ditemukan.</div>";
    exit;
}

$id = intval($_GET['id']);

// Validasi data pengaduan
$cek = mysqli_query($conn, "SELECT * FROM pengaduan WHERE id_pengaduan = $id");
if (mysqli_num_rows($cek) == 0) {
    echo "<div class='alert alert-danger'>Data aduan tidak ditemukan di database.</div>";
    exit;
}
$adu = mysqli_fetch_assoc($cek);

// Jika admin kirim tanggapan atau ubah status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Tambah tanggapan baru
    if (!empty($_POST['isi'])) {
        $isi = mysqli_real_escape_string($conn, $_POST['isi']);
        $id_admin = $_SESSION['id_user'];

        mysqli_query($conn, "
            INSERT INTO tanggapan (id_pengaduan, id_admin, isi_tanggapan)
            VALUES ($id, $id_admin, '$isi')
        ");

        // Ubah status otomatis jadi “Diproses”
        mysqli_query($conn, "
            UPDATE pengaduan SET status = 'Diproses'
            WHERE id_pengaduan = $id
        ");

        echo "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> Tanggapan berhasil ditambahkan dan status diubah ke <b>Diproses</b>.</div>";
    }

    // Ubah status aduan
    if (!empty($_POST['status'])) {
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Kalau status selesai → simpan waktu selesai
        if ($status == 'Selesai') {
            mysqli_query($conn, "
                UPDATE pengaduan SET status = 'Selesai', tanggal_selesai = NOW()
                WHERE id_pengaduan = $id
            ");
        } else {
            mysqli_query($conn, "
                UPDATE pengaduan SET status = '$status'
                WHERE id_pengaduan = $id
            ");
        }

        echo "<div class='alert alert-info'><i class='bi bi-info-circle-fill'></i> Status berhasil diubah menjadi <b>$status</b>.</div>";
        $adu['status'] = $status;
    }
}
?>

<div class="container mt-4 mb-5">
  <a href="admin" class="btn btn-secondary mb-3">&larr; Kembali ke Dashboard</a>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h3 class="card-title mb-3">Tanggapi Aduan</h3>

      <div class="mb-2"><strong>Judul:</strong> <?= htmlspecialchars($adu['judul']) ?></div>
      <div class="mb-2"><strong>Isi Aduan:</strong> <?= htmlspecialchars($adu['isi_aduan']) ?></div>
      <div class="mb-3">
        <strong>Status Saat Ini:</strong> 
        <span class="badge bg-info"><?= htmlspecialchars($adu['status']) ?></span>
      </div>

      <!-- Form Tambah Tanggapan -->
      <form method="post" class="mb-4">
        <div class="mb-3">
          <label class="form-label">Isi Tanggapan</label>
          <textarea name="isi" class="form-control" rows="3" placeholder="Ketik tanggapan di sini..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim Tanggapan</button>
      </form>

      <!-- Form Ubah Status -->
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Ubah Status Aduan</label>
          <select name="status" class="form-select" required>
            <option value="Menunggu" <?= ($adu['status'] == "Menunggu" ? "selected" : "") ?>>Menunggu</option>
            <option value="Diproses" <?= ($adu['status'] == "Diproses" ? "selected" : "") ?>>Diproses</option>
            <option value="Selesai" <?= ($adu['status'] == "Selesai" ? "selected" : "") ?>>Selesai</option>
            <option value="Public" <?= ($adu['status'] == "Public" ? "selected" : "") ?>>Public</option>
          </select>
        </div>
        <button type="submit" class="btn btn-warning text-white">Update Status</button>
      </form>
    </div>
  </div>

  <!-- Riwayat Tanggapan -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="card-title mb-3">Riwayat Tanggapan</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Isi Tanggapan</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $t = mysqli_query($conn, "
                SELECT * FROM tanggapan 
                WHERE id_pengaduan = $id 
                ORDER BY created_at DESC
            ");
            $no = 1;
            while ($row = mysqli_fetch_assoc($t)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td class="text-start"><?= nl2br(htmlspecialchars($row['isi_tanggapan'])) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                  <a href="index.php?page=edit_tanggapan&id=<?= $row['id_tanggapan'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="hapus_tanggapan.php?id=<?= $row['id_tanggapan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus tanggapan ini?')">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

