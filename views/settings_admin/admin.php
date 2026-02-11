<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='alert alert-danger'>Akses ditolak!</div>");
}

function log_admin($conn, $id_admin, $aktivitas) {
    $ip  = $_SERVER['REMOTE_ADDR'] ?? '-';
    $ua  = $_SERVER['HTTP_USER_AGENT'] ?? '-';

    mysqli_query($conn, "
        INSERT INTO admin_log (id_admin, aktivitas, ip_address, user_agent)
        VALUES ('$id_admin', '$aktivitas', '$ip', '$ua')
    ");
}

if (isset($_POST['add_admin'])) {

    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $pass   = trim($_POST['password']);
    $hash   = md5($pass);

    $cek = mysqli_query($conn, "SELECT id_user FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Email sudah digunakan.'];
        header('Location: index.php?page=settings&menu=admin');
        exit;
    } else {

        $ok = mysqli_query($conn, "
            INSERT INTO users (nama_lengkap, email, password, role)
            VALUES ('$nama', '$email', '$hash', 'admin')
        ");

        log_admin($conn, $_SESSION['id_user'], "Menambah admin baru: $nama");

        if ($ok) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Admin berhasil ditambahkan.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menambahkan admin.'];
        }
        header('Location: index.php?page=settings&menu=admin');
        exit;
    }
}

if (isset($_POST['update_admin'])) {

    $id     = intval($_POST['id']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $pass   = trim($_POST['password']);

    if (!empty($pass)) {
        $hash = md5($pass);

        $ok = mysqli_query($conn, "
            UPDATE users SET 
                nama_lengkap='$nama',
                email='$email',
                password='$hash'
            WHERE id_user=$id
        ");

        log_admin($conn, $_SESSION['id_user'], "Mengubah Password (ID $id) + ganti password");

    } else {

        $ok = mysqli_query($conn, "
            UPDATE users SET 
                nama_lengkap='$nama',
                email='$email'
            WHERE id_user=$id
        ");

        log_admin($conn, $_SESSION['id_user'], "Mengubah email admin (ID $id)");
    }

    if ($ok) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Admin berhasil diperbarui.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui admin.'];
    }
    header('Location: index.php?page=settings&menu=admin');
    exit;
}

if (isset($_GET['hapus'])) {

    $id = intval($_GET['hapus']);

    if ($id == $_SESSION['id_user']) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'];
    } else {
        $ok = mysqli_query($conn, "DELETE FROM users WHERE id_user=$id");

        log_admin($conn, $_SESSION['id_user'], "Menghapus admin (ID $id)");

        if ($ok && mysqli_affected_rows($conn) > 0) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Admin berhasil dihapus.'];
        } else {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Admin tidak ditemukan atau gagal dihapus.'];
        }
    }

    header('Location: index.php?page=settings&menu=admin');
    exit;
}

?>


<h4 class="mb-3">Kelola Admin</h4>

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAddAdmin">
    <i class="bi bi-person-plus"></i> Tambah Admin Baru
</button>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Admin</th>
                <th>Email</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM users WHERE role='admin' ORDER BY id_user ASC");
            $no = 1;

            while ($row = mysqli_fetch_assoc($q)):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <button class="btn btn-sm btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEdit<?= $row['id_user'] ?>">
                        Edit
                    </button>

                    <?php if ($row['id_user'] != $_SESSION['id_user']): ?>
                        <a href="settings?menu=admin&hapus=<?= $row['id_user'] ?>"
                            onclick="return confirm('Yakin hapus admin ini?')"
                            class="btn btn-sm btn-danger">
                            Hapus
                        </a>
                    <?php endif; ?>
                </td>
            </tr>

            <div class="modal fade" id="modalEdit<?= $row['id_user'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  
                  <form method="post">
                    <input type="hidden" name="id" value="<?= $row['id_user'] ?>">

                    <div class="modal-header">
                      <h5 class="modal-title">Edit Admin</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                      <div class="mb-2">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required>
                      </div>

                      <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($row['email']) ?>" required>
                      </div>

                      <div class="mb-2">
                        <label>Password Baru (opsional)</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Kosongkan jika tidak ingin ganti password">
                      </div>

                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" name="update_admin" class="btn btn-warning">Simpan</button>
                    </div>

                  </form>
                </div>
              </div>
            </div>

            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalAddAdmin" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="post">

        <div class="modal-header">
          <h5 class="modal-title">Tambah Admin Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-2">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="add_admin" class="btn btn-primary">Tambah</button>
        </div>

      </form>

    </div>
  </div>
</div>
