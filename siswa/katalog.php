<?php
session_start();
// Proteksi: Hanya siswa yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != "siswa") { 
    header("location:../index.php"); 
    exit; 
}
include "../koneksi.php";

// LOGIK PEMINJAMAN
if (isset($_GET['pinjam'])) {
    $id_buku = mysqli_real_escape_string($koneksi, $_GET['pinjam']);
    $user_id = $_SESSION['user_id'];
    
    $result_a = mysqli_query($koneksi, "SELECT id FROM anggota WHERE user_id='$user_id'");
    $data_a = mysqli_fetch_assoc($result_a);
    
    if (!$data_a) {
        echo "<script>alert('Anda harus melengkapi data anggota dahulu!'); window.location='../daftar_anggota.php';</script>";
        exit;
    }

    $id_anggota = $data_a['id'];
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime('+7 days')); 
    
    $cek_buku = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id='$id_buku'");
    $buku = mysqli_fetch_assoc($cek_buku);

    if ($buku && $buku['stok'] > 0) {
        mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id='$id_buku'");
        $insert = mysqli_query($koneksi, "INSERT INTO transaksi (buku_id, anggota_id, tgl_pinjam, tgl_kembali, status) 
                                          VALUES ('$id_buku', '$id_anggota', '$tgl_pinjam', '$tgl_kembali', 'pinjam')");
        
        if ($insert) {
            echo "<script>alert('Berhasil dipinjam!'); window.location='riwayat.php';</script>";
        } else {
            mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id='$id_buku'");
            echo "Gagal: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>alert('Maaf, stok habis!'); window.location='katalog.php';</script>";
    }
}

// LOGIK PENCARIAN
$keyword = "";
if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
    $query_str = "SELECT * FROM buku WHERE judul LIKE '%$keyword%' OR penulis LIKE '%$keyword%' ORDER BY id DESC";
} else {
    $query_str = "SELECT * FROM buku ORDER BY id DESC";
}
$query = mysqli_query($koneksi, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - PerpusKita</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-gray: #64748b;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-light); margin: 0; display: flex; color: var(--text-dark); }
        
        /* Sidebar */
        aside { width: 280px; height: 100vh; background: white; border-right: 1px solid #f1f5f9; padding: 2.5rem 1.5rem; box-sizing: border-box; position: fixed; z-index: 100; }
        .brand { font-weight: 800; font-size: 1.5rem; margin-bottom: 3rem; color: var(--primary); letter-spacing: -1px; }
        nav a { display: flex; align-items: center; padding: 0.8rem 1.2rem; color: var(--text-gray); text-decoration: none; border-radius: 12px; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
        nav a:hover { background: #f8fafc; color: var(--text-dark); }
        nav a.active { background: #f5f3ff; color: var(--primary); }
        
        main { flex: 1; padding: 3rem; margin-left: 280px; }
        
        .header-title h1 { font-size: 2.25rem; font-weight: 800; margin: 0; letter-spacing: -1.5px; }
        .header-title p { color: var(--text-gray); margin: 0.5rem 0 2.5rem 0; }

        /* Search Section */
        .search-container { margin-bottom: 3rem; display: flex; gap: 12px; background: white; padding: 8px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; }
        .search-input { flex: 1; padding: 0.8rem 1rem; border: none; font-size: 0.95rem; outline: none; font-family: inherit; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 0 1.8rem; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-search:hover { background: var(--primary-dark); }

        /* Book Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 2.5rem; }
        .book-card { background: white; padding: 1rem; border-radius: 24px; border: 1px solid #f1f5f9; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: flex; flex-direction: column; }
        .book-card:hover { transform: translateY(-10px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); border-color: var(--primary); }
        
        /* Cover Image Container */
        .cover-wrapper { width: 100%; aspect-ratio: 3/4; border-radius: 18px; overflow: hidden; margin-bottom: 1.25rem; background: #f1f5f9; box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
        .cover-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .book-card:hover .cover-img { transform: scale(1.05); }
        .no-cover { display: flex; align-items: center; justify-content: center; height: 100%; color: #94a3b8; font-weight: 700; font-size: 0.75rem; text-align: center; }

        .book-title { font-weight: 800; font-size: 1.1rem; color: var(--text-dark); margin-bottom: 0.3rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 3rem; }
        .book-meta { font-size: 0.8rem; color: var(--text-gray); margin-bottom: 1rem; font-weight: 500; }
        
        .stok-info { display: flex; align-items: center; gap: 8px; font-size: 0.75rem; font-weight: 700; margin-bottom: 1.25rem; margin-top: auto; }
        .dot { height: 8px; width: 8px; border-radius: 50%; }
        .dot-available { background: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
        .dot-empty { background: #ef4444; }

        .btn-pinjam { background: var(--primary); color: white; text-align: center; padding: 0.9rem; border-radius: 14px; text-decoration: none; font-weight: 700; font-size: 0.85rem; transition: 0.3s; }
        .btn-pinjam:hover { background: var(--primary-dark); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
        .btn-disabled { background: #f1f5f9; color: #94a3b8; padding: 0.9rem; text-align: center; border-radius: 14px; font-weight: 700; font-size: 0.85rem; border: 1px dashed #cbd5e1; cursor: not-allowed; }
    </style>
</head>
<body>
    <aside>
        <div class="brand">📘 PerpusKita</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="katalog.php" class="active">Katalog Buku</a>
            <a href="riwayat.php">Pinjaman Saya</a>
            <a href="../logout.php" style="color: #ef4444; margin-top: 3rem;">Logout</a>
        </nav>
    </aside>

    <main>
        <div class="header-title">
            <h1>E-Library Katalog</h1>
            <p>Jelajahi koleksi buku digital terbaik untuk mendukung belajarmu.</p>
        </div>

        <form action="" method="get" class="search-container">
            <input type="text" name="keyword" class="search-input" placeholder="Cari judul buku atau penulis..." value="<?= htmlspecialchars($keyword); ?>">
            <button type="submit" name="cari" class="btn-search">Cari</button>
            <?php if(isset($_GET['cari'])): ?>
                <a href="katalog.php" style="text-decoration:none; color:var(--text-gray); font-size:0.85rem; font-weight:700; display:flex; align-items:center; padding-right:15px;">Reset</a>
            <?php endif; ?>
        </form>
        
        <div class="grid">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                    <div class='book-card'>
                        <div class="cover-wrapper">
                            <?php if(!empty($row['sampul'])): ?>
                                <img src="../admin/img/<?= $row['sampul']; ?>" class="cover-img" alt="Sampul Buku">
                            <?php else: ?>
                                <div class="no-cover">SAMPUL TIDAK<br>TERSEDIA</div>
                            <?php endif; ?>
                        </div>

                        <div class='book-title'><?= $row['judul']; ?></div>
                        <div class='book-meta'>Oleh <span style="color: var(--text-dark);"><?= $row['penulis']; ?></span></div>
                        
                        <div class="stok-info">
                            <span class="dot <?= ($row['stok'] > 0) ? 'dot-available' : 'dot-empty'; ?>"></span>
                            <?= ($row['stok'] > 0) ? "Tersedia: ".$row['stok']." Buku" : "Stok Habis"; ?>
                        </div>

                        <?php if ($row['stok'] > 0): ?>
                            <a href="?pinjam=<?= $row['id']; ?>" class="btn-pinjam" onclick="return confirm('Pinjam buku ini?')">Pinjam Sekarang</a>
                        <?php else: ?>
                            <div class="btn-disabled">Tidak Tersedia</div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 5rem 0;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                    <h3 style="margin: 0; color: var(--text-gray);">Maaf, buku tidak ditemukan.</h3>
                    <p style="color: var(--text-gray); font-size: 0.9rem;">Coba gunakan kata kunci lain.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
