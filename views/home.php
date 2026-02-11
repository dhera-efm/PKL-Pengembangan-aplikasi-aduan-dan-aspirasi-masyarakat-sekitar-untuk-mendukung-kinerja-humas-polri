<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
if (isset($_SESSION['id_user'])) {
    $id_user = intval($_SESSION['id_user']);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id_user'"));
} else {
    $user = null; 
}
?>

<?php if (isset($_SESSION['alert'])): ?>
  <div class="alert alert-warning text-center mx-5">
    <?= $_SESSION['alert']; ?>
  </div>
  <?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<link rel="stylesheet" type="text/css" href="/lapor-polres-banjar/assets/css/style.css?v=<?= time() ?>">


<div class="container-fluid bg-light text-dark d-flex align-items-center justify-content-center" style="min-height: 80vh;">
  <div class="text-center">
    <h1 class="display-4 fw-bold mb-3">Selamat Datang di <span class="text-primary">LAPOR POLRES BANJAR</span></h1>
    <p class="lead mb-4">Sistem Informasi Layanan Aspirasi dan Pengaduan Masyarakat</p>
    <a href="tambahpengaduan" class="btn btn-lg btn-primary px-4">Buat Pengaduan</a>
  </div>
</div>

<h4 class="mt-5 mb-3">Berita Terbaru</h4>

<?php
$berita = mysqli_query($conn, "SELECT * FROM berita ORDER BY id_berita DESC LIMIT 6");

$list = [];
while ($row = mysqli_fetch_assoc($berita)) {
    $list[] = $row;
}

$chunks = array_chunk($list, 3);
?>

<div id="beritaSlider">
    <div class="slider-track">
        <?php foreach ($chunks as $group): ?>
            <div class="berita-group">

                <?php foreach ($group as $b): ?>
                    <div class="card shadow-sm berita-card" style="width: 16rem;">
                        <img src="assets/img/<?= $b['gambar'] ?>" 
                             class="card-img-top" 
                             style="height:120px; object-fit:cover;">
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($b['judul']) ?></h6>
                            <p class="card-text-limit"><?= strip_tags($b['isi']) ?></p>
                            <a href="index.php?page=berita_detail&id=<?= $b['id_berita'] ?>" 
                               class="btn btn-primary btn-sm mt-3">
                                Baca
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>
    </div>

    <button class="nav-btn" id="prevBtn"><i class="bi bi-chevron-left"></i></button>
    <button class="nav-btn" id="nextBtn"><i class="bi bi-chevron-right"></i></button>
</div>
</div>

<h4 class="mt-5 text-center">Apa Kata Masyarakat Banjar?</h4>
<p class="text-center text-muted mb-4">
  Feedback dari masyarakat yang telah melapor.
</p>

<div id="feedbackCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner text-center">
    <?php
    $i = 0;
    $qf = mysqli_query($conn, "
      SELECT u.nama_lengkap, f.rating, f.komentar, f.tanggal
      FROM feedback f
      JOIN users u ON f.id_user = u.id_user
      WHERE f.status='diterima' AND f.tampil=1
      ORDER BY f.tanggal DESC LIMIT 5
    ");
    if (mysqli_num_rows($qf) > 0):
      while($f = mysqli_fetch_assoc($qf)):
        $active = $i == 0 ? 'active' : '';
        $nama = explode(' ', $f['nama_lengkap'])[0];
    ?>
        <div class="carousel-item <?= $active ?>">
          <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
            <div class="text-warning mb-2"><?php for ($s = 0; $s < (int) $f['rating']; $s++): ?><i class="bi bi-star-fill"></i><?php endfor; ?></div>
            <p class="fst-italic text-muted w-75 mx-auto">
              "<?= htmlspecialchars($f['komentar']) ?>"
            </p>
            <h6 class="fw-bold mb-0"><?= htmlspecialchars($nama) ?></h6>
            <small class="text-muted"><?= date('d M Y', strtotime($f['tanggal'])) ?></small>
          </div>
        </div>
    <?php
        $i++;
      endwhile;
    else:
      echo "<p class='text-muted'>Belum ada feedback yang ditampilkan.</p>";
    endif;
    ?>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.berita-group');
    const totalSlides = slides.length;

    let slideIndex = 0;
    let autoSlide;

    function updateSlidePosition() {
        track.style.transform = `translateX(-${slideIndex * 100}%)`;
    }

    function nextSlide() {
        slideIndex = (slideIndex + 1) % totalSlides;
        updateSlidePosition();
    }

    function prevSlide() {
        slideIndex = (slideIndex - 1 + totalSlides) % totalSlides;
        updateSlidePosition();
    }

    function startAuto() {
        autoSlide = setInterval(nextSlide, 5000);
    }

    function stopAuto() {
        clearInterval(autoSlide);
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        stopAuto();
        nextSlide();
        startAuto();
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        stopAuto();
        prevSlide();
        startAuto();
    });

    document.getElementById('beritaSlider').addEventListener('mouseenter', stopAuto);
    document.getElementById('beritaSlider').addEventListener('mouseleave', startAuto);

    startAuto();
});
</script>


</div>




