<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  echo "<div class='alert alert-danger'>Akses ditolak!</div>";
  return;
}
global $conn;
if (!isset($conn) || !$conn instanceof mysqli) {
  echo "<div class='alert alert-danger'>Database connection not available.</div>";
  return;
}
if (isset($_POST['backup_db'])) {
  $conn->set_charset('utf8');
  $db_name = mysqli_real_escape_string($conn, $conn->query("SELECT DATABASE()")->fetch_row()[0] ?? '');
  $tables = [];
  $res = $conn->query("SHOW TABLES");
  while ($r = $res->fetch_row()) $tables[] = $r[0];
  $filename = "backup_{$db_name}_" . date('Ymd_His') . ".sql";
  header('Content-Type: application/sql; charset=utf-8');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  header('Cache-Control: no-cache, no-store, must-revalidate');
  header('Pragma: no-cache');
  header('Expires: 0');
  $out = fopen('php://output', 'w');
  fwrite($out, "-- Backup database: " . $db_name . " -- " . PHP_EOL);
  fwrite($out, "-- Generated: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL);

  foreach ($tables as $table) {
    $r = $conn->query("SHOW CREATE TABLE `$table`");
    $row = $r->fetch_assoc();

    fwrite($out, "-- --------------------------------------------------" . PHP_EOL);
    fwrite($out, "-- Struktur tabel untuk `$table`" . PHP_EOL);
    fwrite($out, "DROP TABLE IF EXISTS `$table`;" . PHP_EOL);
    fwrite($out, $row['Create Table'] . ";" . PHP_EOL . PHP_EOL);
    $res2 = $conn->query("SELECT * FROM `$table`");
    $cols = $res2->field_count;

    if ($res2->num_rows > 0) {
      fwrite($out, "-- Data untuk `$table`" . PHP_EOL);
      while ($r2 = $res2->fetch_row()) {
        $vals = [];
        for ($i=0;$i<$cols;$i++) {
          if ($r2[$i] === null) $vals[] = "NULL";
          else $vals[] = "'" . $conn->real_escape_string($r2[$i]) . "'";
        }
        fwrite($out, "INSERT INTO `$table` VALUES(" . implode(",", $vals) . ");" . PHP_EOL);
      }
      fwrite($out, PHP_EOL);
    }
  }

  fclose($out);
  exit;
}
?>

<div class="card shadow-sm">
  <div class="card-body">
    <h5>Backup Database</h5>
    <p class="text-muted">Klik tombol untuk mendownload file .SQL berisi seluruh data.</p>

    <a href="actions/backup_do.php" 
       class="btn btn-danger"
       onclick="return confirm('Download backup database sekarang?')">
       <i class="bi bi-hdd-network"></i> Backup & Download
    </a>
  </div>
</div>