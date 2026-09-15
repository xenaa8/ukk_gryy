// ==========================================
// HITUNG GAJI
// ==========================================

function hitungGaji() {

    const gajiPokok =
        parseInt(document.getElementById('gaji_pokok').value) || 0;

    const lembur =
        parseInt(document.getElementById('jumlah_lembur').value) || 0;

    const potongan =
        parseInt(document.getElementById('potongan').value) || 0;


    // Total pendapatan
    const pendapatan = gajiPokok + lembur;

    // Gaji bersih
    const bersih = pendapatan - potongan;


    // Tampilkan total pendapatan
    document.getElementById('hasil_pendapatan').textContent =
        'Rp ' + pendapatan.toLocaleString('id-ID');


    // Tampilkan total potongan
    document.getElementById('hasil_potongan').textContent =
        'Rp ' + potongan.toLocaleString('id-ID');


    // Tampilkan gaji bersih
    document.getElementById('hasil_bersih').textContent =
        'Rp ' + bersih.toLocaleString('id-ID');
}


// ==========================================
// HITUNG OTOMATIS SAAT INPUT
// ==========================================

document.addEventListener('DOMContentLoaded', function () {

    const gajiPokok = document.getElementById('gaji_pokok');
    const lembur = document.getElementById('jumlah_lembur');
    const potongan = document.getElementById('potongan');


    if (gajiPokok) {
        gajiPokok.addEventListener('input', hitungGaji);
    }

    if (lembur) {
        lembur.addEventListener('input', hitungGaji);
    }

    if (potongan) {
        potongan.addEventListener('input', hitungGaji);
    }


    // Hitung saat halaman pertama dibuka
    hitungGaji();
});