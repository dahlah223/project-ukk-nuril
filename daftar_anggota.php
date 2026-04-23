<?php
session_start();

// 1. Cek Session (Hapus "../" karena file sudah di folder utama)
if (!isset($_SESSION['role']) || $_SESSION['role'] != "siswa") {
    header("location:index.php"); // Diubah dari ../index.php
    exit;
}

// 2. Sesuaikan jalur koneksi
include "koneksi.php"; // Diubah dari ../koneksi.php

$error = "";
$success = "";

if (isset($_POST['daftar'])) {
    $user_id = $_SESSION['user_id'];
    $nama = trim($_POST['nama']);
    $nis  = trim($_POST['nis']);
    $alamat = trim($_POST['alamat']);

    if (!is_numeric($nis)) {
        $error = "NIS harus berupa angka!";
    } else {
        // Cek apakah user_id sudah terdaftar
        $cek = $koneksi->prepare("SELECT id FROM anggota WHERE user_id = ?");
        $cek->bind_param("i", $user_id);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Akun Anda sudah terdaftar sebagai anggota!";
        } else {
            // Cek apakah NIS sudah dipakai
            $cekNis = $koneksi->prepare("SELECT id FROM anggota WHERE nis = ?");
            $cekNis->bind_param("s", $nis);
            $cekNis->execute();
            $cekNis->store_result();

            if ($cekNis->num_rows > 0) {
                $error = "NIS sudah digunakan oleh orang lain!";
            } else {
                // Simpan data (Pastikan kolom nama_lengkap sesuai dengan DB kamu)
                $stmt = $koneksi->prepare("INSERT INTO anggota (user_id, nama_lengkap, nis, alamat) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $user_id, $nama, $nis, $alamat);

                if ($stmt->execute()) {
                    $success = "Pendaftaran berhasil! Mengalihkan...";
                    // Beri jeda 2 detik lalu pindah ke dashboard siswa
                    header("refresh:2; url=siswa/dashboard.php");
                } else {
                    $error = "Gagal menyimpan: " . $koneksi->error; 
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota - PerpusKita</title>
    <style>
        /* Desain Body: Menggunakan font Inter untuk tampilan modern, background abu-abu muda, dan layout flex untuk centering */
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
        }
        
        /* Desain Card: Kotak putih dengan padding, border radius, shadow untuk kedalaman, dan border halus */
        .card { 
            background: white; 
            padding: 2.5rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); 
            border: 1px solid #e2e8f0; 
            width: 100%; 
            max-width: 400px; 
        }
        
        /* Desain Heading: Teks center, ukuran sedang, warna gelap */
        h2 { 
            margin: 0 0 0.5rem; 
            font-size: 1.5rem; 
            text-align: center; 
            color: #0f172a; 
        }
        
        /* Desain Paragraf: Teks center, warna muted, margin bawah */
        p { 
            text-align: center; 
            color: #64748b; 
            margin-bottom: 2rem; 
            font-size: 0.875rem; 
        }
        
        /* Desain Form Group: Margin bawah untuk spacing */
        .form-group { 
            margin-bottom: 1.5rem; 
        }
        
        /* Desain Label: Teks uppercase kecil, warna muted, margin bawah */
        label { 
            display: block; 
            font-size: 0.75rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            color: #64748b; 
            margin-bottom: 0.5rem; 
        }
        
        /* Desain Input dan Textarea: Lebar penuh, padding, border halus, border radius, transisi untuk focus */
        input, textarea { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #e2e8f0; 
            border-radius: 6px; 
            box-sizing: border-box; 
            transition: border-color 0.2s; 
        }
        
        /* Desain Focus untuk Input: Border biru dan shadow ringan saat focus */
        input:focus, textarea:focus { 
            outline: none; 
            border-color: #2563eb; 
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); 
        }
        
        /* Desain Button: Lebar penuh, background biru, warna putih, border radius, transisi hover */
        button { 
            width: 100%; 
            padding: 0.75rem; 
            background: #2563eb; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: background 0.2s; 
        }
        
        /* Desain Hover Button: Background biru gelap saat hover */
        button:hover { 
            background: #1d4ed8; 
        }
        
        /* Desain Pesan: Padding, border radius, center, margin bawah */
        .msg { 
            font-size: 0.875rem; 
            margin-bottom: 1rem; 
            text-align: center; 
            padding: 0.5rem; 
            border-radius: 6px; 
        }
        
        /* Desain Pesan Error: Background merah muda, teks merah, border merah */
        .msg[style*="red"] { 
            background: #fef2f2; 
            color: #dc2626; 
            border: 1px solid #fecaca; 
        }
        
        /* Desain Pesan Success: Background hijau muda, teks hijau, border hijau */
        .msg[style*="green"] { 
            background: #f0fdf4; 
            color: #16a34a; 
            border: 1px solid #bbf7d0; 
        }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="text-align: center;">Daftar Anggota</h2>

        <?php if ($error) : ?>
            <div class="msg" style="color:red;">❌ <?= $error ?></div>
        <?php endif; ?>

        <?php if ($success) : ?>
            <div class="msg" style="color:green;">✅ <?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Contoh: Budi Santoso" required>
            
            <label>NIS</label>
            <input type="text" name="nis" placeholder="Contoh: 12345" required>
            
            <label>Alamat</label>
            <textarea name="alamat" rows="3" placeholder="Alamat lengkap..." required></textarea>
            
            <button type="submit" name="daftar">Daftar Sekarang</button>
        </form>
    </div>
</body>
</html>
