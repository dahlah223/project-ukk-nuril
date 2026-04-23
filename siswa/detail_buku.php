<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "siswa") { 
    header("location:../index.php"); 
    exit; 
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id='$id'");
$buku = mysqli_fetch_assoc($query);

if (!$buku) {
    header("location:riwayat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membaca: <?= $buku['judul'] ?> - PerpusKita</title>
    <!-- Fonts: Plus Jakarta Sans & Merriweather -->
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --bg-body: #f8fafc;
            --white: #ffffff;
            --text-dark: #0f172a;
            --text-slate: #475569;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-body); 
            margin: 0; 
            color: var(--text-dark);
            scroll-behavior: smooth;
        }

        /* --- Tombol Kembali Kontras --- */
        .btn-back-floating {
            position: fixed;
            top: 2rem;
            left: 2rem;
            z-index: 1100;
            width: 56px;
            height: 56px;
            background: var(--primary); /* Diubah jadi biru agar kontras */
            border-radius: 18px; /* Bentuk squirclish modern */
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .btn-back-floating:hover {
            transform: scale(1.1) rotate(-8deg);
            background: var(--text-dark);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-back-floating svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: white; /* Panah putih agar terlihat jelas */
            stroke-width: 3;
            transition: 0.3s;
        }

        /* --- Header Glassmorphism --- */
        .nav-header {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 1.2rem 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            text-align: center;
        }

        .nav-header span {
            font-weight: 800; font-size: 0.8rem; letter-spacing: 2px;
            text-transform: uppercase; color: var(--text-slate);
        }

        /* --- Layout --- */
        .wrapper {
            max-width: 1100px; margin: 120px auto 60px;
            display: grid; grid-template-columns: 320px 1fr; gap: 4rem;
            padding: 0 2rem;
        }

        .book-sidebar { position: sticky; top: 120px; height: fit-content; }
        
        .book-card-static {
            background: var(--white); padding: 1.5rem; border-radius: 32px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
        }

        .cover-img { 
            width: 100%; aspect-ratio: 3/4.5; object-fit: cover;
            border-radius: 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .book-status {
            background: #f0fdf4; color: #16a34a; text-align: center;
            padding: 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 800;
        }

        /* --- Reading Section --- */
        .book-content-main {
            background: var(--white); padding: 5rem; border-radius: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }

        .badge-tag {
            background: var(--primary); color: white; padding: 6px 14px;
            border-radius: 10px; font-size: 0.7rem; font-weight: 800;
            display: inline-block; margin-bottom: 2rem;
        }

        h1 { font-size: 3rem; font-weight: 800; margin: 0 0 10px 0; letter-spacing: -1.5px; line-height: 1.1; }
        .author-name { font-size: 1.1rem; color: var(--primary); font-weight: 700; margin-bottom: 4rem; display: block; }

        .reading-area {
            font-family: 'Merriweather', serif;
            line-height: 2.2; font-size: 1.25rem; color: #334155;
            white-space: pre-wrap; text-align: justify;
        }

        /* Dropcap Mewah */
        .reading-area::first-letter {
            float: left; font-size: 4.5rem; line-height: 1;
            font-weight: 900; padding: 8px 12px 4px 0; color: var(--primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .reading-footer {
            margin-top: 5rem; padding-top: 2rem; border-top: 1px solid #f1f5f9;
            text-align: center; color: #94a3b8; font-size: 0.85rem; font-style: italic;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .wrapper { grid-template-columns: 1fr; }
            .book-sidebar { position: static; max-width: 280px; margin: 0 auto 3rem; }
            .btn-back-floating { top: 1rem; left: 1rem; width: 45px; height: 45px; border-radius: 14px; }
            .book-content-main { padding: 3rem 2rem; }
            h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <!-- Tombol Kembali Melayang Kontras -->
    <a href="riwayat.php" class="btn-back-floating" title="Kembali">
        <svg viewBox="0 0 24 24" xmlns="http://w3.org">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <header class="nav-header">
        <span>Premium Reading Experience</span>
    </header>

    <div class="wrapper">
        <aside class="book-sidebar">
            <div class="book-card-static">
                <img src="../admin/img/<?= $buku['sampul'] ?>" class="cover-img" alt="Sampul">
                <div class="book-status">✓ ACCESS GRANTED</div>
                <p style="font-size: 0.7rem; color: #94a3b8; text-align: center; margin-top: 15px; line-height: 1.5;">
                    Selamat menikmati bacaan Anda. Konten ini tersedia secara eksklusif untuk anggota PerpusKita.
                </p>
            </div>
        </aside>

        <main class="book-content-main">
            <div class="badge-tag">OFFICIAL CONTENT</div>
            <h1><?= $buku['judul'] ?></h1>
            <span class="author-name">by <?= $buku['penulis'] ?></span>

            <div class="reading-area">
                <?= nl2br($buku['isi_buku']) ?>
            </div>

            <footer class="reading-footer">
                — End of Document —<br>
                <small>Terima kasih telah membaca melalui platform kami</small>
            </footer>
        </main>
    </div>

</body>
</html>
