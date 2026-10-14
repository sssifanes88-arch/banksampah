<?php
session_start();

if ($_SESSION['status'] <> "sukses" || $_SESSION['role'] <> "admin") {
    header('location:logout.php');
    exit;
}

include "koneksi.php";

// Statistik setoran (dari guru/siswa)
$setoranHariIni = mysqli_fetch_array(mysqli_query($koneksi, "
    SELECT COUNT(*) AS jml, COALESCE(SUM(berat_kg),0) AS kg, COALESCE(SUM(total_rp),0) AS rp
    FROM transaksi WHERE tanggal = CURDATE()
"));
$setoranBulanIni = mysqli_fetch_array(mysqli_query($koneksi, "
    SELECT COUNT(*) AS jml, COALESCE(SUM(berat_kg),0) AS kg, COALESCE(SUM(total_rp),0) AS rp
    FROM transaksi WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
"));

// Statistik penjualan (ke pengepul)
$jualHariIni = mysqli_fetch_array(mysqli_query($koneksi, "
    SELECT COUNT(*) AS jml, COALESCE(SUM(berat_kg),0) AS kg, COALESCE(SUM(total_rp),0) AS rp
    FROM penjualan WHERE tanggal = CURDATE()
"));
$jualBulanIni = mysqli_fetch_array(mysqli_query($koneksi, "
    SELECT COUNT(*) AS jml, COALESCE(SUM(berat_kg),0) AS kg, COALESCE(SUM(total_rp),0) AS rp
    FROM penjualan WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Utama</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
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
                <a href="index.php" class="active"><span class="ic">🏠</span> Beranda</a>
                <a href="nasabah"><span class="ic">👤</span> Nasabah</a>
                <a href="sampah"><span class="ic">🗑️</span> Sampah</a>
                <a href="transaksi"><span class="ic">📥</span> Setoran</a>
                <a href="tarik_saldo"><span class="ic">💰</span> Tarik Saldo</a>
                <a href="penjualan"><span class="ic">🧾</span> Penjualan</a>
                <a href="topup"><span class="ic">🔋</span> Top Up Pengepul</a>
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
                        <h1>Menu Utama</h1>
                        <div class="sub">Halo Admin, selamat datang kembali</div>
                    </div>
                </div>
                <span class="role-tag">Admin</span>
            </div>

            <div class="page">
                <div class="page-head">
                    <h1 style="font-size:15px; font-weight:700; color:var(--ink-900);">Statistik Hari Ini</h1>
                    <div></div>
                </div>
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="ic">📥</div>
                        <div><div class="lbl">Setoran Hari Ini</div><div class="val"><?php echo $setoranHariIni['jml']; ?>x &middot; <?php echo number_format($setoranHariIni['kg'], 1, ',', '.'); ?> kg</div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">💵</div>
                        <div><div class="lbl">Dibayar ke Nasabah Hari Ini</div><div class="val">Rp <?php echo number_format($setoranHariIni['rp'], 0, ',', '.'); ?></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">🧾</div>
                        <div><div class="lbl">Penjualan ke Pengepul Hari Ini</div><div class="val">Rp <?php echo number_format($jualHariIni['rp'], 0, ',', '.'); ?></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">⚖️</div>
                        <div><div class="lbl">Kg Terjual ke Pengepul Hari Ini</div><div class="val"><?php echo number_format($jualHariIni['kg'], 1, ',', '.'); ?> kg</div></div>
                    </div>
                </div>

                <div class="page-head" style="margin-top:6px;">
                    <h1 style="font-size:15px; font-weight:700; color:var(--ink-900);">Statistik Bulan Ini</h1>
                    <div></div>
                </div>
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="ic">📥</div>
                        <div><div class="lbl">Setoran Bulan Ini</div><div class="val"><?php echo $setoranBulanIni['jml']; ?>x &middot; <?php echo number_format($setoranBulanIni['kg'], 1, ',', '.'); ?> kg</div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">💵</div>
                        <div><div class="lbl">Dibayar ke Nasabah Bulan Ini</div><div class="val">Rp <?php echo number_format($setoranBulanIni['rp'], 0, ',', '.'); ?></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">🧾</div>
                        <div><div class="lbl">Penjualan ke Pengepul Bulan Ini</div><div class="val">Rp <?php echo number_format($jualBulanIni['rp'], 0, ',', '.'); ?></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">📈</div>
                        <div><div class="lbl">Estimasi Margin Bulan Ini</div><div class="val">Rp <?php echo number_format($jualBulanIni['rp'] - $setoranBulanIni['rp'], 0, ',', '.'); ?></div></div>
                    </div>
                </div>

                <div class="menu-grid">
                    <a href="nasabah" class="menu-card">
                        <div class="ic">👤</div>
                        <div><h3>Nasabah</h3><p>Kelola data nasabah</p></div>
                    </a>
                    <a href="sampah" class="menu-card">
                        <div class="ic">🗑️</div>
                        <div><h3>Sampah</h3><p>Jenis &amp; harga sampah</p></div>
                    </a>
                    <a href="transaksi" class="menu-card">
                        <div class="ic">📥</div>
                        <div><h3>Setoran</h3><p>Data transaksi setoran</p></div>
                    </a>
                    <a href="tarik_saldo" class="menu-card">
                        <div class="ic">💰</div>
                        <div><h3>Tarik Saldo</h3><p>Pengajuan penarikan nasabah</p></div>
                    </a>
                    <a href="penjualan" class="menu-card">
                        <div class="ic">🧾</div>
                        <div><h3>Penjualan</h3><p>Riwayat penjualan ke pengepul</p></div>
                    </a>
                    <a href="topup" class="menu-card">
                        <div class="ic">🔋</div>
                        <div><h3>Top Up Pengepul</h3><p>Isi saldo beli pengepul</p></div>
                    </a>
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
