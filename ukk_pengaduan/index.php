<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-ASPIRASI | Portal Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --bg-light: #f8fafc;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-light);
            color: var(--primary);
            overflow-x: hidden;
        }

        @keyframes logo-float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
        }

        .animated-logo {
            animation: logo-float 5s ease-in-out infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            background: white;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.08);
        }

        .nav-link {
            position: relative;
            transition: color 0.3s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0; height: 2px; bottom: -4px; left: 0;
            background: var(--accent);
            transition: width 0.3s;
        }
        .nav-link:hover::after { width: 100%; }

        /* Mobile Menu Animation */
        #mobile-menu {
            transition: all 0.3s ease-in-out;
            transform: translateY(-100%);
            opacity: 0;
        }
        #mobile-menu.active {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="antialiased">

    <nav class="fixed top-0 w-full z-[100] py-4 md:py-6 px-6 md:px-10 flex justify-between items-center transition-all duration-300" id="navbar">
        <div class="flex items-center gap-3 relative z-[110]">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-paper-plane text-sm"></i>
            </div>
            <span class="font-extrabold text-lg md:text-xl tracking-tighter">E-ASPIRASI</span>
        </div>
        
        <div class="hidden md:flex gap-10 font-bold text-xs uppercase tracking-widest text-slate-400">
            <a href="index.php" class="nav-link text-slate-900">Beranda</a>
            <a href="login_siswa.php" class="nav-link hover:text-slate-900">Siswa</a>
            <a href="login.php" class="nav-link hover:text-slate-900 text-red-500">Admin Area</a>
        </div>

        <div class="flex items-center gap-4 relative z-[110]">
            <a href="login_siswa.php" class="hidden sm:inline-block bg-slate-900 text-white px-6 py-2.5 rounded-full text-xs font-bold hover:bg-blue-600 transition shadow-lg uppercase tracking-wider">
                Masuk <i class="fas fa-sign-in-alt ml-2"></i>
            </a>
            <button class="md:hidden text-2xl" id="menu-btn">
                <i class="fas fa-bars" id="menu-icon"></i>
            </button>
        </div>

        <div id="mobile-menu" class="fixed inset-0 bg-white z-[105] flex flex-col items-center justify-center gap-8 md:hidden">
            <a href="index.php" class="text-2xl font-bold">Beranda</a>
            <a href="login_siswa.php" class="text-2xl font-bold">Portal Siswa</a>
            <a href="login.php" class="text-2xl font-bold text-red-500">Admin Area</a>
            <a href="login_siswa.php" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-bold">Masuk Sekarang</a>
        </div>
    </nav>

    <section class="min-h-screen flex items-center pt-28 md:pt-20 pb-12">
        <div class="container mx-auto px-6 md:px-20 grid md:grid-cols-2 gap-12 items-center">
            
            <div class="text-center md:text-left order-2 md:order-1">
                <div class="inline-block bg-blue-50 text-blue-600 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6 border border-blue-100">
                    Sistem Aspirasi Digital v2.0
                </div>
                <h1 class="text-4xl md:text-7xl font-extrabold mb-6 leading-[1.1] tracking-tighter text-[#1B2559]">
                    Sampaikan <br> <span class="text-blue-600">Aspirasi Anda</span> <br class="hidden md:block"> Untuk Sekolah.
                </h1>
                <p class="text-slate-500 text-base md:text-lg mb-10 max-w-md mx-auto md:mx-0 font-medium leading-relaxed">
                    Wadah resmi pengaduan dan saran siswa secara digital, transparan, dan terintegrasi untuk kemajuan bersama.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="registrasi.php" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-blue-200 hover:bg-blue-700 transition transform hover:-translate-y-1 text-center">Kirim Aspirasi</a>
                    <a href="login_siswa.php" class="bg-white text-slate-900 px-8 py-4 rounded-2xl font-bold border border-slate-200 hover:bg-slate-50 transition transform hover:-translate-y-1 text-center">Lihat Status</a>
                </div>
            </div>

            <div class="flex justify-center relative order-1 md:order-2 mb-8 md:mb-0">
                <div class="animated-logo z-10">
                    <div class="w-48 h-48 md:w-96 md:h-96 bg-white rounded-[3rem] md:rounded-[4rem] shadow-2xl flex items-center justify-center border border-slate-100">
                        <i class="fas fa-comments text-[70px] md:text-[120px] text-blue-600"></i>
                    </div>
                </div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 md:w-80 md:h-80 bg-blue-400/20 rounded-full blur-[80px] md:blur-[100px]"></div>
            </div>

        </div>
    </section>

    <section class="py-20 bg-slate-50/80">
        <div class="container mx-auto px-6 max-w-5xl">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-[#1B2559]">Pilih Akses Portal</h2>
                <p class="text-slate-400 mt-2 font-medium">Silahkan masuk sesuai hak akses Anda</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 md:gap-8">
                <a href="login_siswa.php" class="glass-card p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] flex flex-col sm:flex-row items-center text-center sm:text-left gap-6 group">
                    <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-3xl flex items-center justify-center text-3xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shrink-0">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#1B2559]">Portal Siswa</h3>
                        <p class="text-slate-400 text-sm font-medium mt-1">Masuk untuk kirim laporan & cek status</p>
                    </div>
                </a>

                <a href="login.php" class="glass-card p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] flex flex-col sm:flex-row items-center text-center sm:text-left gap-6 group">
                    <div class="w-20 h-20 bg-slate-900 text-white rounded-3xl flex items-center justify-center text-3xl group-hover:bg-blue-600 transition-all duration-500 shrink-0">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#1B2559]">Portal Admin</h3>
                        <p class="text-slate-400 text-sm font-medium mt-1">Kelola dan tanggapi aspirasi masuk</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <footer class="py-12 text-center border-t border-slate-100 bg-white px-6">
        <div class="flex justify-center gap-8 mb-8 text-slate-300 text-2xl">
            <i class="fab fa-instagram hover:text-blue-600 transition-colors cursor-pointer"></i>
            <i class="fab fa-facebook hover:text-blue-600 transition-colors cursor-pointer"></i>
            <i class="fab fa-twitter hover:text-blue-600 transition-colors cursor-pointer"></i>
        </div>
        <p class="text-slate-400 text-[9px] md:text-[10px] font-black uppercase tracking-[0.3em] md:tracking-[0.4em] leading-loose">
            © 2026 E-ASPIRASI SYSTEM <span class="hidden md:inline">•</span> <br class="md:hidden"> PREMIUM DASHBOARD EXPERIENCE
        </p>
    </footer>

    <script>
        // Efek Navbar saat Scroll
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.classList.add('bg-white/95', 'backdrop-blur-md', 'py-4', 'shadow-sm', 'border-b', 'border-slate-100');
                nav.classList.remove('py-6');
            } else {
                nav.classList.remove('bg-white/95', 'backdrop-blur-md', 'py-4', 'shadow-sm', 'border-b', 'border-slate-100');
                nav.classList.add('py-6');
            }
        });

        // Mobile Menu Toggle Logic
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            // Toggle Icon
            if (mobileMenu.classList.contains('active')) {
                menuIcon.classList.replace('fa-bars', 'fa-times');
                document.body.style.overflow = 'hidden'; // Prevent scroll
            } else {
                menuIcon.classList.replace('fa-times', 'fa-bars');
                document.body.style.overflow = 'auto';
            }
        });

        // Close menu when clicking links
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                menuIcon.classList.replace('fa-times', 'fa-bars');
                document.body.style.overflow = 'auto';
            });
        });
    </script>
</body>
</html>