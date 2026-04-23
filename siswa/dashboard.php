<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "siswa") { 
    header("location:../index.php"); 
    exit; 
}
include "../koneksi.php";

$user_id = $_SESSION['user_id'];
$query_a = mysqli_query($koneksi, "SELECT * FROM anggota WHERE user_id='$user_id'");
$data_anggota = mysqli_fetch_assoc($query_a);

// Jika siswa login tapi belum isi data anggota, arahkan ke daftar
if (!$data_anggota) {
    header("location:../daftar_anggota.php");
    exit;
}

// Hitung jumlah pinjaman aktif secara otomatis
$count_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM transaksi WHERE anggota_id='".$data_anggota['id']."' AND status='pinjam'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - PerpusKita</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --bg-main: #f1f5f9;
            --glass: rgba(255, 255, 255, 0.8);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-main); 
            margin: 0; 
            display: flex; 
            color: #1e293b;
        }
        
        /* Sidebar Modern */
        aside { 
            width: 280px; 
            height: 100vh; 
            background: white; 
            border-right: 1px solid #e2e8f0; 
            padding: 2.5rem 1.5rem; 
            box-sizing: border-box; 
            position: fixed;
        }
        
        .brand { 
            font-weight: 800; 
            font-size: 1.5rem; 
            margin-bottom: 3rem; 
            color: var(--primary); 
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        nav a { 
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem; 
            color: var(--secondary); 
            text-decoration: none; 
            border-radius: 12px; 
            margin-bottom: 0.5rem; 
            font-weight: 600; 
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        nav a.active { background: #eff6ff; color: var(--primary); }
        nav a:hover:not(.active) { background: #f8fafc; color: #0f172a; }
        
        /* Main Content area */
        main { 
            flex: 1; 
            margin-left: 280px;
            padding: 3rem; 
        }
        
        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%);
            color: white;
            padding: 3rem;
            border-radius: 32px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.2);
            margin-bottom: 2.5rem;
        }

        .welcome-section::after {
            content: "";
            position: absolute;
            width: 200px; height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -50px; right: -50px;
        }

        .welcome-section h1 { font-size: 2.25rem; margin: 0; font-weight: 800; }
        .welcome-section p { opacity: 0.9; margin-top: 0.5rem; font-size: 1.1rem; }

        /* Grid Stats & Info */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        /* Card Style */
        .card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Info Item */
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 1.2rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-item:last-child { border: none; }
        .info-label { color: var(--secondary); font-weight: 500; }
        .info-value { font-weight: 700; color: #0f172a; }

        /* Stat Box */
        .stat-box {
            background: white;
            padding: 2rem;
            border-radius: 24px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }
        .stat-label {
            color: var(--secondary);
            font-weight: 600;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-katalog {
            display: inline-block;
            margin-top: 1.5rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-katalog:hover { background: var(--primary-dark); transform: translateY(-2px); }

    </style>
</head>
<body>
    <aside>
        <div class="brand">📘 PerpusKita</div>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="katalog.php">Katalog Buku</a>
            <a href="riwayat.php">Pinjaman Saya</a>
            <a href="../logout.php" style="color: #ef4444; margin-top: 3rem;">Logout</a>
        </nav>
    </aside>

    <main>
        <div class="welcome-section">
            <h1>Halo, <?= explode(' ', trim($data_anggota['nama_lengkap']))[0] ?>! 👋</h1>
            <p>Senang melihatmu kembali. Mau baca buku apa hari ini?</p>
        </div>
        
        <div class="dashboard-grid">
            <!-- Info Card -->
            <div class="card">
                <div class="card-title">👤 Profil Anggota</div>
                <div class="info-item">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value"><?= $data_anggota['nama_lengkap'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">NIS</span>
                    <span class="info-value"><?= $data_anggota['nis'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Alamat</span>
                    <span class="info-value"><?= $data_anggota['alamat'] ?></span>
                </div>
            </div>

            <!-- Stats Column -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="stat-box">
                    <div class="stat-number"><?= $count_pinjam ?></div>
                    <div class="stat-label">Buku Dipinjam</div>
                    <a href="katalog.php" class="btn-katalog">Pinjam Lagi</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
