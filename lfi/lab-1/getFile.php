<?php
//payload ../../../../../../../../etc/passwd
//payload ../../../../../phpinfo.php
$file = $_GET['file'] ?? null;

$validFiles = ['file1.php', 'file2.php', 'file3.php'];

if (in_array($file, $validFiles)) {
    $filePath = "../../assets/gallery/lab-1/{$file}";

    // Pastikan file tersebut adalah file yang diharapkan
    if (file_exists($filePath) && is_file($filePath)) {
        include($filePath);
    } else {
        die("File tidak ditemukan");
    }
} else {
    die("File tidak valid");
}

?>


