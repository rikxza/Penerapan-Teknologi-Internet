<?php
//Session akan dibahas pada materi selanjutnya
include "session_check.php";
?>
<head>
<title>Buku Tamu</title>
</head>
<center>
<body>
<h1>DAFTAR TAMU</h1>
<h2>Universitas Komputer Indonesia</h2>
<hr>
<a href='input_daftar_menu.php'>Input Data Tamu</a> |
<a href='session_logout.php'>Logout</a>
<hr>
<table border=1>
<?php
require('dbconfig.php');
$data = mysqli_query($mysqli,"SELECT * FROM buku_tamu ORDER BY tanggal_bertamu DESC");
$row = mysqli_num_rows($data);
echo "<tr>
<th>No</th>
<th>Nama Tamu</th>
<th>Alamat</th>
<th>No. Telepon </th>
<th>Pesan </th>
<th> Tanggal Bertamu </th>
<th> Aksi </th>
</tr>";
if($row > 0){
$n=1;
//
while ($res = mysqli_fetch_assoc($data))
{
echo "<tr><td>$n
<td>".$res['nama_tamu']."
<td>".$res['alamat_tamu']."
<td>".$res['notelp_tamu']."
<td>".$res['pesan_tamu']."
<td>".$res['tanggal_bertamu']."
<td><a href='daftar_tamu_update.php?id=".$res['id']."'>Update</a> | <a href='proses_hapus.php?id=".$res['id']."'>Hapus</a>";
$n=$n+1;
}
}
else {
echo "<tr><td colspan=7> <center>Tidak ada data";
}
?>
</body>