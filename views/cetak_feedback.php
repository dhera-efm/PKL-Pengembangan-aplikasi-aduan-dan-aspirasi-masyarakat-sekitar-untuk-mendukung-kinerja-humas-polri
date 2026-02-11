<?php
include '../config/database.php';
session_start();

date_default_timezone_set('Asia/Makassar');

$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = str_replace('/views', '', $base_url);

$where = "";
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $where = "WHERE f.id_feedback = $id";
}

$feedbackQ = mysqli_query($conn, "
  SELECT f.*, u.nama_lengkap, p.judul
  FROM feedback f
  JOIN users u ON f.id_user = u.id_user
  JOIN pengaduan p ON f.id_pengaduan = p.id_pengaduan
  $where
  ORDER BY f.tanggal DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Feedback</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <style>
    @page {
      size: A4;
      margin: 1.2cm 1.6cm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      background: #eef2f7;
      margin: 0;
      padding: 16px;
      line-height: 1.45;
      font-size: 12.5px;
    }
    .print-page {
      max-width: 21cm;
      margin: 0 auto;
      background: #fff;
      border-radius: 0;
      box-shadow: 0 0 20px rgba(0,0,0,0.08);
      padding: 1.6cm;
    }
    .kop {
      text-align: center;
      border-bottom: 3px solid #000;
      padding-bottom: 10px;
      margin-bottom: 22px;
      position: relative;
    }
    .kop::after {
      content: '';
      position: absolute;
      left: 0;
      right: 0;
      bottom: -5px;
      height: 1px;
      background: #000;
    }
    .kop img {
      width: 80px;
      margin-bottom: 6px;
    }
    .kop h5 {
      margin: 0;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 15px;
    }
    .kop p {
      margin: 0;
      font-size: 12px;
    }
    .judul-cetak {
      text-align: center;
      margin-bottom: 18px;
    }
    .judul-cetak h4 {
      margin-bottom: 6px;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 15px;
      text-decoration: underline;
      letter-spacing: 0.5px;
    }
    .judul-cetak p {
      margin: 0;
      font-size: 12px;
    }
    .feedback-card {
      border: 1px solid #333;
      padding: 14px;
      margin-bottom: 16px;
      page-break-inside: avoid;
    }
    .feedback-card h6 {
      font-weight: bold;
      margin-bottom: 6px;
      text-transform: uppercase;
      font-size: 13px;
    }
    .feedback-meta {
      font-size: 12px;
      margin-bottom: 8px;
    }
    .feedback-meta span {
      display: inline-block;
      margin-right: 10px;
    }
    .rating {
      font-weight: bold;
    }
    @media print {
      .no-print { display: none !important; }
      body {
        background: #fff;
        padding: 0;
      }
      .print-page {
        max-width: none;
        margin: 0;
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

  <div class="judul-cetak">
    <h4>Laporan Feedback Pengguna</h4>
    <p>Tanggal Cetak: <?= date('d F Y, H:i'); ?> WITA</p>
  </div>

  <?php if (mysqli_num_rows($feedbackQ) === 0): ?>
    <p class="text-muted text-center">Tidak ada data feedback.</p>
  <?php endif; ?>

  <?php while ($f = mysqli_fetch_assoc($feedbackQ)): ?>
    <div class="feedback-card">
      <h6><?= htmlspecialchars($f['nama_lengkap']); ?></h6>
      <div class="feedback-meta">
        <span>Judul Pengaduan: <?= htmlspecialchars($f['judul']); ?></span>
        <span>Status: <?= htmlspecialchars(ucfirst($f['status'])); ?></span>
        <span>Tanggal: <?= date('d F Y, H:i', strtotime($f['tanggal'])); ?> WITA</span>
      </div>
      <div class="feedback-meta rating">
        Rating: <?= (int) $f['rating']; ?> / 5
      </div>
      <div>
        <?= nl2br(htmlspecialchars($f['komentar'])); ?>
      </div>
    </div>
  <?php endwhile; ?>

  <div class="text-center no-print">
    <button class="btn btn-primary" onclick="window.print()">Cetak</button>
  </div>
</div>
</body>
</html>
