<?php
session_start();
include "koneksi.php";

$error = ""; 

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // MENGGUNAKAN PREPARED STATEMENTS (Jauh lebih aman dari SQL Injection)
    $stmt = $koneksi->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Verifikasi Password (Jika kamu pakai password_hash, gunakan password_verify di sini)
        // Untuk sekarang kita pakai perbandingan langsung sesuai kode awalmu:
        if ($password === $data['password']) {
            $_SESSION['user_id']  = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role']     = $data['role'];

            if ($data['role'] == "admin") {
                header("location:admin/dashboard.php");
                exit;
            } else {
                // Cek status anggota dengan Prepared Statement juga
                $stmt_cek = $koneksi->prepare("SELECT id FROM anggota WHERE user_id = ?");
                $stmt_cek->bind_param("i", $data['id']);
                $stmt_cek->execute();
                $res_anggota = $stmt_cek->get_result();
                
                if ($res_anggota->num_rows > 0) {
                    header("location:siswa/dashboard.php");
                } else {
                    header("location:daftar_anggota.php");
                }
                exit;
            }
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PerpusKita</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --bg-gradient: radial-gradient(circle at top right, #e0e7ff, #f8fafc);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-gradient);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 50%;
            top: -50px; left: -50px;
            z-index: -1;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .login-card { 
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 3rem; 
            border-radius: 24px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); 
            border: 1px solid rgba(255, 255, 255, 0.4);
            width: 100%; 
            max-width: 420px; 
            animation: fadeInScale 0.6s ease-out;
            position: relative;
            z-index: 10;
        }

        .logo-area { text-align: center; margin-bottom: 2rem; }
        .logo-area h1 { 
            margin: 0; font-size: 2rem; font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }
        .logo-area p { color: #64748b; margin-top: 0.5rem; font-size: 0.95rem; }

        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem; }
        
        input { 
            width: 100%; padding: 0.9rem 1.2rem; border: 2px solid #f1f5f9; 
            border-radius: 12px; box-sizing: border-box; background: #f8fafc;
            transition: all 0.3s ease; font-size: 1rem; outline: none;
        }

        input:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }

        button { 
            width: 100%; padding: 1rem; background: var(--primary-gradient); color: white; 
            border: none; border-radius: 12px; font-weight: 700; font-size: 1rem;
            cursor: pointer; transition: all 0.3s ease; margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        button:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4); }

        .error { background: #fff1f2; color: #e11d48; padding: 0.8rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; font-size: 0.875rem; border: 1px solid #fecdd3; }
        .footer-links { text-align: center; margin-top: 2rem; font-size: 0.9rem; color: #64748b; }
        .footer-links a { color: #2563eb; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-area">
            <h1>PerpusKita</h1>
            <p>E-Library Management System</p>
        </div>
        
        <?php if($error != ""): ?>
            <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Autocomplete off ditambahkan di sini -->
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <!-- Menambahkan autocomplete="new-password" seringkali lebih ampuh mengelabui browser -->
                <input type="password" name="password" placeholder="••••••••" required autocomplete="new-password">
            </div>
            <button type="submit" name="login">Masuk ke Dashboard</button>
            
            <div class="footer-links">
                Belum punya akun? <a href="registrasi.php">Buat Akun Baru</a>
            </div>
        </form>
    </div>

</body>
</html>
