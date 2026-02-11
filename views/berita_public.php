<?php
require "config/database.php"; 

$per_halaman   = 5;
$hal_sekarang  = isset($_GET['hal']) ? max(1, intval($_GET['hal'])) : 1;
$mulai         = ($hal_sekarang - 1) * $per_halaman;

$q = mysqli_query($conn, "SELECT * FROM berita ORDER BY id_berita DESC LIMIT $mulai, $per_halaman");

$hitung = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM berita"));
$total_data     = (int)$hitung['jml'];
$jumlah_halaman = $total_data > 0 ? ceil($total_data / $per_halaman) : 1;
?>

<h2 class="mt-4 mb-4">Daftar Berita</h2>

<?php if ($total_data == 0): ?>
  <div class="alert alert-info">Belum ada berita.</div>
<?php endif; ?>

<?php while($b = mysqli_fetch_assoc($q)): ?>
<a href="index.php?page=berita_detail&id=<?= $b['id_berita'] ?>" style="text-decoration:none; color:inherit;">
<div class="card mb-3 shadow-sm p-2">
  <div class="row g-0 align-items-center">
    <div class="col-md-3 d-flex justify-content-center">
      <img src="assets/img/<?= $b['gambar']?>" 
           style="width:100%; max-width:180px; height:120px; object-fit:cover;" 
           class="rounded">
    </div>
    <div class="col-md-9">
      <div class="card-body">
        <h5 class="mb-1"><?= htmlspecialchars($b['judul']) ?></h5>
        <p class="small text-muted mb-1"><?= date('d M Y', strtotime($b['tanggal'])) ?> • <?= $b['penulis'] ?></p>
        <p class="text-secondary mb-2"><?= substr(strip_tags($b['isi']),0,150) ?>...</p>
        <span class="text-primary fw-semibold">Baca Selengkapnya →</span>
      </div>
    </div>
  </div>
</div>
</a>
<?php endwhile; ?>

<?php if ($jumlah_halaman > 1): ?>
<nav>
  <ul class="pagination justify-content-center mt-4">

    <li class="page-item <?= ($hal_sekarang <= 1) ? 'disabled' : '' ?>">
      <a class="page-link" href="index.php?page=berita_public&hal=<?= $hal_sekarang-1 ?>">« Prev</a>
    </li>

    <?php for($i=1; $i <= $jumlah_halaman; $i++): ?>
      <li class="page-item <?= ($i==$hal_sekarang) ? 'active' : '' ?>">
        <a class="page-link" href="index.php?page=berita_public&hal=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <li class="page-item <?= ($hal_sekarang >= $jumlah_halaman) ? 'disabled' : '' ?>">
      <a class="page-link" href="index.php?page=berita_public&hal=<?= $hal_sekarang+1 ?>">Next »</a>
    </li>

  </ul>
</nav>
<?php endif; ?>
