<?php
require "../config/database.php";

if (!isset($_POST['id'])) exit;

$id = intval($_POST['id']);

mysqli_query($conn, "
    UPDATE pengaduan 
    SET notif_admin = 1 
    WHERE id_pengaduan = $id
");
?>