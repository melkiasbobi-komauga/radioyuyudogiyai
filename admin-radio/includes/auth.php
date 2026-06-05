<?php
// Pastikan file ini di-include setelah session_start() dipanggil di file utama.

// Gunakan __DIR__ untuk memastikan path config.php selalu benar relatif terhadap file ini
require_once __DIR__ . '/config.php';

/**
 * Fungsi untuk login admin dengan memverifikasi hash password.
 * Ini adalah metode yang aman dan direkomendasikan.
 *
 * @param string $username Username admin.
 * @param string $password Password yang dimasukkan pengguna (teks biasa).
 * @return bool True jika login berhasil, false jika gagal.
 */
function adminLogin($username, $password) {
    // Pastikan fungsi getDBConnection tersedia
    if (!function_exists('getDBConnection')) {
        die("Error: Fungsi getDBConnection tidak ditemukan. Pastikan config.php dimuat dengan benar.");
    }

    $pdo = getDBConnection();
    
    // Ambil data admin dari database berdasarkan username.
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    // Verifikasi password:
    // 1. Cek apakah admin ditemukan.
    // 2. Gunakan password_verify() untuk membandingkan password yang diinput
    //    dengan hash yang ada di database.
    if ($admin && password_verify($password, $admin['password'])) {
        // Jika verifikasi berhasil, simpan informasi admin ke dalam session.
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_nama'] = $admin['nama_lengkap'];
        return true;
    }
    
    // Jika username tidak ditemukan atau password salah.
    return false;
}

/**
 * Fungsi untuk mendaftarkan admin baru dengan mengenkripsi (hashing) password.
 * Ini adalah metode yang aman untuk menyimpan password.
 *
 * @param string $username Username baru.
 * @param string $password Password baru (teks biasa).
 * @param string $nama_lengkap Nama lengkap admin.
 * @return bool True jika registrasi berhasil, false jika gagal.
 */
function registerAdmin($username, $password, $nama_lengkap) {
    // Pastikan fungsi getDBConnection tersedia
    if (!function_exists('getDBConnection')) {
         // Fallback jika somehow config tidak ter-load, walau require_once di atas sudah handle
         require_once __DIR__ . '/config.php';
    }
    
    $pdo = getDBConnection();
    
    // Buat hash dari password menggunakan algoritma yang aman (PASSWORD_DEFAULT).
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Simpan hash password ke database, bukan password aslinya.
        $stmt = $pdo->prepare("INSERT INTO admin (username, password, nama_lengkap) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hash, $nama_lengkap]);
        return true;
    } catch (PDOException $e) {
        // Gagal jika username sudah ada (jika kolom username di-set sebagai UNIQUE).
        return false;
    }
}

/**
 * Fungsi untuk memeriksa apakah username admin sudah ada di database.
 *
 * @param string $username Username yang akan diperiksa.
 * @return bool True jika username sudah ada, false jika belum.
 */
function checkAdminUsernameExists($username) {
    if (!function_exists('getDBConnection')) {
        require_once __DIR__ . '/config.php';
   }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Fungsi untuk mengecek apakah user sudah login sebagai admin.
 * * @return bool True jika sudah login, false jika belum.
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Fungsi helper untuk redirect (jika belum ada di config.php)
 */
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

/**
 * Fungsi helper untuk mendapatkan data admin yang sedang login
 */
if (!function_exists('getCurrentAdmin')) {
    function getCurrentAdmin() {
        if (!isAdminLoggedIn()) {
            return null;
        }
        
        if (!function_exists('getDBConnection')) {
             require_once __DIR__ . '/config.php';
        }

        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        
        return $stmt->fetch();
    }
}
?>