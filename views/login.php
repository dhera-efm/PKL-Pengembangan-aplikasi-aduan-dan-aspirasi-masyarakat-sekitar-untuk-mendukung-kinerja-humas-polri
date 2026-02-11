<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1");

    if (mysqli_num_rows($cek) > 0) {

        $user = mysqli_fetch_assoc($cek);

        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        if ($user['role'] == 'admin') {
            header("Location: admin");
            exit;
        } else if ($user['role'] == 'user') {
            header("Location: user");
            exit;
        } else {
            header("Location: home");
            exit;
        }
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-lg border-0">
        <div class="card-body text-center">
          <img src="assets/img/logo-polri.png" width="90" class="mb-3">
          <h4 class="mb-3 fw-bold text-primary">Login Sistem Pengaduan</h4>
          <p class="text-muted mb-4">Polres Banjar - Kalimantan Selatan</p>

          <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3 text-start">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <div class="mb-3 text-start">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
          </form>

          <div class="mt-3">
            <small>Belum punya akun? <a href="registrasi" class="text-decoration-none">Daftar di sini</a></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
