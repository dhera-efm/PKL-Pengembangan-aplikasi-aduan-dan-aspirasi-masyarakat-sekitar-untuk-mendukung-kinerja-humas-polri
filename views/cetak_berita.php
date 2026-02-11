<?php
include '../config/database.php';
session_start();

$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = str_replace('/views', '', $base_url);

$where = "";
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $where = "WHERE id_berita = $id";
}
$beritaQ = mysqli_query($conn, "SELECT * FROM berita $where ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Berita</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      background: #eef2f7;
      margin: 0;
      padding: 16px;
    }
    .print-page {
      max-width: 1100px;
      margin: 0 auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      padding: 24px;
    }
    .kop {
      text-align: center;
      border-bottom: 3px double #000;
      padding-bottom: 10px;
      margin-bottom: 30px;
    }
    .kop img {
      width: 80px;
      margin-bottom: 8px;
    }
    .kop h5 {
      margin: 0;
      font-weight: bold;
      text-transform: uppercase;
    }
    .kop p {
      margin: 0;
      font-size: 14px;
    }
    .judul-cetak h4 {
      margin-bottom: 4px;
      font-weight: bold;
      text-transform: uppercase;
    }
    .berita-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .berita-card img {
      display: block;
      max-width: 400px;
      margin: 15px auto;
      border-radius: 6px;
    }
    .berita-card h5 {
      color: #0d6efd;
      font-weight: 600;
    }
    @media print {
      .no-print { display: none !important; }
      .berita-card { page-break-inside: avoid; }
      body {
        background: #fff;
        padding: 0;
      }
      .print-page {
        max-width: none;
        margin: 0;
        border-radius: 0;
        box-shadow: none;
        padding: 0;
      }
    }
  </style>
</head>
<body>
<div class="print-page">
  <div class="kop">
    <img src="<?= $base_url ?>/assets/img/logo-polri.png" alt="Logo Polri">
    <h5>KEPOLISIAN NEGARA REPUBLIK INDONESIA</h5>
    <p>RESOR BANJAR - KALIMANTAN SELATAN</p>
    <p>Jalan Jenderal Achmad Yani, Kabupaten Banjar, Kalimantan Selatan</p>
  </div>

  <div class="text-center mb-4 judul-cetak">
    <h4>Laporan Berita</h4>
    <p class="mb-0"><small>Tanggal Cetak: <?= date('d F Y, H:i'); ?></small></p>
  </div>

  <?php while ($b = mysqli_fetch_assoc($beritaQ)): ?>
    <div class="berita-card">
      <h5><?= htmlspecialchars($b['judul']); ?></h5>
      <p class="text-muted mb-1">
        <?= date('d F Y', strtotime($b['tanggal'])); ?> - <b><?= htmlspecialchars($b['penulis']); ?></b>
      </p>

      <?php if (!empty($b['gambar'])): ?>
        <?php $gambar_url = $base_url . "/assets/img/" . $b['gambar']; ?>
        <img src="<?= $gambar_url ?>" alt="Gambar Berita">
      <?php endif; ?>

      <p class="mt-3"><?= nl2br(htmlspecialchars($b['isi'])); ?></p>
    </div>
  <?php endwhile; ?>

  <div class="text-center mt-4">
    <button class="btn btn-primary no-print" onclick="window.print()">Cetak</button>
  </div>
</div>
</body>
</html>
