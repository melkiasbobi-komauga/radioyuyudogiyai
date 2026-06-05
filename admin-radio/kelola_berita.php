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
$limit = 10; // Jumlah berita per halaman
$offset = ($page - 1) * $limit;

try {
    // Hitung total data
    // PERBAIKAN: Menggunakan tanda tanya (?) untuk parameter
    // Kita butuh 2 parameter untuk pencarian (judul & penulis)
    $count_sql = "SELECT COUNT(*) FROM berita WHERE judul LIKE ? OR penulis LIKE ?";
    $stmt_count = $pdo->prepare($count_sql);
    $param_search = "%$search%";
    $stmt_count->execute([$param_search, $param_search]); // Kirim array dengan 2 nilai
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Ambil data berita dengan limit dan offset
    // PERBAIKAN: Menggunakan tanda tanya (?) untuk konsistensi
    // Urutan parameter: judul, penulis, limit, offset
    // Catatan: LIMIT dan OFFSET di PDO dengan tanda tanya kadang butuh bindValue eksplisit untuk integer
    $sql = "SELECT * FROM berita WHERE judul LIKE :search_judul OR penulis LIKE :search_penulis ORDER BY tanggal_publikasi DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    
    // Binding parameter dengan nama unik agar aman
    $stmt->bindValue(':search_judul', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search_penulis', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $beritas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Tidak bisa mengambil data berita: " . $e->getMessage());
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
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Kelola Berita</h1>
        </div>
        
        <!-- Search Bar & Tombol Tambah -->
        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3 w-full md:w-auto">
            <form action="" method="GET" class="relative w-full md:w-64">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition shadow-sm" 
                    placeholder="Cari judul/penulis...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </form>
            
            <a href="tambah_berita.php"
                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm shadow-sm flex items-center justify-center">
                <i class="fas fa-plus fa-fw mr-2"></i>Tulis Berita
            </a>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        
        <!-- Notifikasi Sukses/Gagal -->
        <?php if(isset($_GET['status'])): ?>
            <div class="mb-6 p-4 rounded-lg shadow-sm flex items-start <?php echo ($_GET['status'] == 'sukses_hapus' || $_GET['status'] == 'sukses_tambah' || $_GET['status'] == 'sukses_edit') ? 'bg-green-50 text-green-700 border-l-4 border-green-500' : 'bg-red-50 text-red-700 border-l-4 border-red-500'; ?>" role="alert">
                <i class="<?php echo ($_GET['status'] == 'gagal') ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?> mr-3 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold text-sm">Notifikasi Sistem</p>
                    <p class="text-sm">
                        <?php 
                            if($_GET['status'] == 'sukses_tambah') echo 'Berita baru berhasil ditambahkan.';
                            elseif($_GET['status'] == 'sukses_edit') echo 'Berita berhasil diperbarui.';
                            elseif($_GET['status'] == 'sukses_hapus') echo 'Berita berhasil dihapus.';
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
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                                Gambar
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Judul & Cuplikan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">
                                Info
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($beritas) > 0): ?>
                            <?php foreach ($beritas as $row): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-5 py-4 bg-white">
                                    <div class="h-16 w-24 rounded overflow-hidden shadow-sm border border-gray-200 relative">
                                        <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="Thumbnail"
                                            class="h-full w-full object-cover" 
                                            onerror="this.src='https://placehold.co/100x70?text=No+Img'">
                                    </div>
                                </td>
                                <td class="px-5 py-4 bg-white">
                                    <p class="text-gray-900 font-medium text-base mb-1"><?php echo htmlspecialchars($row['judul']); ?></p>
                                    <p class="text-gray-500 text-xs line-clamp-2">
                                        <?php echo htmlspecialchars(substr(strip_tags($row['konten']), 0, 100)); ?>...
                                    </p>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm text-gray-600">
                                    <div class="flex flex-col space-y-1">
                                        <span class="flex items-center text-xs">
                                            <i class="fas fa-user w-4 text-blue-400"></i> <?php echo htmlspecialchars($row['penulis']); ?>
                                        </span>
                                        <span class="flex items-center text-xs">
                                            <i class="far fa-calendar-alt w-4 text-orange-400"></i> <?php echo date('d/m/Y', strtotime($row['tanggal_publikasi'])); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="edit_berita.php?id=<?php echo $row['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition shadow-sm" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="proses_berita.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus berita ini?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-5 py-10 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <div class="bg-gray-100 p-4 rounded-full mb-3">
                                            <i class="fas fa-newspaper text-4xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-600">Belum ada berita.</p>
                                        <?php if(!empty($search)): ?>
                                            <p class="text-gray-400 text-xs mt-1">Tidak ditemukan berita dengan kata kunci "<?php echo htmlspecialchars($search); ?>".</p>
                                        <?php else: ?>
                                            <a href="tambah_berita.php" class="mt-2 text-blue-500 hover:underline text-xs">Tulis Berita Baru</a>
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