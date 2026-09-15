
<?php
include 'includes/cek_session.php';
include 'includes/koneksi.php';

function rupiah($n) {
    return 'Rp ' . number_format($n ?? 0, 0, ',', '.');
}

function periode($mulai, $selesai) {
    if (!$mulai || !$selesai) return '-';
    return date('d/m/Y', strtotime($mulai)) . ' - ' . date('d/m/Y', strtotime($selesai));
}

/* HAPUS */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    if ($id > 0) {
        $stmt = $koneksi->prepare("DELETE FROM karyawan WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: dashboard.php");
    exit;
}

/* SEARCH */
$search = trim($_GET['search'] ?? '');

/* SUMMARY */
$total_karyawan = $koneksi->query(
    "SELECT COUNT(*) total FROM karyawan"
)->fetch_assoc()['total'];

$total_gaji = $koneksi->query(
    "SELECT COALESCE(SUM(gaji_bersih),0) total FROM karyawan"
)->fetch_assoc()['total'];

$rata_gaji = $koneksi->query(
    "SELECT COALESCE(AVG(gaji_bersih),0) rata FROM karyawan"
)->fetch_assoc()['rata'];

$bulan = date('Y-m');

$stmt = $koneksi->prepare(
    "SELECT COUNT(*) total FROM karyawan
     WHERE DATE_FORMAT(periode_mulai,'%Y-%m')=?"
);
$stmt->bind_param("s", $bulan);
$stmt->execute();
$data_bulan_ini = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

/* DATA KARYAWAN */
if ($search) {
    $like = "%$search%";

    $stmt = $koneksi->prepare(
        "SELECT * FROM karyawan
         WHERE nama LIKE ? OR nik LIKE ? OR jabatan LIKE ?
         ORDER BY id ASC"
    );

    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $data_karyawan = $stmt->get_result();
} else {
    $data_karyawan = $koneksi->query(
        "SELECT * FROM karyawan ORDER BY id ASC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard - PAYROLLKU</title>
<link rel="stylesheet" href="css/style.css">

<style>
.content{background:#f6f8fc;min-height:100vh}

.dashboard-topbar,.table-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:22px
}

.dashboard-title h1{
    margin:0;
    color:#111827;
    font-size:26px
}

.dashboard-title p{
    margin:6px 0;
    color:#64748b;
    font-size:13px
}

.summary-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:16px;
    margin-bottom:22px
}

.summary-card,.table-panel{
    background:#fff;
    border:1px solid #e5e9ef;
    border-radius:11px;
    box-shadow:0 2px 8px rgba(15,23,42,.03)
}

.summary-card{padding:20px}

.summary-label{
    color:#64748b;
    font-size:12px;
    margin-bottom:9px
}

.summary-value{
    color:#111827;
    font-size:22px;
    font-weight:700
}

.table-panel{overflow:hidden}

.table-header{
    padding:20px 22px;
    margin:0;
    border-bottom:1px solid #eef1f5
}

.table-header h3{
    margin:0;
    color:#1e293b;
    font-size:16px
}

.search-form{
    display:flex;
    gap:7px
}

.search-input{
    padding:9px 11px;
    width:230px;
    border:1px solid #dce2e9;
    border-radius:7px;
    outline:none
}

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:9px 13px;
    border:0;
    border-radius:7px;
    text-decoration:none;
    font-size:12px;
    font-weight:600;
    cursor:pointer
}

.btn-primary{background:#2563eb;color:#fff}
.btn-primary:hover{background:#1d4ed8}
.btn-detail{background:#f1f5f9;color:#475569}
.btn-edit{background:#eff6ff;color:#2563eb}
.btn-delete{background:#fef2f2;color:#dc2626}

.table-wrapper{overflow-x:auto}

table{
    width:100%;
    min-width:1100px;
    border-collapse:collapse
}

th{
    padding:13px 16px;
    background:#f8fafc;
    color:#64748b;
    font-size:11px;
    text-align:left;
    white-space:nowrap
}

td{
    padding:14px 16px;
    color:#334155;
    font-size:12px;
    border-bottom:1px solid #f1f5f9;
    white-space:nowrap
}

.employee-name{
    font-weight:600;
    color:#1e293b
}

.employee-nik{
    color:#64748b;
    font-size:11px
}

.badge-jabatan{
    padding:5px 8px;
    background:#f1f5f9;
    color:#475569;
    border-radius:5px;
    font-size:10px
}

.badge-periode{
    color:#475569;
    font-size:11px
}

.action-group{
    display:flex;
    gap:6px
}

.empty-data{
    padding:45px;
    text-align:center;
    color:#94a3b8
}

@media(max-width:1100px){
    .summary-grid{grid-template-columns:repeat(2,1fr)}
}

@media(max-width:700px){
    .dashboard-topbar,.table-header{
        flex-direction:column;
        align-items:flex-start;
        gap:15px
    }

    .summary-grid{grid-template-columns:1fr}

    .search-form{width:100%}
    .search-input{width:100%}
}
</style>
</head>

<body>

<div class="layout">

<?php include 'includes/sidebar.php'; ?>

<div class="content">

<div class="dashboard-topbar">
    <div class="dashboard-title">
        <h1>Dashboard</h1>
        <p>Kelola data karyawan dan penggajian</p>
    </div>

    <a href="penggajian.php" class="btn btn-primary">
        + Tambah Data Gaji
    </a>
</div>

<div class="summary-grid">

<div class="summary-card">
    <div class="summary-label">Total Karyawan</div>
    <div class="summary-value"><?= number_format($total_karyawan) ?></div>
</div>

<div class="summary-card">
    <div class="summary-label">Total Gaji Bersih</div>
    <div class="summary-value"><?= rupiah($total_gaji) ?></div>
</div>

<div class="summary-card">
    <div class="summary-label">Rata-rata Gaji</div>
    <div class="summary-value"><?= rupiah($rata_gaji) ?></div>
</div>

<div class="summary-card">
    <div class="summary-label">Data Bulan Ini</div>
    <div class="summary-value"><?= number_format($data_bulan_ini) ?></div>
</div>

</div>

<div class="table-panel">

<div class="table-header">

<h3>Data Penggajian</h3>

<form method="GET" class="search-form">
    <input
        type="text"
        name="search"
        class="search-input"
        placeholder="Cari nama / NIK / jabatan..."
        value="<?= htmlspecialchars($search) ?>"
    >
    <button class="btn btn-primary">Cari</button>
</form>

</div>

<div class="table-wrapper">

<?php if ($data_karyawan && $data_karyawan->num_rows): ?>

<table>

<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>NIK</th>
<th>Jabatan</th>
<th>Periode</th>
<th>Gaji Pokok</th>
<th>Lembur</th>
<th>Potongan</th>
<th>Gaji Bersih</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php $no=1; while($row=$data_karyawan->fetch_assoc()): ?>

<tr>

<td><?= $no++ ?></td>

<td class="employee-name">
    <?= htmlspecialchars($row['nama']) ?>
</td>

<td class="employee-nik">
    <?= htmlspecialchars($row['nik']) ?>
</td>

<td>
    <span class="badge-jabatan">
        <?= htmlspecialchars($row['jabatan']) ?>
    </span>
</td>

<td class="badge-periode">
    <?= periode($row['periode_mulai'],$row['periode_selesai']) ?>
</td>

<td><?= rupiah($row['gaji_pokok']) ?></td>

<td><?= rupiah($row['jumlah_lembur']) ?></td>

<td><?= rupiah($row['potongan']) ?></td>

<td><strong><?= rupiah($row['gaji_bersih']) ?></strong></td>

<td>
<div class="action-group">

<a
    href="slip_gaji.php?id=<?= $row['id'] ?>"
    class="btn btn-detail"
>
    Detail
</a>

<a
    href="penggajian.php?id=<?= $row['id'] ?>"
    class="btn btn-edit"
>
    Edit
</a>

<a
    href="dashboard.php?hapus=<?= $row['id'] ?>"
    class="btn btn-delete"
    onclick="return confirm('Yakin ingin menghapus data ini?')"
>
    Hapus
</a>

</div>
</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

<?php else: ?>

<div class="empty-data">
<?= $search ? 'Data tidak ditemukan.' : 'Belum ada data penggajian.' ?>
</div>

<?php endif; ?>

</div>
</div>

</div>
</div>

</body>
</html>
```
