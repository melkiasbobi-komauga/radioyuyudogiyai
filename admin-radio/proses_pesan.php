<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';
if (!isAdminLoggedIn()) { redirect('login.php'); exit(); }

$pdo = getDBConnection();
$aksi = $_GET['aksi'] ?? '';

try {
    if ($aksi == 'hapus') {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            redirect('kelola_pesan.php');
            exit();
        }

        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM pesan_kontak WHERE id = ?");
        $stmt->execute([$id]);
        
        redirect('kelola_pesan.php?status=sukses_hapus');
    } else {
        redirect('kelola_pesan.php');
    }
} catch (PDOException $e) {
    die("Error pada database: " . $e->getMessage());
}
?>
