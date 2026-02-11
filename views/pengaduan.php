<h3 class="mb-4">Daftar Pengaduan</h3>

<?php
$where = "";
if (isset($_SESSION['role']) && $_SESSION['role'] == "user") {
  $id_user = intval($_SESSION['id_user']);
  $where = "WHERE p.id_user = $id_user";
}

$q = mysqli_query($conn, "
  SELECT p.*, k.nama_kategori, u.nama_lengkap 
  FROM pengaduan p
  JOIN kategori k ON p.id_kategori = k.id_kategori
  LEFT JOIN users u ON p.id_user = u.id_user
  $where
  ORDER BY p.created_at DESC
");
?>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Pelapor</th>
            <th>Kategori</th>
            <th>Judul</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Cetak</th>
            <th>Bukti</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          while ($row = mysqli_fetch_assoc($q)): 
            $status = strtolower($row['status']);
            $badgeClass = match ($status) {
              'menunggu' => 'bg-warning text-dark',
              'diproses' => 'bg-primary',
              'selesai'  => 'bg-success',
              'public'   => 'bg-secondary',
              default    => 'bg-info'
            };
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap'] ?? 'Public') ?></td>
            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($row['status']) ?></span></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>

            <!-- Kolom Cetak -->
            <td>
              <?php if ($row['status'] == 'Selesai'): ?>
                <?php 
                  $tanggalSelesai = strtotime($row['updated_at'] ?? $row['created_at']); 
                  $batasCetak = strtotime('+2 days', $tanggalSelesai);
                  $sekarang = time();
                  if ($sekarang <= $batasCetak): ?>
                    <button 
                      class="btn btn-sm btn-success btnCetak" 
                      data-id="<?= $row['id_pengaduan'] ?>">
                      <i class="bi bi-printer"></i> Cetak
                    </button>
                  <?php else: ?>
                    <span class="text-muted">Waktu cetak berakhir</span>
                  <?php endif; ?>
              <?php else: ?>
                <span class="text-muted">Belum bisa cetak</span>
              <?php endif; ?>
            </td>

            <!-- Kolom Bukti -->
            <td>
              <?php if (!empty($row['bukti_file'])): 
                $ext = strtolower(pathinfo($row['bukti_file'], PATHINFO_EXTENSION));
                $filePath = "uploads/" . htmlspecialchars($row['bukti_file']);
              ?>
                <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                  <img src="<?= $filePath ?>" alt="Bukti" class="img-fluid rounded" style="max-width: 120px;">
                <?php elseif (in_array($ext, ['mp4', 'mkv', 'webm'])): ?>
                  <video src="<?= $filePath ?>" class="rounded" style="max-width: 180px;" controls></video>
                <?php elseif (in_array($ext, ['mp3', 'wav'])): ?>
                  <audio src="<?= $filePath ?>" controls></audio>
                <?php else: ?>
                  <a href="<?= $filePath ?>" target="_blank">Lihat File</a>
                <?php endif; ?>
              <?php else: ?>
                <em>-</em>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ===================== MODAL CETAK ===================== -->
<div class="modal fade" id="modalCetak" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Pratinjau Laporan Pengaduan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="frameCetak" src="" width="100%" height="600" frameborder="0"></iframe>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button class="btn btn-success" id="btnPrint"><i class="bi bi-printer"></i> Cetak</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalCetak = new bootstrap.Modal(document.getElementById('modalCetak'));
  const frame = document.getElementById('frameCetak');
  const btnPrint = document.getElementById('btnPrint');

  document.querySelectorAll('.btnCetak').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      frame.src = `views/cetak_tanggapan.php?id=${id}`;
      modalCetak.show();
    });
  });

  btnPrint.addEventListener('click', () => {
    frame.contentWindow.focus();
    frame.contentWindow.print();
  });
});
</script>
