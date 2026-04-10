<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$id_admin = $_SESSION['id']; 
if (!isset($_GET["id"])) {
    header("Location: admin.php");
    exit;
}

$id = $_GET["id"];

// Ambil data laporan detail
$result = query("SELECT aspirasi.*, siswa.nama, siswa.kelas, kategori.nama_kategori 
                  FROM aspirasi 
                  JOIN siswa ON aspirasi.nis = siswa.nis 
                  JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
                  WHERE id_aspirasi = $id");

if (!$result) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='admin.php';</script>";
    exit;
}

$laporan = $result[0];

if (isset($_POST["update"])) {
    $status_baru = $_POST["status"];
    $feedback_baru = mysqli_real_escape_string($conn, htmlspecialchars($_POST["feedback"]));

    // LOGIKA UPLOAD FOTO BUKTI
    $foto_tanggapan = "";
    if ($_FILES['foto_bukti']['error'] === 0) {
        $namaFile = $_FILES['foto_bukti']['name'];
        $tmpName = $_FILES['foto_bukti']['tmp_name'];
        $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
        $namaFileBaru = uniqid() . '.' . $ekstensi;
        
        if (!is_dir('assets/img/')) { mkdir('assets/img/', 0777, true); }
        move_uploaded_file($tmpName, 'assets/img/' . $namaFileBaru);
        $foto_tanggapan = $namaFileBaru;
    }

    // Update aspirasi
    mysqli_query($conn, "UPDATE aspirasi SET status = '$status_baru', feedback = '$feedback_baru' WHERE id_aspirasi = $id");

    // Kelola tabel tanggapan
    $cek_tanggapan = query("SELECT * FROM tanggapan WHERE id_aspirasi = $id");
    if ($cek_tanggapan) {
        $query_up = "UPDATE tanggapan SET tanggapan = '$feedback_baru', id_admin = '$id_admin'";
        if($foto_tanggapan != "") { $query_up .= ", foto_tanggapan = '$foto_tanggapan'"; }
        $query_up .= " WHERE id_aspirasi = $id";
        mysqli_query($conn, $query_up);
    } else {
        mysqli_query($conn, "INSERT INTO tanggapan (id_aspirasi, tanggapan, id_admin, foto_tanggapan, tgl_tanggapan) 
                            VALUES ('$id', '$feedback_baru', '$id_admin', '$foto_tanggapan', CURRENT_DATE)");
    }

    echo "<script>alert('Tanggapan & Bukti Berhasil Disimpan!'); document.location.href = 'admin.php?page=tanggapi';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderasi Laporan #<?= $id; ?> | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; scroll-behavior: smooth; }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .status-pill { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .input-focus:focus { box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); border-color: #4f46e5; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="p-4 sm:p-6 lg:p-12">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 lg:mb-12">
            <div class="order-2 md:order-1">
                <a href="admin.php?page=tanggapi" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors mb-4 uppercase tracking-widest">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-800 tracking-tight">Detail <span class="text-indigo-600">Moderasi</span></h1>
                <p class="text-slate-500 text-sm sm:text-base mt-1">Kelola status dan berikan solusi untuk aspirasi siswa.</p>
            </div>
            <div class="order-1 md:order-2 self-start md:self-auto px-5 py-3 bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col items-start md:items-end">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">ID LAPORAN</span>
                <span class="text-lg sm:text-xl font-black text-indigo-600 font-mono">#<?= $id; ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start">
            
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card p-6 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] shadow-xl shadow-slate-200/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-100">
                            <?= substr($laporan['nama'], 0, 1); ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-base sm:text-lg"><?= $laporan['nama']; ?></h3>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-indigo-50 text-[10px] font-black text-indigo-500 uppercase rounded-md tracking-wider"><?= $laporan['kelas']; ?></span>
                                <span class="text-slate-300">•</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= $laporan['nama_kategori']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="p-5 bg-slate-50/80 border border-slate-100 rounded-2xl">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Pesan Aspirasi</label>
                            <p class="text-slate-700 text-sm sm:text-base leading-relaxed font-medium italic">"<?= $laporan['keterangan']; ?>"</p>
                        </div>

                        <?php if($laporan['foto']): ?>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block px-1">Lampiran Siswa</label>
                                <div class="group relative overflow-hidden rounded-3xl shadow-lg transition-all active:scale-[0.98] cursor-zoom-in">
                                    <img src="assets/img/<?= $laporan['foto']; ?>" class="w-full h-auto object-cover max-h-[300px]">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                                        <p class="text-white text-[10px] font-bold uppercase tracking-widest"><i class="fas fa-search-plus mr-2"></i> Perbesar Foto</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="glass-card p-6 sm:p-8 lg:p-10 rounded-[2rem] sm:rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 border-t-4 border-t-indigo-500">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <div class="w-2 h-8 bg-indigo-500 rounded-full"></div>
                        Form Tindakan Admin
                    </h3>

                    <form action="" method="post" enctype="multipart/form-data" class="space-y-8">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-4 ps-1">Update Status Progres</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <?php 
                                $statuses = ['Menunggu', 'Proses', 'Selesai'];
                                foreach($statuses as $st):
                                ?>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="status" value="<?= $st; ?>" class="peer hidden" <?= $laporan['status'] == $st ? 'checked' : ''; ?>>
                                    <div class="status-pill py-3.5 sm:py-3 text-center rounded-xl border-2 border-slate-50 text-xs font-bold text-slate-400 bg-slate-50/50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 group-hover:border-slate-200 transition-all shadow-sm">
                                        <?php if($st == 'Selesai'): ?> <i class="fas fa-check-circle mr-1"></i> <?php endif; ?>
                                        <?= $st; ?>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-3 ps-1">Tanggapan / Solusi</label>
                            <textarea name="feedback" rows="5" required 
                                class="input-focus w-full p-5 bg-slate-50/50 border border-slate-200 rounded-[1.5rem] outline-none text-sm text-slate-700 transition-all placeholder:text-slate-300 shadow-inner" 
                                placeholder="Tuliskan tindakan yang sudah diambil atau jawaban resmi sekolah..."><?= $laporan['feedback']; ?></textarea>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-3 ps-1">Lampirkan Bukti Penyelesaian (Opsional)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-200 rounded-[1.5rem] cursor-pointer bg-slate-50/30 hover:bg-white hover:border-indigo-300 transition-all group relative overflow-hidden">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                                        <div class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                            <i class="fas fa-cloud-upload-alt text-slate-300 group-hover:text-indigo-500 transition-colors"></i>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-400 group-hover:text-indigo-600 uppercase tracking-tighter">Pilih Foto Bukti Penanganan</p>
                                        <p id="file-name" class="text-[9px] text-indigo-400 mt-2 italic font-medium truncate max-w-xs"></p>
                                    </div>
                                    <input type="file" name="foto_bukti" id="foto_bukti" class="hidden" accept="image/*" />
                                </label>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="update" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-black py-4 sm:py-5 rounded-[1.5rem] shadow-xl shadow-indigo-200 hover:shadow-indigo-300 active:scale-[0.98] transition-all tracking-widest text-[11px] sm:text-xs uppercase">
                                Update & Simpan Moderasi
                            </button>
                            <p class="text-center text-[9px] text-slate-400 mt-4 uppercase tracking-[0.2em] font-bold italic">Sistem E-Aspirasi SD AMYN</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview nama file saat dipilih
        const fileInput = document.getElementById('foto_bukti');
        const fileNameDisplay = document.getElementById('file-name');

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileNameDisplay.textContent = 'Terpilih: ' + this.files[0].name;
            }
        });
    </script>
</body>
</html>