# PAYROLLKU — Sistem Penggajian Karyawan

Website CRUD sederhana untuk mengelola data gaji karyawan.
Dibuat dengan **HTML, CSS, JavaScript, dan PHP native** (tanpa framework) + **MySQL**.

## Struktur File

```
payrollku/
├── index.php              -> Pengalihan awal (ke login/dashboard)
├── login.php               -> Halaman login (WF1 / M1)
├── logout.php              -> Proses logout
├── dashboard.php           -> Ringkasan + tabel data karyawan (WF2 / M2)
├── penggajian.php          -> Form tambah/edit data gaji (WF3 / M3)
├── slip_gaji.php           -> Tampilan & cetak slip gaji (WF4 / M4)
├── buat_akun.php           -> Jalankan SEKALI untuk buat akun admin, lalu hapus
├── database.sql            -> Struktur database (import ke phpMyAdmin)
├── css/
│   └── style.css           -> Semua styling web
├── js/
│   └── penggajian.js       -> Perhitungan gaji otomatis (real-time)
└── includes/
    ├── koneksi.php         -> Koneksi ke database MySQL
    ├── cek_session.php     -> Proteksi halaman (wajib login)
    └── sidebar.php         -> Menu sidebar (dipakai berulang)
```

## Cara Instalasi (pakai XAMPP / Laragon)

1. Copy folder `payrollku` ke dalam folder `htdocs` (XAMPP) atau `www` (Laragon).
2. Buka **phpMyAdmin**, buat database baru lalu **Import** file `database.sql`.
   (Atau jalankan isi file itu langsung di tab SQL.)
3. Cek file `includes/koneksi.php` — sesuaikan `$user` dan `$pass` jika MySQL kamu
   pakai username/password khusus (default XAMPP: user `root`, password kosong).
4. Buka browser, akses:
   ```
   http://localhost/payrollku/buat_akun.php
   ```
   Ini akan membuat akun login pertama:
   - Username: `admin`
   - Password: `admin123`
5. **Setelah muncul pesan sukses, HAPUS file `buat_akun.php`** dari folder (demi keamanan).
6. Akses website di:
   ```
   http://localhost/payrollku/
   ```
   Login dengan akun di atas.

## Alur Fitur (CRUD)

| Fitur   | Halaman              | Keterangan                                  |
|---------|----------------------|----------------------------------------------|
| Create  | penggajian.php        | Isi form → klik "Simpan Data"                |
| Read    | dashboard.php          | Tabel semua data + fitur pencarian           |
| Update  | penggajian.php?id=X   | Klik tombol "Edit" di dashboard               |
| Delete  | dashboard.php?hapus=X | Klik tombol "Hapus" (ada konfirmasi)          |

## Cara Kerja Perhitungan Gaji

Rumus (dihitung otomatis oleh JavaScript & disimpan ulang oleh PHP saat submit):

```
Gaji Bersih = Gaji Pokok + Tunjangan + Lembur - Potongan
```

## Catatan Keamanan Dasar

- Password disimpan ter-**hash** (`password_hash`), bukan teks biasa.
- Login memakai **prepared statement** (`bind_param`) agar aman dari SQL Injection.
- Setiap halaman kecuali login mewajibkan sesi login aktif (`cek_session.php`).

## Fitur Tambahan

- **Cetak PDF**: tombol "Cetak PDF" di slip gaji memakai `window.print()`
  (browser modern bisa langsung "Save as PDF" dari dialog print).
- **Kirim WhatsApp**: tombol ini membuka WhatsApp Web/App dengan pesan otomatis terisi.
