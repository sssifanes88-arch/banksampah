<?php
include "koneksi.php";
session_start();

if ($_SESSION['status'] <> "sukses" || $_SESSION['role'] <> "pengepul") {
    header('location:logout.php');
    exit;
}

$id_user = $_SESSION['id_user'];
$id_sampah = isset($_POST['id_sampah']) ? (int) $_POST['id_sampah'] : 0;
$berat_kg = isset($_POST['berat_kg']) ? (float) $_POST['berat_kg'] : 0;

if ($id_sampah <= 0 || $berat_kg <= 0) {
    header('Location: dashboard_pengepul.php?pesan=input_salah');
    exit;
}

// Ambil data sampah (stok & harga jual) dan lock sederhana lewat query ulang
$cekSampah = mysqli_query($koneksi, "SELECT * FROM sampah WHERE id_sampah='$id_sampah'");
$sampah = mysqli_fetch_array($cekSampah);

if (!$sampah || $berat_kg > (float) $sampah['stok_kg']) {
    header('Location: dashboard_pengepul.php?pesan=stok_kurang');
    exit;
}

$total_rp = $berat_kg * $sampah['harga_jual_kg'];

// Ambil saldo pengepul
$cekUser = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user='$id_user'");
$dataUser = mysqli_fetch_array($cekUser);

if (!$dataUser || $total_rp > (float) $dataUser['saldo']) {
    header('Location: dashboard_pengepul.php?pesan=saldo_kurang');
    exit;
}

$tanggal = date('Y-m-d');
$harga_jual_kg = $sampah['harga_jual_kg'];

$insert = mysqli_query($koneksi, "
    INSERT INTO penjualan (id_pengepul, id_sampah, tanggal, berat_kg, harga_jual_kg, total_rp)
    VALUES ('$id_user', '$id_sampah', '$tanggal', '$berat_kg', '$harga_jual_kg', '$total_rp')
");

if ($insert) {
    // Kurangi saldo pengepul
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $total_rp WHERE id_user='$id_user'");
    // Kurangi stok sampah admin
    mysqli_query($koneksi, "UPDATE sampah SET stok_kg = stok_kg - $berat_kg WHERE id_sampah='$id_sampah'");

    header('Location: dashboard_pengepul.php?pesan=sukses');
    exit;
} else {
    echo "Transaksi gagal: " . mysqli_error($koneksi);
}
?>
