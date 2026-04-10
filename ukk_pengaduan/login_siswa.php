<?php
session_start();
require 'functions.php';

// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'siswa') {
        header("Location: siswa.php");
        exit;
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
        exit;
    }
}

if (isset($_POST['login'])) {
    // Menggunakan $conn dari functions.php
    $nis = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Query mencari siswa berdasarkan NIS
    $result = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");

    // Cek apakah user ditemukan
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi password (input user vs hash di database)
        if (password_verify($password, $row['password'])) {
            
            // --- SET SESSION ---
            $_SESSION['login'] = true;
            $_SESSION['id'] = $row['nis'];      
            $_SESSION['nama'] = $row['nama']; 
            $_SESSION['role'] = 'siswa';
            $_SESSION['kelas'] = $row['kelas']; // Tambahan jika perlu di dashboard

            header("Location: siswa.php");
            exit;
        } else {
            // Jika password salah
            echo "<script>alert('Login Gagal! Password salah.');</script>";
        }
    } else {
        // Jika NIS tidak ditemukan
        echo "<script>alert('Login Gagal! NIS tidak terdaftar.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa | E-ASPIRASI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .auth-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 2.5rem;
            box-shadow: 0 20px 50px rgba(59, 130, 246, 0.08);
            width: 100%;
            max-width: 400px;
            padding: 3rem;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .input-box {
            background: #f8fafc;
            border: 1.5px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .input-box:focus-within {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.05);
        }

        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>
    
    <div class="auth-card">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 border border-blue-100">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Portal Siswa</h2>
            <p class="text-slate-400 text-sm mt-1">Gunakan NIS untuk mengakses akun</p>
        </div>

        <form action="" method="post" class="space-y-5">
            <div class="input-box flex items-center px-4 py-3.5 rounded-2xl">
                <i class="fas fa-id-card text-blue-400/60 w-6"></i>
                <input type="text" name="username" placeholder="Nomor Induk Siswa" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 ml-2 placeholder:text-slate-400">
            </div>

            <div class="input-box flex items-center px-4 py-3.5 rounded-2xl">
                <i class="fas fa-lock text-blue-400/60 w-6"></i>
                <input type="password" name="password" placeholder="Password" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 ml-2 placeholder:text-slate-400">
            </div>
            
            <button type="submit" name="login" class="btn-login w-full text-white font-bold py-4 rounded-2xl mt-4 text-sm tracking-wide shadow-lg shadow-blue-100">
                MASUK SEKARANG
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-slate-500 text-xs">Belum memiliki akun? 
                <a href="registrasi.php" class="text-blue-600 font-bold hover:text-blue-700 transition">Daftar Akun Baru</a>
            </p>
            <div class="mt-6 pt-6 border-t border-slate-50">
                <a href="index.php" class="text-slate-400 hover:text-slate-600 text-[11px] font-bold uppercase tracking-[0.2em] inline-flex items-center transition">
                    <i class="fas fa-arrow-left mr-2"></i> Beranda Utama
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 90, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#3b82f6" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.2, "random": false },
                "size": { "value": 3, "random": true },
                "line_linked": { 
                    "enable": true, 
                    "distance": 150, 
                    "color": "#3b82f6", 
                    "opacity": 0.2, 
                    "width": 1 
                },
                "move": { 
                    "enable": true, 
                    "speed": 2, 
                    "direction": "none", 
                    "random": false, 
                    "straight": false, 
                    "out_mode": "out", 
                    "bounce": false 
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": { "enable": true, "mode": "grab" },
                    "onclick": { "enable": true, "mode": "push" },
                    "resize": true
                },
                "modes": {
                    "grab": { "distance": 200, "line_linked": { "opacity": 0.4 } }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>