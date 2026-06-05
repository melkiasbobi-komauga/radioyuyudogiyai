<?php
// Hubungkan ke config.php yang ada di folder yang sama (Root)
// PERBAIKAN: Menggunakan config utama agar $pdo dikenali
require_once 'config.php';

// Pastikan koneksi PDO tersedia
if (!isset($pdo)) {
    die("Koneksi database gagal. Cek file config.php.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: forum.php");
    exit();
}

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// --- LOGIKA 1: MENAMBAH TOPIK BARU (PDO) ---
if ($aksi === 'tambah_topik') {
    $nama = trim($_POST['nama_pembuat'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');

    // Validasi input
    if (empty($nama) || empty($judul) || empty($isi)) {
        header('Location: forum.php?status=gagal_validasi');
        exit();
    }

    // Sanitisasi
    $nama_clean = htmlspecialchars(strip_tags($nama), ENT_QUOTES, 'UTF-8');
    $judul_clean = htmlspecialchars(strip_tags($judul), ENT_QUOTES, 'UTF-8');
    $isi_clean = strip_tags($isi); // Biarkan teks asli (tanpa HTML tag berbahaya)

    try {
        $stmt = $pdo->prepare("INSERT INTO forum_topik (nama_pembuat, judul, isi, tanggal_dibuat) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$nama_clean, $judul_clean, $isi_clean]);
        
        // Ambil ID topik yang baru dibuat untuk redirect
        $new_id = $pdo->lastInsertId();
        header("Location: lihat_topik.php?id=$new_id&status=sukses_topik");
    } catch (PDOException $e) {
        error_log("Error Insert Topik: " . $e->getMessage());
        header("Location: forum.php?status=gagal_sistem");
    }
    exit();
}

// --- LOGIKA 2: MENAMBAH BALASAN (PDO) ---
elseif ($aksi === 'tambah_balasan') {
    $id_topik = isset($_POST['id_topik']) ? (int)$_POST['id_topik'] : 0;
    $nama = trim($_POST['nama_pembalas'] ?? '');
    $isi = trim($_POST['isi_balasan'] ?? '');

    // Validasi input
    if ($id_topik <= 0 || empty($nama) || empty($isi)) {
        // Jika ID topik ada tapi data lain kosong, kembalikan ke topik tersebut
        if ($id_topik > 0) {
            header("Location: lihat_topik.php?id=$id_topik&status=gagal_validasi");
        } else {
            header("Location: forum.php");
        }
        exit();
    }

    // Sanitisasi
    $nama_clean = htmlspecialchars(strip_tags($nama), ENT_QUOTES, 'UTF-8');
    $isi_clean = strip_tags($isi);

    try {
        $stmt = $pdo->prepare("INSERT INTO forum_balasan (id_topik, nama_pembalas, isi_balasan, tanggal_dibuat) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$id_topik, $nama_clean, $isi_clean]);
        
        header("Location: lihat_topik.php?id=$id_topik&status=sukses_balasan");
    } catch (PDOException $e) {
        error_log("Error Insert Balasan: " . $e->getMessage());
        header("Location: lihat_topik.php?id=$id_topik&status=gagal_sistem");
    }
    exit();
}

else {
    // Jika aksi tidak valid
    header("Location: forum.php");
    exit();
}
?>