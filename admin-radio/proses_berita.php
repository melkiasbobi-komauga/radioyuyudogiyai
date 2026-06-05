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
 * Fungsi untuk menangani upload gambar dengan aman.
 * @param array $file Data file dari $_FILES.
 * @return string|false|null Nama file baru, false jika gagal, null jika tidak ada file.
 */
function uploadGambar($file) {
    // Cek error upload
    if ($file['error'] === 4) { 
        return null; // Tidak ada file yang diupload
    }

    $namaFile = $file['name'];
    $ukuranFile = $file['size'];
    $tmpName = $file['tmp_name'];

    // Validasi ekstensi
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ekstensiGambar = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        return false;
    }

    // Validasi MIME Type untuk keamanan ekstra (Mencegah file PHP disamarkan)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpName);
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mimeType, $allowedMimeTypes)) {
        return false;
    }

    // Validasi ukuran (maks 2MB)
    if ($ukuranFile > 2000000) {
        return false;
    }

    // Generate nama baru yang unik
    $namaFileBaru = uniqid('berita-', true) . '.' . $ekstensiGambar;
    $targetPath = 'uploads/' . $namaFileBaru;

    // Pastikan folder uploads ada
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true); // Permission 755 lebih aman
    }

    // Pindahkan file
    if (move_uploaded_file($tmpName, $targetPath)) {
        return $namaFileBaru;
    }

    return false;
}

$aksi = $_GET['aksi'] ?? '';

try {
    switch ($aksi) {
        case 'tambah':
            // Validasi Input Dasar
            if (empty($_POST['judul']) || empty($_POST['konten'])) {
                redirect('tambah_berita.php?status=gagal_validasi');
                exit();
            }

            $judul = $_POST['judul'];
            $penulis = !empty($_POST['penulis']) ? $_POST['penulis'] : 'Admin'; // Default penulis
            $konten = $_POST['konten'];

            // Upload gambar (wajib untuk tambah baru agar layout tidak rusak)
            $gambar = uploadGambar($_FILES['gambar']);
            
            if ($gambar === false) {
                redirect('tambah_berita.php?status=gagal_upload'); // Gagal validasi/upload
                exit();
            }
            
            // Jika null (tidak ada gambar), bisa set default atau tolak (di sini kita tolak untuk kerapihan)
            if ($gambar === null) {
                redirect('tambah_berita.php?status=gagal_gambar_kosong');
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO berita (judul, penulis, konten, gambar, tanggal_publikasi) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$judul, $penulis, $konten, $gambar]);
            
            redirect('kelola_berita.php?status=sukses_tambah');
            break;

        case 'edit':
            // Validasi ID
            if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
                redirect('kelola_berita.php?status=gagal_id');
                exit();
            }

            $id = $_POST['id'];
            $judul = $_POST['judul'];
            $penulis = $_POST['penulis'];
            $konten = $_POST['konten'];
            $gambar_lama = $_POST['gambar_lama'];
            
            $gambar = $gambar_lama; // Default gunakan gambar lama

            // Cek jika ada gambar baru diupload
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== 4) {
                $gambar_baru = uploadGambar($_FILES['gambar']);
                
                if ($gambar_baru === false) {
                    redirect("edit_berita.php?id=$id&status=gagal_upload");
                    exit();
                } elseif ($gambar_baru) {
                    $gambar = $gambar_baru;
                    // Hapus gambar lama jika ada file fisiknya
                    if (!empty($gambar_lama) && file_exists('uploads/' . $gambar_lama)) {
                        unlink('uploads/' . $gambar_lama);
                    }
                }
            }

            $stmt = $pdo->prepare("UPDATE berita SET judul=?, penulis=?, konten=?, gambar=? WHERE id=?");
            $stmt->execute([$judul, $penulis, $konten, $gambar, $id]);
            
            redirect('kelola_berita.php?status=sukses_edit');
            break;

        case 'hapus':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                redirect('kelola_berita.php');
                exit();
            }
            $id = $_GET['id'];

            // Ambil nama gambar sebelum hapus data untuk menghapus file fisiknya
            $stmt = $pdo->prepare("SELECT gambar FROM berita WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Hapus file fisik
            if ($row && !empty($row['gambar']) && file_exists('uploads/' . $row['gambar'])) {
                unlink('uploads/' . $row['gambar']);
            }

            // Hapus data dari database
            $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
            $stmt->execute([$id]);
            
            redirect('kelola_berita.php?status=sukses_hapus');
            break;

        default:
            redirect('kelola_berita.php');
            break;
    }
} catch (PDOException $e) {
    // Log error jika perlu, jangan tampilkan detail error ke user di produksi
    error_log("Database Error: " . $e->getMessage());
    redirect('kelola_berita.php?status=gagal_sistem');
}
?>
