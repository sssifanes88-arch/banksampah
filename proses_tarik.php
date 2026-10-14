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

// Cek apakah nasabah ini masih punya pengajuan yang belum diproses (pending)
$cekPending = mysqli_query($koneksi, "SELECT * FROM tarik_saldo WHERE id_user='$id_user' AND status='pending'");

if (mysqli_num_rows($cekPending) > 0) {
    // Sudah ada pengajuan pending, jangan buat pengajuan baru
    header('Location: dashboard_nasabah.php?pesan=sudah_pending');
    exit;
}

$query = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user='$id_user'");
$data = mysqli_fetch_array($query);
$saldo = $data['saldo'];

if ($saldo > 0) {
    $tanggal = date('Y-m-d');
    mysqli_query($koneksi, "INSERT INTO tarik_saldo (id_user, nominal, tanggal, status) VALUES ('$id_user', '$saldo', '$tanggal', 'pending')");
} else {
    header('Location: dashboard_nasabah.php?pesan=saldo_kosong');
    exit;
}

header('Location: dashboard_nasabah.php');
exit;
?>
