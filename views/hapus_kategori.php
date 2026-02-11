<?php
include 'config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Akses ditolak!'];
  header('Location: index.php');
  exit;
}

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $cek = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pengaduan WHERE id_kategori=$id"));
  if ($cek > 0) {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Kategori tidak bisa dihapus karena sedang digunakan.'];
    header('Location: index.php?page=admin#kategori');
    exit;
  }
  $ok = mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori=$id");
  if ($ok && mysqli_affected_rows($conn) > 0) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori berhasil dihapus.'];
  } else {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Kategori tidak ditemukan atau gagal dihapus.'];
  }
} else {
  $_SESSION['flash'] = ['type' => 'warning', 'message' => 'ID kategori tidak valid.'];
}

header('Location: index.php?page=admin#kategori');
exit;
