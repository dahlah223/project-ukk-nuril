<?php
session_start();
// Proteksi: Hanya siswa yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != "siswa") { 
    header("location:../index.php"); 
    exit; 
}
include "../koneksi.php";

$user_id = $_SESSION['user_id'];
$query_a = mysqli_query($koneksi, "SELECT id FROM anggota WHERE user_id='$user_id'");
$data_a = mysqli_fetch_assoc($query_a);

if (!$data_a) {
    header("location:../daftar_anggota.php");
    exit;
}

$id_anggota = $data_a['id'];

// Proses Pengembalian
if (isset($_GET['kembali'])) {
    $id_t = mysqli_real_escape_string($koneksi, $_GET['kembali']);
    $tgl_sekarang = date('Y-m-d');
    
    $cek_t = mysqli_query($koneksi, "SELECT buku_id FROM transaksi WHERE id='$id_t'");
    $data_t = mysqli_fetch_assoc($cek_t);
    
    if ($data_t) {
        $id_buku = $data_t['buku_id'];
        // Update status transaksi dan tambah stok buku kembali
        mysqli_query($koneksi, "UPDATE transaksi SET tgl_kembali='$tgl_sekarang', status='kembali' WHERE id='$id_t'");
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id='$id_buku'");
        
        echo "<script>alert('Buku telah dikembalikan!'); window.location='riwayat.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pinjaman Saya - PerpusKita</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --bg: #f8fafc;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; color: #1e293b; }
        
        aside { width: 280px; height: 100vh; background: white; border-right: 1px solid #e2e8f0; padding: 2.5rem 1.5rem; box-sizing: border-box; position: fixed; }
        .brand { font-weight: 800; font-size: 1.5rem; margin-bottom: 3rem; color: var(--primary); letter-spacing: -1px; }
        nav a { display: flex; align-items: center; padding: 0.8rem 1.2rem; color: #64748b; text-decoration: none; border-radius: 12px; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
        nav a.active { background: #f5f3ff; color: var(--primary); }
        nav a:hover:not(.active) { background: #f8fafc; color: #0f172a; }
        
        main { flex: 1; padding: 3rem; margin-left: 280px; }
        h1 { font-size: 2.25rem; font-weight: 800; margin: 0; letter-spacing: -1.5px; }
        .subtitle { color: #64748b; margin-top: 0.5rem; margin-bottom: 2.5rem; }

        .table-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fafafa; padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1px solid #f1f5f9; }
        td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        tr:hover { background: #fcfdfe; }

        .badge { padding: 6px 14px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

        .btn { padding: 0.7rem 1.2rem; border-radius: 12px; text-decoration: none; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; border: none; cursor: pointer; }
        .btn-blue { background: var(--primary); color: white; margin-right: 8px; }
        .btn-blue:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 8px 15px rgba(79, 70, 229, 0.2); }
        .btn-outline { border: 1.5px solid #e2e8f0; color: #64748b; background: white; }
        .btn-outline:hover { border-color: #f43f5e; color: #f43f5e; background: #fff1f2; }
    </style>
</head>
<body>
    <aside>
        <div class="brand">📘 PerpusKita</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="katalog.php">Katalog Buku</a>
            <a href="riwayat.php" class="active">Pinjaman Saya</a>
            <a href="../logout.php" style="color: #f43f5e; margin-top: 4rem;">Logout</a>
        </nav>
    </aside>

    <main>
        <h1>Pinjaman Aktif</h1>
        <p class="subtitle">Buku-buku yang saat ini sedang Anda jelajahi.</p>
        
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                        <th style="text-align: center;">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT transaksi.*, buku.judul 
                            FROM transaksi 
                            JOIN buku ON transaksi.buku_id=buku.id 
                            WHERE transaksi.anggota_id='$id_anggota' AND transaksi.status='pinjam'
                            ORDER BY transaksi.id DESC";
                    
                    $query = mysqli_query($koneksi, $sql);
                    
                    if(mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) :
                    ?>
                        <tr>
                            <td style="font-weight:700; color: #0f172a;"><?= $row['judul']; ?></td>
                            <td style="color: #64748b;"><?= date('d M Y', strtotime($row['tgl_pinjam'])); ?></td>
                            <td><span class="badge">Sedang Dipinjam</span></td>
                            <td style="text-align: center;">
                                <!-- PERBAIKAN: Diarahkan ke detail_buku.php terlebih dahulu -->
                                <a href="detail_buku.php?id=<?= $row['buku_id']; ?>" class="btn btn-blue">📖 Baca</a>
                                <a href="riwayat.php?kembali=<?= $row['id']; ?>" class="btn btn-outline" onclick="return confirm('Ingin mengembalikan buku ini?')">Kembalikan</a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                    ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding:5rem; color:#94a3b8;">
                                <div style="font-size: 3.5rem; margin-bottom: 1.5rem;">📖</div>
                                <div style="font-weight: 700; color: #475569; font-size: 1.1rem;">Belum ada pinjaman aktif</div>
                                <p style="font-size: 0.9rem; margin-top: 5px;">Silakan jelajahi katalog untuk mulai membaca.</p>
                                <br>
                                <a href="katalog.php" class="btn btn-blue">Jelajahi Katalog</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
