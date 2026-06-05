<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';
if (!isAdminLoggedIn()) { redirect('login.php'); exit(); }

$pdo = getDBConnection();

function uploadGambar($file) {
    if ($file['error'] === 4) return null;
    $namaFile = $file['name'];
    $tmpName = $file['tmp_name'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $validExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($ekstensi, $validExt)) return false;
    if ($file['size'] > 2000000) return false;

    $namaFileBaru = uniqid('tim-', true) . '.' . $ekstensi;
    $targetPath = 'uploads/' . $namaFileBaru;
    if (!is_dir('uploads')) mkdir('uploads', 0777, true);
    if (move_uploaded_file($tmpName, $targetPath)) return $namaFileBaru;
    return false;
}

$aksi = $_GET['aksi'] ?? '';

try {
    // VALIDASI CSRF
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die("Error: Validasi keamanan (CSRF) gagal.");
        }
    }

    switch ($aksi) {
        case 'tambah':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('kelola_tim.php'); exit; }

            $nama = trim($_POST['nama_lengkap']);
            $jabatan = trim($_POST['jabatan']);
            $bio = trim($_POST['bio_singkat']);
            $foto = uploadGambar($_FILES['foto']);
            
            if ($foto === false || $foto === null) {
                redirect('tambah_tim.php?status=gagal_upload');
                exit();
            }
            
            $stmt = $pdo->prepare("INSERT INTO tim (nama_lengkap, jabatan, bio_singkat, foto) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $jabatan, $bio, $foto]);
            
            redirect('kelola_tim.php?status=sukses_tambah');
            break;

        case 'edit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('kelola_tim.php'); exit; }

            $id = $_POST['id'];
            $nama = trim($_POST['nama_lengkap']);
            $jabatan = trim($_POST['jabatan']);
            $bio = trim($_POST['bio_singkat']);
            $foto_lama = $_POST['foto_lama'];
            $foto = $foto_lama;

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== 4) {
                $foto_baru = uploadGambar($_FILES['foto']);
                if ($foto_baru) {
                    $foto = $foto_baru;
                    if (!empty($foto_lama) && file_exists('uploads/' . $foto_lama)) unlink('uploads/' . $foto_lama);
                }
            }

            $stmt = $pdo->prepare("UPDATE tim SET nama_lengkap=?, jabatan=?, bio_singkat=?, foto=? WHERE id=?");
            $stmt->execute([$nama, $jabatan, $bio, $foto, $id]);
            
            redirect('kelola_tim.php?status=sukses_edit');
            break;

        case 'hapus':
            if (!isset($_GET['id'])) { redirect('kelola_tim.php'); exit; }
            $id = $_GET['id'];

            $stmt = $pdo->prepare("SELECT foto FROM tim WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['foto']) && file_exists('uploads/' . $row['foto'])) {
                unlink('uploads/' . $row['foto']);
            }

            $stmt = $pdo->prepare("DELETE FROM tim WHERE id = ?");
            $stmt->execute([$id]);
            
            redirect('kelola_tim.php?status=sukses_hapus');
            break;
    }
} catch (PDOException $e) {
    die("Error pada database: " . $e->getMessage());
}
?>
