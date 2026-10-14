<?php
include "koneksi.php";
session_start();

if ($_SESSION['status'] <> "sukses" || $_SESSION['role'] <> "pengepul") {
    header('location:logout.php');
    exit;
}

$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id_user'");
$user = mysqli_fetch_array($query);

// Statistik pembelian bulan ini
$statBulan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS jml, COALESCE(SUM(berat_kg),0) AS total_kg, COALESCE(SUM(total_rp),0) AS total_rp
    FROM penjualan
    WHERE id_pengepul='$id_user' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
");
$bulan = mysqli_fetch_array($statBulan);

// Total keseluruhan
$statTotal = mysqli_query($koneksi, "SELECT COUNT(*) AS jml, COALESCE(SUM(berat_kg),0) AS total_kg FROM penjualan WHERE id_pengepul='$id_user'");
$total = mysqli_fetch_array($statTotal);

// Daftar stok sampah yang tersedia untuk dibeli
$stokSampah = mysqli_query($koneksi, "SELECT * FROM sampah WHERE stok_kg > 0 ORDER BY jenis_sampah");

// Pembelian terakhir
$riwayat = mysqli_query($koneksi, "
    SELECT p.tanggal, s.jenis_sampah, p.berat_kg, p.total_rp
    FROM penjualan p
    JOIN sampah s ON p.id_sampah = s.id_sampah
    WHERE p.id_pengepul = '$id_user'
    ORDER BY p.tanggal DESC
    LIMIT 5
");

// Sapaan sesuai jam
$jam = (int) date('H');
if ($jam < 11) $sapaan = "Selamat pagi";
elseif ($jam < 15) $sapaan = "Selamat siang";
elseif ($jam < 18) $sapaan = "Selamat sore";
else $sapaan = "Selamat malam";

// Pesan status dari proses pembelian
$pesan = null;
$pesanTipe = 'notice';
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'sukses') { $pesan = 'Pembelian berhasil dicatat.'; $pesanTipe = 'sukses'; }
    elseif ($_GET['pesan'] == 'saldo_kurang') { $pesan = 'Saldo kamu tidak cukup untuk pembelian ini.'; }
    elseif ($_GET['pesan'] == 'stok_kurang') { $pesan = 'Stok sampah tidak mencukupi.'; }
    elseif ($_GET['pesan'] == 'input_salah') { $pesan = 'Jumlah kg tidak valid.'; }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengepul - SiDU Bank Sampah</title>
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
                <a href="dashboard_pengepul.php" class="active"><span class="ic">🏠</span> Beranda</a>
                <a href="riwayat_pembelian.php"><span class="ic">📜</span> Riwayat Pembelian</a>
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
                        <h1><?php echo $sapaan; ?>, <?php echo htmlspecialchars($user['nama']); ?></h1>
                        <div class="sub">Beli sampah dari stok Bank Sampah SiDU</div>
                    </div>
                </div>
                <span class="role-tag">Pengepul</span>
            </div>

            <div class="page">

                <div class="balance-card">
                    <div class="lbl">Saldo Beli Kamu Saat Ini</div>
                    <div class="val">Rp <?php echo number_format($user['saldo'], 0, ',', '.'); ?></div>

                    <?php if ($pesan) { ?>
                        <div class="notice"><?php echo htmlspecialchars($pesan); ?></div>
                    <?php } ?>

                    <div class="actions">
                        <a href="riwayat_pembelian.php" class="btn btn-ghost-white">Lihat Riwayat Pembelian</a>
                    </div>
                </div>

                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="ic">📦</div>
                        <div><div class="lbl">Pembelian Bulan Ini</div><div class="val"><?php echo $bulan['jml']; ?>x</div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">⚖️</div>
                        <div><div class="lbl">Kg Dibeli Bulan Ini</div><div class="val"><?php echo number_format($bulan['total_kg'], 1, ',', '.'); ?> kg</div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">💸</div>
                        <div><div class="lbl">Total Dibelanjakan Bulan Ini</div><div class="val">Rp <?php echo number_format($bulan['total_rp'], 0, ',', '.'); ?></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">♻</div>
                        <div><div class="lbl">Total Kg Sepanjang Waktu</div><div class="val"><?php echo number_format($total['total_kg'], 1, ',', '.'); ?> kg</div></div>
                    </div>
                </div>

                <div class="card" style="margin-bottom:20px;">
                    <div class="card-head"><h2>Beli Sampah dari Stok SiDU</h2></div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Jenis Sampah</th>
                                    <th>Stok Tersedia</th>
                                    <th>Harga / Kg</th>
                                    <th class="aksi">Beli</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($stokSampah) === 0) { ?>
                                    <tr><td colspan="4"><div class="empty-state"><span class="emoji">📦</span><p>Belum ada stok sampah tersedia</p></div></td></tr>
                                <?php } ?>
                                <?php while ($s = mysqli_fetch_array($stokSampah)) { ?>
                                    <tr>
                                        <td>
                                            <div class="cell-user">
                                                <div class="avatar">♻</div>
                                                <span class="cell-name"><?php echo $s['jenis_sampah']; ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($s['stok_kg'], 1, ',', '.'); ?> kg</td>
                                        <td>Rp <?php echo number_format($s['harga_jual_kg'], 0, ',', '.'); ?></td>
                                        <td class="aksi">
                                            <form action="beli_proses.php" method="POST" style="display:flex; gap:6px; justify-content:flex-end; align-items:center;">
                                                <input type="hidden" name="id_sampah" value="<?php echo $s['id_sampah']; ?>">
                                                <input type="number" step="0.1" min="0.1" max="<?php echo $s['stok_kg']; ?>" name="berat_kg" placeholder="Kg" required style="width:90px; padding:8px 10px; border:1px solid var(--ink-100); border-radius:8px; font-size:13px;">
                                                <button type="submit" class="btn btn-primary btn-sm">Beli</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head"><h2>Pembelian Terakhir</h2></div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Berat</th>
                                    <th>Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($riwayat) === 0) { ?>
                                    <tr><td colspan="4"><div class="empty-state"><span class="emoji">📜</span><p>Belum ada pembelian</p></div></td></tr>
                                <?php } ?>
                                <?php while ($row = mysqli_fetch_array($riwayat)) { ?>
                                    <tr>
                                        <td><?php echo date("d-m-Y", strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo $row['jenis_sampah']; ?></td>
                                        <td><?php echo $row['berat_kg']; ?> kg</td>
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
