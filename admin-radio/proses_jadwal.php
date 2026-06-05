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
    switch ($aksi) {
        case 'tambah':
            // Validasi input
            if (empty($_POST['waktu']) || empty($_POST['program']) || empty($_POST['penyiar'])) {
                redirect('kelola_jadwal.php?status=gagal_validasi'); 
                exit();
            }

            $waktu = trim($_POST['waktu']);
            $program = trim($_POST['program']);
            $penyiar = trim($_POST['penyiar']);

            $stmt = $pdo->prepare("INSERT INTO jadwal (waktu, program, penyiar) VALUES (?, ?, ?)");
            $stmt->execute([$waktu, $program, $penyiar]);
            
            redirect('kelola_jadwal.php?status=sukses_tambah');
            break;

        case 'edit':
            if (empty($_POST['id']) || empty($_POST['waktu']) || empty($_POST['program']) || empty($_POST['penyiar'])) {
                redirect('kelola_jadwal.php?status=gagal_validasi');
                exit();
            }

            $id = $_POST['id'];
            $waktu = trim($_POST['waktu']);
            $program = trim($_POST['program']);
            $penyiar = trim($_POST['penyiar']);

            $stmt = $pdo->prepare("UPDATE jadwal SET waktu=?, program=?, penyiar=? WHERE id=?");
            $stmt->execute([$waktu, $program, $penyiar, $id]);
            
            redirect('kelola_jadwal.php?status=sukses_edit');
            break;

        case 'hapus':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                redirect('kelola_jadwal.php?status=gagal_id');
                exit();
            }

            $id = $_GET['id'];

            $stmt = $pdo->prepare("DELETE FROM jadwal WHERE id=?");
            $stmt->execute([$id]);
            
            redirect('kelola_jadwal.php?status=sukses_hapus');
            break;

        default:
            redirect('kelola_jadwal.php');
            break;
    }
} catch (PDOException $e) {
    error_log("Database Error (Jadwal): " . $e->getMessage());
    redirect('kelola_jadwal.php?status=gagal_sistem');
}
?>
