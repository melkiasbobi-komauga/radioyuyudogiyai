<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';
if (!isAdminLoggedIn()) { redirect('login.php'); exit(); }

$pdo = getDBConnection();

function uploadFile($file) {
    if ($file['error'] === 4) return null;
    $namaFile = $file['name'];
    $tmpName = $file['tmp_name'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $valid = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    
    if (!in_array($ekstensi, $valid)) return false;
    if ($file['size'] > 5000000) return false;

    $namaFileBaru = uniqid('lampiran-', true) . '.' . $ekstensi;
    $targetPath = 'uploads/' . $namaFileBaru;
    if (!is_dir('uploads')) mkdir('uploads', 0777, true);

    if (move_uploaded_file($tmpName, $targetPath)) return $namaFileBaru;
    return false;
}

$aksi = $_GET['aksi'] ?? '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die("Error: Validasi keamanan (CSRF) gagal.");
        }
    }

    switch ($aksi) {
        case 'tambah':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('kelola_pengumuman.php'); exit; }

            $judul = trim($_POST['judul']);
            $isi = trim($_POST['isi_pengumuman']);
            $tgl_akhir = !empty($_POST['tanggal_berakhir']) ? $_POST['tanggal_berakhir'] : NULL;
            $status = $_POST['status'];
            
            $file_lampiran = uploadFile($_FILES['file_lampiran']);
            if ($file_lampiran === false) {
                redirect('tambah_pengumuman.php?status=gagal_upload');
                exit();
            }
            if ($file_lampiran === null) $file_lampiran = ''; 

            $stmt = $pdo->prepare("INSERT INTO pengumuman (judul, isi_pengumuman, file_lampiran, tanggal_berakhir, status, tanggal_dibuat) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$judul, $isi, $file_lampiran, $tgl_akhir, $status]);
            
            redirect('kelola_pengumuman.php?status=sukses_tambah');
            break;

        case 'edit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('kelola_pengumuman.php'); exit; }

            $id = $_POST['id'];
            $judul = trim($_POST['judul']);
            $isi = trim($_POST['isi_pengumuman']);
            $tgl_akhir = !empty($_POST['tanggal_berakhir']) ? $_POST['tanggal_berakhir'] : NULL;
            $status = $_POST['status'];
            $file_lama = $_POST['file_lama'];
            $file_lampiran = $file_lama;

            if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] !== 4) {
                $file_baru = uploadFile($_FILES['file_lampiran']);
                if ($file_baru === false) {
                    redirect("edit_pengumuman.php?id=$id&status=gagal_upload");
                    exit();
                } elseif ($file_baru) {
                    $file_lampiran = $file_baru;
                    if (!empty($file_lama) && file_exists('uploads/' . $file_lama)) {
                        unlink('uploads/' . $file_lama);
                    }
                }
            }

            $stmt = $pdo->prepare("UPDATE pengumuman SET judul=?, isi_pengumuman=?, file_lampiran=?, tanggal_berakhir=?, status=? WHERE id=?");
            $stmt->execute([$judul, $isi, $file_lampiran, $tgl_akhir, $status, $id]);
            
            redirect('kelola_pengumuman.php?status=sukses_edit');
            break;

        case 'hapus':
            if (!isset($_GET['id'])) { redirect('kelola_pengumuman.php'); exit; }
            $id = $_GET['id'];

            $stmt_select = $pdo->prepare("SELECT file_lampiran FROM pengumuman WHERE id = ?");
            $stmt_select->execute([$id]);
            $row = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['file_lampiran']) && file_exists('uploads/' . $row['file_lampiran'])) {
                unlink('uploads/' . $row['file_lampiran']);
            }

            $stmt_delete = $pdo->prepare("DELETE FROM pengumuman WHERE id = ?");
            $stmt_delete->execute([$id]);
            
            redirect('kelola_pengumuman.php?status=sukses_hapus');
            break;
    }
} catch (PDOException $e) {
    die("Error pada database: " . $e->getMessage());
}
?>
