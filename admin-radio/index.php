<?php
/**
 * File: admin-radio/index.php
 * Fungsi: Mencegah akses langsung ke direktori admin-radio dan mengarahkan ke login.
 */

// Redirect ke halaman login
header('Location: login.php');
exit();
?>
