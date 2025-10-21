<?php
//set time zone
date_default_timezone_set("Asia/Bangkok");


// Koneksi
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_berita";

// Laporkan error mysqli sebagai exception sehingga bisa ditangani
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli($host, $user, $pass, $db);
    // Pastikan set charset
    $mysqli->set_charset('utf8');
} catch (mysqli_sql_exception $e) {
    // Tampilkan pesan yang lebih ramah dan hentikan eksekusi
    echo "Koneksi database gagal: " . htmlspecialchars($e->getMessage());
    exit;
}
?>
