<?php
function hitungIMT($berat, $tinggi) {
    $tinggiMeter = $tinggi / 100;
    $imt = $berat / ($tinggiMeter * $tinggiMeter);
    
    if ($imt < 18.5) {
        return "Kurus";
    } elseif ($imt >= 18.5 && $imt < 25) {
        return "Normal";
    } elseif ($imt >= 25 && $imt < 30) {
        return "Gemuk";
    } else {
        return "Obesitas";
    }
}

$berat_badan = 65; 
$tinggi_badan = 170; 
$kategori = hitungIMT($berat_badan, $tinggi_badan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tugas 2 - Hitung IMT</title>
</head>
<body>
    <h2>Kalkulator Indeks Massa Tubuh (IMT)</h2>
    <p>Berat Badan: <?= $berat_badan ?> kg</p>
    <p>Tinggi Badan: <?= $tinggi_badan ?> cm</p>
    <h3>Kategori Hasil IMT: <strong><?= $kategori ?></strong></h3>
</body>
</html>