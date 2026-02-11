<?php
require "config/database.php";

$id = intval($_GET['id']);
$b = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM berita WHERE id_berita=$id"));
?>

<h2><?= htmlspecialchars($b['judul']) ?></h2>
<p class="text-muted"><?= date('d M Y', strtotime($b['tanggal'])) ?> • <?= $b['penulis'] ?></p>

<div style="text-align:center;">
    <img src="assets/img/<?= $b['gambar']?>" 
         style="max-width:500px; width:100%; height:auto;"
         class="mb-4">
</div>

<p style="line-height:1.7; text-align:justify;">
    <?= nl2br(html_entity_decode($b['isi'])) ?>
</p>

<a href="index.php?page=berita_public" class="btn btn-secondary mt-4">← Kembali</a>
