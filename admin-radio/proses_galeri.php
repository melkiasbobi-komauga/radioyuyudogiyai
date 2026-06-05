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

/**
 * Fungsi upload gambar (sama seperti di proses_berita, gunakan include functions jika mau lebih DRY)
 */
function uploadGambar($file) {
    if ($file['error'] === 4) { return null; }

    $namaFile = $file['name'];
    $ukuranFile = $file['size'];
    $tmpName = $file['tmp_name'];

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    
    if (!in_array($ekstensi, $ekstensiValid)) { return false; }

    // Validasi MIME Type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpName);
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedMimeTypes)) { return false; }

    if ($ukuranFile > 5000000) { return false; } // 5MB

    $namaFileBaru = uniqid('galeri-', true) . '.' . $ekstensi;
    $targetPath = 'uploads/' . $namaFileBaru;

    if (!is_dir('uploads')) { mkdir('uploads', 0755, true); }

    if (move_uploaded_file($tmpName, $targetPath)) {
        return $namaFileBaru;
    }

    return false;
}

$aksi = $_GET['aksi'] ?? '';

try {
    switch ($aksi) {
        case 'tambah':
            if (empty($_POST['judul']) || empty($_POST['tipe_media'])) {
                redirect('tambah_galeri.php?status=gagal_validasi');
                exit();
            }

            $judul = trim($_POST['judul']);
            $tipe = $_POST['tipe_media'];
            $keterangan = trim($_POST['keterangan']);
            $file_media = '';

            if ($tipe == 'foto') {
                $file_media = uploadGambar($_FILES['file_media_foto']);
                if ($file_media === false) {
                    redirect('tambah_galeri.php?status=gagal_upload');
                    exit();
                } elseif ($file_media === null) {
                    redirect('tambah_galeri.php?status=gagal_upload'); // Foto wajib
                    exit();
                }
            } else { // video
                $file_media = trim($_POST['file_media_video']);
                if (empty($file_media)) {
                    redirect('tambah_galeri.php?status=gagal_validasi');
                    exit();
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO galeri (judul, tipe_media, keterangan, file_media, tanggal_upload) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$judul, $tipe, $keterangan, $file_media]);
            
            redirect('kelola_galeri.php?status=sukses_tambah');
            break;

        case 'edit':
            if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
                redirect('kelola_galeri.php?status=gagal_id');
                exit();
            }
            $id = $_POST['id'];
            $judul = trim($_POST['judul']);
            $tipe = $_POST['tipe_media']; 
            $keterangan = trim($_POST['keterangan']);
            $file_media_lama = $_POST['file_media_lama'];
            
            $file_media = $file_media_lama;

            if ($tipe == 'foto') {
                if (isset($_FILES['file_media_foto']) && $_FILES['file_media_foto']['error'] !== 4) {
                    $file_media_baru = uploadGambar($_FILES['file_media_foto']);
                    if ($file_media_baru === false) {
                        redirect("edit_galeri.php?id=$id&status=gagal_upload");
                        exit();
                    } elseif ($file_media_baru) {
                        $file_media = $file_media_baru;
                        // Hapus foto lama
                        if (!empty($file_media_lama) && file_exists('uploads/' . $file_media_lama)) {
                            unlink('uploads/' . $file_media_lama);
                        }
                    }
                }
            } else { // video
                $file_media = trim($_POST['file_media_video']);
            }

            $stmt = $pdo->prepare("UPDATE galeri SET judul=?, keterangan=?, file_media=? WHERE id=?");
            $stmt->execute([$judul, $keterangan, $file_media, $id]);
            
            redirect('kelola_galeri.php?status=sukses_edit');
            break;

        case 'hapus':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                redirect('kelola_galeri.php');
                exit();
            }
            
            $id = $_GET['id'];

            // Ambil info file
            $stmt_select = $pdo->prepare("SELECT tipe_media, file_media FROM galeri WHERE id = ?");
            $stmt_select->execute([$id]);
            $item = $stmt_select->fetch(PDO::FETCH_ASSOC);

            // Hapus file fisik jika foto
            if ($item && $item['tipe_media'] == 'foto' && !empty($item['file_media']) && file_exists('uploads/' . $item['file_media'])) {
                unlink('uploads/' . $item['file_media']);
            }

            $stmt_delete = $pdo->prepare("DELETE FROM galeri WHERE id = ?");
            $stmt_delete->execute([$id]);
            
            redirect('kelola_galeri.php?status=sukses_hapus');
            break;

        default:
            redirect('kelola_galeri.php');
            break;
    }
} catch (PDOException $e) {
    error_log("Database Error (Galeri): " . $e->getMessage());
    redirect('kelola_galeri.php?status=gagal_sistem');
}
?>
