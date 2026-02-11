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
  $ok = mysqli_query($conn, "DELETE FROM berita WHERE id_berita=$id");
  if ($ok && mysqli_affected_rows($conn) > 0) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Berita berhasil dihapus.'];
  } else {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Berita tidak ditemukan atau gagal dihapus.'];
  }
} else {
  $_SESSION['flash'] = ['type' => 'warning', 'message' => 'ID berita tidak valid.'];
}

header('Location: index.php?page=admin#berita');
exit;
