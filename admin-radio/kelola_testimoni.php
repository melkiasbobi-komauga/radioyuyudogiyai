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
    // PERBAIKAN: Menggunakan parameter unik :search1 dan :search2 untuk menghitung total
    $count_sql = "SELECT COUNT(*) FROM testimoni WHERE nama_pengirim LIKE :search1 OR pesan LIKE :search2";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt_count->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt_count->execute();
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // PERBAIKAN: Menggunakan parameter unik :search1 dan :search2 untuk ambil data
    $sql = "SELECT * FROM testimoni WHERE nama_pengirim LIKE :search1 OR pesan LIKE :search2 ORDER BY tanggal_kirim DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Error: " . $e->getMessage()); 
}

require_once 'header_admin.php'; 
?>

<div class="flex-1 flex flex-col bg-gray-50">
    <header class="bg-white shadow-sm p-4 flex flex-col md:flex-row justify-between items-center sticky top-0 z-10 space-y-4 md:space-y-0">
        <div class="flex items-center w-full md:w-auto">
            <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4"><i class="fas fa-bars fa-lg"></i></button>
            <h1 class="text-xl font-bold text-gray-800">Kelola Testimoni</h1>
        </div>
        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3 w-full md:w-auto">
            <form action="" method="GET" class="relative w-full md:w-64">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 text-sm transition shadow-sm" placeholder="Cari pengirim...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-search text-gray-400"></i></div>
            </form>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <?php if(isset($_GET['status'])): ?>
            <div class="mb-6 p-4 rounded-lg shadow-sm <?php echo ($_GET['status'] == 'sukses_hapus' || $_GET['status'] == 'sukses_update') ? 'bg-green-50 text-green-700 border-l-4 border-green-500' : 'bg-red-50 text-red-700 border-l-4 border-red-500'; ?> flex items-center">
                <i class="<?php echo ($_GET['status'] == 'gagal_id') ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?> mr-3 text-lg mt-0.5"></i>
                <p class="text-sm font-bold">
                    <?php 
                        if ($_GET['status'] == 'sukses_hapus') echo 'Testimoni berhasil dihapus.';
                        elseif ($_GET['status'] == 'sukses_update') echo 'Status testimoni berhasil diperbarui.';
                        elseif ($_GET['status'] == 'gagal_id') echo 'Terjadi kesalahan data.';
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Pengirim</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pesan</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($testimonials) > 0): ?>
                            <?php foreach ($testimonials as $row): ?>
                            <tr class="hover:bg-gray-50 transition <?php echo ($row['status'] == 'sembunyi') ? 'bg-yellow-50' : ''; ?>">
                                <td class="px-5 py-4"><p class="text-gray-900 font-medium text-sm"><?php echo htmlspecialchars($row['nama_pengirim']); ?></p><p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($row['peran']); ?></p></td>
                                <td class="px-5 py-4"><p class="text-gray-600 text-sm italic">"<?php echo htmlspecialchars($row['pesan']); ?>"</p><p class="text-xs text-gray-400 mt-1"><?php echo date('d M Y', strtotime($row['tanggal_kirim'])); ?></p></td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo ($row['status'] == 'tampil') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <?php if ($row['status'] == 'sembunyi'): ?>
                                            <a href="proses_testimoni.php?aksi=tampil&id=<?php echo $row['id']; ?>" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition shadow-sm" title="Tampilkan di Web"><i class="fas fa-eye"></i></a>
                                        <?php else: ?>
                                            <a href="proses_testimoni.php?aksi=sembunyi&id=<?php echo $row['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition shadow-sm" title="Sembunyikan"><i class="fas fa-eye-slash"></i></a>
                                        <?php endif; ?>
                                        <a href="proses_testimoni.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus testimoni ini?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm" title="Hapus"><i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Belum ada testimoni yang masuk.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
             <?php if ($total_pages > 1): ?>
            <div class="px-5 py-4 bg-white border-t border-gray-200 flex flex-col xs:flex-row items-center xs:justify-between">
                <span class="text-xs text-gray-600 mb-2 xs:mb-0">Hal <?php echo $page; ?> dari <?php echo $total_pages; ?></span>
                <div class="inline-flex mt-2 xs:mt-0">
                    <?php if ($page > 1): ?><a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-gray-200 px-3 py-1 rounded-l hover:bg-gray-300">Prev</a><?php endif; ?>
                    <?php if ($page < $total_pages): ?><a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="text-sm bg-gray-200 px-3 py-1 rounded-r hover:bg-gray-300 ml-1">Next</a><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once 'footer_admin.php'; ?>