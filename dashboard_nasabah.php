<?php
include "koneksi.php";
session_start();

if ($_SESSION['status'] <> "sukses" || $_SESSION['role'] == "admin") {
    header('location:logout.php');
    exit;
}
if ($_SESSION['role'] == "pengepul") {
    header('location:dashboard_pengepul.php');
    exit;
}

$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id_user'");
$user = mysqli_fetch_array($query);

// Cek apakah nasabah masih punya pengajuan tarik saldo yang pending
$cekPending = mysqli_query($koneksi, "SELECT * FROM tarik_saldo WHERE id_user='$id_user' AND status='pending'");
$sedangPending = mysqli_num_rows($cekPending) > 0;

// Total jumlah setoran & total berat
$statQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total_setoran, COALESCE(SUM(berat_kg),0) AS total_berat FROM transaksi WHERE id_user='$id_user'");
$stat = mysqli_fetch_array($statQuery);
$total_setoran = $stat['total_setoran'];
$total_berat = $stat['total_berat'];

// Estimasi dampak lingkungan (perkiraan kasar, boleh disesuaikan)
$co2_terhindar = $total_berat * 1.5;   // kg CO2 setara per kg sampah didaur ulang
$energi_hemat = $total_berat * 0.45;   // kWh setara per kg sampah didaur ulang

// Data setoran 6 bulan terakhir (untuk grafik)
$namaBulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];

$bulanData = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime("-$i month");
    $key = date('Y-m', $ts);
    $bulanData[$key] = ['label' => $namaBulan[date('m', $ts)], 'total' => 0];
}

$chartQuery = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(tanggal, '%Y-%m') AS ym, SUM(berat_kg) AS total
    FROM transaksi
    WHERE id_user='$id_user' AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY ym
");
while ($row = mysqli_fetch_array($chartQuery)) {
    if (isset($bulanData[$row['ym']])) {
        $bulanData[$row['ym']]['total'] = (float) $row['total'];
    }
}

$chartLabels = array_map(fn($d) => $d['label'], $bulanData);
$chartValues = array_map(fn($d) => $d['total'], $bulanData);

// Riwayat setoran terakhir
$riwayat = mysqli_query($koneksi, "
    SELECT t.tanggal, s.jenis_sampah, t.total_rp
    FROM transaksi t
    JOIN sampah s ON t.id_sampah = s.id_sampah
    WHERE t.id_user = '$id_user'
    ORDER BY t.tanggal DESC
    LIMIT 5
");

// Sapaan sesuai jam
$jam = (int) date('H');
if ($jam < 11) $sapaan = "Selamat pagi";
elseif ($jam < 15) $sapaan = "Selamat siang";
elseif ($jam < 18) $sapaan = "Selamat sore";
else $sapaan = "Selamat malam";

// Warna grafik menyesuaikan tema role yang sedang login
$warnaChart = [
    'siswa'    => '#0ea5e9',
    'guru'     => '#0ea5e9',
    'pengepul' => '#0ea5e9',
];
$warnaBar = $warnaChart[$_SESSION['role']] ?? '#0ea5e9';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SiDU Bank Sampah</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>

<body class="role-<?php echo htmlspecialchars($_SESSION['role']); ?>">

    <div class="app-shell">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="mark">♻</div>
                <div class="txt"><strong>SiDU</strong><span>Bank Sampah Sekolah</span></div>
            </div>
            <nav class="sidebar-nav">
                <p class="sidebar-label">Menu</p>
                <a href="dashboard_nasabah.php" class="active"><span class="ic">🏠</span> Beranda</a>
                <a href="dashboard_nasabah.php#saldo"><span class="ic">💰</span> Tarik Saldo</a>
                <a href="riwayat.php"><span class="ic">📜</span> Riwayat</a>
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
                        <div class="sub">Selamat datang kembali di SiDU Bank Sampah</div>
                    </div>
                </div>
                <span class="role-tag"><?php echo ucfirst($_SESSION['role']); ?></span>
            </div>

            <div class="page">

                <div id="saldo" class="balance-card">
                    <div class="lbl">Saldo tabungan Anda saat ini</div>
                    <div class="val">Rp <?php echo number_format($user['saldo'], 0, ',', '.'); ?></div>

                    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'sudah_pending') { ?>
                        <div class="notice">Kamu masih punya pengajuan tarik saldo yang belum diproses admin.</div>
                    <?php } elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'saldo_kosong') { ?>
                        <div class="notice">Saldo kamu masih Rp 0, belum bisa mengajukan tarik saldo.</div>
                    <?php } ?>

                    <div class="actions">
                        <?php if ($sedangPending) { ?>
                            <button type="button" disabled class="btn btn-ghost-white" style="cursor:not-allowed;">Menunggu Persetujuan</button>
                        <?php } else { ?>
                            <form action="proses_tarik.php" method="POST" style="margin:0;">
                                <button type="submit" class="btn btn-white">Tarik Saldo</button>
                            </form>
                        <?php } ?>
                        <a href="riwayat.php" class="btn btn-ghost-white">Lihat Riwayat</a>
                    </div>
                </div>

                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="ic">♻</div>
                        <div><div class="lbl">Total Setoran</div><div class="val"><?php echo $total_setoran; ?>x</div></div>
                    </div>
                    <div class="stat-card">
                        <div class="ic">🗑️</div>
                        <div><div class="lbl">Total Sampah Disetor</div><div class="val"><?php echo number_format($total_berat, 1, ',', '.'); ?> kg</div></div>
                    </div>
                </div>

                <div class="impact-card">
                    <div class="title">Dampak Lingkunganmu</div>
                    <div class="grid-2">
                        <div class="impact-item">
                            <span style="font-size:20px;">☁️</span>
                            <div>
                                <div class="num"><?php echo number_format($co2_terhindar, 1, ',', '.'); ?> kg</div>
                                <div class="lbl">Estimasi emisi CO₂ yang terhindar</div>
                            </div>
                        </div>
                        <div class="impact-item">
                            <span style="font-size:20px;">⚡</span>
                            <div>
                                <div class="num"><?php echo number_format($energi_hemat, 2, ',', '.'); ?> kWh</div>
                                <div class="lbl">Estimasi energi yang dihemat</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="card card-pad">
                        <h2 style="font-size:14px;font-weight:700;margin-bottom:12px;">Setoran 6 Bulan Terakhir</h2>
                        <canvas id="chartSetoran" height="180"></canvas>
                    </div>

                    <div class="card">
                        <div class="card-head"><h2>Setoran Terakhir</h2></div>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($riwayat) === 0) { ?>
                                        <tr><td colspan="3"><div class="empty-state"><span class="emoji">📜</span><p>Belum ada setoran</p></div></td></tr>
                                    <?php } ?>
                                    <?php while ($row = mysqli_fetch_array($riwayat)) { ?>
                                        <tr>
                                            <td><?php echo date("d-m-Y", strtotime($row['tanggal'])); ?></td>
                                            <td><?php echo $row['jenis_sampah']; ?></td>
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

    </div>

    <script>
        document.getElementById('menuToggle')?.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('open');
        });

        const ctx = document.getElementById('chartSetoran');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_values($chartLabels)); ?>,
                datasets: [{
                    label: 'Kg Sampah',
                    data: <?php echo json_encode(array_values($chartValues)); ?>,
                    backgroundColor: '<?php echo $warnaBar; ?>',
                    borderRadius: 6,
                    maxBarThickness: 36
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f2f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</body>
</html>
