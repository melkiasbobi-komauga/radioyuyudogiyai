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
 * Fungsi untuk menangani upload file media (gambar/audio) dengan aman.
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

    // Konfigurasi berdasarkan tipe
    if ($type === 'image') {
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 2000000; // 2MB untuk gambar
        $prefix = 'podcast-img-';
    } else { // audio
        $validExtensions = ['mp3', 'wav', 'ogg', 'm4a'];
        $maxSize = 50000000; // 50MB untuk audio
        $prefix = 'podcast-audio-';
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
        mkdir('uploads', 0777, true);
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
    // VALIDASI CSRF
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die("Error: Validasi keamanan (CSRF) gagal.");
        }
    }

    switch ($aksi) {
        case 'tambah':
            // Validasi input
            if (empty($_POST['judul'])) {
                redirect('tambah_podcast.php?status=gagal_validasi');
                exit();
            }

            $judul = $_POST['judul'];
            $deskripsi = $_POST['deskripsi'];

            // Upload Media
            $gambar = uploadMedia($_FILES['file_gambar'], 'image');
            $audio = uploadMedia($_FILES['file_audio'], 'audio');
            
            // PERBAIKAN VALIDASI: Audio dan Gambar wajib diupload saat TAMBAH
            if ($gambar === false || $audio === false) {
                redirect('tambah_podcast.php?status=gagal_upload');
                exit();
            }
            if ($gambar === null || $audio === null) {
                // Jika tidak ada file yang diupload (error=4)
                redirect('tambah_podcast.php?status=gagal_validasi_file');
                exit();
            }
            
            $stmt = $pdo->prepare("INSERT INTO podcast (judul, deskripsi, file_gambar, file_audio, tanggal_upload) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$judul, $deskripsi, $gambar, $audio]);
            
            redirect('kelola_podcast.php?status=sukses_tambah');
            break;

        case 'edit':
            $id = $_POST['id'];
            $judul = $_POST['judul'];
            $deskripsi = $_POST['deskripsi'];
            $gambar_lama = $_POST['gambar_lama'];
            $audio_lama = $_POST['audio_lama'];
            
            $gambar = $gambar_lama;
            $audio = $audio_lama;

            // Cek & Proses Upload Gambar Baru
            if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] !== 4) {
                $gambar_baru = uploadMedia($_FILES['file_gambar'], 'image');
                if ($gambar_baru === false) {
                    redirect("edit_podcast.php?id=$id&status=gagal_upload");
                    exit();
                } elseif ($gambar_baru) {
                    $gambar = $gambar_baru;
                    // Hapus gambar lama
                    if (!empty($gambar_lama) && file_exists('uploads/' . $gambar_lama)) {
                        unlink('uploads/' . $gambar_lama);
                    }
                }
            }

            // Cek & Proses Upload Audio Baru
            if (isset($_FILES['file_audio']) && $_FILES['file_audio']['error'] !== 4) {
                $audio_baru = uploadMedia($_FILES['file_audio'], 'audio');
                if ($audio_baru === false) {
                    redirect("edit_podcast.php?id=$id&status=gagal_upload");
                    exit();
                } elseif ($audio_baru) {
                    $audio = $audio_baru;
                    // Hapus audio lama
                    if (!empty($audio_lama) && file_exists('uploads/' . $audio_lama)) {
                        unlink('uploads/' . $audio_lama);
                    }
                }
            }

            // PERBAIKAN VALIDASI: Pastikan audio dan gambar TIDAK menjadi kosong saat EDIT
            if (empty($gambar) || empty($audio)) {
                redirect("edit_podcast.php?id=$id&status=gagal_validasi_edit");
                exit();
            }

            $stmt = $pdo->prepare("UPDATE podcast SET judul=?, deskripsi=?, file_gambar=?, file_audio=? WHERE id=?");
            $stmt->execute([$judul, $deskripsi, $gambar, $audio, $id]);
            
            redirect('kelola_podcast.php?status=sukses_edit');
            break;

        case 'hapus':
            if (!isset($_GET['id'])) {
                redirect('kelola_podcast.php');
                exit();
            }
            $id = $_GET['id'];

            // Ambil nama file sebelum menghapus data untuk bersih-bersih file
            $stmt = $pdo->prepare("SELECT file_gambar, file_audio FROM podcast WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Hapus file fisik
            if ($row) {
                if (!empty($row['file_gambar']) && file_exists('uploads/' . $row['file_gambar'])) {
                    unlink('uploads/' . $row['file_gambar']);
                }
                if (!empty($row['file_audio']) && file_exists('uploads/' . $row['file_audio'])) {
                    unlink('uploads/' . $row['file_audio']);
                }
            }

            // Hapus data dari database
            $stmt = $pdo->prepare("DELETE FROM podcast WHERE id = ?");
            $stmt->execute([$id]);
            
            redirect('kelola_podcast.php?status=sukses_hapus');
            break;

        default:
            redirect('kelola_podcast.php');
            break;
    }
} catch (PDOException $e) {
    die("Error pada database: " . $e->getMessage());
}
?>
