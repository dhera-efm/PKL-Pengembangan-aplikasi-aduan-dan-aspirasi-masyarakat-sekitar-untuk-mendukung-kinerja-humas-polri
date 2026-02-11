<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['id_user'])) {
  exit('Anda belum login.');
}

$id_user = $_SESSION['id_user'];
$id_pengaduan = $_POST['id_pengaduan'];
$rating = $_POST['rating'];
$komentar = $_POST['komentar'];

$cek = mysqli_query($conn, "SELECT * FROM feedback WHERE id_pengaduan = '$id_pengaduan' AND id_user = '$id_user'");
if (mysqli_num_rows($cek) > 0) {
  mysqli_query($conn, "UPDATE feedback SET rating='$rating', komentar='$komentar', tanggal=NOW() WHERE id_pengaduan='$id_pengaduan' AND id_user='$id_user'");
  echo "Feedback berhasil diperbarui!";
} else {
  mysqli_query($conn, "INSERT INTO feedback (id_pengaduan, id_user, rating, komentar) VALUES ('$id_pengaduan', '$id_user', '$rating', '$komentar')");
  echo "Terima kasih atas feedback Anda!";
}
?>