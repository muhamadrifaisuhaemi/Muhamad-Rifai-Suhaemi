<?php
session_start();
require 'functions.php';

// Proteksi Halaman
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

// Validasi ID agar tidak terjadi error jika ID kosong
if (!isset($_GET["id"])) {
    header("Location: siswa.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET["id"]);
$nis = $_SESSION['id'];

// Pastikan aspirasi yang dihapus adalah milik siswa yang sedang login 
// dan statusnya masih 'Menunggu' demi keamanan.
$query = "DELETE FROM aspirasi WHERE id_aspirasi = '$id' AND nis = '$nis' AND status = 'Menunggu'";
mysqli_query($conn, $query);

$success = mysqli_affected_rows($conn) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses Penghapusan...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .loader {
            border-top-color: #4f46e5;
            animation: spinner 1.5s linear infinite;
        }
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-[#f8fafc] flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-sm bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 text-center border border-white">
        <div class="relative w-20 h-20 mx-auto mb-6">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-slate-100 h-20 w-20"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-trash-alt text-slate-300 text-xl"></i>
            </div>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Sedang Memproses</h3>
        <p class="text-sm text-slate-400 leading-relaxed">Mohon tunggu sebentar, sistem sedang menghapus data aduan Anda...</p>
    </div>

    <script>
        setTimeout(function() {
            <?php if ($success) : ?>
                alert('Berhasil! Aspirasi telah dihapus secara permanen.');
            <?php else : ?>
                alert('Gagal menghapus! Aspirasi mungkin sudah diproses oleh Admin atau tidak ditemukan.');
            <?php endif; ?>
            window.location.href = 'siswa.php';
        }, 800); // Delay 0.8 detik agar transisi terlihat halus
    </script>

</body>
</html>