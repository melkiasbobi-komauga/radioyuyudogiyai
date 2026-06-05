<?php
// Hubungkan ke config.php yang ada di folder yang sama (Root)
// PERBAIKAN: Jalur sebelumnya salah mengarah ke admin-radio/config.php
require_once 'config.php';

// Pastikan koneksi PDO tersedia
if (!isset($pdo)) {
    die("Koneksi database gagal. Cek file config.php.");
}

// Cek metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Ambil data
$email = trim($_POST['email'] ?? '');

// 1. Validasi Format Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?status=gagal_email#newsletter');
    exit();
}

// Sanitisasi
$email_clean = htmlspecialchars(strip_tags($email), ENT_QUOTES, 'UTF-8');

try {
    // 2. Cek apakah email sudah terdaftar
    $check_stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $check_stmt->execute([$email_clean]);
    
    if ($check_stmt->rowCount() > 0) {
        // Email sudah ada
        header("Location: index.php?status=gagal_double_entry#newsletter");
        exit();
    }

    // 3. Simpan Email Baru
    $insert_stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, tanggal_daftar) VALUES (?, NOW())");
    
    if ($insert_stmt->execute([$email_clean])) {
        header("Location: index.php?status=sukses_newsletter#newsletter");
    } else {
        header("Location: index.php?status=gagal_sistem#newsletter");
    }

} catch (PDOException $e) {
    // Log error untuk admin, jangan tampilkan ke user
    error_log("Error Newsletter: " . $e->getMessage());
    header("Location: index.php?status=gagal_sistem#newsletter");
}

exit();
?>