<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitasi input untuk keamanan
    $nama   = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email  = mysqli_real_escape_string($conn, trim($_POST['email']));
    $no_hp  = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $pass   = mysqli_real_escape_string($conn, trim($_POST['password']));
    $hash   = md5($pass);

    // Cek apakah email sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $alert = "<div class='alert alert-danger'>Email sudah terdaftar!</div>";
    } else {
        // Simpan ke database
        $query = "
            INSERT INTO users (nama_lengkap, email, password, role, no_hp, alamat)
            VALUES ('$nama', '$email', '$hash', 'user', '$no_hp', '$alamat')
        ";
        if (mysqli_query($conn, $query)) {
            $alert = "<div class='alert alert-success'>Registrasi berhasil! Silakan login.</div>";
        } else {
            $alert = "<div class='alert alert-danger'>Terjadi kesalahan, coba lagi.</div>";
        }
    }
}
?>

<div class="container mt-4 mb-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-center mb-4">Register Akun Baru</h3>

          <?= $alert ?? '' ?>

          <form method="post">
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap Anda">
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required placeholder="Masukkan alamat email aktif">
            </div>

            <div class="mb-3">
              <label class="form-label">Nomor HP</label>
              <input type="text" name="no_hp" class="form-control" required placeholder="Masukkan nomor HP Anda">
            </div>

            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" class="form-control" rows="3" required placeholder="Masukkan alamat lengkap Anda"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required placeholder="Masukkan password minimal 6 karakter">
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <a href="login" class="text-decoration-none">Sudah punya akun?</a>
              <button type="submit" class="btn btn-primary">Daftar Sekarang</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
