<?php
//import file config untuk koneksi ke database
require('dbconfig.php');
$username=$_POST['username'];
$password=$_POST['password'];
$password2=$_POST['password2'];
//form validasi
if($username == "" || $password == "" || $password2 == "" ){
header("location:daftar_admin.php?pesan=gagal");
//Menghentikan proses kebawah
die;
}
//Jika form password dan ulangi password berbeda
if ($password != $password2){
header("location:daftar_admin.php?pesan=password");
//Menghentikan proses kebawah
die;
}
//cek username
$data = mysqli_query($mysqli,"select * from admin where username='$username'");
$cek = mysqli_num_rows($data);
//Jika username sudah terdaftar makan kembali ke form pendaftaran
if($cek > 0){
header("location:daftar_admin.php?pesan=username");
//Menghentikan proses kebawah
die;
}
//Membuat SHA1 Password
$password_new = sha1($password);
//
$result = mysqli_query($mysqli,
"INSERT INTO `admin` (`username`, `password`) VALUES ('$username', '$password_new');
");
if ($result) {
//Jika berhasil
header("location:login.php");
exit;
} else {
echo "Error: " . "<br>" . mysqli_error($mysqli);
}