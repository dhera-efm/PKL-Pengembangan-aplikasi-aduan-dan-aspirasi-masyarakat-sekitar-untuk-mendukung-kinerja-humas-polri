<?php
if (isset($_SESSION['id_user'])) {
    $id_user = intval($_SESSION['id_user']);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_user'"));
} else {
    $user = null;
}
?>

<?php
$SET = [];
$qset = mysqli_query($conn, "SELECT * FROM system_settings");
while ($row = mysqli_fetch_assoc($qset)) {
    $SET[$row['key']] = $row['value'];
}

function sys($key, $default='') {
    global $SET;
    return $SET[$key] ?? $default;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= sys('site_name', 'Sistem Lapor')?></title>

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <audio id="notifSound" src="assets/sound/s_not.mp3" preload="auto"></audio>
  <script src="assets/js/notif.js"></script>

  <style>
    @media print {
      nav, footer, .btn { display: none !important; }
      body { background: #fff; color: #000; }
    }

    html, body { height: 100%; margin: 0; }
    body { display: flex; flex-direction: column; background-color: #f8f9fa; }
.main-content {
    flex: 1;
    padding-bottom: 50px; 
}
    .navbar-nav .nav-link:hover { color: #ffc107 !important; }
  </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="home">

    <?php if(sys('site_logo')): ?>
        <img src="assets/img/<?= sys('site_logo') ?>" 
             alt="logo"
             style="height:32px; margin-right:8px;">
    <?php endif; ?>

    <?= sys('site_name', 'LAPOR') ?>

</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
  <ul class="navbar-nav ms-auto">  
    <li class="nav-item"><a class="nav-link" href="home">Home</a></li>
    <li class="nav-item"><a class="nav-link" href="pengaduan">Pengaduan</a></li>
    <li class="nav-item"><a class="nav-link" href="index.php?page=berita_public">Berita</a></li>


    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
      <li class="nav-item position-relative">
  <a class="nav-link" href="index.php?page=admin">
    Dashboard Admin
    <span id="notifDot" 
          class="position-absolute top-0 start-100 translate-middle p-1 bg-danger rounded-circle d-none">
    </span>
  </a>
</li>
      <li class="nav-item"><a class="nav-link" href="laporan">Laporan</a></li>
      <li class="nav-item"><a class="nav-link" href="settings">Settings</a></li>
      <li class="nav-item"><a class="nav-link" href="logout">Logout</a></li>
    <?php endif; ?>
  </ul>

  <ul class="navbar-nav ms-3">  
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" data-bs-toggle="dropdown">

          <span class="me-2">
            <?= htmlspecialchars(explode(' ', $user['nama_lengkap'])[0]) ?>
          </span>

          <?php if (!empty($user['foto_profil'])): ?>
            <img src="assets/img/<?= htmlspecialchars($user['foto_profil']) ?>" 
                 class="rounded-circle"
                 width="35" height="35"
                 style="object-fit: cover; border:2px solid #fff;">
          <?php else: ?>
            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                 style="width:35px;height:35px;">
              <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
            </div>
          <?php endif; ?>

        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item" href="user">Dashboard</a></li>
          <li><a class="dropdown-item" href="index.php?page=settings">Pengaturan</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout">Logout</a></li>
        </ul>
      </li>
    <?php endif; ?>

     <?php if (!isset($_SESSION['role'])): ?>
          <li class="nav-item"><a class="nav-link" href="login">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="registrasi"><span class="text-white sm-bold">Daftar</span></a></li>
        <?php endif; ?>
  </ul>
</div>
  </div>
</nav>


<div class="main-content container mt-4">
<?php if (!empty($_SESSION['flash']) && is_array($_SESSION['flash'])): ?>
  <?php
    $flashType = $_SESSION['flash']['type'] ?? 'info';
    $flashMessage = $_SESSION['flash']['message'] ?? '';
    unset($_SESSION['flash']);
  ?>
  <?php if ($flashMessage !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flashMessage) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
<?php endif; ?>

