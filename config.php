<?php
// FILE: config.php (Letakkan di folder UTAMA/ROOT website)

if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__);
}

// 1. KONFIGURASI DATABASE
define('DB_HOST', 'localhost');
define('DB_USER', 'u347230510_yuyukominfo');      
define('DB_PASS', 'Yuyukominfo#08');          
define('DB_NAME', 'u347230510_yuyukominfo'); 

// Identitas Website
define('STATION_NAME', "RADIO YUYU KOMINFO DOGIYAI");
define('ORGANIZATION', "93.6 FM - SUARA DOGIYAI");

// URL Dasar
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $protocol . '://' . $host . $path);

// Timezone
date_default_timezone_set('Asia/Jayapura');

// 2. KONEKSI DATABASE UTAMA (PDO) - AMAN
if (file_exists(__DIR__ . '/config_preview.php')) {
    require_once __DIR__ . '/config_preview.php';
} else {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        die("Maaf, terjadi gangguan koneksi ke database.");
    }
}

// Fungsi pembantu agar admin panel lama tetap jalan jika memanggil getDBConnection()
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        global $pdo;
        return $pdo;
    }
}
?>