<?php
include 'includes/cek_session.php';
include 'includes/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $koneksi->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    header("Location: dashboard.php");
    exit;
}

$data = $result->fetch_assoc();

function rupiah($n) {
    return 'Rp ' . number_format($n ?? 0, 0, ',', '.');
}

function tanggal($tgl) {
    return $tgl ? date('d/m/Y', strtotime($tgl)) : '-';
}

$periode = tanggal($data['periode_mulai']) . ' - ' . tanggal($data['periode_selesai']);

$pesan = "SLIP GAJI KARYAWAN\n\n"
       . "Nama: {$data['nama']}\n"
       . "NIK: {$data['nik']}\n"
       . "Jabatan: {$data['jabatan']}\n"
       . "Periode: $periode\n\n"
       . "Gaji Pokok: " . rupiah($data['gaji_pokok']) . "\n"
       . "Lembur: " . rupiah($data['jumlah_lembur']) . "\n"
       . "Potongan: " . rupiah($data['potongan']) . "\n\n"
       . "GAJI BERSIH: " . rupiah($data['gaji_bersih']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Slip Gaji - PAYROLLKU</title>
<link rel="stylesheet" href="css/style.css">

<style>
.content{background:#f6f8fc;min-height:100vh;padding:30px}
.slip-header,.company-header,.total-box,.slip-footer{
    display:flex;justify-content:space-between;align-items:center
}
.slip-header{margin-bottom:20px}
.slip-title h1{margin:0;color:#111827}
.slip-title p{color:#64748b;font-size:13px}
.slip-actions{display:flex;gap:8px}

.btn{
    padding:10px 14px;border:0;border-radius:7px;
    text-decoration:none;font-size:12px;font-weight:600;cursor:pointer
}
.btn-back,.btn-cancel{background:#e2e8f0;color:#475569}
.btn-print{background:#2563eb;color:white}
.btn-wa{background:#16a34a;color:white}
.btn-email{background:#475569;color:white}

.slip-container{display:flex;justify-content:center}
.slip{
    width:100%;max-width:850px;background:white;
    border:1px solid #e2e8f0;border-radius:12px;
    padding:35px;box-shadow:0 4px 15px #0000000d
}

.company-header{
    padding-bottom:20px;margin-bottom:25px;
    border-bottom:2px solid #1e293b
}
.company-name{font-size:22px;font-weight:800}
.company-description,.slip-label-subtitle,.info-label{
    color:#64748b;font-size:12px
}
.slip-label{text-align:right}
.slip-label-title{font-size:18px;font-weight:700}

.section-title{
    margin:15px 0 12px;font-size:14px;font-weight:700
}
.employee-info{
    display:grid;grid-template-columns:1fr 1fr;
    gap:12px 40px;margin-bottom:25px
}
.info-item{
    display:flex;justify-content:space-between;
    padding-bottom:9px;border-bottom:1px solid #eef1f5
}
.info-value{font-size:12px;font-weight:600}

.salary-table{
    width:100%;border-collapse:collapse;margin-bottom:20px
}
.salary-table th,.salary-table td{
    padding:12px;font-size:12px;text-align:left;
    border-bottom:1px solid #eef1f5
}
.salary-table th{background:#f8fafc;color:#64748b}
.salary-table th:last-child,.salary-table td:last-child{text-align:right}

.total-box{
    padding:18px 20px;background:#f8fafc;
    border:1px solid #e2e8f0;border-radius:8px
}
.total-label{font-weight:700;color:#475569}
.total-value{font-size:21px;font-weight:800}

.slip-footer{
    margin-top:25px;padding-top:15px;
    border-top:1px solid #e2e8f0;
    color:#94a3b8;font-size:10px
}

@media(max-width:700px){
    .slip-header,.company-header{flex-direction:column;align-items:flex-start;gap:15px}
    .employee-info{grid-template-columns:1fr}
    .slip{padding:20px}
    .slip-label{text-align:left}
}

@media print{
    @page{size:A4;margin:15mm}
    body{background:white!important}
    .no-print,.slip-header{display:none!important}
    .layout{display:block!important}
    .content{padding:0!important;background:white!important}
    .slip{max-width:none;padding:0;border:0;box-shadow:none}
}
</style>
</head>

<body>

<div class="layout">

<div class="no-print">
    <?php include 'includes/sidebar.php'; ?>
</div>

<div class="content">

    <div class="slip-header">

        <a href="dashboard.php" class="btn btn-back">← Kembali</a>

        <div class="slip-title">
            <h1>Slip Gaji</h1>
            <p>Rincian pembayaran gaji karyawan</p>
        </div>

        <div class="slip-actions">
            <button class="btn btn-print" onclick="window.print()">Cetak PDF</button>

            <a class="btn btn-wa"
               href="https://wa.me/?text=<?= rawurlencode($pesan) ?>"
               target="_blank">
                WhatsApp
            </a>

            <a class="btn btn-email"
               href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&su=<?= rawurlencode('Slip Gaji - ' . $data['nama']) ?>&body=<?= rawurlencode($pesan) ?>"
               target="_blank">
                Kirim Email
            </a>
        </div>

    </div>

    <div class="slip-container">
        <div class="slip">

            <div class="company-header">
                <div>
                    <div class="company-name">PAYROLLKU</div>
                    <div class="company-description">
                        Sistem Informasi Penggajian Karyawan
                    </div>
                </div>

                <div class="slip-label">
                    <div class="slip-label-title">SLIP GAJI</div>
                    <div class="slip-label-subtitle">
                        Periode <?= htmlspecialchars($periode) ?>
                    </div>
                </div>
            </div>

            <div class="section-title">Informasi Karyawan</div>

            <div class="employee-info">
                <?php
                $info = [
                    'Nama' => $data['nama'],
                    'NIK' => $data['nik'],
                    'Jabatan' => $data['jabatan'],
                    'Periode' => $periode,
                    'Tanggal' => $data['tanggal'] ?? '-'
                ];

                foreach ($info as $label => $value):
                ?>
                    <div class="info-item">
                        <span class="info-label"><?= $label ?></span>
                        <span class="info-value"><?= htmlspecialchars($value) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-title">Rincian Penggajian</div>

            <table class="salary-table">
                <tr>
                    <th>Komponen</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                </tr>

                <tr>
                    <td>Gaji Pokok</td>
                    <td>Gaji dasar karyawan</td>
                    <td><?= rupiah($data['gaji_pokok']) ?></td>
                </tr>

                <tr>
                    <td>Lembur</td>
                    <td>Pembayaran lembur</td>
                    <td><?= rupiah($data['jumlah_lembur']) ?></td>
                </tr>

                <tr>
                    <td>Potongan</td>
                    <td>Potongan gaji</td>
                    <td>- <?= rupiah($data['potongan']) ?></td>
                </tr>
            </table>

            <div class="total-box">
                <span class="total-label">TOTAL GAJI BERSIH</span>
                <span class="total-value">
                    <?= rupiah($data['gaji_bersih']) ?>
                </span>
            </div>

            <div class="slip-footer">
                <span>PAYROLLKU</span>
                <span>Slip gaji dibuat secara otomatis.</span>
            </div>

        </div>
    </div>

</div>
</div>


</body>
</html>