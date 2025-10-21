<?php
//set time zone
date_default_timezone_set("Asia/Bangkok");
// Koneksi
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_berita";
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
echo "Koneksi Gagal !". $mysqli->connect_errno;
} else {
// echo "Berhasil Konek !";
}
?>