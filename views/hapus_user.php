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
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'ID user tidak valid.'];
    header('Location: index.php?page=admin#users');
    exit;
}
$id = intval($_GET['id']);
$ok = mysqli_query($conn, "DELETE FROM users WHERE id_user=$id");
if ($ok && mysqli_affected_rows($conn) > 0) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'User berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'User tidak ditemukan atau gagal dihapus.'];
}
header('Location: index.php?page=admin#users');
exit;
