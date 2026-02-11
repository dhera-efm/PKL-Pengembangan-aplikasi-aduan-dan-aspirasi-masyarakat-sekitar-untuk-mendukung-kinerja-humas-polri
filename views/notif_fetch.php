<?php
require "../config/database.php";

$res = mysqli_query($conn, "
    SELECT p.id_pengaduan, p.judul, p.created_at, u.nama_lengkap
    FROM pengaduan p
    LEFT JOIN users u ON p.id_user = u.id_user
    WHERE p.notif_admin = 0
    ORDER BY p.created_at DESC
");

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>