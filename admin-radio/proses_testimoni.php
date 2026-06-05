<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Untuk proses front-end POST tidak butuh login admin
// Tapi untuk aksi GET (hapus/tampil/sembunyi) butuh login admin
$aksi = $_GET['aksi'] ?? '';

if ($aksi != '' && !isAdminLoggedIn()) {
    redirect('login.php');
    exit();
}

$pdo = getDBConnection();
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

try {
    switch ($aksi) {
        case 'tampil':
        case 'sembunyi':
            if (!$id) { redirect('kelola_testimoni.php'); exit; }
            $status_baru = ($aksi == 'tampil') ? 'tampil' : 'sembunyi';
            $stmt = $pdo->prepare("UPDATE testimoni SET status = ? WHERE id = ?");
            $stmt->execute([$status_baru, $id]);
            redirect('kelola_testimoni.php?status=sukses_update');
            break;

        case 'hapus':
            if (!$id) { redirect('kelola_testimoni.php'); exit; }
            $stmt = $pdo->prepare("DELETE FROM testimoni WHERE id = ?");
            $stmt->execute([$id]);
            redirect('kelola_testimoni.php?status=sukses_hapus');
            break;
        
        // Jika POST dari frontend (submit testimoni baru)
        // Ini biasanya ada di file terpisah (proses_testimoni.php di root), 
        // tapi jika digabung, pastikan handlingnya benar.
        // Di struktur Anda saat ini, sepertinya ada file proses_testimoni.php di root untuk frontend.
        // File ini khusus admin.
        default:
            redirect('kelola_testimoni.php');
            break;
    }
} catch (PDOException $e) {
    die("Error pada database: " . $e->getMessage());
}
?>
