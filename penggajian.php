
<?php
session_start();
include 'includes/cek_session.php';
include 'includes/koneksi.php';

function captcha() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $hasil = '';
    for ($i = 0; $i < 6; $i++)
        $hasil .= $chars[random_int(0, strlen($chars) - 1)];
    return $hasil;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' &&
    (isset($_GET['refresh_captcha']) || !isset($_SESSION['captcha_code']))) {
    $_SESSION['captcha_code'] = captcha();
}

$id = intval($_GET['id'] ?? 0);

$data = [
    'id'=>'','nama'=>'','nik'=>'','jabatan'=>'',
    'periode_mulai'=>'','periode_selesai'=>'',
    'gaji_pokok'=>'','jumlah_lembur'=>'',
    'potongan'=>'','gaji_bersih'=>''
];

if ($id > 0) {
    $stmt = $koneksi->prepare("SELECT * FROM karyawan WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();

    if ($hasil->num_rows) $data = $hasil->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $mulai = $_POST['periode_mulai'] ?? '';
    $selesai = $_POST['periode_selesai'] ?? '';
    $pokok = intval($_POST['gaji_pokok'] ?? 0);
    $lembur = intval($_POST['jumlah_lembur'] ?? 0);
    $potongan = intval($_POST['potongan'] ?? 0);
    $inputCaptcha = trim($_POST['captcha'] ?? '');

    if (!isset($_SESSION['captcha_code']) ||
        !hash_equals($_SESSION['captcha_code'], $inputCaptcha)) {
        $_SESSION['captcha_code'] = captcha();
        echo "<script>alert('Kode CAPTCHA salah!');history.back();</script>";
        exit;
    }

    unset($_SESSION['captcha_code']);

    if (!$nama || !$nik || !$jabatan || !$mulai || !$selesai) {
        echo "<script>alert('Semua data wajib diisi!');history.back();</script>";
        exit;
    }

    if ($selesai < $mulai) {
        echo "<script>alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');history.back();</script>";
        exit;
    }

    $bersih = $pokok + $lembur - $potongan;

    if ($id > 0) {
        $stmt = $koneksi->prepare("
            UPDATE karyawan SET
            nama=?, nik=?, jabatan=?, periode_mulai=?, periode_selesai=?,
            gaji_pokok=?, jumlah_lembur=?, potongan=?, gaji_bersih=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssssiiiii",
            $nama, $nik, $jabatan, $mulai, $selesai,
            $pokok, $lembur, $potongan, $bersih, $id
        );

    } else {
        $tanggal = date('Y-m-d');

        $stmt = $koneksi->prepare("
            INSERT INTO karyawan
            (nama,nik,jabatan,periode_mulai,periode_selesai,
            gaji_pokok,jumlah_lembur,potongan,gaji_bersih,tanggal)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "sssssiiiis",
            $nama, $nik, $jabatan, $mulai, $selesai,
            $pokok, $lembur, $potongan, $bersih, $tanggal
        );
    }

    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $id ? 'Edit Penggajian' : 'Penggajian' ?></title>

<style>
*{box-sizing:border-box}
body{margin:0;font-family:Arial;background:#f5f7fb;color:#1f2937}
.container{max-width:900px;margin:40px auto;padding:0 20px}
.card{background:#fff;border-radius:12px;padding:28px;box-shadow:0 3px 12px rgba(0,0,0,.06)}
h2{margin:0 0 25px;font-size:22px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.form-group{display:flex;flex-direction:column}
.full{grid-column:1/-1}
label{margin-bottom:7px;font-size:13px;font-weight:600}
input{width:100%;padding:11px 12px;border:1px solid #d9dee7;border-radius:8px;outline:0;font-size:14px}
input:focus{border-color:#2563eb}
.periode{display:grid;grid-template-columns:1fr 1fr;gap:12px}

.hasil{margin-top:25px;padding:18px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px}
.hasil-row{display:flex;justify-content:space-between;padding:7px 0;font-size:14px}
.hasil-bersih{margin-top:8px;padding-top:12px;border-top:1px solid #e2e8f0;font-size:17px}

.captcha-area{margin-top:25px}
.captcha-title{font-size:13px;font-weight:600;margin-bottom:8px}
.captcha-box{display:flex;align-items:center;gap:12px;padding:12px;border:1px solid #d9dee7;border-radius:10px;max-width:500px}
.captcha-code{width:125px;height:52px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;border:1px solid #d9dee7;border-radius:8px;font-size:18px;font-weight:bold;letter-spacing:3px}
.captcha-right{flex:1}
.captcha-label{display:block;margin-bottom:5px;font-size:11px;color:#64748b}
.captcha-refresh{width:42px;height:42px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#475569;border-radius:8px;text-decoration:none;font-size:22px}

.button-area{margin-top:25px;display:flex;justify-content:space-between}
.btn{border:0;border-radius:8px;padding:11px 18px;font-size:14px;cursor:pointer;text-decoration:none}
.btn-back{background:#e5e7eb;color:#374151}
.btn-save{background:#2563eb;color:white}

@media(max-width:600px){
.form-grid{grid-template-columns:1fr}
.full{grid-column:auto}
.periode{grid-template-columns:1fr}
.captcha-box{flex-wrap:wrap}
.captcha-code{flex:1;width:auto}
.captcha-right{width:100%;flex:none}
}
</style>
</head>

<body>

<div class="container">
<div class="card">

<h2><?= $id ? 'Edit Penggajian' : 'Penggajian' ?></h2>

<form method="POST">

<input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

<div class="form-grid">

<div class="form-group">
<label>Nama Karyawan</label>
<input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
</div>

<div class="form-group">
<label>NIK</label>
<input type="text" name="nik" value="<?= htmlspecialchars($data['nik']) ?>" required>
</div>

<div class="form-group full">
<label>Jabatan</label>
<input type="text" name="jabatan" value="<?= htmlspecialchars($data['jabatan']) ?>" required>
</div>

<div class="form-group full">
<label>Periode Penggajian</label>
<div class="periode">
<input type="date" name="periode_mulai" value="<?= $data['periode_mulai'] ?>" required>
<input type="date" name="periode_selesai" value="<?= $data['periode_selesai'] ?>" required>
</div>
</div>

<div class="form-group">
<label>Gaji Pokok</label>
<input type="number" id="gaji_pokok" name="gaji_pokok" min="0" value="<?= $data['gaji_pokok'] ?>" required>
</div>

<div class="form-group">
<label>Jumlah Lembur</label>
<input type="number" id="jumlah_lembur" name="jumlah_lembur" min="0" value="<?= $data['jumlah_lembur'] ?>" required>
</div>

<div class="form-group full">
<label>Potongan</label>
<input type="number" id="potongan" name="potongan" min="0" value="<?= $data['potongan'] ?>" required>
</div>

</div>

<div class="hasil">
<div class="hasil-row">
<span>Pendapatan</span>
<strong id="hasil_pendapatan">Rp 0</strong>
</div>

<div class="hasil-row">
<span>Potongan</span>
<strong id="hasil_potongan">Rp 0</strong>
</div>

<div class="hasil-row hasil-bersih">
<span>Gaji Bersih</span>
<strong id="hasil_bersih">Rp 0</strong>
</div>
</div>

<div class="captcha-area">
<div class="captcha-title">Verifikasi Keamanan</div>

<div class="captcha-box">

<div class="captcha-code">
<?= htmlspecialchars($_SESSION['captcha_code']) ?>
</div>

<div class="captcha-right">
<span class="captcha-label">Masukkan kode</span>
<input type="text" name="captcha" maxlength="6"
       autocomplete="off" placeholder="Kode CAPTCHA" required>
</div>

<a class="captcha-refresh"
   href="penggajian.php<?= $id ? '?id='.$id.'&refresh_captcha=1' : '?refresh_captcha=1' ?>">
↻
</a>

</div>
</div>

<div class="button-area">
<a href="dashboard.php" class="btn btn-back">Kembali</a>

<button type="submit" class="btn btn-save">
<?= $id ? 'Update Data' : 'Simpan Data' ?>
</button>
</div>

</form>
</div>
</div>

<script src="js/penggajian.js"></script>

</body>
</html>
```
