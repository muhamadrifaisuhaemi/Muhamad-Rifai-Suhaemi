<?php
session_start();
require 'functions.php';

// 1. Proteksi Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

global $conn;

// Ambil halaman aktif
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Hitung Notifikasi
$query_notif = mysqli_query($conn, "SELECT COUNT(*) as jml FROM aspirasi WHERE status = 'Menunggu'");
$notif_data = mysqli_fetch_assoc($query_notif);
$jml_notif = $notif_data['jml'] ?? 0;

// --- LOGIKA KELOLA ADMIN ---
if (isset($_POST['tambah_admin'])) {
    $username = htmlspecialchars($_POST['username']);
    $nama = htmlspecialchars($_POST['nama_petugas']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query($conn, "SELECT username FROM admin WHERE username = '$username'");
    if(mysqli_fetch_assoc($cek)) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        $query_tambah = "INSERT INTO admin (username, password, nama_petugas) VALUES ('$username', '$password', '$nama')";
        mysqli_query($conn, $query_tambah);
        echo "<script>alert('Admin baru berhasil ditambahkan!'); window.location='admin.php?page=kelola_admin';</script>";
    }
}

if (isset($_GET['hapus_admin'])) {
    $id_hapus = $_GET['hapus_admin'];
    if ($id_hapus == $_SESSION['id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='admin.php?page=kelola_admin';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM admin WHERE id_admin = $id_hapus");
        echo "<script>alert('Admin berhasil dihapus!'); window.location='admin.php?page=kelola_admin';</script>";
    }
}

if (isset($_POST['delete_history'])) {
    if (!empty($_POST['selected_ids'])) {
        $ids = implode(',', array_map('intval', $_POST['selected_ids']));
        $query_hapus = "DELETE FROM tanggapan WHERE id_tanggapan IN ($ids)";
        mysqli_query($conn, $query_hapus);
        echo "<script>alert('Riwayat terpilih berhasil dihapus!'); window.location='admin.php?page=history';</script>";
    }
}

// 3. Query Data Utama
$query_base = "SELECT aspirasi.*, siswa.nama, siswa.kelas, kategori.nama_kategori 
                FROM aspirasi 
                JOIN siswa ON aspirasi.nis = siswa.nis 
                JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori";
$laporan = query($query_base . " ORDER BY tanggal DESC");

// Ambil Galeri Foto
$galeri_foto = query("SELECT tanggapan.foto_tanggapan, aspirasi.keterangan, siswa.nama 
                      FROM tanggapan 
                      JOIN aspirasi ON tanggapan.id_aspirasi = aspirasi.id_aspirasi 
                      JOIN siswa ON aspirasi.nis = siswa.nis
                      WHERE tanggapan.foto_tanggapan IS NOT NULL AND tanggapan.foto_tanggapan != ''
                      ORDER BY tanggapan.id_tanggapan DESC LIMIT 5");

// Statistik
$total = count($laporan);
$pending = count(array_filter($laporan, function($item) { return $item['status'] == 'Menunggu'; }));
$proses = count(array_filter($laporan, function($item) { return $item['status'] == 'Proses'; }));
$selesai = count(array_filter($laporan, function($item) { return $item['status'] == 'Selesai'; }));

$list_admin = query("SELECT * FROM admin ORDER BY id_admin DESC");

// Query Riwayat
$riwayat = query("SELECT tanggapan.*, aspirasi.keterangan as aduan, admin.nama_petugas 
                  FROM tanggapan 
                  JOIN aspirasi ON tanggapan.id_aspirasi = aspirasi.id_aspirasi
                  JOIN admin ON tanggapan.id_admin = admin.id_admin
                  ORDER BY id_tanggapan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Aspirasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; min-height: 100vh; }
        .sidebar { width: 280px; position: fixed; height: 100vh; background: white; border-right: 1px solid #e2e8f0; z-index: 100; transition: all 0.3s; }
        .main-content { margin-left: 280px; padding: 40px; transition: all 0.3s; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 24px; }
        .nav-link-custom { display: flex; align-items: center; padding: 14px 24px; border-radius: 16px; color: #64748b; font-weight: 600; text-decoration: none; transition: 0.3s; margin-bottom: 8px; }
        .nav-link-custom:hover { background: #f1f5f9; color: #4f46e5; }
        .nav-link-custom.active { background: #4f46e5; color: white; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2); }
        .excel-table th { background: #1e293b; color: white; font-size: 11px; text-transform: uppercase; border: 1px solid #334155; padding: 15px; }
        .excel-table td { border: 1px solid #e2e8f0; vertical-align: middle; padding: 12px; font-size: 13px; }
        
        /* Animasi */
        .fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        @media (max-width: 992px) { .sidebar { left: -280px; } .main-content { margin-left: 0; } }
        
        /* PRINT OPTIMIZATION */
        @media print {
            .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .sidebar { display: none !important; }
            #print-area { border: none !important; box-shadow: none !important; width: 100% !important; }
            .excel-table th { background: #000 !important; color: #fff !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="flex">

    <aside class="sidebar p-4 no-print">
        <div class="d-flex align-items-center gap-3 mb-5 px-3">
            <div class="bg-indigo-600 rounded-3 text-white d-flex align-items-center justify-content-center font-bold" style="width: 42px; height: 42px;">A</div>
            <h1 class="text-lg font-black text-slate-800 tracking-tighter">E-ASPIRASI</h1>
        </div>
        
        <nav>
            <a href="admin.php?page=dashboard" class="nav-link-custom <?= $page == 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="admin.php?page=tanggapi" class="nav-link-custom <?= $page == 'tanggapi' ? 'active' : '' ?> d-flex justify-content-between">
                Tanggapi 
                <?php if($jml_notif > 0): ?><span class="badge bg-danger rounded-pill"><?= $jml_notif ?></span><?php endif; ?>
            </a>
            <a href="admin.php?page=galeri" class="nav-link-custom <?= $page == 'galeri' ? 'active' : '' ?>">Galeri Bukti</a>
            <a href="admin.php?page=history" class="nav-link-custom <?= $page == 'history' ? 'active' : '' ?>">Riwayat</a>
            <a href="admin.php?page=kelola_admin" class="nav-link-custom <?= $page == 'kelola_admin' ? 'active' : '' ?>">Administrator</a>
        </nav>

        <div class="position-absolute bottom-0 start-0 w-100 p-4">
            <a href="logout.php" onclick="return confirm('Log out?')" class="text-rose-500 font-bold text-sm px-4 py-3 d-flex align-items-center gap-2 rounded-3 hover:bg-rose-50 no-underline transition-all">Keluar Panel</a>
        </div>
    </aside>

    <main class="main-content flex-1">
        
        <?php if ($page == 'dashboard') : ?>
            <div class="fade-in">
                <div class="mb-5">
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Ringkasan Sistem</h2>
                    <p class="text-slate-400">Monitor data aspirasi dan penanganan secara real-time.</p>
                </div>

                <div class="mb-10 relative overflow-hidden rounded-[2.5rem] bg-white shadow-xl shadow-indigo-100/40" x-data="{ active: 0, loop() { setInterval(() => { this.active = (this.active + 1) % <?= max(count($galeri_foto), 1); ?> }, 6000) } }" x-init="loop()">
                    <div class="flex transition-transform duration-1000 ease-in-out h-[360px]" :style="'transform: translateX(-' + (active * 100) + '%)'">
                        <?php if(empty($galeri_foto)): ?>
                            <div class="w-full flex-shrink-0 flex items-center justify-center bg-slate-50 text-slate-400 font-bold uppercase tracking-widest">Belum ada dokumentasi penanganan</div>
                        <?php else: ?>
                            <?php foreach($galeri_foto as $foto): ?>
                            <div class="w-full flex-shrink-0 d-flex flex-column flex-md-row align-items-center">
                                <img src="assets/img/<?= $foto['foto_tanggapan']; ?>" class="w-full w-md-50 h-100 object-cover">
                                <div class="p-10 w-full w-md-50 bg-white h-100 d-flex flex-column justify-content-center">
                                    <span class="bg-indigo-50 text-indigo-600 text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest w-fit">Status: Selesai</span>
                                    <h3 class="text-2xl font-black text-slate-800 mt-4 mb-2">Penanganan oleh Admin</h3>
                                    <p class="text-slate-500 italic text-sm leading-relaxed border-l-4 border-indigo-200 ps-4">"Aduan dari <?= $foto['nama'] ?> telah berhasil diproses dan didokumentasikan sebagai bukti transparansi."</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="absolute bottom-6 right-10 d-flex gap-2">
                        <?php foreach($galeri_foto as $i => $f): ?>
                            <button @click="active = <?= $i ?>" class="w-2 h-2 rounded-full transition-all duration-300" :class="active === <?= $i ?> ? 'bg-indigo-600 w-8' : 'bg-slate-200'"></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="row g-4 mb-10">
                    <div class="col-md-3">
                        <div class="glass-card p-4 shadow-sm text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total</p>
                            <h3 class="text-4xl font-black text-slate-900"><?= $total ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="glass-card p-4 shadow-sm text-center border-bottom border-rose-500 border-4">
                            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-2">Menunggu</p>
                            <h3 class="text-4xl font-black text-slate-900"><?= $pending ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="glass-card p-4 shadow-sm text-center border-bottom border-amber-500 border-4">
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2">Proses</p>
                            <h3 class="text-4xl font-black text-slate-900"><?= $proses ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="glass-card p-4 shadow-sm text-center border-bottom border-emerald-500 border-4">
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Selesai</p>
                            <h3 class="text-4xl font-black text-slate-900"><?= $selesai ?></h3>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Visualisasi Tren Pengaduan</h3>
                    <canvas id="chartLaporan" height="100"></canvas>
                </div>
            </div>

        <?php elseif ($page == 'history') : ?>
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Arsip Riwayat</h2>
                        <p class="text-slate-400">Dokumen resmi seluruh tanggapan petugas.</p>
                    </div>
                    <div class="flex gap-2 no-print">
                        <button onclick="window.print()" class="bg-slate-800 text-white px-6 py-3 rounded-2xl font-bold text-xs uppercase shadow-lg hover:bg-slate-700 transition-all">Print Table</button>
                        <button onclick="exportToPDF()" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold text-xs uppercase shadow-lg hover:bg-indigo-700 transition-all">Export PDF</button>
                    </div>
                </div>

                <form action="" method="post">
                    <div class="no-print mb-4 d-flex justify-content-between align-items-center glass-card p-4">
                        <div class="d-flex align-items-center gap-3 ps-3">
                            <input type="checkbox" id="select-all" class="form-check-input w-5 h-5 border-slate-300">
                            <label for="select-all" class="text-xs font-black text-slate-500 uppercase">Pilih Semua</label>
                        </div>
                        <button type="submit" name="delete_history" onclick="return confirm('Hapus data terpilih permanen?')" class="bg-rose-50 text-rose-600 px-6 py-2 rounded-xl font-bold text-[10px] uppercase hover:bg-rose-500 hover:text-white transition-all">Hapus Terpilih</button>
                    </div>

                    <div id="print-area" class="bg-white rounded-4 overflow-hidden shadow-sm border border-slate-200">
                        <div class="p-8 border-b border-slate-100 hidden print:block text-center">
                            <h1 class="text-2xl font-black text-slate-900">LAPORAN RIWAYAT ASPIRASI SISWA</h1>
                            <p class="text-sm text-slate-500">SD AMYN - Sistem Informasi Aspirasi & Keluhan Sarana</p>
                            <div class="mt-2 text-[10px] text-slate-400 uppercase tracking-widest">Dicetak pada: <?= date('d F Y H:i') ?></div>
                        </div>

                        <table class="table table-bordered mb-0 excel-table">
                            <thead>
                                <tr>
                                    <th class="no-print text-center" style="width: 50px;">#</th>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th>Aduan Siswa</th>
                                    <th>Tanggapan Admin</th>
                                    <th style="width: 150px;">Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($riwayat as $r) : ?>
                                <tr>
                                    <td class="text-center no-print">
                                        <input type="checkbox" name="selected_ids[]" value="<?= $r['id_tanggapan']; ?>" class="check-item form-check-input">
                                    </td>
                                    <td class="font-mono text-xs text-slate-500">
                                        <?= (isset($r['tgl_tanggapan']) && $r['tgl_tanggapan'] != '0000-00-00') ? date('d/m/Y', strtotime($r['tgl_tanggapan'])) : date('d/m/Y'); ?>
                                    </td>
                                    <td class="italic text-slate-400">"<?= $r['aduan']; ?>"</td>
                                    <td class="font-semibold text-slate-700"><?= $r['tanggapan']; ?></td>
                                    <td>
                                        <span class="text-[10px] font-black text-indigo-600 uppercase bg-indigo-50 px-3 py-1 rounded-full d-inline-block"><?= $r['nama_petugas']; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

        <?php elseif ($page == 'tanggapi') : ?>
            <div class="glass-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4 text-xs font-black text-slate-400 uppercase">Identitas</th>
                                <th class="p-4 text-xs font-black text-slate-400 uppercase">Isi Aduan</th>
                                <th class="p-4 text-xs font-black text-slate-400 uppercase text-center">Status</th>
                                <th class="p-4 text-xs font-black text-slate-400 uppercase text-center">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($laporan as $row) : ?>
                            <tr>
                                <td class="p-4">
                                    <p class="font-bold text-slate-800 mb-0"><?= $row['nama']; ?></p>
                                    <p class="text-[10px] text-indigo-500 font-bold"><?= $row['kelas']; ?></p>
                                </td>
                                <td class="p-4"><p class="text-sm text-slate-500 italic line-clamp-1">"<?= $row['keterangan']; ?>"</p></td>
                                <td class="p-4 text-center">
                                    <span class="badge rounded-pill <?= ($row['status'] == 'Selesai' ? 'bg-success' : ($row['status'] == 'Proses' ? 'bg-warning text-dark' : 'bg-danger')) ?> px-3 py-2 text-[9px] uppercase font-black"><?= $row['status']; ?></span>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="proses_laporan.php?id=<?= $row['id_aspirasi']; ?>" class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase no-underline shadow-lg shadow-slate-200">Respon</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'galeri') : ?>
            <div class="row g-4 fade-in">
                <?php foreach($galeri_foto as $f): ?>
                <div class="col-md-4">
                    <div class="glass-card overflow-hidden h-100 hover:shadow-lg transition-all duration-300">
                        <img src="assets/img/<?= $f['foto_tanggapan']; ?>" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <span class="text-[9px] font-black text-emerald-500 uppercase bg-emerald-50 px-2 py-1 rounded mb-2 d-inline-block">Selesai</span>
                            <p class="font-bold text-slate-800 small mb-2">Laporan dari: <?= $f['nama'] ?></p>
                            <p class="text-xs text-slate-500 italic">"<?= $f['keterangan'] ?>"</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($page == 'kelola_admin') : ?>
            <div class="row g-4 fade-in">
                <div class="col-md-4">
                    <div class="glass-card p-4">
                        <h3 class="text-lg font-black text-slate-800 mb-4">Tambah Admin</h3>
                        <form method="post" class="space-y-4">
                            <input type="text" name="nama_petugas" required class="form-control rounded-3 py-3" placeholder="Nama Lengkap">
                            <input type="text" name="username" required class="form-control rounded-3 py-3" placeholder="Username">
                            <input type="password" name="password" required class="form-control rounded-3 py-3" placeholder="Password">
                            <button type="submit" name="tambah_admin" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">Daftarkan Admin</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="glass-card overflow-hidden">
                        <table class="table mb-0">
                            <thead class="bg-slate-50">
                                <tr><th class="p-4 text-xs font-black uppercase">Data Admin</th><th class="p-4 text-center text-xs font-black uppercase">Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($list_admin as $adm) : ?>
                                <tr>
                                    <td class="p-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="w-10 h-10 rounded-circle bg-indigo-50 text-indigo-600 d-flex align-items-center justify-content-center font-bold"><?= strtoupper(substr($adm['nama_petugas'], 0, 1)) ?></div>
                                            <div><p class="font-bold text-slate-800 mb-0"><?= $adm['nama_petugas'] ?></p><p class="text-xs text-slate-400">@<?= $adm['username'] ?></p></div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <?php if($adm['id_admin'] != $_SESSION['id']): ?>
                                            <a href="admin.php?page=kelola_admin&hapus_admin=<?= $adm['id_admin']; ?>" class="text-rose-500 font-black text-[10px] uppercase bg-rose-50 px-4 py-2 rounded-pill no-underline">Hapus</a>
                                        <?php else: ?><span class="text-slate-300 text-[10px] font-black uppercase">Aktif Sekarang</span><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 1. Charting Logic
        <?php if($page == 'dashboard'): ?>
        const ctx = document.getElementById('chartLaporan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Menunggu', 'Proses', 'Selesai'],
                datasets: [{
                    label: 'Status Aduan',
                    data: [<?= $pending; ?>, <?= $proses; ?>, <?= $selesai; ?>],
                    backgroundColor: ['#f43f5e', '#f59e0b', '#10b981'],
                    borderRadius: 12,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                }
            }
        });
        <?php endif; ?>

        // 2. Select All Checkboxes
        const selectAll = document.getElementById('select-all');
        if(selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
            });
        }

        // 3. OPTIMIZED EXPORT TO PDF (CANVAS)
        async function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const element = document.getElementById('print-area');
            const btn = event.target;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            btn.disabled = true;

            try {
                // Konfigurasi Canvas agar tajam
                const canvas = await html2canvas(element, { 
                    scale: 3, 
                    useCORS: true,
                    backgroundColor: "#ffffff",
                    windowWidth: element.scrollWidth,
                    onclone: (clonedDoc) => {
                        // Memastikan elemen yang hidden print:block muncul di canvas
                        clonedDoc.getElementById('print-area').querySelector('.print\\:block').style.display = 'block';
                    }
                });
                
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                
                const imgWidth = pageWidth - 20; // Margin 10mm kiri kanan
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                // Posisikan gambar di tengah halaman
                pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
                pdf.save('Laporan_Aspirasi_SDAMYN_' + new Date().getTime() + '.pdf');

            } catch (error) {
                console.error("PDF Export Error: ", error);
                alert("Gagal mengekspor PDF.");
            } finally {
                btn.innerHTML = 'Export PDF';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>