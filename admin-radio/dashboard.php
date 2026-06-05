<?php
// Memulai session dan output buffering
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';
require_once 'includes/auth.php';

// Cek Login
if (!isAdminLoggedIn()) {
    redirect('login.php');
    exit();
}

$admin = getCurrentAdmin();
$pdo = getDBConnection();

// Inisialisasi nilai default statistik
$stats = [
    'total_pengumuman' => 0,
    'total_berita' => 0,
    'total_agenda' => 0,
    'total_pesan' => 0,
    'total_galeri' => 0,
    'total_podcast' => 0,
    'total_program' => 0
];

// Ambil data statistik
try {
    $checkTable = $pdo->query("SHOW TABLES LIKE 'program_unggulan'");
    $hasProgramTable = $checkTable->rowCount() > 0;

    $query_stats = "SELECT
        (SELECT COUNT(*) FROM pengumuman WHERE status = 'aktif') AS total_pengumuman,
        (SELECT COUNT(*) FROM berita) AS total_berita,
        (SELECT COUNT(*) FROM agenda) AS total_agenda,
        (SELECT COUNT(*) FROM pesan_kontak WHERE status = 'belum dibaca') AS total_pesan,
        (SELECT COUNT(*) FROM galeri) AS total_galeri,
        (SELECT COUNT(*) FROM podcast) AS total_podcast
    ";
    
    if ($hasProgramTable) {
        $query_stats = "SELECT
            (SELECT COUNT(*) FROM pengumuman WHERE status = 'aktif') AS total_pengumuman,
            (SELECT COUNT(*) FROM berita) AS total_berita,
            (SELECT COUNT(*) FROM agenda) AS total_agenda,
            (SELECT COUNT(*) FROM pesan_kontak WHERE status = 'belum dibaca') AS total_pesan,
            (SELECT COUNT(*) FROM galeri) AS total_galeri,
            (SELECT COUNT(*) FROM podcast) AS total_podcast,
            (SELECT COUNT(*) FROM program_unggulan) AS total_program
        ";
    }

    $stmt = $pdo->query($query_stats);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $stats = array_merge($stats, $result);
    }

} catch (PDOException $e) {
    error_log("Error Statistik Dashboard: " . $e->getMessage());
    $dashboard_error = "Gagal memuat sebagian data statistik.";
}

require_once 'header_admin.php';
?>

<!-- Custom CSS untuk Scrollbar & Efek Dashboard -->
<style>
    /* Kustomisasi Scrollbar */
    main::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    main::-webkit-scrollbar-track {
        background: transparent;
    }
    main::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
        border: 2px solid #f8fafc; /* Mencocokkan warna background */
    }
    main::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    /* Animasi Hover Card */
    .dashboard-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .dashboard-card:hover .icon-bg {
        transform: scale(1.1) rotate(5deg);
    }
    
    /* Animasi Blob Background */
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
</style>

<div class="flex-1 flex flex-col bg-slate-50 h-screen overflow-hidden">

    <!-- Header Navbar -->
    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 p-4 flex justify-between items-center sticky top-0 z-20">
        <div class="flex items-center">
            <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight leading-tight">Dashboard Overview</h1>
                <p class="text-xs text-gray-500 hidden sm:block">Panel Kontrol Utama Radio</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <div class="hidden md:flex flex-col items-end mr-2">
                <span class="text-sm font-semibold text-gray-700"><?php echo htmlspecialchars($admin['nama_lengkap'] ?? 'Admin'); ?></span>
                <span class="text-xs text-gray-400">Administrator</span>
            </div>
            <a href="logout.php" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white font-medium p-2 rounded-lg transition duration-200 shadow-sm" title="Logout">
                <i class="fas fa-sign-out-alt fa-lg"></i>
            </a>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 scroll-smooth">
        
        <?php if (isset($dashboard_error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm flex items-center" role="alert">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                <div>
                    <p class="font-bold">Peringatan Sistem</p>
                    <p class="text-sm"><?php echo $dashboard_error; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Welcome Banner Modern -->
        <div class="relative w-full bg-blue-900 rounded-3xl overflow-hidden shadow-xl mb-8 group">
            <!-- Decorative Background Blobs -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-blue-500 mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-32 -left-20 w-80 h-80 rounded-full bg-purple-600 mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-2">Halo, Admin! 👋</h2>
                    <p class="text-blue-100 text-lg opacity-90">Selamat datang kembali di panel kontrol Radio Yuyu Kominfo.</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="tambah_berita.php" class="bg-white/10 hover:bg-white/20 text-white border border-white/30 px-4 py-2 rounded-full text-sm font-medium transition backdrop-blur-sm">
                            <i class="fas fa-pen mr-2"></i> Tulis Berita
                        </a>
                        <a href="kelola_jadwal.php" class="bg-white/10 hover:bg-white/20 text-white border border-white/30 px-4 py-2 rounded-full text-sm font-medium transition backdrop-blur-sm">
                            <i class="fas fa-calendar-alt mr-2"></i> Atur Jadwal
                        </a>
                    </div>
                </div>
                <div class="hidden md:block transform transition-transform duration-500 group-hover:scale-105 group-hover:rotate-3">
                    <i class="fas fa-broadcast-tower text-9xl text-white/10"></i>
                </div>
            </div>
        </div>

        <!-- Section Title -->
        <h3 class="text-gray-700 font-bold text-lg mb-4 flex items-center">
            <i class="fas fa-chart-pie mr-2 text-blue-600"></i> Statistik Utama
        </h3>

        <!-- Grid Statistik Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- Kartu Pesan -->
            <div class="dashboard-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1 bg-red-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pesan Masuk</p>
                        <h3 class="text-3xl font-extrabold text-gray-800"><?php echo number_format($stats['total_pesan']); ?></h3>
                    </div>
                    <div class="icon-bg p-3 bg-red-50 text-red-500 rounded-xl transition-transform duration-300">
                        <i class="fas fa-envelope fa-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center">
                    <span class="text-xs text-red-500 bg-red-50 px-2 py-1 rounded-md font-medium">Perlu Respon</span>
                    <a href="kelola_pesan.php" class="text-xs text-gray-400 hover:text-red-600 transition flex items-center group">
                        Lihat <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Kartu Berita -->
            <div class="dashboard-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Berita</p>
                        <h3 class="text-3xl font-extrabold text-gray-800"><?php echo number_format($stats['total_berita']); ?></h3>
                    </div>
                    <div class="icon-bg p-3 bg-blue-50 text-blue-500 rounded-xl transition-transform duration-300">
                        <i class="fas fa-newspaper fa-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center">
                    <span class="text-xs text-blue-500 bg-blue-50 px-2 py-1 rounded-md font-medium">Publikasi</span>
                    <a href="kelola_berita.php" class="text-xs text-gray-400 hover:text-blue-600 transition flex items-center group">
                        Kelola <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Kartu Pengumuman -->
            <div class="dashboard-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1 bg-yellow-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pengumuman Aktif</p>
                        <h3 class="text-3xl font-extrabold text-gray-800"><?php echo number_format($stats['total_pengumuman']); ?></h3>
                    </div>
                    <div class="icon-bg p-3 bg-yellow-50 text-yellow-600 rounded-xl transition-transform duration-300">
                        <i class="fas fa-bullhorn fa-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center">
                    <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-1 rounded-md font-medium">Informasi</span>
                    <a href="kelola_pengumuman.php" class="text-xs text-gray-400 hover:text-yellow-600 transition flex items-center group">
                        Cek <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Kartu Agenda -->
            <div class="dashboard-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1 bg-purple-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Agenda</p>
                        <h3 class="text-3xl font-extrabold text-gray-800"><?php echo number_format($stats['total_agenda']); ?></h3>
                    </div>
                    <div class="icon-bg p-3 bg-purple-50 text-purple-500 rounded-xl transition-transform duration-300">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center">
                    <span class="text-xs text-purple-500 bg-purple-50 px-2 py-1 rounded-md font-medium">Kegiatan</span>
                    <a href="kelola_agenda.php" class="text-xs text-gray-400 hover:text-purple-600 transition flex items-center group">
                        Lihat <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- Section Title -->
        <h3 class="text-gray-700 font-bold text-lg mb-4 flex items-center">
            <i class="fas fa-layer-group mr-2 text-indigo-600"></i> Konten Lainnya
        </h3>

        <!-- Grid Statistik Sekunder -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Program Unggulan -->
             <div class="dashboard-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between group">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-50 text-orange-500 rounded-xl mr-4 group-hover:bg-orange-100 transition">
                        <i class="fas fa-star fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wide">Program Unggulan</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo number_format($stats['total_program']); ?></p>
                    </div>
                </div>
                <a href="kelola_program.php" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-orange-500 group-hover:text-white transition">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Galeri Stats -->
            <div class="dashboard-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between group">
                <div class="flex items-center">
                    <div class="p-3 bg-pink-50 text-pink-500 rounded-xl mr-4 group-hover:bg-pink-100 transition">
                        <i class="fas fa-images fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wide">Total Galeri</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo number_format($stats['total_galeri']); ?></p>
                    </div>
                </div>
                <a href="kelola_galeri.php" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-pink-500 group-hover:text-white transition">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Podcast Stats -->
            <div class="dashboard-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between group">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-50 text-indigo-500 rounded-xl mr-4 group-hover:bg-indigo-100 transition">
                        <i class="fas fa-podcast fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wide">Total Podcast</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo number_format($stats['total_podcast']); ?></p>
                    </div>
                </div>
                <a href="kelola_podcast.php" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-indigo-500 group-hover:text-white transition">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>

    </main>
</div>

<?php 
include 'footer_admin.php'; 
ob_end_flush();
?>