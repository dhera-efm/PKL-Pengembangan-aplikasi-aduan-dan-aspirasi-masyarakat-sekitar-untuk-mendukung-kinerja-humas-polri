<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($conn)) {
    require_once __DIR__ . '/../config/database.php';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Akses ditolak');
}

function redirect_with_fallback(string $url): void
{
    if (!headers_sent()) {
        header("Location: $url");
        exit;
    }

    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo '<script>window.location.href=' . json_encode($url) . ';</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . $safeUrl . '"></noscript>';
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$aksi = $_GET['aksi'] ?? '';
$allowedAksi = ['terima', 'tolak', 'tampil'];
$isDirectViewScript = basename($_SERVER['SCRIPT_NAME'] ?? '') === 'ubah_feedback.php';
$redirectUrl = $isDirectViewScript
    ? '../index.php?page=admin&active_tab=feedback'
    : 'index.php?page=admin&active_tab=feedback';

if ($id <= 0 || !in_array($aksi, $allowedAksi, true)) {
    $_SESSION['alert'] = 'Aksi feedback tidak valid.';
    $_SESSION['active_tab'] = 'feedback';
    redirect_with_fallback($redirectUrl);
}

$sql = '';
switch ($aksi) {
    case 'terima':
        $sql = "UPDATE feedback SET status='diterima' WHERE id_feedback=$id";
        $_SESSION['alert'] = 'Feedback diterima!';
        break;
    case 'tolak':
        $sql = "UPDATE feedback SET status='ditolak', tampil=0 WHERE id_feedback=$id";
        $_SESSION['alert'] = 'Feedback ditolak!';
        break;
    case 'tampil':
        $sql = "UPDATE feedback SET tampil=1, status='diterima' WHERE id_feedback=$id";
        $_SESSION['alert'] = 'Feedback kini tampil di halaman utama!';
        break;
}

if (!mysqli_query($conn, $sql)) {
    $_SESSION['alert'] = 'Gagal memproses feedback.';
}

$_SESSION['active_tab'] = 'feedback';
redirect_with_fallback($redirectUrl);
?>
