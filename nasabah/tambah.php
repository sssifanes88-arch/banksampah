<?php
include "koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Nasabah</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=3">
</head>

<body class="role-admin">

    <div class="form-wrap">
        <div class="form-card">

            <div class="icon-top">👤</div>
            <h1>Tambah Nasabah</h1>
            <p class="sub">Masukkan data nasabah baru</p>

            <form action="simpan.php" method="post">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" placeholder="Masukkan password" required>
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" placeholder="Masukkan nama" required>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                        <option value="pengepul">Pengepul</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Saldo Awal</label>
                    <input type="number" step="0.01" name="saldo" placeholder="0" value="0" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Simpan Data</button>

            </form>

            <a href="index.php" class="form-back">← Kembali ke Data Nasabah</a>

        </div>
    </div>

</body>
</html>
