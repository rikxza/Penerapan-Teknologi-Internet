<?php
session_start();
//setting dahulu xampp -> apache -> bin -> php.ini baris 976 ganti auto start dengan 1
//import file config untuk koneksi ke database
require('dbconfig.php');
$username=$_POST['username'];
$password=$_POST['password'];
if($username == "" || $password == ""){
header("location:login.php?pesan=gagal");
//Menghentikan proses kebawah
exit;
}
//Mengubah password menjadi SHA1
$password_new = sha1($password);
// Cari data ke dalam db dengan username dan password
$data = mysqli_query($mysqli,"select * from admin where username='$username' and password='$password_new'");
// menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($data);
if($cek > 0){
$_SESSION['username'] = $username;
$_SESSION['status'] = "login";
header("location:tambah_berita.php");
}else{
header("location:login.php?pesan=tidak_cocok");
}
