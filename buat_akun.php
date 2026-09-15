<?php
// ==========================================
// JALANKAN FILE INI SEKALI SAJA UNTUK BUAT AKUN ADMIN
// Setelah berhasil, HAPUS file ini dari server (untuk keamanan)
// Akses: http://localhost/payrollku/buat_akun.php
// ==========================================
include 'includes/koneksi.php';

$username = "admin";
$password_asli = "admin123";
$password_hash = password_hash($password_asli, PASSWORD_DEFAULT);

$cek = $koneksi->query("SELECT * FROM users WHERE username='$username'");

if ($cek->num_rows > 0) {
    echo "Akun 'admin' sudah ada. Tidak perlu buat ulang.";
} else {
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password_hash')";
    if ($koneksi->query($sql)) {
        echo "Akun admin berhasil dibuat!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br><br>";
        echo "<b>PENTING: Hapus file buat_akun.php sekarang demi keamanan.</b>";
    } else {
        echo "Gagal: " . $koneksi->error;
    }
}
?>
