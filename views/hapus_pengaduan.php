<?php
include "config/database.php";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Akses ditolak!'];
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'ID pengaduan tidak valid.'];
    header('Location: index.php?page=admin');
    exit;
}

$id = intval($_GET['id']);
$ok = mysqli_query($conn, "DELETE FROM pengaduan WHERE id_pengaduan = $id");
if ($ok && mysqli_affected_rows($conn) > 0) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Pengaduan berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Pengaduan tidak ditemukan atau gagal dihapus.'];
}

header('Location: index.php?page=admin');
exit;
