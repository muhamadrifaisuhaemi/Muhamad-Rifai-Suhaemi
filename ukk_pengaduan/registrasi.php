<?php
session_start();
require 'functions.php';

if (isset($_POST['register'])) {
    // Pastikan fungsi registrasi() di functions.php menggunakan password_hash
    $res = registrasi($_POST);
    if ($res > 0) {
        echo "<script>
                alert('Pendaftaran Berhasil! Silahkan Login.');
                window.location='login_siswa.php';
              </script>";
    } else {
        echo "<script>alert('Gagal! NIS mungkin sudah terdaftar atau data tidak valid.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa | E-ASPIRASI</title>
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
            background-color: #ffffff;
            z-index: 1;
        }

        .auth-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .input-box {
            background: #f9fafb;
            border: 1.5px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        .input-box:focus-within {
            border-color: #ef4444;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.05);
        }

        .btn-regis {
            background: #ef4444;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-regis:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
        }

        /* Custom Scrollbar for Card if needed */
        .auth-card::-webkit-scrollbar {
            width: 5px;
        }
        .auth-card::-webkit-scrollbar-thumb {
            background: #f1f1f1;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>
    
    <div class="auth-card">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-xl mx-auto mb-4 border border-red-100">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Pendaftaran Siswa</h2>
            <p class="text-slate-400 text-sm mt-1">Silahkan buat akun untuk akses sistem</p>
        </div>

        <form action="" method="post" class="space-y-4">
            <input type="hidden" name="role" value="siswa">
            
            <div class="input-box flex items-center px-4 py-3 rounded-xl">
                <i class="fas fa-id-card text-slate-400 w-6"></i>
                <input type="text" name="username" placeholder="NIS" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 ml-2">
            </div>

            <div class="input-box flex items-center px-4 py-3 rounded-xl">
                <i class="fas fa-user text-slate-400 w-6"></i>
                <input type="text" name="nama" placeholder="Nama Lengkap" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 ml-2">
            </div>

            <div class="input-box flex items-center px-4 py-3 rounded-xl">
                <i class="fas fa-graduation-cap text-slate-400 w-6"></i>
                <input type="text" name="kelas" placeholder="Kelas" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 ml-2">
            </div>

            <div class="input-box flex items-center px-4 py-3 rounded-xl">
                <i class="fas fa-lock text-slate-400 w-6"></i>
                <input type="password" name="password" placeholder="Password" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 ml-2">
            </div>
            
            <button type="submit" name="register" class="btn-regis w-full text-white font-bold py-3.5 rounded-xl mt-4 text-sm tracking-wide">
                DAFTAR SEKARANG
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-slate-500 text-xs">Sudah memiliki akun? 
                <a href="login_siswa.php" class="text-red-600 font-bold hover:text-red-700 transition">Masuk Akun</a>
            </p>
            <div class="mt-6 flex justify-center border-t border-slate-100 pt-6">
                <a href="index.php" class="text-slate-400 hover:text-slate-600 text-[11px] font-bold uppercase tracking-widest flex items-center transition">
                    <i class="fas fa-arrow-left mr-2"></i> Beranda
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 100, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#ef4444" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.2, "random": false },
                "size": { "value": 3, "random": true },
                "line_linked": { 
                    "enable": true, 
                    "distance": 150, 
                    "color": "#ef4444", 
                    "opacity": 0.15, 
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
                    "grab": { "distance": 200, "line_linked": { "opacity": 0.3 } }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>