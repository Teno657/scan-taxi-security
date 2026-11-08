<?php
// download.php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Sécurité : évite les chemins malveillants
    $path = 'qrcodes/' . $file;

    if (file_exists($path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
header("Location: index.php");
?>