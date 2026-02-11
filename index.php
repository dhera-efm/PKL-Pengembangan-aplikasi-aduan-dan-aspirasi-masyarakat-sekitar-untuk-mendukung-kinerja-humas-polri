<?php
ob_start();
session_start();

include 'config/database.php';
include 'routes/routes.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$settings = [];
$q = mysqli_query($conn, "SELECT * FROM system_settings");
while ($s = mysqli_fetch_assoc($q)) {
    $settings[$s['key']] = $s['value'];
}

// Ambil variabel penting untuk kebutuhan maintenance
$maintenance = $settings['maintenance_mode'] ?? '0';
$title       = $settings['maintenance_title'] ?? "Website Dalam Perawatan";
$msg         = $settings['maintenance_msg'] ?? "Website sedang dalam pemeliharaan";

// Selalu bypass maintenance saat akses dari localhost untuk kebutuhan testing.
$host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
$hostWithoutPort = explode(':', $host)[0];
$isLocalhost = in_array($hostWithoutPort, ['localhost', '127.0.0.1', '::1'], true);
if ($isLocalhost) {
    $maintenance = '0';
}

// ==============================
// MAINTENANCE MODE
// ==============================
if ($maintenance == '1') {

    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {

    }

    elseif (!isset($_SESSION['role'])) {

        if ($page == "login") {
        } else {
            include "views/maintenance_page.php";
            exit;
        }
    }

    else {
        include "views/maintenance_page.php";
        exit;
    }
}

include 'views/header.php';
include route($page);
include 'views/footer.php';

