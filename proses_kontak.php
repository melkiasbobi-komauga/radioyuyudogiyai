<?php
// Hubungkan ke config.php yang ada di folder yang sama (Root)
// PERBAIKAN: Menggunakan config utama agar $pdo dikenali
require_once 'config.php';

// Pastikan koneksi PDO tersedia
if (!isset($pdo)) {
    die("Koneksi database gagal.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kontak.php");
    exit();
}

// Ambil input
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
// Menangani field telepon jika ada di form, jika tidak kosongkan
$telepon = trim($_POST['telepon'] ?? ''); 
$subjek = trim($_POST['subjek'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');

// 1. Validasi Input Wajib
if (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
    header('Location: kontak.php?status=gagal_validasi');
    exit();
}

// 2. Validasi Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: kontak.php?status=gagal_email');
    exit();
}

// Sanitisasi
$nama_clean = htmlspecialchars(strip_tags($nama), ENT_QUOTES, 'UTF-8');
$subjek_clean = htmlspecialchars(strip_tags($subjek), ENT_QUOTES, 'UTF-8');
$pesan_clean = strip_tags($pesan); // Strip tags saja untuk pesan agar aman tapi tetap raw

try {
    // 3. Masukkan ke Database
    // Catatan: Jika tabel Anda tidak memiliki kolom 'telepon', hapus bagian telepon dari query di bawah
    // Berdasarkan file SQL Anda, tabel pesan_kontak TIDAK memiliki kolom telepon, jadi saya sesuaikan.
    // Tetapi di form kontak.php ada input telepon. Saya akan masukkan telepon ke dalam isi pesan agar tidak hilang.
    
    if (!empty($telepon)) {
        $pesan_clean .= "\n\n(No. Telepon: " . htmlspecialchars($telepon) . ")";
    }

    $stmt = $pdo->prepare("INSERT INTO pesan_kontak (nama_pengirim, email_pengirim, subjek, isi_pesan, tanggal_kirim, status) VALUES (?, ?, ?, ?, NOW(), 'belum dibaca')");
    $stmt->execute([$nama_clean, $email, $subjek_clean, $pesan_clean]);
    
    header("Location: kontak.php?status=sukses");
} catch (PDOException $e) {
    error_log("Error Kontak: " . $e->getMessage());
    header("Location: kontak.php?status=gagal_sistem");
}

exit();
?>