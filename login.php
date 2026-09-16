<?php
// ==========================================
// HALAMAN LOGIN
// ==========================================
session_start();
include 'includes/koneksi.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil user berdasarkan username (pakai prepared statement, aman dari SQL Injection)
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $hasil = $stmt->get_result();

    if ($hasil->num_rows == 1) {
        $user = $hasil->fetch_assoc();

        // Cek password yang di-hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - PAYROLLKU</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-box">
        <h1>PAYROLLKU</h1>
        <p class="subtitle">Sistem Penggajian Karyawan</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
        </form>

    </div>
</div>

</body>
</html>
