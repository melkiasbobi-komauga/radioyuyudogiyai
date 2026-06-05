<?php
// 1. Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Sertakan file konfigurasi dan auth
require_once 'includes/config.php';
require_once 'includes/auth.php';

// 3. Cek Login
if (!isAdminLoggedIn()) {
    redirect('login.php');
    exit();
}

// 4. Ambil koneksi PDO
$pdo = getDBConnection();

// Tentukan aksi
$aksi = $_GET['aksi'] ?? '';

try {
    // Admin hanya bisa menghapus topik forum
    if ($aksi == 'hapus') {
        // Validasi ID
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            redirect('kelola_forum.php?status=gagal_id');
            exit();
        }

        $id = $_GET['id'];

        // Mulai transaksi untuk menghapus topik dan balasannya secara atomik
        $pdo->beginTransaction();

        // 1. Hapus semua balasan yang terkait dengan topik ini terlebih dahulu
        $stmt_balasan = $pdo->prepare("DELETE FROM forum_balasan WHERE id_topik = ?");
        $stmt_balasan->execute([$id]);

        // 2. Hapus topik utama
        $stmt_topik = $pdo->prepare("DELETE FROM forum_topik WHERE id = ?");
        $stmt_topik->execute([$id]);

        // Commit transaksi (simpan perubahan permanen)
        $pdo->commit();
        
        redirect('kelola_forum.php?status=sukses_hapus');
    } else {
        // Jika aksi tidak dikenali, kembali ke daftar
        redirect('kelola_forum.php');
    }
} catch (PDOException $e) {
    // Rollback jika terjadi error di tengah jalan (batalkan semua perubahan)
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Database Error (Forum): " . $e->getMessage());
    redirect('kelola_forum.php?status=gagal_sistem');
}
?>
