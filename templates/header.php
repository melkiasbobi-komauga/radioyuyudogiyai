<?php
// Hubungkan konfigurasi
$rootConfig = __DIR__ . '/../config.php';
if (file_exists($rootConfig)) {
    require_once $rootConfig;
} else {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Radio-yuyu-website/config.php';
}

// Helper: Slugify
if (!function_exists('create_slug')) {
    function create_slug($text) { return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text))); }
}

// Helper: Fetch All Data (Versi PDO Aman)
if (!function_exists('fetchAll')) {
    function fetchAll($pdo, $table, $options = []) {
        $orderBy = $options['orderBy'] ?? 'id DESC';
        $where = $options['where'] ?? ''; 
        $limit = isset($options['limit']) ? " LIMIT " . (int)$options['limit'] : ''; 
        
        try {
            $sql = "SELECT * FROM $table $where ORDER BY $orderBy $limit";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}

// === PENGAMBILAN DATA GLOBAL ===
$header_profil = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM profil_website");
        while ($row = $stmt->fetch()) {
            $header_profil[$row['nama_pengaturan']] = $row['isi_pengaturan'];
        }
    } catch (Exception $e) { /* Ignore error in header */ }
}

$stationName = $header_profil['station_name'] ?? (defined('STATION_NAME') ? STATION_NAME : "RADIO YUYU KOMINFO");
$organization = $header_profil['organization_name'] ?? (defined('ORGANIZATION') ? ORGANIZATION : "93.6 FM - SUARA DOGIYAI");
$logoFile = $header_profil['logo_file'] ?? "logo.png";

// Berita Ticker (Running Text)
$berita_ticker = fetchAll($pdo, 'berita', ['orderBy' => 'tanggal_publikasi DESC', 'limit' => 5]);
$dynamicTitle = isset($pageTitle) ? $pageTitle . " | " . $stationName : $stationName . " | " . $organization;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($organization); ?>">
    <title><?php echo htmlspecialchars($dynamicTitle); ?></title>
    <link rel="icon" type="image/png" href="images/<?php echo htmlspecialchars($logoFile); ?>">
    
    <!-- Preconnect untuk performa -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Teko:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo time(); ?>">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
</head>
<body>

    <header class="main-header">
        <!-- Top Bar -->
        <div class="top-bar d-none d-md-block">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center w-75 overflow-hidden">
                    <strong class="text-warning me-3 small font-monospace">TERKINI:</strong>
                    <marquee behavior="scroll" direction="left" scrollamount="6" class="flex-grow-1 text-white small" onmouseover="this.stop();" onmouseout="this.start();">
                        <?php
                            if (!empty($berita_ticker)) {
                                foreach ($berita_ticker as $news) {
                                    $slug = create_slug($news['judul']);
                                    echo '<a href="baca_berita.php?id=' . $news['id'] . '&judul=' . $slug . '" class="marquee-link">' . htmlspecialchars($news['judul']) . '</a> • ';
                                }
                            } else {
                                echo "Selamat Datang di Website Resmi " . htmlspecialchars($stationName);
                            }
                        ?>
                    </marquee>
                </div>
                <div class="text-end text-white small opacity-75">
                    <i class="far fa-calendar-alt"></i> <span id="current-date"></span>
                    <span class="mx-2">|</span>
                    <i class="far fa-clock"></i> <span id="current-time"></span>
                </div>
            </div>
        </div>

        <!-- Navbar -->
        <div class="container header-navbar">
            <a href="./" class="brand-logo">
                <img src="images/<?php echo htmlspecialchars($logoFile); ?>" alt="Logo" width="60" height="60" loading="eager">
                <div class="brand-text">
                    <h1 style="font-size: 1.4rem;" class="m-0"><?php echo htmlspecialchars($stationName); ?></h1>
                    <p style="font-size: 0.7rem;" class="m-0"><?php echo htmlspecialchars($organization); ?></p>
                </div>
            </a>
            
            <nav class="d-none d-lg-block">
                <ul class="nav-menu" id="nav-menu-desktop">
                    <?php $cp = basename($_SERVER['PHP_SELF']); ?>
                    <li><a href="./" class="nav-link-custom <?php echo ($cp == 'index.php') ? 'active' : ''; ?>">Beranda</a></li>
                    <li><a href="berita.php" class="nav-link-custom <?php echo ($cp == 'berita.php') ? 'active' : ''; ?>">Berita</a></li>
                    <li><a href="agenda.php" class="nav-link-custom <?php echo ($cp == 'agenda.php') ? 'active' : ''; ?>">Agenda</a></li>
                    <li><a href="pengumuman.php" class="nav-link-custom <?php echo ($cp == 'pengumuman.php') ? 'active' : ''; ?>">Pengumuman</a></li>
                    <li><a href="jadwal.php" class="nav-link-custom <?php echo ($cp == 'jadwal.php') ? 'active' : ''; ?>">Jadwal</a></li>
                    <li><a href="galeri.php" class="nav-link-custom <?php echo ($cp == 'galeri.php') ? 'active' : ''; ?>">Galeri</a></li>
                    <li><a href="forum.php" class="nav-link-custom <?php echo ($cp == 'forum.php') ? 'active' : ''; ?>">Forum</a></li>
                    <li><a href="tentang.php" class="nav-link-custom <?php echo ($cp == 'tentang.php') ? 'active' : ''; ?>">Tentang</a></li>
                    <li><a href="kontak.php" class="nav-link-custom <?php echo ($cp == 'kontak.php') ? 'active' : ''; ?>">Kontak</a></li>
                </ul>
            </nav>

            <div class="d-flex align-items-center gap-2">
                <button class="mobile-toggle d-lg-none" id="hamburger-btn"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <nav class="d-lg-none">
            <ul class="nav-menu" id="nav-menu">
                <?php $cp = basename($_SERVER['PHP_SELF']); ?>
                <li><a href="./" class="nav-link-custom <?php echo ($cp == 'index.php') ? 'active' : ''; ?>">Beranda</a></li>
                <li><a href="berita.php" class="nav-link-custom <?php echo ($cp == 'berita.php') ? 'active' : ''; ?>">Berita</a></li>
                <li><a href="agenda.php" class="nav-link-custom <?php echo ($cp == 'agenda.php') ? 'active' : ''; ?>">Agenda</a></li>
                <li><a href="pengumuman.php" class="nav-link-custom <?php echo ($cp == 'pengumuman.php') ? 'active' : ''; ?>">Pengumuman</a></li>
                <li><a href="jadwal.php" class="nav-link-custom <?php echo ($cp == 'jadwal.php') ? 'active' : ''; ?>">Jadwal</a></li>
                <li><a href="galeri.php" class="nav-link-custom <?php echo ($cp == 'galeri.php') ? 'active' : ''; ?>">Galeri</a></li>
                <li><a href="forum.php" class="nav-link-custom <?php echo ($cp == 'forum.php') ? 'active' : ''; ?>">Forum</a></li>
                <li><a href="tentang.php" class="nav-link-custom <?php echo ($cp == 'tentang.php') ? 'active' : ''; ?>">Tentang</a></li>
                <li><a href="kontak.php" class="nav-link-custom <?php echo ($cp == 'kontak.php') ? 'active' : ''; ?>">Kontak</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Script inline yang konflik telah dihapus. Navigasi ditangani oleh assets/js/script.js -->