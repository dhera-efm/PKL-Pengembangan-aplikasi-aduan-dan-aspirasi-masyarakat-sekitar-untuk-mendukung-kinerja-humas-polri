<?php
include "../config/database.php";
session_start();
if (!isset($_SESSION['id_user'])) die("Akses ditolak!");

$id = intval($_GET['id']);
$q = mysqli_query($conn, "
  SELECT p.*, u.nama_lengkap, k.nama_kategori,
         t.isi_tanggapan, t.created_at AS tgl_tanggapan,
         a.nama_lengkap AS admin
  FROM pengaduan p
  JOIN kategori k ON p.id_kategori = k.id_kategori
  LEFT JOIN users u ON p.id_user = u.id_user
  LEFT JOIN tanggapan t ON t.id_pengaduan = p.id_pengaduan
  LEFT JOIN users a ON t.id_admin = a.id_user
  WHERE p.id_pengaduan = $id
");
$data = mysqli_fetch_assoc($q);
if (!$data) die("Data tidak ditemukan!");

setlocale(LC_TIME, 'id_ID.utf8');
$hari = strftime('%A');
$tanggal = strftime('%e %B %Y');
$lokasiTanggal = "Kabupaten Banjar, $hari $tanggal";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Pengaduan Resmi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Times New Roman', serif;
      color: #000;
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
    .kop img { width: 80px; margin-bottom: 10px; }
    .kop h5, .kop p { margin: 0; }
    .kop h5 { font-weight: bold; text-transform: uppercase; }
    .kop p { font-size: 14px; }
    .laporan {
      border: none;
      border-radius: 0;
      padding: 0;
    }
    .ttd {
      margin-top: 60px;
      text-align: right;
      font-size: 14px;
    }
    .ttd p { margin-bottom: 3px; }
    .signature-space { height: 80px; }
    @media print {
      .no-print { display: none !important; }
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
    <img src="../assets/img/logo-polri.png" alt="Logo Polri">
    <h5>KEPOLISIAN NEGARA REPUBLIK INDONESIA</h5>
    <p>RESOR BANJAR - KALIMANTAN SELATAN</p>
  </div>

  <h5 class="text-center mb-4">LAPORAN HASIL TANGGAPAN PENGADUAN MASYARAKAT</h5>

  <div class="laporan">
    <table class="table table-bordered">
      <tr><th width="30%">Pelapor</th><td><?= htmlspecialchars($data['nama_lengkap'] ?? 'Public') ?></td></tr>
      <tr><th>Kategori</th><td><?= htmlspecialchars($data['nama_kategori']) ?></td></tr>
      <tr><th>Judul Aduan</th><td><?= htmlspecialchars($data['judul']) ?></td></tr>
      <tr><th>Isi Aduan</th><td><?= nl2br(htmlspecialchars($data['isi_aduan'])) ?></td></tr>
      <tr><th>Status</th><td><?= htmlspecialchars($data['status']) ?></td></tr>
      <tr><th>Tanggal Aduan</th><td><?= date('d-m-Y H:i', strtotime($data['created_at'])) ?></td></tr>
    </table>

    <h6 class="mt-4 fw-bold">Tanggapan Admin Humas:</h6>
    <p class="ps-2"><?= $data['isi_tanggapan'] ? nl2br(htmlspecialchars($data['isi_tanggapan'])) : "<i>Belum ada tanggapan</i>" ?></p>

    <p><strong>Admin Humas:</strong> <?= htmlspecialchars($data['admin'] ?? '-') ?><br>
       <strong>Tanggal Tanggapan:</strong> <?= $data['tgl_tanggapan'] ? date('d-m-Y H:i', strtotime($data['tgl_tanggapan'])) : '-' ?></p>
  </div>

  <div class="ttd">
    <p><?= $lokasiTanggal ?></p>
    <p><strong>Admin Humas Polres Banjar</strong></p>
    <div class="signature-space"></div>
    <p><u><?= htmlspecialchars($data['admin'] ?? '...................................') ?></u></p>
    <p>NIP. <?= rand(100000,999999) ?><?= rand(100,999) ?><?= rand(10,99) ?></p>
  </div>
</div>
</body>
</html>
