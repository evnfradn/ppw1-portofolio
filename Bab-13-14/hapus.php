<?php
include_once("config.php");
requireLogin();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT foto FROM mahasiswa WHERE id=$id");
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['foto']) deleteFile($row['foto']); // Hapus file fisik
        mysqli_query($conn, "DELETE FROM mahasiswa WHERE id=$id"); // Hapus record
    }
}
header('Location: index.php');
exit();
?>