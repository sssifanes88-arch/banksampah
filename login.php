<?php
include "koneksi.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");

    $cek = mysqli_num_rows($query);

    if ($cek > 0) {

        $data = mysqli_fetch_array($query);

        session_start();

        $_SESSION['status']   = "sukses";
        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama']     = $data['nama'];
        $_SESSION['role']     = $data['role'];

        if ($data['role'] == "admin") {
            header('location:index.php');
        } elseif ($data['role'] == "pengepul") {
            header('location:dashboard_pengepul.php');
        } else {
            header('location:dashboard_nasabah.php');
        }
        exit;
    } else {
        $pesan = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiDU Bank Sampah</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
</head>

<body>

    <div class="auth-wrap">
        <div class="auth-card">

            <div class="auth-mark">♻</div>
            <h1>SiDU Bank Sampah</h1>
            <p class="sub">Masuk untuk melanjutkan</p>

            <?php if (isset($pesan)) { ?>
                <div class="auth-error"><?php echo $pesan; ?></div>
            <?php } ?>

            <form action="" method="post">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>

            </form>

            <div class="auth-foot">Sistem Informasi Bank Sampah Sekolah</div>

        </div>
    </div>

</body>
</html>
