<?php
include "koneksi.php";

$username = $_POST['username'];
$password = $_POST['password'];
$nama = $_POST['nama'];
$saldo = $_POST['saldo'];
$role = $_POST['role'];

$query = mysqli_query($koneksi, "INSERT INTO users (username, password, nama, saldo, role) VALUES ('$username', '$password', '$nama', '$saldo', '$role')");

if ($query) {
    header('Location: index.php');
    exit;
} else {
    echo "Data gagal disimpan: " . mysqli_error($koneksi);
}
?>
