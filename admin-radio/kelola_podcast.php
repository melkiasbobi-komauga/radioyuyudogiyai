<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php'; 
require_once 'includes/auth.php';
if (!isAdminLoggedIn()) { redirect('login.php'); exit(); }
$pdo = getDBConnection();

// LOGIKA PAGINATION & PENCARIAN
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // PERBAIKAN: Gunakan parameter unik :search1 dan :search2
    $count_sql = "SELECT COUNT(*) FROM podcast WHERE judul LIKE :search1 OR deskripsi LIKE :search2";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt_count->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt_count->execute();
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // PERBAIKAN: Gunakan parameter unik :search1 dan :search2 juga di sini
    $sql = "SELECT * FROM podcast WHERE judul LIKE :search1 OR deskripsi LIKE :search2 ORDER BY tanggal_upload DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
require_once 'header_admin.php'; 
?>

<div class="flex-1 flex flex-col bg-gray-50">
    <header class="bg-white shadow-sm p-4 flex flex-col md:flex-row justify-between items-center sticky top-0 z-10 space-y-4 md:space-y-0">
        <div class="flex items-center w-full md:w-auto">
            <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4"><i class="fas fa-bars fa-lg"></i></button>
            <h1 class="text-xl font-bold text-gray-800">Kelola Podcast</h1>
        </div>
        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3 w-full md:w-auto">
            <form action="" method="GET" class="relative w-full md:w-64">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 text-sm transition shadow-sm" placeholder="Cari podcast...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-search text-gray-400"></i></div>
            </form>
            <a href="tambah_podcast.php" class="w-full md:w-auto bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition text-sm shadow-sm flex items-center justify-center"><i class="fas fa-plus fa-fw mr-2"></i>Tambah Podcast</a>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <?php if(isset($_GET['status'])): ?>
            <div class="mb-6 p-4 rounded-lg shadow-sm flex items-start <?php echo (strpos($_GET['status'], 'sukses') !== false) ? 'bg-green-50 text-green-700 border-green-500' : 'bg-red-50 text-red-700 border-red-500'; ?> border-l-4">
                <i class="<?php echo (strpos($_GET['status'], 'sukses') !== false) ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?> mr-3 text-lg mt-0.5"></i>
                <p class="text-sm font-bold"><?php echo (strpos($_GET['status'], 'sukses') !== false) ? 'Berhasil!' : 'Gagal!'; ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Cover</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Info Podcast</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">Tanggal Upload</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($podcasts) > 0): ?>
                            <?php foreach ($podcasts as $row): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-4">
                                    <div class="h-16 w-16 rounded-lg overflow-hidden shadow-sm border relative">
                                        <img src="uploads/<?php echo htmlspecialchars($row['file_gambar']); ?>" class="h-full w-full object-cover" onerror="this.src='https://placehold.co/100x100?text=Cover'">
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($row['judul']); ?></p>
                                    <div class="mt-2"><audio controls class="h-8 w-48"><source src="uploads/<?php echo htmlspecialchars($row['file_audio']); ?>" type="audio/mpeg"></audio></div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600"><i class="far fa-calendar-alt mr-2 text-purple-400"></i> <?php echo date('d M Y', strtotime($row['tanggal_upload'])); ?></td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="edit_podcast.php?id=<?php echo $row['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition shadow-sm"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="proses_podcast.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus podcast ini?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm"><i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500"><div class="bg-gray-100 p-4 rounded-full inline-block mb-3"><i class="fas fa-microphone-slash text-4xl text-gray-300"></i></div><p>Tidak ada data podcast.</p></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
             <?php if ($total_pages > 1): ?>
            <div class="px-5 py-4 bg-white border-t border-gray-200 flex justify-between items-center">
                <span class="text-xs text-gray-600">Hal <?php echo $page; ?> dari <?php echo $total_pages; ?></span>
                <div class="inline-flex">
                    <?php if ($page > 1): ?><a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-gray-200 px-3 py-1 rounded-l hover:bg-gray-300">Prev</a><?php endif; ?>
                    <?php if ($page < $total_pages): ?><a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-gray-200 px-3 py-1 rounded-r hover:bg-gray-300 ml-1">Next</a><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once 'footer_admin.php'; ?>