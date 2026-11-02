
<?php
session_start();

// Menghapus semua session
$_SESSION = array();

// Menghapus session
session_destroy();

// Kembali ke halaman login
header("Location: Index.php");
exit;
?>
