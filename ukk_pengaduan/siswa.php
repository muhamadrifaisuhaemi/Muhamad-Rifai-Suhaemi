<?php
session_start();
require 'functions.php';

// Proteksi Halaman
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

$nis = $_SESSION['id'];
$nama_user = $_SESSION['nama'];
global $conn;

// Navigasi Halaman
$page = $_GET['page'] ?? 'dashboard';

// --- LOGIKA KIRIM LAPORAN ---
if (isset($_POST["kirim"])) {
    $isi_laporan = htmlspecialchars($_POST["isi_laporan"]);
    $id_kategori = $_POST["id_kategori"];
    $lokasi = htmlspecialchars($_POST["lokasi"]);
    $mode_waktu = $_POST["mode_waktu"];

    if ($mode_waktu == "otomatis") {
        $tanggal_final = date("Y-m-d H:i:s");
    } else {
        $tanggal_final = $_POST["tanggal_kejadian"] . " " . $_POST["jam_kejadian"] . ":00";
    }

    $nama_foto = $_FILES['foto']['name'];
    if($nama_foto != "") {
        $ekstensi = pathinfo($nama_foto, PATHINFO_EXTENSION);
        $nama_baru = uniqid() . "." . $ekstensi;
        if (!is_dir('./assets/img/')) {
            mkdir('./assets/img/', 0777, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], './assets/img/' . $nama_baru);
    } else { $nama_baru = ""; }

    $query_insert = "INSERT INTO aspirasi VALUES (NULL, '$nis', '$id_kategori', '$lokasi', '$isi_laporan', '$nama_baru', '$tanggal_final', 'Menunggu', '')";
    mysqli_query($conn, $query_insert);
    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>alert('Aspirasi Berhasil Dikirim!'); document.location.href = 'siswa.php?page=riwayat';</script>";
    }
}

// Logika Hapus Massal
if (isset($_POST["hapus_masal"])) {
    if (!empty($_POST['id_laporan'])) {
        $id_list = implode(",", array_map('intval', $_POST['id_laporan']));
        mysqli_query($conn, "DELETE FROM aspirasi WHERE id_aspirasi IN ($id_list) AND nis = '$nis'");
        echo "<script>alert('Laporan berhasil dihapus!'); document.location.href = 'siswa.php?page=riwayat';</script>";
    }
}

// DATA STATISTIK UNTUK DASHBOARD
$count_total = query("SELECT COUNT(*) as jml FROM aspirasi WHERE nis = '$nis'")[0]['jml'];
$count_proses = query("SELECT COUNT(*) as jml FROM aspirasi WHERE nis = '$nis' AND status = 'Proses'")[0]['jml'];
$count_selesai = query("SELECT COUNT(*) as jml FROM aspirasi WHERE nis = '$nis' AND status = 'Selesai'")[0]['jml'];

// Query Riwayat & Tanggapan
$filter_tgl = $_GET['tgl'] ?? '';
$query_riwayat = "SELECT aspirasi.*, kategori.nama_kategori 
                  FROM aspirasi 
                  JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
                  WHERE nis = '$nis'";
if ($filter_tgl) $query_riwayat .= " AND DATE(aspirasi.tanggal) = '$filter_tgl'";
$query_riwayat .= " ORDER BY tanggal DESC";

$riwayat = query($query_riwayat);
$kategori = query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Dashboard - E-Aspirasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .sidebar-active { background: #4f46e5; color: white !important; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .slider-container { overflow: hidden; position: relative; border-radius: 2rem; }
        .slider-wrapper { display: flex; transition: transform 0.7s ease-in-out; }
        .slide { min-width: 100%; position: relative; }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-72 bg-white border-r border-slate-100 flex-shrink-0 flex flex-col fixed h-full z-50">
        <div class="p-8">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-indigo-200 shadow-lg font-bold text-xl">E</div>
                <h1 class="font-extrabold text-xl tracking-tight text-slate-800 uppercase italic">Aspirasi</h1>
            </div>
            
            <nav class="space-y-2">
                <a href="?page=dashboard" class="flex items-center gap-3 p-4 rounded-2xl text-slate-400 font-bold text-sm transition <?= ($page == 'dashboard') ? 'sidebar-active' : 'hover:bg-slate-50 hover:text-indigo-600'; ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="?page=kirim" class="flex items-center gap-3 p-4 rounded-2xl text-slate-400 font-bold text-sm transition <?= ($page == 'kirim') ? 'sidebar-active' : 'hover:bg-slate-50 hover:text-indigo-600'; ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Kirim Aspirasi
                </a>
                <a href="?page=riwayat" class="flex items-center gap-3 p-4 rounded-2xl text-slate-400 font-bold text-sm transition <?= ($page == 'riwayat') ? 'sidebar-active' : 'hover:bg-slate-50 hover:text-indigo-600'; ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Riwayat & Respon
                </a>
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <a href="logout.php" class="flex items-center gap-3 p-4 rounded-2xl text-rose-400 font-bold text-sm hover:bg-rose-50 hover:text-rose-600 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log Out
                    </a>
                </div>
            </nav>
        </div>
        <div class="mt-auto p-8">
            <div class="bg-indigo-600 rounded-3xl p-5 text-white">
                <p class="text-[10px] font-black uppercase opacity-60 mb-1">ID SISWA</p>
                <p class="text-xs font-bold truncate"><?= $nama_user; ?></p>
                <p class="text-[10px] mt-1 opacity-80"><?= $nis; ?></p>
            </div>
        </div>
    </aside>

    <main class="flex-1 ml-72 p-10 bg-slate-50/50">
        
        <header class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                    <?php 
                        if($page == 'dashboard') echo "Ringkasan Laporan";
                        elseif($page == 'kirim') echo "Buat Laporan Baru";
                        else echo "Riwayat Aspirasi";
                    ?>
                </h2>
                <p class="text-slate-400 text-sm font-medium">Sistem Informasi Pengaduan Siswa</p>
            </div>
            <div class="text-right">
                <p id="clock-digital" class="text-xl font-black text-indigo-600 tabular-nums">00:00:00</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= date('l, d F Y'); ?></p>
            </div>
        </header>

        <?php if($page == 'dashboard') : ?>
            <div class="space-y-8">
                <div class="slider-container h-64 shadow-2xl shadow-indigo-100">
                    <div class="slider-wrapper" id="slider">
                        <div class="slide bg-indigo-600 flex items-center p-12 text-white">
                            <div class="z-10 relative">
                                <h3 class="text-3xl font-black mb-2 uppercase italic">Suaramu Penting!</h3>
                                <p class="opacity-80 max-w-md text-sm leading-relaxed font-medium">Laporkan setiap kendala fasilitas atau aspirasi untuk sekolah yang lebih baik melalui portal E-Aspirasi.</p>
                            </div>
                            <div class="absolute right-10 opacity-20"><svg class="w-48 h-48" fill="currentColor" viewBox="0 0 20 20"><path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/></svg></div>
                        </div>
                        <div class="slide bg-slate-800 flex items-center p-12 text-white">
                            <div>
                                <h3 class="text-3xl font-black mb-2 uppercase italic">Proses Transparan</h3>
                                <p class="opacity-80 max-w-md text-sm leading-relaxed font-medium">Pantau status laporanmu mulai dari 'Menunggu', 'Proses', hingga 'Selesai' secara real-time.</p>
                            </div>
                            <div class="absolute right-10 opacity-20"><svg class="w-48 h-48" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Laporan</p>
                        <h4 class="text-4xl font-black text-slate-800"><?= $count_total; ?></h4>
                        <div class="mt-4 h-1 w-12 bg-indigo-600 rounded-full"></div>
                    </div>
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sedang Diproses</p>
                        <h4 class="text-4xl font-black text-amber-500"><?= $count_proses; ?></h4>
                        <div class="mt-4 h-1 w-12 bg-amber-500 rounded-full"></div>
                    </div>
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Laporan Selesai</p>
                        <h4 class="text-4xl font-black text-emerald-500"><?= $count_selesai; ?></h4>
                        <div class="mt-4 h-1 w-12 bg-emerald-500 rounded-full"></div>
                    </div>
                </div>

                <div class="bg-indigo-50 p-8 rounded-[2.5rem] border border-indigo-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-indigo-900 mb-1">Ingin mengirim aspirasi?</h4>
                        <p class="text-indigo-600/70 text-xs">Klik tombol di samping untuk mulai menulis laporan baru.</p>
                    </div>
                    <a href="?page=kirim" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-200">Mulai Melapor</a>
                </div>
            </div>

        <?php elseif($page == 'kirim') : ?>
            <div class="max-w-2xl">
                <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40">
                    <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                        <div class="flex p-1 bg-slate-100 rounded-2xl">
                            <label class="flex-1">
                                <input type="radio" name="mode_waktu" value="otomatis" class="hidden peer" checked onchange="toggleWaktu(this.value)">
                                <div class="text-center py-4 rounded-xl text-[10px] font-black uppercase peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-400 transition cursor-pointer">Waktu Sekarang</div>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="mode_waktu" value="manual" class="hidden peer" onchange="toggleWaktu(this.value)">
                                <div class="text-center py-4 rounded-xl text-[10px] font-black uppercase peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-400 transition cursor-pointer">Atur Manual</div>
                            </label>
                        </div>

                        <div id="manualInput" class="hidden grid grid-cols-2 gap-4">
                            <input type="date" name="tanggal_kejadian" class="p-4 bg-slate-50 border-none rounded-2xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                            <input type="time" name="jam_kejadian" class="p-4 bg-slate-50 border-none rounded-2xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Kategori</label>
                                <select name="id_kategori" class="w-full p-4 bg-slate-50 border-none rounded-2xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                                    <?php foreach($kategori as $k) : ?>
                                        <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Lokasi Kejadian</label>
                                <input type="text" name="lokasi" placeholder="Misal: Kantin" required class="w-full p-4 bg-slate-50 border-none rounded-2xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Detail Aspirasi</label>
                            <textarea name="isi_laporan" rows="5" placeholder="Ceritakan detail masalah atau saran..." required class="w-full p-5 bg-slate-50 border-none rounded-[2rem] text-xs font-medium outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Foto Bukti (Opsional)</label>
                            <input type="file" name="foto" class="block w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-600 cursor-pointer">
                        </div>

                        <button type="submit" name="kirim" class="w-full bg-indigo-600 text-white font-black py-5 rounded-[2rem] shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition transform active:scale-95 text-xs uppercase tracking-widest">Kirim Laporan</button>
                    </form>
                </div>
            </div>

        <?php elseif($page == 'riwayat') : ?>
            <div class="space-y-6">
                <div class="flex items-center justify-between bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm">
                    <form action="" method="get" class="flex gap-2">
                        <input type="hidden" name="page" value="riwayat">
                        <input type="date" name="tgl" value="<?= $filter_tgl; ?>" class="p-3 bg-slate-50 rounded-xl text-[10px] font-bold border-none outline-none focus:ring-1 focus:ring-indigo-400">
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest">Filter</button>
                        <?php if($filter_tgl) : ?>
                            <a href="?page=riwayat" class="bg-slate-100 text-slate-400 px-4 py-3 rounded-xl text-[10px] flex items-center justify-center">✖</a>
                        <?php endif; ?>
                    </form>

                    <button type="submit" form="formHapusMasal" name="hapus_masal" onclick="return confirm('Hapus laporan terpilih?')" class="bg-rose-50 text-rose-600 px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition">Hapus Terpilih</button>
                </div>

                <form action="" method="post" id="formHapusMasal" class="space-y-5">
                <?php if(empty($riwayat)) : ?>
                    <div class="bg-white p-20 rounded-[3rem] border-2 border-dashed border-slate-200 text-center">
                        <p class="text-slate-400 font-bold text-sm">Tidak ditemukan riwayat laporan.</p>
                    </div>
                <?php endif; ?>

                <?php foreach($riwayat as $row) : 
                    $colors = ['Menunggu' => 'bg-rose-500', 'Proses' => 'bg-amber-500', 'Selesai' => 'bg-emerald-500'];
                    $accent = $colors[$row['status']] ?? 'bg-slate-400';
                ?>
                <div class="glass-card p-8 rounded-[3rem] flex gap-6 relative overflow-hidden transition hover:shadow-lg">
                    <div class="absolute left-0 top-0 bottom-0 w-2 <?= $accent; ?>"></div>
                    <div class="pt-1">
                        <input type="checkbox" name="id_laporan[]" value="<?= $row['id_aspirasi']; ?>" class="w-6 h-6 rounded-lg text-indigo-600 cursor-pointer">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase tracking-widest"><?= $row['nama_kategori']; ?></span>
                                <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase">ID: #<?= $row['id_aspirasi']; ?> • <?= date('d M Y', strtotime($row['tanggal'])); ?></p>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1 bg-white border border-slate-100 rounded-full shadow-sm">
                                <span class="w-2 h-2 rounded-full <?= $accent; ?> animate-pulse"></span>
                                <span class="text-[9px] font-black uppercase text-slate-700"><?= $row['status']; ?></span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic mb-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-50">"<?= htmlspecialchars($row['keterangan']); ?>"</p>
                        
                        <?php if(!empty($row['feedback'])) : ?>
                        <div class="p-5 bg-indigo-600 rounded-[2rem] text-white">
                            <p class="text-[9px] font-black uppercase tracking-widest mb-1 opacity-70">Respon Petugas:</p>
                            <p class="text-xs font-medium leading-relaxed"><?= htmlspecialchars($row['feedback']); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="flex justify-end gap-2 mt-4">
                            <?php if($row['status'] == 'Menunggu') : ?>
                                <a href="edit_aspirasi.php?id=<?= $row['id_aspirasi']; ?>" class="p-3 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition">Edit</a>
                            <?php endif; ?>
                            <a href="hapus_aspirasi.php?id=<?= $row['id_aspirasi']; ?>" onclick="return confirm('Hapus?')" class="p-3 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">Hapus</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </form>
            </div>
        <?php endif; ?>

    </main>

    <script>
        // JAM DIGITAL
        function runClock() {
            const now = new Date();
            document.getElementById('clock-digital').innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(runClock, 1000); runClock();

        // SLIDER OTOMATIS
        let currentSlide = 0;
        const slider = document.getElementById('slider');
        const slides = document.querySelectorAll('.slide');
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        }
        setInterval(nextSlide, 5000);

        // TOGGLE MANUAL WAKTU
        function toggleWaktu(val) {
            const area = document.getElementById('manualInput');
            const inputs = area.querySelectorAll('input');
            if(val === 'manual') {
                area.classList.remove('hidden'); area.classList.add('grid');
                inputs.forEach(i => i.required = true);
            } else {
                area.classList.add('hidden'); area.classList.remove('grid');
                inputs.forEach(i => i.required = false);
            }
        }
    </script>
</body>
</html>