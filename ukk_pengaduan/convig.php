<?php
// Pengaturan Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_aspirasi"; // Sesuaikan dengan nama database kamu

// Melakukan Koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

/**
 * Fungsi query untuk menyederhanakan pengambilan data (Fetch)
 * Digunakan di file siswa.php untuk menampilkan riwayat
 */
function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
?>