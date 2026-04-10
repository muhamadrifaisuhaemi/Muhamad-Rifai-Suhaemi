<?php
$conn = mysqli_connect("localhost", "root", "", "db_pengaduan_sekolah");

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while( $row = mysqli_fetch_assoc($result) ) { 
        $rows[] = $row;
    }
    return $rows;
}

function registrasi($data) {
    global $conn;

    $nis = mysqli_real_escape_string($conn, $data['username']);
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $kelas = mysqli_real_escape_string($conn, $data['kelas']);
    $password = mysqli_real_escape_string($conn, $data['password']);

    // Cek apakah NIS sudah ada
    $cek_nis = mysqli_query($conn, "SELECT nis FROM siswa WHERE nis = '$nis'");
    if(mysqli_fetch_assoc($cek_nis)) {
        return 0; // Gagal karena NIS sudah ada
    }

    // ENKRIPSI PASSWORD (Ini yang bikin login bisa pakai password_verify)
    $password_baru = password_hash($password, PASSWORD_DEFAULT);

    // Insert ke database
    $query = "INSERT INTO siswa (nis, nama, kelas, password) 
              VALUES ('$nis', '$nama', '$kelas', '$password_baru')";
    
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function cek_login($username, $password) {
    global $conn;
    $q_admin = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");
    if ($row = mysqli_fetch_assoc($q_admin)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION = ['login' => true, 'role' => 'admin', 'nama' => $row['nama_petugas'], 'id' => $row['id_admin']];
            return "admin";
        }
    }
    $q_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$username'");
    if ($row = mysqli_fetch_assoc($q_siswa)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION = ['login' => true, 'role' => 'siswa', 'nama' => $row['nama'], 'id' => $row['nis']];
            return "siswa";
        }
    }
    return false;
}
?>
