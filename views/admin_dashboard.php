<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak! Hanya admin yang bisa masuk.</div>";
    exit;
}

$activeTab = $_SESSION['active_tab'] ?? 'pengaduan';
unset($_SESSION['active_tab']);


$q = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap, k.nama_kategori 
    FROM pengaduan p
    LEFT JOIN users u ON p.id_user = u.id_user
    JOIN kategori k ON p.id_kategori = k.id_kategori
    WHERE p.status != 'Public'
    ORDER BY p.created_at DESC
");
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
<style>
  .pengaduan-table .col-aksi {
    width: 130px;
    min-width: 130px;
  }

  .pengaduan-actions {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    align-items: stretch;
  }

  .pengaduan-actions .btn {
    width: 100%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    white-space: nowrap;
  }

  .feedback-col-aksi {
    width: 170px;
    min-width: 170px;
  }

  .feedback-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.45rem;
  }

  .feedback-actions .btn {
    width: 100%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    white-space: nowrap;
  }

  .feedback-actions .btn-show {
    grid-column: 1 / -1;
  }
  .feedback-actions .btn-print {
    grid-column: 1 / -1;
  }

  .chart-card-body {
    height: 320px;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .chart-title {
    font-size: 0.95rem;
    font-weight: 600;
    text-align: center;
    margin: 0;
  }
  .chart-card-body canvas {
    width: 100% !important;
    height: 100% !important;
    flex: 1 1 auto;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Dashboard Admin</h2>
    <div class="dropdown">
        <button class="btn position-relative" id="notifToggle" data-bs-toggle="dropdown">
            <i class="bi bi-bell-fill text-warning" style="font-size: 1.6rem;"></i>
            <span id="notifBadge" 
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                  style="font-size: 0.65rem;">
            </span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow p-2" 
            style="width: 330px; max-height: 350px; overflow-y: auto;" 
            id="notifDropdownBox">
            <li class="text-center text-muted small py-2">Memuat...</li>
        </ul>
    </div>
</div>


<!-- Tab Navigasi -->
<ul class="nav nav-tabs" id="adminTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link <?= $activeTab == 'pengaduan' ? 'active' : '' ?>" id="pengaduan-tab" data-bs-toggle="tab" data-bs-target="#pengaduan" type="button" role="tab">Kelola Pengaduan</button>
  </li>
  <li class="nav-item" role="presentation">

<button class="nav-link <?= $activeTab == 'laporan' ? 'active' : '' ?>" id="laporan-tab" data-bs-toggle="tab" data-bs-target="#laporan" type="button" role="tab">Laporan</button>
  </li>
  <li class="nav-item" role="presentation">
<button class="nav-link <?= $activeTab == 'users' ? 'active' : '' ?>" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Kelola User</button>
  </li>
  <li class="nav-item" role="presentation">
<button class="nav-link <?= $activeTab == 'berita' ? 'active' : '' ?>" id="berita-tab" data-bs-toggle="tab" data-bs-target="#berita" type="button" role="tab">Kelola Berita</button>
  </li>
    <li class="nav-item" role="presentation">
    <button class="nav-link <?= $activeTab == 'feedback' ? 'active' : '' ?>" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback" type="button" role="tab">Kelola Feedback</button>
  </li>
  <li class="nav-item" role="presentation">
<button class="nav-link <?= $activeTab == 'lokasi' ? 'active' : '' ?>" id="lokasi-tab" data-bs-toggle="tab" data-bs-target="#lokasi" type="button" role="tab">Kelola Lokasi</button>
  </li>
  <li class="nav-item" role="presentation">
<button class="nav-link <?= $activeTab == 'kategori' ? 'active' : '' ?>" id="kategori-tab" data-bs-toggle="tab" data-bs-target="#kategori" type="button" role="tab">Kelola Kategori</button>
  </li>
</ul>

<div class="tab-content mt-3" id="adminTabContent">

  <!-- ===================== TAB PENGADUAN ===================== -->
<div class="tab-pane fade <?= $activeTab == 'pengaduan' ? 'show active' : '' ?>" id="pengaduan" role="tabpanel">

  <h4 class="mb-3">Kelola Pengaduan</h4>

  <?php
  $search = $_GET['search'] ?? '';
  $status = $_GET['status'] ?? '';
  $kategori = $_GET['kategori'] ?? '';
  $perPage = 10;
  $page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
  $offset = ($page - 1) * $perPage;
  $where = " WHERE p.status!='Public' ";
  if ($search !== '') {
      $s = mysqli_real_escape_string($conn, $search);
      $where .= " AND (p.judul LIKE '%$s%' OR p.isi_aduan LIKE '%$s%' OR u.nama_lengkap LIKE '%$s%') ";
  }
  if ($status !== '') {
      $st = mysqli_real_escape_string($conn, $status);
      $where .= " AND p.status = '$st' ";
  }
  if ($kategori !== '') {
      $where .= " AND p.id_kategori=".intval($kategori);
  }
  $total = mysqli_fetch_assoc(mysqli_query($conn, "
      SELECT COUNT(*) AS jml FROM pengaduan p
      LEFT JOIN users u ON p.id_user=u.id_user
      $where
  "))['jml'];
  $query = mysqli_query($conn, "
      SELECT p.*, u.nama_lengkap, k.nama_kategori, t.tanggal AS tanggal_respon, t.isi_tanggapan
      FROM pengaduan p
      LEFT JOIN users u ON p.id_user=u.id_user
      LEFT JOIN kategori k ON p.id_kategori=k.id_kategori
      LEFT JOIN tanggapan t ON p.id_pengaduan=t.id_pengaduan
      $where
      ORDER BY p.created_at DESC
      LIMIT $offset,$perPage
  ");
  $katOpt = mysqli_query($conn,"SELECT * FROM kategori ORDER BY nama_kategori");
  ?>
  <form method="get" action="index.php" class="row g-2 mb-3">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="active_tab" value="pengaduan">

    <div class="col-md-4">
      <input type="text" name="search" placeholder="Cari judul / pelapor" class="form-control" value="<?= htmlspecialchars($search) ?>">
    </div>

    <div class="col-md-2">
      <select name="status" class="form-select">
        <option value="">Semua Status</option>
        <option value="Menunggu" <?= $status=='Menunggu'?'selected':'' ?>>Menunggu</option>
        <option value="Diproses" <?= $status=='Diproses'?'selected':'' ?>>Diproses</option>
        <option value="Selesai" <?= $status=='Selesai'?'selected':'' ?>>Selesai</option>
      </select>
    </div>

    <div class="col-md-3">
      <select name="kategori" class="form-select">
        <option value="">Semua Kategori</option>
        <?php while($k = mysqli_fetch_assoc($katOpt)): ?>
          <option value="<?= $k['id_kategori'] ?>" <?= $kategori == $k['id_kategori'] ? 'selected':'' ?>>
            <?= $k['nama_kategori'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-md-2">
      <button class="btn btn-primary w-100">Filter</button>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle pengaduan-table">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Pelapor</th>
          <th>Kategori</th>
          <th>Judul</th>
          <th>Status</th>
          <th>Tgl Lapor</th>
          <th>Tgl Direspon</th>
          <th>Isi Respon</th>
          <th class="col-aksi">Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php 
        $no = $offset + 1;
        if (mysqli_num_rows($query) == 0): ?>
          <tr><td colspan="9" class="text-muted">Tidak ada data.</td></tr>

        <?php else: ?>
          <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $row['nama_lengkap'] ?: 'Public' ?></td>
              <td><?= $row['nama_kategori'] ?></td>
              <td><?= $row['judul'] ?></td>

              <td>
                <?php 
                  $badge = [
                    'Menunggu'=>'secondary',
                    'Diproses'=>'warning',
                    'Selesai'=>'success'
                  ];
                ?>
                <span class="badge bg-<?= $badge[$row['status']] ?? 'dark' ?>">
                  <?= $row['status'] ?>
                </span>
              </td>

              <td><?= $row['created_at'] ?></td>
              <td><?= $row['tanggal_respon'] ?: '<span class="text-muted">Belum</span>' ?></td>
              <td><?= $row['isi_tanggapan'] ?: '<span class="text-muted">Belum ada</span>' ?></td>

              <td class="col-aksi">
                <div class="pengaduan-actions">
                  <a href="index.php?page=tanggapan&id=<?= $row['id_pengaduan'] ?>" 
                     class="btn btn-sm btn-primary">Tanggapi</a>

                  <a href="index.php?page=hapus_pengaduan&id=<?= $row['id_pengaduan'] ?>"
                     onclick="return confirm('Yakin hapus aduan?')"
                     class="btn btn-sm btn-danger">Hapus</a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-3 d-flex justify-content-center">
    <nav>
      <ul class="pagination">

        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=admin&active_tab=pengaduan&p=<?= $page-1 ?>">Prev</a>
          </li>
        <?php endif; ?>

        <?php
        $totalPages = ceil($total / $perPage);
        for ($i=1; $i<=$totalPages; $i++): ?>
          <li class="page-item <?= $page==$i?'active':'' ?>">
            <a class="page-link" href="?page=admin&active_tab=pengaduan&p=<?= $i ?>">
              <?= $i ?>
            </a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=admin&active_tab=pengaduan&p=<?= $page+1 ?>">Next</a>
          </li>
        <?php endif; ?>

      </ul>
    </nav>
  </div>

</div>

<!-- ===================== TAB USER ===================== -->
  <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Daftar User</h4>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $u = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin'");
          $no = 1;
          while ($row = mysqli_fetch_assoc($u)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
              <td><?= htmlspecialchars($row['email']); ?></td>
              <td><?= htmlspecialchars($row['role']); ?></td>
              <td class="d-flex justify-content-center gap-1">
                <button 
                  class="btn btn-sm btn-primary btnCetakUser" 
                  data-id="<?= $row['id_user'] ?>">
                  <i class="bi bi-printer"></i> Cetak Profil
                </button>
                <a href="index.php?page=hapus_user&id=<?= $row['id_user'] ?>" 
                  class="btn btn-sm btn-danger" 
                  onclick="return confirm('Yakin hapus user ini?')">
                  Hapus
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Script Cetak Per User -->
  <script>
  document.querySelectorAll('.btnCetakUser').forEach(btn => {
    btn.addEventListener('click', () => {
      const userId = btn.getAttribute('data-id');

      fetch(`views/cetak_user.php?id=${userId}`)
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
        .catch(err => alert('Gagal mencetak profil user: ' + err));
    });
  });
  </script>

  <!-- ===================== TAB LAPORAN ===================== -->
  <div class="tab-pane fade" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
    <a href="index.php?page=cetak_laporan" class="btn btn-danger mb-3">Cetak Laporan</a>
    <h4 class="mb-3">Laporan Pengaduan</h4>
    <div class="card shadow-sm">
      <div class="card-body chart-card-body">
        <p class="chart-title">Grafik Jumlah Aduan per Kategori</p>
        <canvas id="laporanChart" class="chart-canvas"></canvas>
      </div>
    </div>

    <?php
    $kategori = [];
    $jumlah = [];
    $res = mysqli_query($conn, "
        SELECT k.nama_kategori, COUNT(*) AS total 
        FROM pengaduan p 
        JOIN kategori k ON p.id_kategori = k.id_kategori 
        GROUP BY k.id_kategori
    ");
    while ($r = mysqli_fetch_assoc($res)) {
        $kategori[] = $r['nama_kategori'];
        $jumlah[] = $r['total'];
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      const ctx = document.getElementById('laporanChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode($kategori) ?>,
          datasets: [{
            label: 'Jumlah Aduan per Kategori',
            data: <?= json_encode($jumlah) ?>,
            backgroundColor: '#0d6efd',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: { padding: { top: 4, right: 6, bottom: 4, left: 6 } },
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: { boxWidth: 12, padding: 10 }
            },
            title: { display: false }
          },
          scales: { 
            x: { 
              title: { display: true, text: 'Kategori' },
              ticks: { autoSkip: true, maxRotation: 0, minRotation: 0 }
            },
            y: { 
              beginAtZero: true, 
              title: { display: true, text: 'Jumlah Aduan' },
              ticks: { precision: 0 }
            }
          }
        }
      });
    </script>
  </div>

  <!-- ===================== TAB BERITA ===================== -->
  <div class="tab-pane fade" id="berita" role="tabpanel" aria-labelledby="berita-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Daftar Berita</h4>
    </div>

    <a href="index.php?page=tambah_berita" class="btn btn-success mb-2">Tambah Berita</a>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $berita = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal DESC");
          $no = 1;
          while ($row = mysqli_fetch_assoc($berita)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <?php if ($row['gambar']): ?>
                  <img src="assets/img/<?= htmlspecialchars($row['gambar']) ?>" width="70" class="img-thumbnail">
                <?php else: ?>
                  <span class="text-muted">Tidak ada</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['judul']) ?></td>
              <td><?= htmlspecialchars($row['penulis']) ?></td>
              <td><?= htmlspecialchars($row['tanggal']) ?></td>
              <td class="d-flex justify-content-center gap-1">
                <button 
                  class="btn btn-sm btn-primary btnCetakBerita" 
                  data-id="<?= $row['id_berita'] ?>">
                  <i class="bi bi-printer"></i> Cetak
                </button>
                <a href="index.php?page=edit_berita&id=<?= $row['id_berita'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="index.php?page=hapus_berita&id=<?= $row['id_berita'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus berita ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Script Cetak Berita -->
  <script>
  document.querySelectorAll('.btnCetakBerita').forEach(btn => {
    btn.addEventListener('click', () => {
      const beritaId = btn.getAttribute('data-id');

      fetch(`views/cetak_berita.php?id=${beritaId}`)
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

          const doc = printFrame.contentDocument;
          const images = Array.from(doc.images || []);
          const waitImages = images.length
            ? Promise.all(images.map(img => (img.complete ? Promise.resolve() : new Promise(resolve => {
                img.onload = resolve;
                img.onerror = resolve;
              }))))
            : Promise.resolve();

          waitImages.then(() => {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
            setTimeout(() => printFrame.remove(), 2000);
          });
        })
        .catch(err => alert('Gagal mencetak berita: ' + err));
    });
  });
  </script>

<!--=== Tab Feed ===-->
<div class="tab-pane fade <?= $activeTab == 'feedback' ? 'show active' : '' ?>" id="feedback" role="tabpanel" aria-labelledby="feedback-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Daftar Feedback Pengguna</h4>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>User</th>
            <th>Judul Pengaduan</th>
            <th>Rating</th>
            <th>Komentar</th>
            <th>Status</th>
            <th>Tampilkan di Home</th>
            <th class="feedback-col-aksi">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $feedback = mysqli_query($conn, "
            SELECT f.*, u.nama_lengkap, p.judul
            FROM feedback f
            JOIN users u ON f.id_user = u.id_user
            JOIN pengaduan p ON f.id_pengaduan = p.id_pengaduan
            ORDER BY f.tanggal DESC
          ");
          while ($f = mysqli_fetch_assoc($feedback)): ?>
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
                <?= $f['tampil'] ? '<span class="badge bg-info">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' ?>
              </td>
              <td class="feedback-col-aksi">
               <div class="feedback-actions">
                 <button class="btn btn-sm btn-primary btn-print btnCetakFeedback" data-id="<?= $f['id_feedback'] ?>">
                   <i class="bi bi-printer"></i> Cetak
                 </button>
                 <a href="index.php?page=ubah_feedback&id=<?= $f['id_feedback'] ?>&aksi=terima" class="btn btn-sm btn-success">Terima</a>
                 <a href="index.php?page=ubah_feedback&id=<?= $f['id_feedback'] ?>&aksi=tolak" class="btn btn-sm btn-danger">Tolak</a>
                 <?php if ((int) $f['tampil'] === 1): ?>
                   <button type="button" class="btn btn-sm btn-secondary btn-show" disabled>Sudah Tampil</button>
                 <?php else: ?>
                   <a href="index.php?page=ubah_feedback&id=<?= $f['id_feedback'] ?>&aksi=tampil" class="btn btn-sm btn-info btn-show">Tampilkan</a>
                 <?php endif; ?>
               </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Script Cetak Feedback -->
  <script>
  document.querySelectorAll('.btnCetakFeedback').forEach(btn => {
    btn.addEventListener('click', () => {
      const feedbackId = btn.getAttribute('data-id');

      fetch(`views/cetak_feedback.php?id=${feedbackId}`)
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
        .catch(err => alert('Gagal mencetak feedback: ' + err));
    });
  });
  </script>

  <!-- ===================== TAB LOKASI ===================== -->
  <div class="tab-pane fade" id="lokasi" role="tabpanel" aria-labelledby="lokasi-tab">
    <h4>Daftar Lokasi</h4>
    <a href="index.php?page=tambah_lokasi" class="btn btn-success mb-2">Tambah Lokasi</a>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Nama Lokasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $lokasi = mysqli_query($conn, "SELECT * FROM lokasi ORDER BY id_lokasi DESC");
          $no = 1;
          while ($row = mysqli_fetch_assoc($lokasi)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $row['nama_lokasi'] ?></td>
              <td>
                <a href="index.php?page=edit_lokasi&id=<?= $row['id_lokasi'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="index.php?page=hapus_lokasi&id=<?= $row['id_lokasi'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus lokasi ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ===================== TAB KATEGORI ===================== -->
  <div class="tab-pane fade" id="kategori" role="tabpanel" aria-labelledby="kategori-tab">
    <h4>Daftar Kategori</h4>
    <a href="index.php?page=tambah_kategori" class="btn btn-success mb-2">Tambah Kategori</a>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
          $no = 1;
          while ($row = mysqli_fetch_assoc($kategori)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
              <td>
                <a href="index.php?page=edit_kategori&id=<?= $row['id_kategori'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="index.php?page=hapus_kategori&id=<?= $row['id_kategori'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus kategori ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
