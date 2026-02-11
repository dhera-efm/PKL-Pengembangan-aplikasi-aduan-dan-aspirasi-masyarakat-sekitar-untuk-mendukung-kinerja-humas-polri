</div>

<?php
$SET = [];
$q = mysqli_query($conn, "SELECT * FROM system_settings");
while ($s = mysqli_fetch_assoc($q)) {
    $SET[$s['key']] = $s['value'];
}
$logo = "assets/img/" . ($SET['site_logo'] ?? "default-logo.png");
$get = function (string $key, string $fallback) use ($SET): string {
    $value = $SET[$key] ?? '';
    if (!is_string($value)) {
        return $fallback;
    }
    $trimmed = trim($value);
    return $trimmed !== '' ? $trimmed : $fallback;
};
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.footer-instansi {
    background: #111827;
    color: #e5e7eb;
    padding: 18px 0 12px 0; 
    font-size: 13px;
}

.footer-instansi h5,
.footer-instansi h6 {
    color: #f3f4f6;
    font-weight: 600;
    font-size: 14px;       
}

.footer-instansi a {
    color: #9ca3af;
    text-decoration: none;
    font-size: 13px;
}

.footer-divider {
    border-top: 1px solid #374151;
    margin: 15px 0 10px 0;   
}

.footer-logo {
    width: 55px;              
    filter: brightness(95%);
}

@media (max-width: 768px) {
    .footer-instansi {
        text-align: center;
    }
}
</style>

<footer class="footer-instansi mt-auto">
    <div class="container">
        <div class="row">

            <div class="col-md-4 mb-3 text-center text-md-start">
                <?php if (!empty($SET['site_logo']) && file_exists($logo)): ?>
                    <img src="<?= htmlspecialchars($logo) ?>" class="footer-logo mb-2" alt="Logo">
                <?php endif; ?>
                <h5><?= htmlspecialchars($get('site_name', 'Polres Banjar')) ?></h5>
                <p class="mb-1"><?= htmlspecialchars($get('address', 'Jalan Jenderal Achmad Yani, Kabupaten Banjar, Kalimantan Selatan')) ?></p>
            </div>

            <div class="col-md-4 mb-3">
                <h6>Kontak Resmi</h6>
                <p class="mb-1"><i class="bi bi-envelope"></i> <?= htmlspecialchars($get('email_service', 'humaspolresbanjar@polri.go.id')) ?></p>
                <p class="mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($get('whatsapp', '(0511) 1234567')) ?></p>
                <p class="mb-1"><i class="bi bi-globe"></i> <?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? '') ?></p>
            </div>

            <div class="col-md-4 mb-3 text-center text-md-start">
                <h6>Informasi</h6>
                <ul class="list-unstyled">
                    <li><a href="home">Beranda</a></li>
                    <li><a href="pengaduan">Pengaduan</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="index.php?page=admin">Dashboard Admin</a></li>
                        <li><a href="settings">Pengaturan Sistem</a></li>
                    <?php endif; ?>
                    <?php if (!isset($_SESSION['role'])): ?>
                        <li><a href="login">Masuk</a></li>
                        <li><a href="registrasi">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="footer-divider"></div>

        <div class="text-center small">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($get('site_name', 'Polres Banjar')) ?> — Seluruh Hak Dilindungi.
        </div>
    </div>
</footer>


<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
