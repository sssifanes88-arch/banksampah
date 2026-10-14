<?php
include "koneksi.php";
session_start();

if ($_SESSION['status'] <> "sukses" || $_SESSION['role'] <> "admin") {
    header('location:../logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nasabah</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=3">
</head>

<body class="role-admin">

    <div class="app-shell">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="mark">♻</div>
                <div class="txt"><strong>SiDU</strong><span>Bank Sampah Sekolah</span></div>
            </div>
            <nav class="sidebar-nav">
                <p class="sidebar-label">Menu</p>
                <a href="../index.php"><span class="ic">🏠</span> Beranda</a>
                <a href="../nasabah" class="active"><span class="ic">👤</span> Nasabah</a>
                <a href="../sampah"><span class="ic">🗑️</span> Sampah</a>
                <a href="../transaksi"><span class="ic">📥</span> Setoran</a>
                <a href="../tarik_saldo"><span class="ic">💰</span> Tarik Saldo</a>
                <a href="../penjualan"><span class="ic">🧾</span> Penjualan</a>
                <a href="../topup"><span class="ic">🔋</span> Top Up Pengepul</a>
                <p class="sidebar-label">Akun</p>
                <a href="../logout.php" class="logout"><span class="ic">🚪</span> Keluar</a>
            </nav>
            <div class="sidebar-foot">SiDU © Bank Sampah Sekolah</div>
        </aside>

        <div class="main">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="menu-toggle" id="menuToggle" aria-label="Menu">☰</button>
                    <div>
                        <h1>Data Nasabah</h1>
                        <div class="sub">Daftar nasabah bank sampah</div>
                    </div>
                </div>
            </div>

            <div class="page">
                <div class="page-head">
                    <div></div>
                    <a href="tambah.php" class="btn btn-primary">+ Tambah Data</a>
                </div>

                <div class="card">
                    <div class="card-head"><h2>Daftar Nasabah</h2></div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Saldo</th>
                                    <th class="aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = mysqli_query($koneksi, "SELECT * FROM users WHERE role != 'admin'");
                                if (mysqli_num_rows($query) === 0) {
                                ?>
                                    <tr><td colspan="5">
                                        <div class="empty-state"><span class="emoji">👤</span><p>Belum ada data nasabah</p></div>
                                    </td></tr>
                                <?php
                                }
                                while ($data = mysqli_fetch_array($query)) {
                                ?>
                                    <tr>
                                        <td><?php echo $data['username']; ?></td>
                                        <td>
                                            <div class="cell-user">
                                                <div class="avatar"><?php echo strtoupper(substr($data['nama'], 0, 1)); ?></div>
                                                <span class="cell-name"><?php echo $data['nama']; ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-neutral"><?php echo ucfirst($data['role']); ?></span></td>
                                        <td>Rp <?php echo number_format($data['saldo'], 0, ',', '.'); ?></td>
                                        <td class="aksi">
                                            <a href="edit.php?id_user=<?php echo $data['id_user']; ?>" class="btn btn-outline btn-sm">Edit</a>
                                            <a href="hapus.php?id_user=<?php echo $data['id_user']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn btn-danger-ghost btn-sm">Hapus</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('menuToggle')?.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>

</body>
</html>
