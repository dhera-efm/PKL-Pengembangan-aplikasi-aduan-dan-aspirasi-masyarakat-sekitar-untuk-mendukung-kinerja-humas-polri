<?php
include '../config/database.php';
session_start();

$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = str_replace('/views', '', $base_url);
$logo_url = $base_url . "/assets/img/logo-polri.png";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id"));
    if (!$user) {
        die("<div class='alert alert-danger'>Data user tidak ditemukan.</div>");
    }
    $is_single = true;
} else {
    $users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' ORDER BY id_user ASC");
    $is_single = false;
}
$judul_dokumen = $is_single ? "Laporan Profil Pengguna" : "Laporan Data Pengguna";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Data User - Polres Banjar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @page {
      size: A4;
      margin: 1.2cm 1.6cm;
    }
    
    body {
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      background: #f5f5f5;
      margin: 0;
      padding: 20px;
      line-height: 1.45;
    }
    
    .print-page {
      max-width: 21cm;
      margin: 0 auto;
      background: #fff;
      border-radius: 0;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      padding: 1.6cm;
    }
    
    /* Kop Surat Resmi */
    .kop {
      position: relative;
      text-align: center;
      border-bottom: 3px solid #000;
      padding-bottom: 10px;
      margin-bottom: 24px;
    }
    
    .kop::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      right: 0;
      height: 1px;
      background: #000;
    }
    
    .kop-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 6px;
      margin-bottom: 6px;
    }
    
    .kop .kop-logo {
      width: 80px;
      height: auto;
      display: block;
    }
    
    .kop-text {
      text-align: center;
    }
    
    .kop h5 {
      font-weight: bold;
      text-transform: uppercase;
      margin: 0;
      font-size: 15px;
      letter-spacing: 0.5px;
    }
    
    .kop h6 {
      font-weight: bold;
      margin: 5px 0;
      font-size: 13px;
    }
    
    .kop p {
      margin: 2px 0;
      font-size: 11px;
    }
    
    .kop .alamat {
      font-size: 10px;
      margin-top: 4px;
    }
    
    /* Judul Dokumen */
    .judul-cetak {
      text-align: center;
      margin-bottom: 20px;
    }
    
    .judul-cetak h4 {
      margin-bottom: 8px;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 16px;
      text-decoration: underline;
      letter-spacing: 1px;
    }
    
    .judul-cetak p {
      font-size: 12px;
      margin: 0;
    }
    
    /* Meta Dokumen */
    .meta-dokumen {
      width: 100%;
      margin-bottom: 18px;
      font-size: 12.5px;
      border-collapse: collapse;
    }
    
    .meta-dokumen td {
      padding: 5px 8px;
      vertical-align: top;
    }
    
    .meta-dokumen td:first-child {
      width: 150px;
      font-weight: 500;
    }
    
    /* Section Title */
    .section-title {
      font-weight: bold;
      text-transform: uppercase;
      margin: 18px 0 10px;
      font-size: 14px;
      border-bottom: 2px solid #000;
      padding-bottom: 5px;
    }
    
    /* Tabel Profil */
    .profil-table {
      width: 100%;
      margin-bottom: 30px;
      border-collapse: collapse;
      font-size: 12.5px;
    }
    
    .profil-table th,
    .profil-table td {
      border: 1px solid #333;
      padding: 8px 10px;
      text-align: left;
    }
    
    .profil-table th {
      width: 35%;
      background: #f0f0f0;
      font-weight: 600;
    }
    
    .profil-table td {
      background: #fff;
    }
    
    /* Tanda Tangan */
    .ttd {
      margin-top: 35px;
      text-align: right;
      font-size: 12.5px;
    }
    
    .ttd p {
      margin-bottom: 5px;
    }
    
    .signature-space {
      height: 55px;
    }
    
    .ttd .nama-ttd {
      font-weight: bold;
      text-decoration: underline;
    }
    
    /* User Cards untuk Multiple Users */
    .user-card {
      border: 2px solid #333;
      border-radius: 0;
      padding: 20px;
      margin-bottom: 18px;
      page-break-inside: avoid;
      background: #fafafa;
    }
    
    .user-card h5 {
      font-weight: bold;
      margin-bottom: 15px;
      font-size: 15px;
      border-bottom: 2px solid #000;
      padding-bottom: 8px;
      text-transform: uppercase;
    }
    
    .user-card p {
      margin: 8px 0;
      font-size: 12.5px;
    }
    
    .user-card strong {
      display: inline-block;
      width: 150px;
    }
    
    /* Button Cetak */
    .btn-cetak-wrapper {
      text-align: center;
      margin-top: 30px;
    }
    
    .btn-cetak {
      padding: 12px 40px;
      font-size: 15px;
      font-weight: 600;
      background: #1a73e8;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      transition: all 0.3s;
    }
    
    .btn-cetak:hover {
      background: #1557b0;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    
    /* Print Styles */
    @media print {
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
      
      .no-print {
        display: none !important;
      }
      
      .user-card {
        page-break-inside: avoid;
        border: 1px solid #000;
        background: #fff;
      }
      
      .kop {
        border-bottom: 3px solid #000;
      }
      
      .kop::after {
        bottom: -5px;
      }
    }
  </style>
</head>
<body>
<div class="print-page">
  <!-- KOP SURAT RESMI -->
  <div class="kop">
    <div class="kop-header">
      <img class="kop-logo" src="<?= htmlspecialchars($logo_url) ?>" alt="Logo Polri">
      <div class="kop-text">
        <h5>KEPOLISIAN NEGARA REPUBLIK INDONESIA</h5>
        <h6>DAERAH KALIMANTAN SELATAN</h6>
        <p><strong>RESOR BANJAR</strong></p>
      </div>
    </div>
    <p class="alamat">
      Jalan Jenderal Achmad Yani, Kabupaten Banjar, Kalimantan Selatan<br>
      Telp: (0511) 1234567 | Email: humaspolresbanjar@polri.go.id
    </p>
  </div>

  <!-- JUDUL DOKUMEN -->
  <div class="text-center mb-4 judul-cetak">
    <h4><?= strtoupper($judul_dokumen) ?></h4>
    <p class="mb-0"><small>Tanggal Cetak: <?= date('d F Y, H:i'); ?> WIB</small></p>
  </div>

  <?php if ($is_single): ?>
    <!-- LAPORAN PROFIL SINGLE USER -->
    <table class="meta-dokumen">
      <tr>
        <td>Nomor Dokumen</td>
        <td>: PROFIL/<?= str_pad((string) $user['id_user'], 4, '0', STR_PAD_LEFT) ?>/POLRES-BJR/<?= date('Y') ?></td>
      </tr>
      <tr>
        <td>Sifat</td>
        <td>: Resmi</td>
      </tr>
      <tr>
        <td>Perihal</td>
        <td>: Laporan Profil Pengguna</td>
      </tr>
      <tr>
        <td>Lampiran</td>
        <td>: 1 (satu) berkas</td>
      </tr>
    </table>

    <div class="section-title">Data Profil Pengguna</div>
    <table class="profil-table">
      <tr>
        <th>ID User</th>
        <td><?= str_pad((string) $user['id_user'], 4, '0', STR_PAD_LEFT); ?></td>
      </tr>
      <tr>
        <th>Nama Lengkap</th>
        <td><?= htmlspecialchars($user['nama_lengkap']); ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?= htmlspecialchars($user['email']); ?></td>
      </tr>
      <tr>
        <th>Nomor HP</th>
        <td><?= htmlspecialchars($user['no_hp'] ?? '-'); ?></td>
      </tr>
      <tr>
        <th>Alamat</th>
        <td><?= htmlspecialchars($user['alamat'] ?? '-'); ?></td>
      </tr>
      <tr>
        <th>Role</th>
        <td><?= ucfirst(htmlspecialchars($user['role'])); ?></td>
      </tr>
      <tr>
        <th>Tanggal Terdaftar</th>
        <td><?= date('d F Y, H:i', strtotime($user['created_at'])); ?> WIB</td>
      </tr>
    </table>

    <p style="font-size: 12.5px; text-align: justify; margin: 20px 0;">
      Demikian laporan profil pengguna ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <div class="ttd">
      <p>Kabupaten Banjar, <?= strftime('%d %B %Y', strtotime('now')); ?></p>
      <p><strong>Admin Humas</strong></p>
      <p><strong>Polres Banjar</strong></p>
      <div class="signature-space"></div>
      <p class="nama-ttd"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin Humas') ?></p>
      <p>NRP. __________________</p>
    </div>

  <?php else: ?>
    <!-- LAPORAN MULTIPLE USERS -->
    <table class="meta-dokumen">
      <tr>
        <td>Nomor Dokumen</td>
        <td>: DATA-USER/POLRES-BJR/<?= date('m/Y') ?></td>
      </tr>
      <tr>
        <td>Sifat</td>
        <td>: Resmi</td>
      </tr>
      <tr>
        <td>Perihal</td>
        <td>: Laporan Data Pengguna Terdaftar</td>
      </tr>
    </table>

    <div class="section-title">Daftar Pengguna Terdaftar</div>
    
    <?php 
    $no = 1;
    while ($u = mysqli_fetch_assoc($users)): 
    ?>
      <div class="user-card">
        <h5><?= $no++; ?>. <?= htmlspecialchars($u['nama_lengkap']); ?></h5>
        <p><strong>ID User:</strong> <?= str_pad((string) $u['id_user'], 4, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($u['email']); ?></p>
        <p><strong>No. HP:</strong> <?= htmlspecialchars($u['no_hp'] ?? '-'); ?></p>
        <p><strong>Alamat:</strong> <?= htmlspecialchars($u['alamat'] ?? '-'); ?></p>
        <p><strong>Role:</strong> <?= ucfirst($u['role']); ?></p>
        <p><strong>Tanggal Terdaftar:</strong> <?= date('d F Y, H:i', strtotime($u['created_at'])); ?> WIB</p>
      </div>
    <?php endwhile; ?>

    <p style="font-size: 12.5px; text-align: justify; margin: 30px 0 20px 0;">
      Demikian laporan data pengguna ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <div class="ttd">
      <p>Kabupaten Banjar, <?= date('d F Y'); ?></p>
      <p><strong>Admin Humas</strong></p>
      <p><strong>Polres Banjar</strong></p>
      <div class="signature-space"></div>
      <p class="nama-ttd"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin Humas') ?></p>
      <p>NRP. __________________</p>
    </div>
  <?php endif; ?>

  <!-- BUTTON CETAK -->
  <div class="btn-cetak-wrapper no-print">
    <button class="btn-cetak" onclick="window.print()">
      <span style="font-size: 16px;"><i class="bi bi-printer"></i></span> Cetak Dokumen
    </button>
    <br><br>
    <a href="javascript:history.back()" style="color: #666; text-decoration: none; font-size: 12.5px;">
      ← Kembali
    </a>
  </div>
</div>
</body>
</html>

