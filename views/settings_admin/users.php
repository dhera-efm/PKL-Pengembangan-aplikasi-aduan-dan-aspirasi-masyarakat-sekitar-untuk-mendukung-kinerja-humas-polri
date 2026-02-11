<?php
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak!</div>";
    exit;
}

echo "<h4 class='mb-3'>Kelola User</h4>";

if (isset($_POST['add_user'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<div class='alert alert-danger'>Email sudah digunakan!</div>";
    } else {
        mysqli_query($conn, "
            INSERT INTO users(nama_lengkap,email,password,role)
            VALUES('$nama','$email','$password','user')
        ");

        $ida = $_SESSION['id_user'];
        mysqli_query($conn,"
            INSERT INTO admin_log(id_admin,aksi,waktu)
            VALUES($ida,'Menambah User Baru ($nama)',NOW())
        ");

        echo "<div class='alert alert-success'>User berhasil ditambahkan!</div>";
    }
}

if (isset($_POST['edit_user'])) {

    $id  = intval($_POST['id_user']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    mysqli_query($conn, "
        UPDATE users SET 
        nama_lengkap='$nama',
        email='$email'
        WHERE id_user=$id
    ");

    $ida = $_SESSION['id_user'];
    mysqli_query($conn,"
        INSERT INTO admin_log(id_admin,aksi,waktu)
        VALUES($ida,'Mengedit User ID $id',NOW())
    ");

    echo "<div class='alert alert-success'>Data user berhasil diperbarui!</div>";
}

if (isset($_POST['reset_pw'])) {
    $id = intval($_POST['id_user']);
    $new_pass = md5("123456");

    mysqli_query($conn,"
        UPDATE users SET password='$new_pass'
        WHERE id_user=$id
    ");

    $ida = $_SESSION['id_user'];
    mysqli_query($conn,"
        INSERT INTO admin_log(id_admin,aksi,waktu)
        VALUES($ida,'Reset Password User ID $id',NOW())
    ");

    echo "<div class='alert alert-success'>Password user direset ke <b>123456</b></div>";
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    mysqli_query($conn, "DELETE FROM users WHERE id_user=$id");

    $ida = $_SESSION['id_user'];
    mysqli_query($conn,"
        INSERT INTO admin_log(id_admin,aksi,waktu)
        VALUES($ida,'Menghapus User ID $id',NOW())
    ");

    echo "<div class='alert alert-success'>User berhasil dihapus!</div>";
}

$search = $_GET['search'] ?? "";
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$whereSQL = "WHERE role='user'";

if ($search !== "") {
    $s = mysqli_real_escape_string($conn, $search);
    $whereSQL .= " AND (nama_lengkap LIKE '%$s%' OR email LIKE '%$s%') ";
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS jml FROM users $whereSQL
"))['jml'];

$users = mysqli_query($conn,"
    SELECT * FROM users 
    $whereSQL
    ORDER BY id_user DESC
    LIMIT $offset,$limit
");

?>
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        Tambah User Baru
    </div>
    <div class="card-body">
        <form method="post">

            <div class="row">
                <div class="col-md-4 mb-2">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <button class="btn btn-success mt-2" name="add_user">
                Tambah User
            </button>
        </form>
    </div>
</div>

<form class="mb-3" method="get">
    <input type="hidden" name="page" value="settings">
    <input type="hidden" name="menu" value="users">

    <div class="input-group">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
               class="form-control" placeholder="Cari nama atau email">

        <button class="btn btn-primary">Cari</button>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php
        if (mysqli_num_rows($users) == 0):
        ?>
            <tr><td colspan="4" class="text-muted">Tidak ada data</td></tr>
        <?php
        else:
            $no = $offset + 1;
            while ($u = mysqli_fetch_assoc($users)):
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>

                <td class="d-flex justify-content-center gap-1">

                    <button class="btn btn-sm btn-warning" 
                            data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $u['id_user'] ?>">
                        Edit
                    </button>

                    <form method="post" onsubmit="return confirm('Reset password ke 123456?')">
                        <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">
                        <button class="btn btn-sm btn-secondary" name="reset_pw">
                            Reset PW
                        </button>
                    </form>

                    <a href="settings?page=settings&menu=users&hapus=<?= $u['id_user'] ?>"
                       onclick="return confirm('Yakin hapus user ini?')"
                       class="btn btn-sm btn-danger">
                       Hapus
                    </a>

                </td>
            </tr>
            <div class="modal fade" id="editModal<?= $u['id_user'] ?>">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <form method="post">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">

                            <div class="mb-2">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control"
                                       value="<?= htmlspecialchars($u['nama_lengkap']) ?>" required>
                            </div>

                            <div class="mb-2">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= htmlspecialchars($u['email']) ?>" required>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" name="edit_user">Simpan</button>
                        </div>

                        </form>

                    </div>
                </div>
            </div>

        <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>
<?php
$pages = ceil($total / $limit);
?>

<nav class="mt-3">
    <ul class="pagination justify-content-center">

    <?php if ($page > 1): ?>
        <li class="page-item">
            <a class="page-link" href="?page=settings&menu=users&p=<?= $page-1 ?>">Prev</a>
        </li>
    <?php endif; ?>

    <?php for ($i=1; $i <= $pages; $i++): ?>
        <li class="page-item <?= ($i==$page?'active':'') ?>">
            <a class="page-link" href="?page=settings&menu=users&p=<?= $i ?>">
                <?= $i ?>
            </a>
        </li>
    <?php endfor; ?>

    <?php if ($page < $pages): ?>
        <li class="page-item">
            <a class="page-link" href="?page=settings&menu=users&p=<?= $page+1 ?>">Next</a>
        </li>
    <?php endif; ?>

    </ul>
</nav>