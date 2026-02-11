<?php
include "config/database.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
    die("Akses ditolak!");
}
date_default_timezone_set('Asia/Makassar');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Laporan Pengaduan</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <style>
    @page {
      size: A4;
      margin: 1.2cm 1.6cm;
    }
    body {
      font-size: 12.5px;
      line-height: 1.45;
      color: #000;
      background: #eef2f7;
      margin: 0;
      padding: 16px;
    }
    .print-page {
      max-width: 21cm;
      margin: 0 auto;
      background: #fff;
      border-radius: 0;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      padding: 1.6cm;
    }
    .kop {
      text-align: center;
      border-bottom: 3px solid #000;
      padding-bottom: 10px;
      margin-bottom: 22px;
    }
    .kop::after {
      content: '';
      display: block;
      margin-top: 6px;
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
    .judul-laporan h4 {
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 4px;
      font-size: 15px;
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
        border-radius: 0;
        box-shadow: none;
        padding: 0;
      }
    }
  </style>
</head>
<body>
<div class="print-page">
  <a href="admin" class="btn btn-secondary mb-3 no-print">Kembali ke Dashboard</a>

  <div class="kop">
    <img src="assets/img/logo-polri.png" alt="Logo Polri">
    <h5>KEPOLISIAN NEGARA REPUBLIK INDONESIA</h5>
    <p>RESOR BANJAR - KALIMANTAN SELATAN</p>
    <p>Jalan Jenderal Achmad Yani, Kabupaten Banjar, Kalimantan Selatan</p>
  </div>

  <div class="text-center mb-4 judul-laporan">
    <h4>Laporan Pengaduan Masyarakat</h4>
    <p class="mb-1">Wilayah Hukum Polres Banjar</p>
    <p class="mb-0"><small>Tanggal Cetak: <?= date('d F Y, H:i'); ?> WITA</small></p>
  </div>
  
  <button class="btn btn-primary no-print mb-3" onclick="window.print()">Cetak / Simpan PDF</button>
  
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Pelapor</th>
        <th>Kategori</th>
        <th>Judul</th>
        <th>Status</th>
        <th>Tanggal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $q = mysqli_query($conn,"SELECT p.*,u.nama_lengkap,k.nama_kategori 
                               FROM pengaduan p 
                               JOIN kategori k ON p.id_kategori=k.id_kategori
                               LEFT JOIN users u ON p.id_user=u.id_user
                               ORDER BY p.created_at DESC");
      $no=1;
      while($row=mysqli_fetch_assoc($q)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['nama_lengkap'] ?? 'Public' ?></td>
          <td><?= $row['nama_kategori'] ?></td>
          <td><?= $row['judul'] ?></td>
          <td><?= $row['status'] ?></td>
          <td><?= $row['created_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
