<?php
session_start();
require 'functions.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            // SET SESSION
            $_SESSION['login'] = true;
            $_SESSION['id'] = $row['id_admin'];
            $_SESSION['nama'] = $row['nama_petugas'];
            $_SESSION['role'] = 'admin';

            header("Location: admin.php");
            exit;
        }
    }
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | E-ASPIRASI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617; 
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            overflow-y: auto; /* Mengizinkan scroll jika layar pendek */
            padding: 20px;
        }

        #particles-js {
            position: fixed; /* Fixed agar tidak bergeser saat scroll */
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .auth-card {
            position: relative;
            z-index: 10;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px);
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem 2rem; /* Padding lebih kecil di mobile */
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin: auto;
        }

        @media (min-width: 640px) {
            .auth-card {
                padding: 3rem;
                border-radius: 2.5rem;
            }
        }

        .input-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .input-box:focus-within {
            border-color: #6366f1;
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-admin {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }

        .admin-icon {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        /* Sembunyikan scrollbar untuk tampilan bersih */
        body::-webkit-scrollbar { display: none; }
        body { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body>

    <div id="particles-js"></div>
    
    <div class="auth-card">
        <div class="text-center mb-8 md:mb-10">
            <div class="w-14 h-14 md:w-16 md:h-16 admin-icon text-indigo-400 rounded-2xl flex items-center justify-center text-xl md:text-2xl mx-auto mb-4">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">Admin Portal</h2>
            <p class="text-slate-400 text-xs md:text-sm mt-1">Otoritas Akses Petugas</p>
        </div>

        <?php if (isset($_POST['login'])) : ?>
            <?php endif; ?>

        <form action="" method="post" class="space-y-4 md:space-y-5">
            <div class="input-box flex items-center px-4 py-3 md:py-3.5 rounded-xl md:rounded-2xl">
                <i class="fas fa-user-shield text-indigo-400/60 w-5 md:w-6 text-center"></i>
                <input type="text" name="username" placeholder="Username Petugas" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-white ml-3 placeholder:text-slate-500">
            </div>

            <div class="input-box flex items-center px-4 py-3 md:py-3.5 rounded-xl md:rounded-2xl">
                <i class="fas fa-key text-indigo-400/60 w-5 md:w-6 text-center"></i>
                <input type="password" name="password" placeholder="Password" required 
                    class="bg-transparent border-none outline-none w-full text-sm font-medium text-white ml-3 placeholder:text-slate-500">
            </div>
            
            <button type="submit" name="login" class="btn-admin w-full text-white font-bold py-3.5 md:py-4 rounded-xl md:rounded-2xl mt-4 text-[10px] md:text-xs tracking-[0.2em] uppercase shadow-lg shadow-indigo-900/20">
                Authorize Access
            </button>
        </form>

        <div class="mt-8 text-center border-t border-white/5 pt-8">
            <a href="index.php" class="text-slate-500 hover:text-white text-[10px] md:text-xs font-semibold flex items-center justify-center transition uppercase tracking-widest">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 60, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#6366f1" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.3, "random": true },
                "size": { "value": 2, "random": true },
                "line_linked": { 
                    "enable": true, 
                    "distance": 150, 
                    "color": "#6366f1", 
                    "opacity": 0.15, 
                    "width": 1 
                },
                "move": { 
                    "enable": true, 
                    "speed": 1.2, 
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
                    "grab": { "distance": 180, "line_linked": { "opacity": 0.4 } }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>