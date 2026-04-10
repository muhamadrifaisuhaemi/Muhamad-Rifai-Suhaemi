<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out... | E-ASPIRASI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            /* Menggunakan min-height agar tidak terpotong di layar pendek */
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.3);
        }

        .loader {
            width: 42px;
            height: 42px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-bottom-color: #fff;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fade-out {
            animation: fadeOut 0.8s ease-out forwards;
            animation-delay: 2.2s;
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.95); }
        }

        /* Mencegah scrollbar muncul saat animasi */
        body::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="p-6">

    <div class="glass fade-out max-w-sm w-full p-8 md:p-10 rounded-[2.5rem] text-center text-white relative z-10">
        <div class="mb-6 flex justify-center">
            <span class="loader"></span>
        </div>

        <h2 class="text-xl md:text-2xl font-extrabold mb-2 tracking-tight">Terima Kasih!</h2>
        <p class="text-white/80 text-xs md:text-sm font-medium leading-relaxed px-2">
            Sesi Anda telah berakhir. <br> Menghapus data akses secara aman...
        </p>

        <div class="mt-8 w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
            <div id="progress" class="bg-white h-full transition-all duration-[2500ms] ease-out" style="width: 0%"></div>
        </div>
    </div>

    <div class="absolute top-[-10%] left-[-10%] w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>

    <script>
        // Memulai animasi progress bar segera setelah halaman dimuat
        window.addEventListener('load', () => {
            setTimeout(() => {
                const prog = document.getElementById('progress');
                if(prog) prog.style.width = '100%';
            }, 100);
        });

        // Redirect otomatis setelah 3 detik
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 3000);
    </script>
</body>
</html>