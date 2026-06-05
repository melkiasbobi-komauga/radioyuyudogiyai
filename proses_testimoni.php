<?php
// Hubungkan ke config.php yang ada di folder yang sama (Root)
// PERBAIKAN: Menggunakan config utama
require_once 'config.php';

// Pastikan koneksi PDO tersedia
if (!isset($pdo)) {
    die("Koneksi database gagal.");
}

// Hanya menerima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Jika diakses langsung via URL (GET), kembalikan ke home
    header("Location: index.php");
    exit();
}

// Ambil input
$nama = trim($_POST['nama_pengirim'] ?? '');
$peran = trim($_POST['peran'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');

// 1. Validasi
if (empty($nama) || empty($pesan)) {
    header('Location: index.php?status=gagal_validasi#testimonials');
    exit();
}

// Default peran jika kosong
if (empty($peran)) {
    $peran = 'Pendengar Setia';
}

// Sanitisasi
$nama_clean = htmlspecialchars(strip_tags($nama), ENT_QUOTES, 'UTF-8');
$peran_clean = htmlspecialchars(strip_tags($peran), ENT_QUOTES, 'UTF-8');
$pesan_clean = strip_tags($pesan); 

try {
    // 2. Simpan ke Database
    // Status default 'sembunyi' agar admin bisa moderasi dulu sebelum tampil
    $stmt = $pdo->prepare("INSERT INTO testimoni (nama_pengirim, peran, pesan, status, tanggal_kirim) VALUES (?, ?, ?, 'sembunyi', NOW())");
    $stmt->execute([$nama_clean, $peran_clean, $pesan_clean]);
    
    header("Location: index.php?status=sukses_testimoni#testimonials");
} catch (PDOException $e) {
    error_log("Error Testimoni: " . $e->getMessage()); 
    header("Location: index.php?status=gagal_sistem#testimonials");
}

exit();
?>