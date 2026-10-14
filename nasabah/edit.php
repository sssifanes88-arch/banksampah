<?php
include "koneksi.php";

$id_user = $_GET['id_user'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id_user'");
$data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Nasabah</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=3">
</head>

<body class="role-admin">

    <div class="form-wrap">
        <div class="form-card">

            <div class="icon-top">✏️</div>
            <h1>Edit Nasabah</h1>
            <p class="sub">Ubah data nasabah</p>

            <form action="update.php" method="post">

                <input type="hidden" name="id_user" value="<?php echo $data['id_user'] ?>">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo $data['username'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" value="<?php echo $data['password'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" value="<?php echo $data['nama'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="siswa" <?php echo ($data['role'] == 'siswa') ? 'selected' : ''; ?>>Siswa</option>
                        <option value="guru" <?php echo ($data['role'] == 'guru') ? 'selected' : ''; ?>>Guru</option>
                        <option value="pengepul" <?php echo ($data['role'] == 'pengepul') ? 'selected' : ''; ?>>Pengepul</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Saldo</label>
                    <input type="number" step="0.01" name="saldo" value="<?php echo $data['saldo'] ?>" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>

            </form>

            <a href="index.php" class="form-back">← Kembali ke Data Nasabah</a>

        </div>
    </div>

</body>
</html>
