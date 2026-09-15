<?php
// ==========================================
// HALAMAN AWAL - alihkan ke login atau dashboard
// ==========================================
session_start();

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>
