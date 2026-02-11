<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak!</div>";
    exit;
}

$menu = $_GET['menu'] ?? 'profil';
?>

<h2 class="mb-4">Pengaturan</h2>

<div class="row">

    <div class="col-md-3">
        <div class="list-group shadow-sm">

            <a href="index.php?page=settings&menu=profil"
   class="list-group-item list-group-item-action <?= $menu=='profil'?'active':'' ?>">
   <i class="bi bi-person"></i> Profil Admin
</a>

<a href="index.php?page=settings&menu=password"
   class="list-group-item list-group-item-action <?= $menu=='password'?'active':'' ?>">
   <i class="bi bi-key"></i> Ganti Password
</a>

<a href="index.php?page=settings&menu=sistem"
   class="list-group-item list-group-item-action <?= $menu=='sistem'?'active':'' ?>">
   <i class="bi bi-gear"></i> Pengaturan Sistem
</a>

<a href="index.php?page=settings&menu=users"
   class="list-group-item list-group-item-action <?= $menu=='users'?'active':'' ?>">
   <i class="bi bi-people"></i> Kelola User
</a>

<a href="index.php?page=settings&menu=lokasi"
   class="list-group-item list-group-item-action <?= $menu=='lokasi'?'active':'' ?>">
   <i class="bi bi-geo-alt"></i> Kelola Lokasi
</a>

<a href="index.php?page=settings&menu=kategori"
   class="list-group-item list-group-item-action <?= $menu=='kategori'?'active':'' ?>">
   <i class="bi bi-tags"></i> Kelola Kategori
</a>

<a href="index.php?page=settings&menu=maintenance"
   class="list-group-item list-group-item-action <?= $menu=='maintenance'?'active':'' ?>">
   <i class="bi bi-cone-striped"></i> Mode Maintenance
</a>

<a href="index.php?page=settings&menu=notif"
   class="list-group-item list-group-item-action <?= $menu=='notif'?'active':'' ?>">
   <i class="bi bi-bell"></i> Notifikasi Realtime & Email
</a>

<!-- Fitur Export dinonaktifkan sampai benar benar perlu.

<a href="index.php?page=settings&menu=export"
   class="list-group-item list-group-item-action <?= $menu=='export'?'active':'' ?>">
   <i class="bi bi-file-earmark-arrow-down"></i> Export Data
</a>
-->
<a href="index.php?page=settings&menu=backup"
   class="list-group-item list-group-item-action <?= $menu=='backup'?'active':'' ?>">
   <i class="bi bi-hdd-network"></i> Backup Database
</a>

<a href="index.php?page=settings&menu=admin"
   class="list-group-item list-group-item-action <?= $menu=='admin'?'active':'' ?>">
   <i class="bi bi-shield-lock"></i> Kelola Admin
</a>

<a href="index.php?page=settings&menu=log"
   class="list-group-item list-group-item-action <?= $menu=='log'?'active':'' ?>">
   <i class="bi bi-clipboard-data"></i> Log Aktivitas Admin
</a>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-body">

                <?php
                switch ($menu) {
                    case 'profil':       include "settings_admin/profil.php"; break;
                    case 'password':     include "settings_admin/password.php"; break;
                    case 'sistem':       include "settings_admin/sistem.php"; break;
                    case 'users':        include "settings_admin/users.php"; break;
                    case 'lokasi':       include "settings_admin/lokasi.php"; break;
                    case 'kategori':     include "settings_admin/kategori.php"; break;
                    case 'maintenance':  include "settings_admin/maintenance.php"; break;
                    case 'notif':        include "settings_admin/notif.php"; break;
                    case 'export':       include "settings_admin/export.php"; break;
                    case 'backup':       include "settings_admin/backup.php"; break;
                    case 'admin':        include "settings_admin/admin.php"; break;
                    case 'log':          include "settings_admin/log.php"; break;
                    default:             include "settings_admin/profil.php"; break;
                }
                ?>

            </div>

         </div>
    </div>
</div>

</div>
