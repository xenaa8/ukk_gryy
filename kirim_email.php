<?php

include 'includes/cek_session.php';
include 'includes/koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


/* ========================================
   CEK DATA POST
======================================== */

if (!isset($_POST['id']) || !isset($_POST['email'])) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_POST['id']);
$email = trim($_POST['email']);


/* ========================================
   VALIDASI EMAIL
======================================== */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email tidak valid.");
}


/* ========================================
   AMBIL DATA KARYAWAN
======================================== */

$stmt = $koneksi->prepare(
    "SELECT * FROM karyawan WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();


if (!$result->num_rows) {
    die("Data karyawan tidak ditemukan.");
}

$data = $result->fetch_assoc();


/* ========================================
   FUNCTION
======================================== */

function rupiah($n)
{
    return 'Rp ' .
        number_format(
            $n ?? 0,
            0,
            ',',
            '.'
        );
}


function tanggal($tgl)
{
    return $tgl
        ? date('d/m/Y', strtotime($tgl))
        : '-';
}


/* ========================================
   DATA KARYAWAN
======================================== */

$nama = htmlspecialchars($data['nama']);
$nik = htmlspecialchars($data['nik']);
$jabatan = htmlspecialchars($data['jabatan']);

$periode =
    tanggal($data['periode_mulai']) .
    ' - ' .
    tanggal($data['periode_selesai']);


/* ========================================
   ISI EMAIL
======================================== */

$body = "

<!DOCTYPE html>

<html>

<head>

<meta charset='UTF-8'>

</head>

<body
style='
font-family:Arial,sans-serif;
background:#f6f8fc;
padding:20px;
'
>

<div
style='
max-width:600px;
margin:auto;
background:white;
padding:25px;
border-radius:10px;
'
>

<h2
style='
margin-top:0;
color:#111827;
'
>
PAYROLLKU
</h2>

<p
style='
color:#64748b;
'
>
Sistem Informasi Penggajian Karyawan
</p>

<hr>

<h3>
Slip Gaji Karyawan
</h3>


<table
width='100%'
cellpadding='8'
cellspacing='0'
style='
border-collapse:collapse;
'
>

<tr>
<td><b>Nama</b></td>
<td>$nama</td>
</tr>

<tr>
<td><b>NIK</b></td>
<td>$nik</td>
</tr>

<tr>
<td><b>Jabatan</b></td>
<td>$jabatan</td>
</tr>

<tr>
<td><b>Periode</b></td>
<td>$periode</td>
</tr>

<tr>
<td><b>Gaji Pokok</b></td>
<td>
" . rupiah($data['gaji_pokok']) . "
</td>
</tr>

<tr>
<td><b>Lembur</b></td>
<td>
" . rupiah($data['jumlah_lembur']) . "
</td>
</tr>

<tr>
<td><b>Potongan</b></td>
<td>
- " . rupiah($data['potongan']) . "
</td>
</tr>

<tr>
<td><b>Gaji Bersih</b></td>
<td>
<b>
" . rupiah($data['gaji_bersih']) . "
</b>
</td>
</tr>

</table>

<hr>

<p
style='
font-size:12px;
color:#94a3b8;
'
>
Email ini dikirim secara otomatis oleh sistem PAYROLLKU.
</p>

</div>

</body>

</html>

";


/* ========================================
   PHPMailer
======================================== */

$mail = new PHPMailer(true);


try {

    /* ====================================
       SMTP GMAIL
    ==================================== */

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;


    /* EMAIL GMAIL KAMU */

    $mail->Username = 'xenpratama9@gmail.com';


    /*
       MASUKKAN APP PASSWORD GMAIL BARU
       BUKAN PASSWORD LOGIN GMAIL
    */

    $mail->Password = 'MASUKKAN_APP_PASSWORD_DI_SINI';


    /* ENKRIPSI */

    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;


    /* PORT */

    $mail->Port = 587;


    /* ====================================
       PENGIRIM
    ==================================== */

    $mail->setFrom(
        'xenpratama9@gmail.com',
        'PAYROLLKU'
    );


    /* ====================================
       PENERIMA
    ==================================== */

    $mail->addAddress(
        $email,
        $nama
    );


    /* ====================================
       EMAIL
    ==================================== */

    $mail->isHTML(true);

    $mail->CharSet = 'UTF-8';

    $mail->Subject =
        "Slip Gaji - $nama";

    $mail->Body = $body;

    $mail->AltBody =
        "Slip Gaji Karyawan\n\n" .
        "Nama: $nama\n" .
        "NIK: $nik\n" .
        "Jabatan: $jabatan\n" .
        "Periode: $periode\n\n" .
        "Gaji Pokok: " .
        rupiah($data['gaji_pokok']) . "\n" .
        "Lembur: " .
        rupiah($data['jumlah_lembur']) . "\n" .
        "Potongan: " .
        rupiah($data['potongan']) . "\n" .
        "Gaji Bersih: " .
        rupiah($data['gaji_bersih']);


    /* ====================================
       KIRIM EMAIL
    ==================================== */

    $mail->send();


    /* ====================================
       BERHASIL
    ==================================== */

    echo "

    <script>

    alert(
        'Slip gaji berhasil dikirim ke $email'
    );

    window.location.href =
        'slip_gaji.php?id=$id';

    </script>

    ";


} catch (Exception $e) {


    /* ====================================
       GAGAL
    ==================================== */

    echo "

    <script>

    alert(
        'Email gagal dikirim: " .
        addslashes($mail->ErrorInfo) .
        "'
    );

    window.location.href =
        'slip_gaji.php?id=$id';

    </script>

    ";

}

?>