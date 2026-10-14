<?php
$koneksi = mysqli_connect("localhost", "root", "", "banksampah_anes");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
