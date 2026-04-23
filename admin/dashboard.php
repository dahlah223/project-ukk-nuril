<?php
session_start();
// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") { 
    header("location:../index.php"); 
    exit; 
}
include "../koneksi.php";

// Ambil data statistik
$buku = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM buku"));
$anggota = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM anggota"));
$transaksi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='pinjam'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - PerpusKita</title>
    <!-- Google Fonts: Inter -->
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --bg-main: #f1f5f9;
            --success: #10b981;
            --warning: #f59e0b;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-main); 
            margin: 0; 
            display: flex; 
            color: #1e293b;
        }
        
        /* Sidebar Modern (Konsisten dengan Siswa) */
        aside { 
            width: 280px; 
            height: 100vh; 
            background: white; 
            border-right: 1px solid #e2e8f0; 
            padding: 2.5rem 1.5rem; 
            box-sizing: border-box; 
            position: fixed;
            display: flex;
            flex-direction: column;
        }
        
        .brand { 
            font-weight: 800; 
            font-size: 1.6rem; 
            margin-bottom: 3rem; 
            color: var(--primary); 
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        nav { flex-grow: 1; }

        nav a { 
            display: flex;
            align-items: center;
            padding: 0.9rem 1.2rem; 
            color: var(--secondary); 
            text-decoration: none; 
            border-radius: 14px; 
            margin-bottom: 0.6rem; 
            font-weight: 600; 
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        nav a.active { background: #eff6ff; color: var(--primary); }
        nav a:hover:not(.active) { background: #f8fafc; color: #0f172a; transform: translateX(5px); }
        
        .logout-box {
            border-top: 1px solid #f1f5f9;
            padding-top: 1.5rem;
        }

        .btn-logout { 
            color: #ef4444; 
            font-weight: 700;
            display: flex;
            align-items: center;
            padding: 0.9rem 1.2rem;
            text-decoration: none;
            border-radius: 14px;
            transition: 0.3s;
        }
        .btn-logout:hover { background: #fef2f2; }

        /* Main Content area */
        main { 
            flex: 1; 
            margin-left: 280px;
            padding: 3rem; 
        }

        header { margin-bottom: 3rem; }
        header h1 { font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -1px; }
        header p { color: var(--secondary); margin-top: 0.5rem; }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border-color: var(--primary); }

        .stat-icon {
            width: 60px; height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Variasi Warna Icon */
        .icon-blue { background: #eff6ff; color: var(--primary); }
        .icon-green { background: #ecfdf5; color: var(--success); }
        .icon-orange { background: #fffbeb; color: var(--warning); }

        .stat-info .label { font-size: 0.85rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-info .value { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1.2; }

        /* Quick Actions */
        .quick-actions {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 2.5rem;
            border-radius: 24px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quick-actions h3 { margin: 0; font-size: 1.25rem; font-weight: 700; }
        .action-btns { display: flex; gap: 15px; }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .btn-white { background: white; color: #0f172a; }
        .btn-white:hover { background: #f1f5f9; transform: scale(1.05); }

    </style>
</head>
<body>
    <aside>
        <div class="brand">
            <span>🛡️</span> PerpusKita
        </div>
        
        <nav>
            <a href="dashboard.php" class="active">🏠 Dashboard</a>
            <a href="buku.php">📚 Kelola Buku</a>
            <a href="anggota.php">👥 Kelola Anggota</a>
            <a href="transaksi.php">🔄 Transaksi Pinjam</a>
        </nav>

        <div class="logout-box">
            <a href="../logout.php" class="btn-logout">🚪 Logout System</a>
        </div>
    </aside>

    <main>
        <header>
            <h1>Dashboard Overview</h1>
            <p>Selamat datang, <b>Administrator</b>. Berikut adalah ringkasan data perpustakaan hari ini.</p>
        </header>

        <div class="stats-grid">
            <!-- Card Total Buku -->
            <div class="stat-card">
                <div class="stat-icon icon-blue">📚</div>
                <div class="stat-info">
                    <div class="label">Total Buku</div>
                    <div class="value"><?= $buku ?></div>
                </div>
            </div>

            <!-- Card Anggota -->
            <div class="stat-card">
                <div class="stat-icon icon-green">👥</div>
                <div class="stat-info">
                    <div class="label">Anggota Siswa</div>
                    <div class="value"><?= $anggota ?></div>
                </div>
            </div>

            <!-- Card Transaksi Aktif -->
            <div class="stat-card">
                <div class="stat-icon icon-orange">⏳</div>
                <div class="stat-info">
                    <div class="label">Peminjaman Aktif</div>
                    <div class="value"><?= $transaksi ?></div>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <div>
                <h3>Akses Cepat Admin</h3>
                <p style="margin: 5px 0 0; opacity: 0.7; font-size: 0.9rem;">Kelola inventaris dan aktivitas perpustakaan.</p>
            </div>
            <div class="action-btns">
                <a href="buku.php" class="btn btn-white">+ Tambah Buku</a>
                <a href="transaksi.php" class="btn btn-white">Lihat Semua Laporan</a>
            </div>
        </div>
    </main>
</body>
</html>
