<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';
if (!isAdminLoggedIn()) { redirect('login.php'); exit(); }

$pdo = getDBConnection();

function uploadProgramImg($file) {
    if ($file['error'] === 4) return null;
    $namaFile = $file['name'];
    $tmpName = $file['tmp_name'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $validExt = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (!in_array($ekstensi, $validExt)) return false;
    if ($file['size'] > 2000000) return false;

    $namaFileBaru = uniqid('prog-', true) . '.' . $ekstensi;
    $targetPath = 'uploads/' . $namaFileBaru;
    if (!is_dir('uploads')) mkdir('uploads', 0755, true);
    if (move_uploaded_file($tmpName, $targetPath)) return $namaFileBaru;
    return false;
}

$aksi = $_GET['aksi'] ?? '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die("Error: Validasi CSRF gagal.");
        }
    }

    switch ($aksi) {
        case 'tambah':
            $judul = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $gambar = uploadProgramImg($_FILES['gambar']);
            
            if ($gambar === false || $gambar === null) {
                redirect('tambah_program.php?status=gagal_upload');
                exit();
            }
            
            $stmt = $pdo->prepare("INSERT INTO program_unggulan (judul, deskripsi, gambar) VALUES (?, ?, ?)");
            $stmt->execute([$judul, $deskripsi, $gambar]);
            redirect('kelola_program.php?status=sukses_tambah');
            break;

        case 'edit':
            $id = $_POST['id'];
            $judul = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $gambar_lama = $_POST['gambar_lama'];
            $gambar = $gambar_lama;

            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== 4) {
                $gambar_baru = uploadProgramImg($_FILES['gambar']);
                if ($gambar_baru) {
                    $gambar = $gambar_baru;
                    if (!empty($gambar_lama) && file_exists('uploads/' . $gambar_lama)) unlink('uploads/' . $gambar_lama);
                }
            }

            $stmt = $pdo->prepare("UPDATE program_unggulan SET judul=?, deskripsi=?, gambar=? WHERE id=?");
            $stmt->execute([$judul, $deskripsi, $gambar, $id]);
            redirect('kelola_program.php?status=sukses_edit');
            break;

        case 'hapus':
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT gambar FROM program_unggulan WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['gambar']) && file_exists('uploads/' . $row['gambar'])) unlink('uploads/' . $row['gambar']);

            $stmt = $pdo->prepare("DELETE FROM program_unggulan WHERE id = ?");
            $stmt->execute([$id]);
            redirect('kelola_program.php?status=sukses_hapus');
            break;
    }
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>
