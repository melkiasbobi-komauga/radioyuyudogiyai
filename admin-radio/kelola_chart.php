<?php 
// 1. Pastikan session dimulai
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

// 4. Ambil koneksi database PDO
$pdo = getDBConnection();

// --- LOGIKA PAGINATION & PENCARIAN ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10; // Jumlah data per halaman
$offset = ($page - 1) * $limit;

try {
    // Hitung total data
    $count_sql = "SELECT COUNT(*) FROM chart_lagu WHERE judul_lagu LIKE :search1 OR artis LIKE :search2";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt_count->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt_count->execute();
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Ambil data dengan limit dan offset
    $sql = "SELECT * FROM chart_lagu WHERE judul_lagu LIKE :search1 OR artis LIKE :search2 ORDER BY peringkat ASC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Tidak bisa mengambil data chart: " . $e->getMessage());
}

// 6. Sertakan header admin
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-slate-50 h-screen overflow-hidden">
    
    <!-- Header Halaman -->
    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 p-4 flex flex-col md:flex-row justify-between items-center sticky top-0 z-20 space-y-4 md:space-y-0">
        <div class="flex items-center w-full md:w-auto">
            <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight leading-tight">Kelola Chart Lagu</h1>
                <p class="text-xs text-gray-500 hidden sm:block">Daftar Tangga Lagu Terpopuler</p>
            </div>
        </div>
        
        <!-- Search Bar & Tombol Tambah -->
        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3 w-full md:w-auto">
            <form action="" method="GET" class="relative w-full md:w-64 group">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                    class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition shadow-sm" 
                    placeholder="Cari lagu atau artis...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 group-focus-within:text-blue-500 transition"></i>
                </div>
            </form>
            
            <a href="tambah_lagu.php"
                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-full transition duration-200 text-sm shadow-md hover:shadow-lg flex items-center justify-center transform hover:-translate-y-0.5">
                <i class="fas fa-plus fa-fw mr-2"></i>Tambah Lagu
            </a>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 scroll-smooth">
        
        <!-- Notifikasi -->
        <?php 
        if(isset($_GET['status'])) {
            $status = $_GET['status'];
            $msg = '';
            $type = 'error';

            if ($status == 'sukses_tambah') { $msg = 'Lagu baru berhasil ditambahkan ke chart!'; $type = 'success'; }
            elseif ($status == 'sukses_edit') { $msg = 'Data lagu berhasil diperbarui!'; $type = 'success'; }
            elseif ($status == 'sukses_hapus') { $msg = 'Lagu berhasil dihapus dari chart!'; $type = 'success'; }
            elseif ($status == 'gagal_validasi') { $msg = 'Gagal: Data tidak lengkap.'; }
            elseif ($status == 'gagal_upload') { $msg = 'Gagal: File Cover atau Audio tidak valid.'; }
            elseif ($status == 'gagal_id') { $msg = 'Gagal: ID Lagu tidak ditemukan.'; }
            elseif ($status == 'gagal_sistem') { $msg = 'Gagal: Terjadi kesalahan sistem database.'; }
            else { $msg = 'Info: Proses selesai dengan status: ' . htmlspecialchars($status); }

            $bgClass = ($type == 'success') ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700';
            $iconClass = ($type == 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle';
            ?>
            
            <div class="mb-6 p-4 rounded-xl shadow-sm border-l-4 <?php echo $bgClass; ?> flex items-center animate-fade-in-down" role="alert">
                <i class="fas <?php echo $iconClass; ?> mr-3 text-xl"></i>
                <div>
                    <p class="font-bold text-sm">Notifikasi Sistem</p>
                    <p class="text-sm"><?php echo $msg; ?></p>
                </div>
            </div>
        <?php } ?>

        <!-- Tabel Data Modern -->
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-16">Rank</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Cover</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Lagu & Audio</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Artis</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (count($songs) > 0): ?>
                            <?php foreach ($songs as $row): 
                                $rankClass = "bg-gray-100 text-gray-600 border-gray-200";
                                if($row['peringkat'] == 1) $rankClass = "bg-yellow-100 text-yellow-700 border-yellow-200 shadow-sm";
                                elseif($row['peringkat'] == 2) $rankClass = "bg-gray-200 text-gray-700 border-gray-300 shadow-sm";
                                elseif($row['peringkat'] == 3) $rankClass = "bg-orange-100 text-orange-700 border-orange-200 shadow-sm";
                            ?>
                            <tr class="hover:bg-blue-50/50 transition duration-150">
                                <td class="px-6 py-4 text-center align-middle">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center font-bold mx-auto border <?php echo $rankClass; ?>">
                                        <?php echo htmlspecialchars($row['peringkat']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <div class="h-16 w-16 rounded-xl overflow-hidden shadow-sm border border-gray-200 relative group">
                                        <img src="uploads/<?php echo htmlspecialchars($row['cover_album']); ?>" alt="Cover"
                                            class="h-full w-full object-cover transition transform group-hover:scale-110" 
                                            onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <p class="text-gray-900 font-bold text-base mb-1"><?php echo htmlspecialchars($row['judul_lagu']); ?></p>
                                    
                                    <?php if (isset($row['file_audio']) && !empty($row['file_audio'])): ?>
                                        <div class="mt-2">
                                            <audio controls class="h-8 w-60 rounded-full shadow-sm bg-gray-100" style="outline: none;">
                                                <source src="uploads/<?php echo htmlspecialchars($row['file_audio']); ?>" type="audio/mpeg">
                                                Browser Anda tidak mendukung elemen audio.
                                            </audio>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 italic bg-gray-50 px-2 py-1 rounded border border-gray-100"><i class="fas fa-volume-mute mr-1"></i> Audio tidak tersedia</span>
                                    <?php endif; ?>
                                    
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex items-center text-sm font-medium text-gray-700">
                                        <i class="fas fa-microphone-alt text-gray-400 mr-2"></i>
                                        <?php echo htmlspecialchars($row['artis']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center align-middle">
                                    <div class="flex justify-center space-x-2">
                                        <a href="edit_lagu.php?id=<?php echo $row['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 hover:text-yellow-700 transition shadow-sm border border-yellow-100" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="proses_chart.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus lagu ini? Data tidak dapat dikembalikan.')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 hover:text-red-700 transition shadow-sm border border-red-100" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-100 p-4 rounded-full mb-3 shadow-inner">
                                            <i class="fas fa-music text-4xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-600">Belum ada lagu di chart.</p>
                                        <a href="tambah_lagu.php" class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Lagu Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Modern -->
            <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col xs:flex-row items-center justify-between">
                <span class="text-xs text-gray-500 font-medium mb-2 xs:mb-0">
                    Menampilkan Halaman <span class="font-bold text-gray-800"><?php echo $page; ?></span> dari <?php echo $total_pages; ?>
                </span>
                <div class="inline-flex shadow-sm rounded-md">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-l-lg border border-gray-200 transition">Prev</a>
                    <?php endif; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-r-lg border-t border-b border-r border-gray-200 transition">Next</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once 'footer_admin.php'; ?>