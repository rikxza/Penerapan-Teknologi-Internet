<?php
session_start();
if (!isset($_SESSION['username']))
{
echo "<hr> <a href='session_logout.php'>Silakan Login Kembali </a>";
exit;
}
?>