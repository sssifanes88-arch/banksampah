<?php
include "koneksi.php";
session_start();

if ($_SESSION['status'] <> "sukses" || $_SESSION['role'] <> "pengepul") {
    header('location:logout.php');
    exit;
}

$id_user = $_SESSION['id_user'];

$riwayat = mysqli_query($koneksi, "
    SELECT p.tanggal, s.jenis_sampah, p.berat_kg, p.harga_jual_kg, p.total_rp
    FROM penjualan p
    JOIN sampah s ON p.id_sampah = s.id_sampah
    WHERE p.id_pengepul = '$id_user'
    ORDER BY p.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian - SiDU Bank Sampah</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
</head>

<body class="role-pengepul">

    <div class="app-shell">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="mark">♻</div>
                <div class="txt"><strong>SiDU</strong><span>Bank Sampah Sekolah</span></div>
            </div>
            <nav class="sidebar-nav">
                <p class="sidebar-label">Menu</p>
                <a href="dashboard_pengepul.php"><span class="ic">🏠</span> Beranda</a>
                <a href="riwayat_pembelian.php" class="active"><span class="ic">📜</span> Riwayat Pembelian</a>
                <p class="sidebar-label">Akun</p>
                <a href="logout.php" class="logout"><span class="ic">🚪</span> Keluar</a>
            </nav>
            <div class="sidebar-foot">SiDU © Bank Sampah Sekolah</div>
        </aside>

        <div class="main">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="menu-toggle" id="menuToggle" aria-label="Menu">☰</button>
                    <div>
                        <h1>Riwayat Pembelian</h1>
                        <div class="sub">Semua sampah yang sudah kamu beli dari SiDU</div>
                    </div>
                </div>
                <span class="role-tag">Pengepul</span>
            </div>

            <div class="page">
                <div class="card">
                    <div class="card-head"><h2>Riwayat Pembelian</h2></div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Berat</th>
                                    <th>Harga/Kg</th>
                                    <th>Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($riwayat) === 0) { ?>
                                    <tr><td colspan="5"><div class="empty-state"><span class="emoji">📜</span><p>Belum ada riwayat pembelian</p></div></td></tr>
                                <?php } ?>
                                <?php while ($row = mysqli_fetch_array($riwayat)) { ?>
                                    <tr>
                                        <td><?php echo date("d-m-Y", strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo $row['jenis_sampah']; ?></td>
                                        <td><?php echo $row['berat_kg']; ?> kg</td>
                                        <td>Rp <?php echo number_format($row['harga_jual_kg'], 0, ',', '.'); ?></td>
                                        <td style="font-weight:700;color:var(--pink-600);">Rp <?php echo number_format($row['total_rp'], 0, ',', '.'); ?></td>
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
