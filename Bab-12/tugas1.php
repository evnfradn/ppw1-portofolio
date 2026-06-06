<?php
$nama = "Evan Fausta Pradana"; 
$nim = "25/560829/SV/26481"; 
$prodi = "Teknologi Rekayasa Perangkat Lunak (TRPL)";
$asal_kota = "Yogyakarta";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tugas 1 - Profil Diri</title>
    <style>
        table { border-collapse: collapse; width: 40%; margin: 20px 0; font-family: sans-serif; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Profil Mahasiswa</h2>
    <table>
        <tr>
            <th>Biodata</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>Nama</td>
            <td><?php echo $nama; ?></td>
        </tr>
        <tr>
            <td>NIM</td>
            <td><?php echo $nim; ?></td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td><?php echo $prodi; ?></td>
        </tr>
        <tr>
            <td>Asal Kota</td>
            <td><?php echo $asal_kota; ?></td>
        </tr>
    </table>
</body>
</html>