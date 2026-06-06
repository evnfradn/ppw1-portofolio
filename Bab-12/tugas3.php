<?php
date_default_timezone_set('Asia/Jakarta');

$nama_bulan = [
    1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
];
$angka_bulan = (int)date('n'); 
$nama_bulan_sekarang = $nama_bulan[$angka_bulan];

$total_hari_bulan_ini = (int)date('t'); 
$hari_sekarang = (int)date('j');        
$sisa_hari = $total_hari_bulan_ini - $hari_sekarang;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tugas 3 - Informasi Bulan Berjalan</title>
</head>
<body>
    <h2>Informasi Waktu Bulan Ini</h2>
    <p>Hari ini adalah tanggal: <strong><?= date("d/m/Y") ?></strong></p>
    <p>Nama Bulan Sekarang: <strong><?= $nama_bulan_sekarang ?></strong></p>
    <p>Jumlah hari tersisa di bulan <?= $nama_bulan_sekarang ?>: <strong><?= $sisa_hari ?> hari lagi</strong></p>
</body>
</html>