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
$limit = 10; // Jumlah pesan per halaman
$offset = ($page - 1) * $limit;

try {
    // Hitung total data
    // PERBAIKAN: Menggunakan parameter unik :search1, :search2, :search3
    $count_sql = "SELECT COUNT(*) FROM pesan_kontak WHERE nama_pengirim LIKE :search1 OR subjek LIKE :search2 OR email_pengirim LIKE :search3";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt_count->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt_count->bindValue(':search3', "%$search%", PDO::PARAM_STR);
    $stmt_count->execute();
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Ambil data pesan dengan limit dan offset
    // PERBAIKAN: Menggunakan parameter unik
    $sql = "SELECT * FROM pesan_kontak WHERE nama_pengirim LIKE :search1 OR subjek LIKE :search2 OR email_pengirim LIKE :search3 ORDER BY tanggal_kirim DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search3', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Tidak bisa mengambil data pesan: " . $e->getMessage());
}

// 6. Sertakan header admin
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-gray-50">
    
    <!-- Header Halaman -->
    <header class="bg-white shadow-sm p-4 flex flex-col md:flex-row justify-between items-center sticky top-0 z-10 space-y-4 md:space-y-0">
        <div class="flex items-center w-full md:w-auto">
            <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Pesan Masuk</h1>
        </div>
        
        <!-- Search Bar -->
        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3 w-full md:w-auto">
            <form action="" method="GET" class="relative w-full md:w-64">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition shadow-sm" 
                    placeholder="Cari pengirim/subjek...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </form>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        
        <!-- Notifikasi Sukses/Gagal -->
        <?php if(isset($_GET['status'])): ?>
            <div class="mb-6 p-4 rounded-lg shadow-sm flex items-start <?php echo ($_GET['status'] == 'sukses_hapus') ? 'bg-green-50 text-green-700 border-l-4 border-green-500' : 'bg-red-50 text-red-700 border-l-4 border-red-500'; ?>" role="alert">
                <i class="<?php echo ($_GET['status'] == 'gagal') ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?> mr-3 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold text-sm">Notifikasi Sistem</p>
                    <p class="text-sm">
                        <?php 
                            if($_GET['status'] == 'sukses_hapus') echo 'Pesan berhasil dihapus.';
                            else echo 'Terjadi kesalahan saat memproses data.';
                        ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabel Data -->
        <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">
                                Pengirim
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Subjek
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">
                                Tanggal
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                                Status
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($messages) > 0): ?>
                            <?php foreach ($messages as $row): ?>
                            <tr class="hover:bg-gray-50 transition duration-150 <?php echo ($row['status'] == 'belum dibaca') ? 'bg-blue-50' : ''; ?>">
                                <td class="px-5 py-4">
                                    <p class="text-gray-900 font-bold text-sm"><?php echo htmlspecialchars($row['nama_pengirim']); ?></p>
                                    <a href="mailto:<?php echo htmlspecialchars($row['email_pengirim']); ?>" class="text-blue-500 hover:underline text-xs mt-1 block">
                                        <?php echo htmlspecialchars($row['email_pengirim']); ?>
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-gray-800 font-medium text-sm"><?php echo htmlspecialchars($row['subjek']); ?></p>
                                    <p class="text-gray-500 text-xs mt-1 truncate max-w-xs">
                                        <?php echo htmlspecialchars(substr($row['isi_pesan'], 0, 50)) . '...'; ?>
                                    </p>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500">
                                    <?php echo date('d M Y', strtotime($row['tanggal_kirim'])); ?>
                                    <span class="text-xs block text-gray-400"><?php echo date('H:i', strtotime($row['tanggal_kirim'])); ?></span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if ($row['status'] == 'belum dibaca'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Baru
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Dibaca
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center text-sm">
                                    <div class="flex justify-center space-x-2">
                                        <a href="lihat_pesan.php?id=<?php echo $row['id']; ?>" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition shadow-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="proses_pesan.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-5 py-10 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <div class="bg-gray-100 p-4 rounded-full mb-3">
                                            <i class="fas fa-inbox text-4xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-600">Kotak masuk kosong.</p>
                                        <?php if(!empty($search)): ?>
                                            <p class="text-gray-400 text-xs mt-1">Tidak ditemukan pesan dengan kata kunci "<?php echo htmlspecialchars($search); ?>".</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="px-5 py-4 bg-white border-t border-gray-200 flex flex-col xs:flex-row items-center xs:justify-between">
                <span class="text-xs text-gray-900 mb-2 xs:mb-0">
                    Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?>
                </span>
                <div class="inline-flex mt-2 xs:mt-0">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-l transition">
                            <i class="fas fa-chevron-left mr-1"></i> Prev
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-r transition border-l border-gray-300">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>