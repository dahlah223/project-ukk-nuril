<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") { 
    header("location:../index.php"); 
    exit; 
}

// Fitur Kembalikan Buku
if (isset($_GET['kembalikan'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_GET['kembalikan']);
    $cek_t = mysqli_query($koneksi, "SELECT buku_id FROM transaksi WHERE id='$id_transaksi'");
    $data_t = mysqli_fetch_assoc($cek_t);
    $id_buku = $data_t['buku_id'];

    mysqli_query($koneksi, "UPDATE transaksi SET status='kembali' WHERE id='$id_transaksi'");
    mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id='$id_buku'");
    header("location:transaksi.php");
    exit;
}

$keyword = "";
$where_clause = "";
if (isset($_GET['cari']) && !empty(trim($_GET['keyword']))) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
    $where_clause = " WHERE a.nama_lengkap LIKE '%$keyword%' OR b.judul LIKE '%$keyword%' ";
}

$query = mysqli_query($koneksi, "SELECT t.*, b.judul, a.nama_lengkap 
                                FROM transaksi t
                                JOIN buku b ON t.buku_id = b.id
                                JOIN anggota a ON t.anggota_id = a.id
                                $where_clause
                                ORDER BY t.id DESC");

$total_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM transaksi WHERE status='pinjam'"));
$total_kembali = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM transaksi WHERE status='kembali'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Transaksi - PerpusKita</title>
    <!-- Link Font diperbaiki -->
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #4338ca;
            --primary-light: #eef2ff;
            --success: #059669;
            --bg: #f3f4f6;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg);
            margin: 0; display: flex; color: #1f2937;
        }

        /* Sidebar Modern */
        aside { width: 260px; background: white; border-right: 1px solid #e5e7eb; padding: 2.5rem 1.5rem; position: fixed; height: 100vh; box-sizing: border-box; }
        .brand { font-weight: 800; font-size: 1.4rem; color: var(--primary); margin-bottom: 3rem; display: flex; align-items: center; gap: 10px; }
        nav a { display: flex; align-items: center; padding: 0.8rem 1.2rem; color: #6b7280; text-decoration: none; border-radius: 12px; margin-bottom: 0.5rem; font-weight: 600; transition: 0.3s; }
        nav a.active { background: var(--primary-light); color: var(--primary); }
        nav a:hover:not(.active) { background: #f9fafb; color: var(--primary); }

        main { flex: 1; margin-left: 260px; padding: 3rem; box-sizing: border-box; }

        /* Stats Cards */
        .stat-grid { display: flex; gap: 2rem; margin-bottom: 2.5rem; }
        .stat-card { 
            background: white; padding: 1.5rem; border-radius: 20px; flex: 1; 
            display: flex; align-items: center; gap: 1.5rem; 
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.8);
        }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }

        /* Search Bar */
        .search-area { display: flex; gap: 12px; margin-bottom: 2rem; }
        .search-input { flex: 1; border: 1px solid #e5e7eb; padding: 0.8rem 1.2rem; border-radius: 14px; outline: none; background: white; font-family: inherit; transition: 0.3s; }
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }
        .btn-cari { background: #1f2937; color: white; border: none; padding: 0 1.8rem; border-radius: 14px; cursor: pointer; font-weight: 700; transition: 0.3s; }
        .btn-cari:hover { background: #000; }

        /* Table Style */
        .card-table { background: white; border-radius: 24px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.04); overflow: hidden; border: 1px solid #f3f4f6; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fafafa; padding: 1.2rem 1.5rem; text-align: left; font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #f3f4f6; }
        td { padding: 1.2rem 1.5rem; border-bottom: 1px solid #f9fafb; font-size: 14px; }
        tr:hover { background: #f9fafb; }

        /* Badges */
        .status-badge { padding: 6px 14px; border-radius: 10px; font-size: 11px; font-weight: 800; display: inline-block; }
        .badge-pinjam { background: #fff7ed; color: #c2410c; }
        .badge-kembali { background: #ecfdf5; color: #047857; }

        .btn-action { background: var(--primary); color: white; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 12px; font-weight: 700; transition: 0.3s; }
        .btn-action:hover { box-shadow: 0 4px 12px rgba(67, 56, 202, 0.3); }

        .btn-report { background: var(--success); color: white; padding: 0.8rem 1.5rem; border-radius: 14px; text-decoration: none; font-weight: 700; font-size: 13px; }
    </style>
</head>
<body>

    <aside>
        <div class="brand">📘 PerpusKita</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="buku.php">Koleksi Buku</a>
            <a href="anggota.php">Data Anggota</a>
            <a href="transaksi.php" class="active">Log Transaksi</a>
        </nav>
        <a href="../logout.php" style="margin-top:auto; color:#dc2626; text-decoration:none; font-weight:700; padding:1rem;">Keluar</a>
    </aside>

    <main>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2.5rem;">
            <h1 style="font-size: 1.8rem; font-weight: 800; margin:0;">Riwayat Transaksi</h1>
            <a href="laporan_otomatis.php" target="_blank" class="btn-report">Cetak Laporan</a>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff7ed; color:#f97316;">⏳</div>
                <div>
                    <span style="color:#9ca3af; font-size:11px; font-weight:700; text-transform:uppercase;">Sedang Dipinjam</span>
                    <div style="font-size:1.6rem; font-weight:800;"><?= $total_pinjam ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5; color:#10b981;">✅</div>
                <div>
                    <span style="color:#9ca3af; font-size:11px; font-weight:700; text-transform:uppercase;">Sudah Kembali</span>
                    <div style="font-size:1.6rem; font-weight:800;"><?= $total_kembali ?></div>
                </div>
            </div>
        </div>

        <form action="" method="get" class="search-area">
            <input type="text" name="keyword" class="search-input" placeholder="Cari siswa atau buku..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" name="cari" class="btn-cari">Cari Data</button>
        </form>

        <div class="card-table">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Siswa</th>
                        <th>Judul Buku</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($query)) : ?>
                        <tr>
                            <td style="color:#9ca3af; font-weight:600;"><?= $no++; ?></td>
                            <td><div style="font-weight:700;"><?= htmlspecialchars($row['nama_lengkap']) ?></div></td>
                            <td style="color:#4b5563; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($row['judul']) ?>
                            </td>
                            <td style="color:#6b7280; font-size:13px;"><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                            <td>
                                <span class="status-badge <?= ($row['status'] == 'pinjam') ? 'badge-pinjam' : 'badge-kembali'; ?>">
                                    <?= strtoupper($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'pinjam'): ?>
                                    <a href="?kembalikan=<?= $row['id'] ?>" class="btn-action">Selesai</a>
                                <?php else: ?>
                                    <span style="color:#10b981; font-weight:800; font-size:12px;">DITERIMA</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" align="center" style="padding:4rem; color:#9ca3af; font-weight:600;">Belum ada riwayat transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
