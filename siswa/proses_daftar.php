<?php
session_start();
include '../koneksi.php';

$user_id = $_SESSION['user_id'];
$nama = $_POST['nama'];
$nis = $_POST['nis'];
$alamat = $_POST['alamat'];

// validasi sederhana
if (!is_numeric($nis)) {
    die("NIS harus angka!");
}

// cek sudah daftar
$cek = mysqli_query($koneksi, "SELECT * FROM anggota WHERE user_id = '$user_id'");
if (mysqli_num_rows($cek) > 0) {
    die("Kamu sudah terdaftar!");
}

// simpan
$stmt = $koneksi->prepare("INSERT INTO anggota (user_id, nama_lengkap, nis, alamat) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $nama, $nis, $alamat);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=1");
} else {
    echo "Gagal: " . $stmt->error;
}