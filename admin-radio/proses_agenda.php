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

// Tentukan aksi berdasarkan parameter 'aksi' di URL
$aksi = $_GET['aksi'] ?? '';

try {
    // VALIDASI CSRF UNTUK SEMUA AKSI POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die("Error: Validasi keamanan (CSRF) gagal.");
        }
    }

    switch ($aksi) {
        case 'tambah':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('kelola_agenda.php'); exit; }

            // Validasi input dasar
            if (empty($_POST['nama_kegiatan']) || empty($_POST['tanggal_mulai'])) {
                redirect('tambah_agenda.php?status=gagal_validasi'); 
                exit();
            }

            $nama = trim($_POST['nama_kegiatan']);
            $deskripsi = trim($_POST['deskripsi']);
            $tgl_mulai = $_POST['tanggal_mulai'];
            $waktu_mulai = !empty($_POST['waktu_mulai']) ? $_POST['waktu_mulai'] : null;
            $lokasi = trim($_POST['lokasi']);

            $stmt = $pdo->prepare("INSERT INTO agenda (nama_kegiatan, deskripsi, tanggal_mulai, waktu_mulai, lokasi) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $deskripsi, $tgl_mulai, $waktu_mulai, $lokasi]);
            
            redirect('kelola_agenda.php?status=sukses_tambah');
            break;

        case 'edit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('kelola_agenda.php'); exit; }

            if (empty($_POST['id']) || empty($_POST['nama_kegiatan']) || empty($_POST['tanggal_mulai'])) {
                redirect('kelola_agenda.php?status=gagal_validasi');
                exit();
            }

            $id = $_POST['id'];
            $nama = trim($_POST['nama_kegiatan']);
            $deskripsi = trim($_POST['deskripsi']);
            $tgl_mulai = $_POST['tanggal_mulai'];
            $waktu_mulai = !empty($_POST['waktu_mulai']) ? $_POST['waktu_mulai'] : null;
            $lokasi = trim($_POST['lokasi']);

            $stmt = $pdo->prepare("UPDATE agenda SET nama_kegiatan=?, deskripsi=?, tanggal_mulai=?, waktu_mulai=?, lokasi=? WHERE id=?");
            $stmt->execute([$nama, $deskripsi, $tgl_mulai, $waktu_mulai, $lokasi, $id]);
            
            redirect('kelola_agenda.php?status=sukses_edit');
            break;

        case 'hapus':
            // Hapus biasanya via GET, tapi idealnya via POST form. 
            // Jika masih via GET, CSRF check di atas (blok POST) tidak jalan.
            // Untuk keamanan, kita bisa tambahkan konfirmasi JS, tapi di sini kita proses langsung.
            
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                redirect('kelola_agenda.php?status=gagal_id');
                exit();
            }

            $id = $_GET['id'];

            $stmt = $pdo->prepare("DELETE FROM agenda WHERE id=?");
            $stmt->execute([$id]);
            
            redirect('kelola_agenda.php?status=sukses_hapus');
            break;

        default:
            redirect('kelola_agenda.php');
            break;
    }
} catch (PDOException $e) {
    error_log("Database Error (Agenda): " . $e->getMessage());
    redirect('kelola_agenda.php?status=gagal_sistem');
}

exit();
?>
