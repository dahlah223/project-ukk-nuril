<?php
include "../koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $tgl_sekarang = date('Y-m-d');

    // 1. Ambil buku_id dari transaksi ini sebelum diupdate
    $cek_transaksi = mysqli_query($koneksi, "SELECT buku_id FROM transaksi WHERE id='$id'");
    $data = mysqli_fetch_assoc($cek_transaksi);
    $buku_id = $data['buku_id'];

    // 2. Update status transaksi menjadi 'kembali'
    $update_transaksi = mysqli_query($koneksi, "UPDATE transaksi SET 
        status='kembali', 
        tgl_kembali='$tgl_sekarang' 
        WHERE id='$id'");

    if ($update_transaksi) {
        // 3. Tambahkan stok buku kembali karena buku sudah dipulangkan
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id='$buku_id'");
        
        echo "<script>alert('Buku berhasil dikembalikan!'); window.location='transaksi.php';</script>";
    } else {
        echo "Gagal memproses pengembalian: " . mysqli_error($koneksi);
    }
}
?>
