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

// Fungsi Upload Logo
function uploadLogo($file) {
    if ($file['error'] === 4) { return null; } // Tidak ada file

    $namaFile = $file['name'];
    $tmpName = $file['tmp_name'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $validExt = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

    if (!in_array($ekstensi, $validExt)) return false;
    if ($file['size'] > 2000000) return false; // Max 2MB

    // Nama file tetap agar mudah di-replace atau unik
    // Kita pakai 'logo.png' agar konsisten, atau bisa uniqid jika ingin history
    // Untuk kemudahan, kita akan menimpa file lama atau membuat baru dengan nama unik
    $namaBaru = 'logo-' . uniqid() . '.' . $ekstensi;
    
    // Path tujuan: folder images di root website (naik satu level dari admin-radio)
    $targetDir = '../images/'; 
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    if (move_uploaded_file($tmpName, $targetDir . $namaBaru)) {
        return $namaBaru;
    }
    return false;
}

// Pastikan request adalah metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Memulai transaksi
        $pdo->beginTransaction();

        // Siapkan query helper
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM profil_website WHERE nama_pengaturan = ?");
        $stmt_update = $pdo->prepare("UPDATE profil_website SET isi_pengaturan = ? WHERE nama_pengaturan = ?");
        $stmt_insert = $pdo->prepare("INSERT INTO profil_website (nama_pengaturan, isi_pengaturan) VALUES (?, ?)");

        // Fungsi helper untuk menyimpan data
        function simpanPengaturan($pdo, $key, $val, $stmt_check, $stmt_update, $stmt_insert) {
            $stmt_check->execute([$key]);
            if ($stmt_check->fetchColumn()) {
                $stmt_update->execute([$val, $key]);
            } else {
                $stmt_insert->execute([$key, $val]);
            }
        }

        // 1. Proses Upload Logo (Jika ada)
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== 4) {
            $logoBaru = uploadLogo($_FILES['logo_file']);
            if ($logoBaru) {
                simpanPengaturan($pdo, 'logo_file', $logoBaru, $stmt_check, $stmt_update, $stmt_insert);
            } else {
                // Jika gagal upload, batalkan semua dan redirect error
                $pdo->rollBack();
                redirect('kelola_profil.php?status=gagal_upload');
                exit();
            }
        }

        // 2. Simpan Data Teks
        // Kita list field yang diizinkan agar aman
        $allowedFields = [
            'station_name', 'organization_name', 'copyright_text',
            'profil_singkat', 'sejarah', 'visi', 'misi',
            'alamat_kantor', 'email_kontak', 'telepon_kontak', 'iframe_peta'
        ];

        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                simpanPengaturan($pdo, $field, $_POST[$field], $stmt_check, $stmt_update, $stmt_insert);
            }
        }

        // Commit transaksi
        $pdo->commit();

        // Arahkan kembali ke halaman profil dengan status sukses
        redirect('kelola_profil.php?status=sukses_update');

    } catch (PDOException $e) {
        // Jika terjadi error, batalkan semua perubahan (rollback)
        $pdo->rollBack();
        die("Error pada database: " . $e->getMessage());
    }
} else {
    // Jika bukan metode POST, arahkan kembali ke halaman profil
    redirect('kelola_profil.php');
}

exit();
?>
