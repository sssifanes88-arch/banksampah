<?php
include "koneksi.php";

if (isset($_GET['id_user'])) {

    $id_user = $_GET['id_user'];

    $query = mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id_user'");

    if ($query) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    echo "ID user tidak ditemukan!";
}
?>
