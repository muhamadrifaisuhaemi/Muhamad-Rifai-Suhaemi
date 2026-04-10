<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

$id = $_GET["id"];
$nis = $_SESSION['id'];

// Ambil data aspirasi yang akan diedit
$data = query("SELECT * FROM aspirasi WHERE id_aspirasi = '$id' AND nis = '$nis'")[0];
$kategori = query("SELECT * FROM kategori");

if (!$data || $data['status'] != 'Menunggu') {
    header("Location: siswa.php");
    exit;
}

if (isset($_POST["update"])) {
    $isi_laporan = htmlspecialchars($_POST["isi_laporan"]);
    $id_kategori = $_POST["id_kategori"];
    $lokasi = htmlspecialchars($_POST["lokasi"]);
    $foto_lama = $_POST["foto_lama"];

    // Cek apakah user upload foto baru
    if ($_FILES['foto']['error'] === 4) {
        $foto_final = $foto_lama;
    } else {
        $nama_foto = $_FILES['foto']['name'];
        $ukuran_foto = $_FILES['foto']['size'];
        $tmp_name = $_FILES['foto']['tmp_name'];
        
        // Ekstensi valid
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_foto = explode('.', $nama_foto);
        $ekstensi_foto = strtolower(end($ekstensi_foto));

        if (!in_array($ekstensi_foto, $ekstensi_valid)) {
            echo "<script>alert('Format foto harus JPG, JPEG, atau PNG!');</script>";
        } elseif ($ukuran_foto > 2000000) {
            echo "<script>alert('Ukuran foto terlalu besar (Maks 2MB)!');</script>";
        } else {
            // Generate nama baru agar tidak bentrok
            $foto_final = uniqid() . "." . $ekstensi_foto;
            
            // Pindahkan file ke folder img
            move_uploaded_file($tmp_name, 'img/' . $foto_final);
            
            // Hapus foto lama jika ada
            if ($foto_lama != '' && file_exists('img/' . $foto_lama)) {
                unlink('img/' . $foto_lama);
            }
        }
    }

    $query = "UPDATE aspirasi SET 
                id_kategori = '$id_kategori',
                lokasi = '$lokasi',
                keterangan = '$isi_laporan',
                foto = '$foto_final'
              WHERE id_aspirasi = '$id'";

    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>
                alert('Aspirasi berhasil diupdate!');
                document.location.href = 'siswa.php';
              </script>";
    } else {
        echo "<script>
                alert('Tidak ada perubahan data.');
                document.location.href = 'siswa.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Aspirasi | E-ASPIRASI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-4 md:p-8">

    <div class="w-full max-w-xl">
        <div class="mb-6 flex items-center justify-between px-2">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Laporan</h2>
                <p class="text-sm text-slate-500">Perbarui rincian aduan Anda</p>
            </div>
            <div class="bg-indigo-50 text-indigo-600 p-3 rounded-2xl">
                <i class="fas fa-edit text-xl"></i>
            </div>
        </div>

        <div class="glass-card rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-6 md:p-10 border border-white">
            <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="foto_lama" value="<?= $data['foto']; ?>">
                
                <div class="space-y-2">
                    <label class="flex items-center text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">
                        <i class="fas fa-tag mr-2"></i> Kategori
                    </label>
                    <select name="id_kategori" class="w-full p-4 bg-slate-50/50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none cursor-pointer text-slate-700 font-medium">
                        <?php foreach($kategori as $k) : ?>
                            <option value="<?= $k['id_kategori']; ?>" <?= ($k['id_kategori'] == $data['id_kategori']) ? 'selected' : ''; ?>>
                                <?= $k['nama_kategori']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">
                        <i class="fas fa-map-marker-alt mr-2"></i> Lokasi Kejadian
                    </label>
                    <input type="text" name="lokasi" value="<?= $data['lokasi']; ?>" required 
                        placeholder="Misal: Kantin atau Kelas 5A"
                        class="w-full p-4 bg-slate-50/50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 font-medium placeholder:text-slate-400">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">
                        <i class="fas fa-align-left mr-2"></i> Isi Laporan / Aduan
                    </label>
                    <textarea name="isi_laporan" rows="4" required 
                        class="w-full p-4 bg-slate-50/50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 font-medium placeholder:text-slate-400"><?= $data['keterangan']; ?></textarea>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">
                        <i class="fas fa-camera mr-2"></i> Lampiran Foto
                    </label>
                    
                    <div class="p-4 bg-slate-50/50 border border-dashed border-slate-200 rounded-3xl">
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <?php if($data['foto']) : ?>
                                <div class="relative group">
                                    <img src="img/<?= $data['foto']; ?>" class="w-24 h-24 object-cover rounded-2xl border-2 border-white shadow-md">
                                    <div class="absolute inset-0 bg-black/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <p class="text-xs font-bold text-slate-600 mb-1">Foto saat ini</p>
                                    <p class="text-[10px] text-indigo-500 font-mono truncate max-w-[150px] mx-auto sm:mx-0"><?= $data['foto']; ?></p>
                                </div>
                            <?php else : ?>
                                <div class="w-24 h-24 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                                    <i class="fas fa-image text-3xl"></i>
                                </div>
                                <p class="text-[10px] text-slate-400 italic">Belum ada foto terlampir.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4">
                            <input type="file" name="foto" id="foto" class="hidden">
                            <label for="foto" class="block w-full text-center py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 cursor-pointer hover:bg-slate-50 transition-all shadow-sm">
                                <i class="fas fa-upload mr-2"></i> Ganti Foto Baru
                            </label>
                            <p class="text-center text-[9px] text-slate-400 mt-2 uppercase tracking-tighter">* Maksimal 2MB (JPG, JPEG, PNG)</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="siswa.php" class="order-2 sm:order-1 flex-1 text-center bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition-all uppercase tracking-widest text-[11px]">
                        Batal
                    </a>
                    <button type="submit" name="update" class="order-1 sm:order-2 flex-1 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-bold py-4 rounded-2xl hover:opacity-90 transition-all shadow-lg shadow-indigo-200 uppercase tracking-widest text-[11px]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        
        <p class="text-center text-[10px] text-slate-400 mt-8 uppercase tracking-[0.2em] font-bold">E-Aspirasi System &bull; SD AMYN</p>
    </div>

    <script>
        // Preview nama file saat dipilih
        document.getElementById('foto').onchange = function() {
            if(this.files[0]) {
                const label = document.querySelector('label[for="foto"]');
                label.innerHTML = `<i class="fas fa-check-circle mr-2 text-emerald-500"></i> ${this.files[0].name}`;
                label.classList.add('border-emerald-200', 'bg-emerald-50');
            }
        };
    </script>
</body>
</html>