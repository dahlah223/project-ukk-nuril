<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") { 
    header("location:../index.php"); 
    exit; 
}

// Logika Hapus
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $cek_gambar = mysqli_query($koneksi, "SELECT sampul FROM buku WHERE id='$id'");
    $data_gambar = mysqli_fetch_assoc($cek_gambar);
    
    if($data_gambar['sampul'] != NULL && file_exists("img/" . $data_gambar['sampul'])) {
        unlink("img/" . $data_gambar['sampul']);
    }
    mysqli_query($koneksi, "DELETE FROM buku WHERE id='$id'");
    header("location:buku.php");
    exit;
}

// Logika Simpan/Update
if (isset($_POST['simpan'])) {
    $judul   = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $stok    = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $isi     = mysqli_real_escape_string($koneksi, $_POST['isi_buku']);
    
    $nama_file = $_FILES['sampul']['name'];
    $tmp_file  = $_FILES['sampul']['tmp_name'];

    if (isset($_POST['id_buku']) && !empty($_POST['id_buku'])) {
        $id = $_POST['id_buku'];
        if (!empty($nama_file)) {
            $file_final = time() . "_" . $nama_file;
            move_uploaded_file($tmp_file, "img/" . $file_final);
            $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', stok='$stok', isi_buku='$isi', sampul='$file_final' WHERE id='$id'";
        } else {
            $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', stok='$stok', isi_buku='$isi' WHERE id='$id'";
        }
    } else {
        $file_final = !empty($nama_file) ? time() . "_" . $nama_file : NULL;
        if($file_final) move_uploaded_file($tmp_file, "img/" . $file_final);
        $sql = "INSERT INTO buku (judul, penulis, stok, isi_buku, sampul) VALUES ('$judul', '$penulis', '$stok', '$isi', '$file_final')";
    }
    mysqli_query($koneksi, $sql);
    header("location:buku.php");
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $res_edit = mysqli_query($koneksi, "SELECT * FROM buku WHERE id='$id_edit'");
    $edit_data = mysqli_fetch_assoc($res_edit);
}

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, $_GET['keyword']) : "";
$query_buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE judul LIKE '%$keyword%' OR penulis LIKE '%$keyword%' ORDER BY id DESC");
$total_buku = mysqli_num_rows($query_buku);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Management Buku - PerpusKita Admin</title>
    <!-- Perbaikan Link Font -->
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-soft: #f5f3ff;
            --bg: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; color: var(--text-main); }
        
        aside { width: 280px; height: 100vh; background: var(--white); border-right: 1px solid var(--border); padding: 2.5rem 1.5rem; position: fixed; display: flex; flex-direction: column; box-sizing: border-box; }
        .brand { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 3.5rem; letter-spacing: -1px; }
        nav { flex: 1; }
        nav a { display: flex; align-items: center; padding: 1rem 1.25rem; color: var(--text-muted); text-decoration: none; border-radius: 14px; font-weight: 600; font-size: 0.9rem; transition: 0.3s; margin-bottom: 8px; }
        nav a:hover, nav a.active { background: var(--primary-soft); color: var(--primary); }
        .logout { color: #f43f5e; padding: 1rem; text-decoration: none; font-weight: 700; font-size: 0.9rem; border-radius: 14px; transition: 0.3s; }
        .logout:hover { background: #fff1f2; }

        main { flex: 1; padding: 3rem; margin-left: 280px; }
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
        .page-header h1 { font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -1px; }
        
        .search-bar { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 5px 5px 5px 15px; display: flex; align-items: center; gap: 10px; width: 320px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .search-bar input { border: none; outline: none; flex: 1; font-family: inherit; font-size: 0.9rem; }
        .btn-search { background: var(--text-main); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; }

        .layout-grid { display: grid; grid-template-columns: 380px 1fr; gap: 2.5rem; align-items: start; }
        
        .card { background: white; border-radius: 24px; border: 1px solid var(--border); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02); overflow: hidden; }
        .card-padding { padding: 1.8rem; }
        .card h3 { margin: 0 0 1.5rem 0; font-size: 1.1rem; font-weight: 800; }
        
        .field { margin-bottom: 1.2rem; }
        .field label { display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; }
        .field input, .field textarea { width: 100%; box-sizing: border-box; padding: 0.8rem 1rem; border: 1.5px solid #f1f5f9; border-radius: 12px; font-family: inherit; font-size: 0.9rem; transition: 0.3s; background: #fafbfc; }
        .field input:focus, .field textarea:focus { border-color: var(--primary); background: white; outline: none; }
        
        .btn-save { background: var(--primary); color: white; border: none; width: 100%; padding: 0.9rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #fcfcfd; padding: 1rem 1.25rem; text-align: left; font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border); }
        td { padding: 1.1rem 1.25rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
        
        .book-item { display: flex; align-items: center; gap: 1rem; }
        .thumb { width: 45px; height: 60px; border-radius: 8px; object-fit: cover; background: #f1f5f9; border: 1px solid #eee; }
        .stock-tag { background: #f1f5f9; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; }
        
        .btn-action { padding: 6px 12px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 0.75rem; transition: 0.2s; }
        .edit { background: #eef2ff; color: var(--primary); margin-right: 5px; }
        .delete { background: #fff1f2; color: #f43f5e; }
        .btn-action:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <aside>
        <div class="brand">🛡️ AdminPerpus</div>
        <nav>
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="buku.php" class="active">📚 Katalog Master</a>
            <a href="anggota.php">👥 Data Anggota</a>
            <a href="transaksi.php">🔄 Transaksi</a>
        </nav>
        <a href="../logout.php" class="logout">🚪 Keluar Sistem</a>
    </aside>

    <main>
        <div class="page-header">
            <div>
                <h1>Katalog Master</h1>
                <p style="color: var(--text-muted); margin-top: 5px;">Kelola koleksi buku dan stok perpustakaan.</p>
            </div>
            <form class="search-bar" method="GET">
                <input type="text" name="keyword" placeholder="Cari judul atau penulis..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" class="btn-search">Cari</button>
            </form>
        </div>

        <div class="layout-grid">
            <!-- Form Card -->
            <div class="card card-padding">
                <h3>✨ <?= $edit_data ? 'Edit Buku' : 'Tambah Buku' ?></h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <?php if($edit_data): ?>
                        <input type="hidden" name="id_buku" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="field">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" value="<?= $edit_data['judul'] ?? '' ?>" required>
                    </div>
                    <div class="field">
                        <label>Nama Penulis</label>
                        <input type="text" name="penulis" value="<?= $edit_data['penulis'] ?? '' ?>" required>
                    </div>
                    <div class="field">
                        <label>Stok</label>
                        <input type="number" name="stok" value="<?= $edit_data['stok'] ?? '' ?>" required>
                    </div>
                    <div class="field">
                        <label>Sampul (Image)</label>
                        <input type="file" name="sampul">
                    </div>
                    <div class="field">
                        <label>Isi / Sinopsis</label>
                        <textarea name="isi_buku" rows="4"><?= $edit_data['isi_buku'] ?? '' ?></textarea>
                    </div>
                    
                    <button type="submit" name="simpan" class="btn-save">
                        <?= $edit_data ? 'Perbarui Katalog' : 'Simpan ke Katalog' ?>
                    </button>
                    <?php if($edit_data): ?>
                        <a href="buku.php" style="display:block; text-align:center; margin-top:10px; color:var(--text-muted); text-decoration:none; font-size:0.8rem;">Batal Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table Card -->
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Detail Buku</th>
                            <th width="80">Stok</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query_buku) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query_buku)): ?>
                            <tr>
                                <td>
                                    <div class="book-item">
                                        <?php 
                                            $img = (!empty($row['sampul']) && file_exists("img/".$row['sampul'])) ? "img/".$row['sampul'] : "https://placeholder.com";
                                        ?>
                                        <img src="<?= $img ?>" class="thumb">
                                        <div>
                                            <div style="font-weight: 800;"><?= htmlspecialchars($row['judul']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($row['penulis']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="stock-tag"><?= $row['stok'] ?></span></td>
                                <td>
                                    <a href="?edit=<?= $row['id'] ?>" class="btn-action edit">Edit</a>
                                    <a href="?hapus=<?= $row['id'] ?>" class="btn-action delete" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" align="center" style="padding:2rem; color:var(--text-muted);">Belum ada data buku.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FOOTER MODERN -->
        <footer style="margin-top: 5rem; padding-top: 2rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 0.85rem;">
            <div>
                <strong>PerpusKita</strong> &copy; <?= date('Y') ?> - Panel Administrasi
            </div>
            <div style="display: flex; gap: 20px;">
                <span>v2.1.0</span>
                <span style="color: var(--primary); font-weight: 600;">Status: Sistem Online ✅</span>
            </div>
        </footer>
    </main>
</body>
</html>
