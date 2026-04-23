<?php
session_start();
include "../koneksi.php";

// 1. Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") { 
    header("location:../index.php"); 
    exit; 
}

// 2. Logika Hapus Anggota
if (isset($_GET['hapus'])) {
    $id_anggota = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    // Cek pinjaman aktif
    $cek_pinjam = mysqli_query($koneksi, "SELECT id FROM transaksi WHERE anggota_id='$id_anggota' AND status='pinjam'");
    
    if (mysqli_num_rows($cek_pinjam) > 0) {
        echo "<script>alert('Gagal! Siswa ini masih meminjam buku.'); window.location='anggota.php';</script>";
    } else {
        // Hapus relasi di tabel transaksi (riwayat) lalu hapus anggota
        mysqli_query($koneksi, "DELETE FROM transaksi WHERE anggota_id='$id_anggota'");
        $delete = mysqli_query($koneksi, "DELETE FROM anggota WHERE id='$id_anggota'");
        
        if($delete) {
            echo "<script>alert('Data berhasil dimusnahkan!'); window.location='anggota.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
    exit;
}

// 3. Logika Pencarian
$keyword = "";
if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
    $query_str = "SELECT * FROM anggota WHERE nama_lengkap LIKE '%$keyword%' OR nis LIKE '%$keyword%' ORDER BY id DESC";
} else {
    $query_str = "SELECT * FROM anggota ORDER BY id DESC";
}

$query = mysqli_query($koneksi, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Anggota | PerpusKita</title>
    <!-- Link Font -->
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg-body: #f1f5f9;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --danger: #ef4444;
            --glass: rgba(255, 255, 255, 0.7);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Modern */
        aside { 
            width: 280px; 
            background: var(--white); 
            padding: 2rem 1.5rem; 
            display: flex; 
            flex-direction: column;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            height: 100vh;
        }

        .brand { 
            font-size: 1.5rem; 
            font-weight: 800; 
            color: var(--primary); 
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        nav { flex: 1; }
        nav a { 
            display: flex; 
            align-items: center; 
            padding: 12px 16px; 
            color: var(--text-muted); 
            text-decoration: none; 
            border-radius: 12px; 
            margin-bottom: 8px; 
            font-weight: 600; 
            transition: all 0.3s ease;
        }

        nav a:hover, nav a.active { 
            background: var(--primary); 
            color: white; 
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        /* Main Content */
        main { flex: 1; margin-left: 280px; padding: 3rem; }

        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 2.5rem; 
        }

        h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }

        /* Search Bar */
        .search-box {
            display: flex;
            background: var(--white);
            padding: 6px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .search-box input {
            border: none;
            padding: 10px 15px;
            outline: none;
            width: 250px;
            font-family: inherit;
        }

        .btn-cari {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        /* Table Design */
        .card-table {
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
        }

        table { width: 100%; border-collapse: collapse; }
        
        th { 
            background: #f8fafc; 
            padding: 18px 24px; 
            text-align: left; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            color: var(--text-muted);
            border-bottom: 1px solid #f1f5f9;
        }

        td { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

        .user-info { display: flex; align-items: center; gap: 15px; }
        .avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
        }

        .nis-tag {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        /* Action Button */
        .btn-action-delete {
            background: #fff1f2;
            color: var(--danger);
            padding: 10px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-action-delete:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* Empty State */
        .empty { padding: 40px; text-align: center; color: var(--text-muted); }

    </style>
</head>
<body>

    <aside>
        <div class="brand"><span>🚀</span> PerpusKita</div>
        <nav>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="buku.php">📚 Kelola Buku</a>
            <a href="anggota.php" class="active">👥 Data Anggota</a>
            <a href="transaksi.php">🔄 Transaksi</a>
        </nav>
        <a href="../logout.php" style="color: var(--danger); text-decoration: none; font-weight: 700; padding: 12px 16px;">🚪 Keluar</a>
    </aside>

    <main>
        <div class="header">
            <div>
                <h1>Data Anggota</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola seluruh siswa terdaftar di perpustakaan.</p>
            </div>

            <form method="get" class="search-box">
                <input type="text" name="keyword" placeholder="Cari Nama/NIS..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" name="cari" class="btn-cari">Cari</button>
            </form>
        </div>

        <div class="card-table">
            <table>
                <thead>
                    <tr>
                        <th width="80">No</th>
                        <th>Identitas Siswa</th>
                        <th>NIS</th>
                        <th>Alamat</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($query) > 0) :
                        while($row = mysqli_fetch_assoc($query)) : 
                            $initial = strtoupper(substr($row['nama_lengkap'], 0, 1));
                    ?>
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 600;">#<?= $no++; ?></td>
                        <td>
                            <div class="user-info">
                                <div class="avatar"><?= $initial ?></div>
                                <div>
                                    <div style="font-weight: 700;"><?= ucwords(strtolower($row['nama_lengkap'])); ?></div>
                                    <div style="font-size: 0.75rem; color: #10b981; font-weight: 600;">● Terverifikasi</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="nis-tag"><?= $row['nis']; ?></span></td>
                        <td style="color: var(--text-muted);"><?= $row['alamat']; ?></td>
                        <td>
                            <!-- Pastikan menggunakan ?hapus= -->
                            <a href="anggota.php?hapus=<?= $row['id']; ?>" 
                               class="btn-action-delete"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini? Tindakan ini tidak bisa dibatalkan.')">
                               Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="empty">Data tidak ditemukan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
