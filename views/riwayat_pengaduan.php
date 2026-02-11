<?php
if (!isset($_SESSION['id_user'])) {
  echo "<div class='alert alert-danger'>Silakan login terlebih dahulu!</div>";
  exit;
}

$id_user = intval($_SESSION['id_user']);
$q = mysqli_query($conn, "
  SELECT p.*, k.nama_kategori
  FROM pengaduan p
  JOIN kategori k ON p.id_kategori = k.id_kategori
  WHERE p.id_user = $id_user
  ORDER BY p.created_at DESC
");
?>

<h3 class="mb-4">Riwayat Pengaduan Saya</h3>

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
            <div class="d-flex flex-column gap-1">
              <button 
                class="btn btn-sm btn-success btnCetakRiwayat" 
                data-id="<?= $row['id_pengaduan'] ?>">
                <i class="bi bi-printer"></i> Cetak
              </button>
              <button 
                class="btn btn-sm btn-warning btnFeedback" 
                data-id="<?= $row['id_pengaduan'] ?>">
                <i class="bi bi-star"></i> Feedback
              </button>
            </div>
          <?php else: ?>
            <span class="text-muted">Belum bisa cetak</span>
          <?php endif; ?>

          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

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
</div>

<!-- Script Cetak -->
<script>
document.querySelectorAll('.btnCetakRiwayat').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-id');

    fetch(`views/cetak_riwayat.php?id=${id}`)
      .then(res => res.text())
      .then(html => {
        const printFrame = document.createElement('iframe');
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        document.body.appendChild(printFrame);

        printFrame.contentDocument.open();
        printFrame.contentDocument.write(html);
        printFrame.contentDocument.close();

        printFrame.contentWindow.focus();
        printFrame.contentWindow.print();

        setTimeout(() => printFrame.remove(), 2000);
      })
      .catch(err => alert('Gagal mencetak riwayat: ' + err));
  });
});
</script>

<script>
document.querySelectorAll('.btnFeedback').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-id');
    document.getElementById('id_pengaduan').value = id;
    const modal = new bootstrap.Modal(document.getElementById('modalFeedback'));
    modal.show();
  });
});

document.getElementById('formFeedback').addEventListener('submit', e => {
  e.preventDefault();
  const formData = new FormData(e.target);

  fetch('views/simpan_feedback.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(msg => {
    alert(msg);
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalFeedback'));
    modal.hide();
  })
  .catch(err => alert('Gagal mengirim feedback: ' + err));
});
</script>

