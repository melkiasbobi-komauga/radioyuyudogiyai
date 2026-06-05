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
 * Fungsi untuk menangani upload file media dengan aman.
 * @param array $file Data file dari $_FILES.
 * @param string $type Tipe file ('image' atau 'audio').
 * @return string|false|null Nama file baru, false jika gagal, null jika tidak ada file.
 */
function uploadMedia($file, $type = 'image') {
    // Cek error upload
    if ($file['error'] === 4) { 
        return null; // Tidak ada file yang diupload
    }

    $namaFile = $file['name'];
    $ukuranFile = $file['size'];
    $tmpName = $file['tmp_name'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if ($type === 'image') {
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 2000000; // 2MB
        $prefix = 'cover-';
    } else {
        $validExtensions = ['mp3', 'wav', 'ogg', 'm4a'];
        $maxSize = 10000000; // 10MB
        $prefix = 'lagu-';
    }

    // Validasi Ekstensi
    if (!in_array($ekstensi, $validExtensions)) {
        return false;
    }

    // Validasi Ukuran
    if ($ukuranFile > $maxSize) {
        return false;
    }

    // Generate nama baru yang unik
    $namaFileBaru = uniqid($prefix, true) . '.' . $ekstensi;
    $targetPath = 'uploads/' . $namaFileBaru;

    // Pastikan folder uploads ada
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    // Pindahkan file
    if (move_uploaded_file($tmpName, $targetPath)) {
        return $namaFileBaru;
    }

    return false;
}

// Tentukan aksi
$aksi = $_GET['aksi'] ?? '';

try {
    // VALIDASI CSRF UNTUK SEMUA AKSI POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die("Error: Validasi keamanan (CSRF) gagal. Silakan refresh halaman.");
        }
    }

    switch ($aksi) {
        case 'tambah':
            // Validasi input
            if (empty($_POST['judul_lagu']) || empty($_POST['artis']) || empty($_POST['peringkat'])) {
                redirect('kelola_chart.php?status=gagal_validasi');
                exit();
            }

            $judul = trim($_POST['judul_lagu']);
            $artis = trim($_POST['artis']);
            $peringkat = (int)$_POST['peringkat'];

            // Upload Cover
            $cover = uploadMedia($_FILES['cover_album'], 'image');
            if ($cover === false) {
                redirect('kelola_chart.php?status=gagal_upload');
                exit();
            }
            $cover = $cover ?? ''; 

            // Upload Audio
            $audio = uploadMedia($_FILES['file_audio'], 'audio');
            if ($audio === false) {
                // Bersihkan cover jika audio gagal
                if(!empty($cover) && file_exists('uploads/'.$cover)) unlink('uploads/'.$cover);
                redirect('kelola_chart.php?status=gagal_upload');
                exit();
            }
            $audio = $audio ?? '';

            // Insert ke DB
            $stmt = $pdo->prepare("INSERT INTO chart_lagu (judul_lagu, artis, peringkat, cover_album, file_audio) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$judul, $artis, $peringkat, $cover, $audio]);
            
            redirect('kelola_chart.php?status=sukses_tambah');
            break;

        case 'edit':
            if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
                redirect('kelola_chart.php?status=gagal_id');
                exit();
            }

            $id = $_POST['id'];
            $judul = trim($_POST['judul_lagu']);
            $artis = trim($_POST['artis']);
            $peringkat = (int)$_POST['peringkat'];
            
            $cover = $_POST['cover_lama'];
            $audio = $_POST['audio_lama']; // Pastikan ini dikirim dari form edit

            // Proses Upload Cover Baru
            if (isset($_FILES['cover_album']) && $_FILES['cover_album']['error'] !== 4) {
                $cover_baru = uploadMedia($_FILES['cover_album'], 'image');
                if ($cover_baru === false) {
                    redirect("edit_lagu.php?id=$id&status=gagal_upload");
                    exit();
                } elseif ($cover_baru) {
                    // Hapus file lama jika ada dan bukan default/kosong
                    if (!empty($cover) && file_exists('uploads/' . $cover)) {
                        unlink('uploads/' . $cover);
                    }
                    $cover = $cover_baru;
                }
            }

            // Proses Upload Audio Baru
            if (isset($_FILES['file_audio']) && $_FILES['file_audio']['error'] !== 4) {
                $audio_baru = uploadMedia($_FILES['file_audio'], 'audio');
                if ($audio_baru === false) {
                    redirect("edit_lagu.php?id=$id&status=gagal_upload");
                    exit();
                } elseif ($audio_baru) {
                    // Hapus file lama
                    if (!empty($audio) && file_exists('uploads/' . $audio)) {
                        unlink('uploads/' . $audio);
                    }
                    $audio = $audio_baru;
                }
            }

            $stmt = $pdo->prepare("UPDATE chart_lagu SET judul_lagu=?, artis=?, peringkat=?, cover_album=?, file_audio=? WHERE id=?");
            $stmt->execute([$judul, $artis, $peringkat, $cover, $audio, $id]);
            
            redirect('kelola_chart.php?status=sukses_edit');
            break;

        case 'hapus':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                redirect('kelola_chart.php?status=gagal_id');
                exit();
            }
            $id = $_GET['id'];

            // Ambil nama file sebelum menghapus data
            $stmt = $pdo->prepare("SELECT cover_album, file_audio FROM chart_lagu WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Hapus file fisik
            if ($row) {
                if (!empty($row['cover_album']) && file_exists('uploads/' . $row['cover_album'])) {
                    unlink('uploads/' . $row['cover_album']);
                }
                if (!empty($row['file_audio']) && file_exists('uploads/' . $row['file_audio'])) {
                    unlink('uploads/' . $row['file_audio']);
                }
            }

            // Hapus data dari database
            $stmt = $pdo->prepare("DELETE FROM chart_lagu WHERE id = ?");
            $stmt->execute([$id]);
            
            redirect('kelola_chart.php?status=sukses_hapus');
            break;

        default:
            redirect('kelola_chart.php');
            break;
    }
} catch (PDOException $e) {
    // Log error detail ke file log server (jangan tampilkan ke user di production)
    error_log("Database Error (Chart): " . $e->getMessage());
    
    // Redirect dengan status gagal sistem
    redirect('kelola_chart.php?status=gagal_sistem');
}
?>