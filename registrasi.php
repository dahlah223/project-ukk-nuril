<?php
include "koneksi.php";

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role     = "siswa"; 

    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username sudah digunakan, cari yang lain!";
    } else {
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        
        if (mysqli_query($koneksi, $query)) {
            $success = "Akun berhasil dibuat! Mengalihkan...";
            header("refresh:2; url=index.php");
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - PerpusKita</title>
    <!-- Google Fonts: Inter -->
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary-grad: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            --accent: #2563eb;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
            /* Dekorasi background abstrak */
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.1) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.1) 0, transparent 50%);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
            overflow: hidden;
        }

        /* Animasi masuk */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reg-card { 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 3rem; 
            border-radius: 28px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); 
            border: 1px solid rgba(255, 255, 255, 0.6);
            width: 100%; 
            max-width: 420px; 
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .header-area { text-align: center; margin-bottom: 2.5rem; }
        
        .header-area h1 { 
            margin: 0; 
            font-size: 2.25rem; 
            font-weight: 800; 
            background: var(--primary-grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1.5px; 
        }

        .header-area p { color: var(--text-muted); margin-top: 0.5rem; font-size: 0.95rem; }

        .form-group { margin-bottom: 1.5rem; }
        
        label { 
            display: block; 
            font-size: 0.8rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            color: var(--text-main); 
            margin-bottom: 0.6rem; 
            margin-left: 4px;
            letter-spacing: 0.05em;
        }
        
        input { 
            width: 100%; 
            padding: 0.9rem 1.2rem; 
            border: 2px solid #f1f5f9; 
            border-radius: 14px; 
            box-sizing: border-box; 
            transition: all 0.3s ease;
            font-size: 1rem;
            background: #f8fafc;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* Desain Tombol Register */
        button { 
            width: 100%; 
            padding: 1.1rem; 
            background: var(--primary-grad); 
            color: white; 
            border: none; 
            border-radius: 14px; 
            font-weight: 700; 
            font-size: 1rem;
            cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(67, 56, 202, 0.3);
            margin-top: 1rem;
        }

        button:hover { 
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(67, 56, 202, 0.4);
            filter: brightness(1.1);
        }

        button:active { transform: translateY(0); }

        /* Link Kembali */
        .btn-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            padding: 0.9rem;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            border: 2px solid #f1f5f9;
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .btn-link:hover {
            background: #f1f5f9;
            color: var(--text-dark);
            border-color: #e2e8f0;
        }

        /* Notifikasi Modern */
        .msg {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .error { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
        .success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
    <div class="reg-card">
        <div class="header-area">
            <h1>PerpusKita</h1>
            <p>Ayo bergabung jadi Anggota Baru!</p>
        </div>

        <?php if($error): ?>
            <div class="msg error"><span>⚠️</span> <?= $error ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="msg success"><span>✅</span> <?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username Baru</label>
                <input type="text" name="username" placeholder="Contoh: budi_perpus" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Min. 8 karakter" required>
            </div>
            <button type="submit" name="register">Daftar Akun Sekarang</button>
            
            <a href="index.php" class="btn-link">← Sudah punya akun? Login</a>
        </form>
    </div>
</body>
</html>
