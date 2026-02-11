<?php
include '../config/database.php';
session_start();

$feedbacks = mysqli_query($conn, "
  SELECT f.*, u.nama_lengkap, p.judul 
  FROM feedback f
  JOIN users u ON f.id_user = u.id_user
  JOIN pengaduan p ON f.id_pengaduan = p.id_pengaduan
  ORDER BY f.tanggal DESC
");
?>

<h3 class="mb-4">Daftar Feedback Pengguna</h3>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>User</th>
        <th>Judul Pengaduan</th>
        <th>Rating</th>
        <th>Komentar</th>
        <th>Status</th>
        <th>Tampilkan di Home</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($f=mysqli_fetch_assoc($feedbacks)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($f['nama_lengkap']) ?></td>
        <td><?= htmlspecialchars($f['judul']) ?></td>
        <td><?= str_repeat('⭐', $f['rating']) ?></td>
        <td><?= htmlspecialchars($f['komentar']) ?></td>
        <td>
          <span class="badge 
            <?= $f['status']=='pending'?'bg-secondary':
                ($f['status']=='diterima'?'bg-success':'bg-danger') ?>">
            <?= ucfirst($f['status']) ?>
          </span>
        </td>
        <td>
          <?php if ($f['tampil']): ?>
            <span class="badge bg-info">Ya</span>
          <?php else: ?>
            <span class="badge bg-secondary">Tidak</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="ubah_feedback.php?id=<?= $f['id_feedback'] ?>&aksi=terima" class="btn btn-sm btn-success">Terima</a>
          <a href="ubah_feedback.php?id=<?= $f['id_feedback'] ?>&aksi=tolak" class="btn btn-sm btn-danger">Tolak</a>
          <a href="ubah_feedback.php?id=<?= $f['id_feedback'] ?>&aksi=tampil" class="btn btn-sm btn-info">Tampilkan</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
