<?php
define('BASEPATH', __DIR__);
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'radio_yuyu.db');

try {
    $pdo = new PDO('sqlite:radio_yuyu.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS profil_website (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama_pengaturan TEXT UNIQUE,
        isi_pengaturan TEXT
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS galeri (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        judul TEXT,
        file_media TEXT,
        tipe_media TEXT,
        keterangan TEXT,
        tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS berita (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        judul TEXT,
        isi TEXT,
        gambar TEXT,
        tanggal_publikasi DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert mock data
    $pdo->exec("INSERT OR IGNORE INTO profil_website (nama_pengaturan, isi_pengaturan) VALUES 
        ('station_name', 'RADIO YUYU DOGIYAI'),
        ('organization_name', '93.6 FM - SUARA DOGIYAI'),
        ('logo_file', 'logo.png'),
        ('app_download_url', 'https://example.com/radioyuyu.apk')");

    $pdo->exec("INSERT OR IGNORE INTO galeri (judul, file_media, tipe_media, keterangan) VALUES 
        ('Siaran Pagi Hari', 'foto1.jpg', 'foto', 'Kegiatan siaran rutin setiap pagi.'),
        ('Kunjungan Bupati', 'foto2.jpg', 'foto', 'Kunjungan Bapak Bupati ke studio.'),
        ('Video Profil Radio', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'video', 'Video profil resmi Radio Yuyu Dogiyai.')");

} catch (PDOException $e) {
    die("Preview DB Error: " . $e->getMessage());
}

if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        global $pdo;
        return $pdo;
    }
}
?>
