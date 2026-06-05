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

// 5. Siapkan dan jalankan query untuk mengambil semua data galeri
try {
    $stmt = $pdo->query("SELECT * FROM galeri ORDER BY tanggal_upload DESC");
    $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Tidak bisa mengambil data galeri: " . $e->getMessage());
}

// 6. Sertakan header admin
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-gray-50">
    
    <!-- Header Halaman -->
    <header class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center">
            <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Kelola Galeri</h1>
        </div>
        <a href="tambah_galeri.php"
            class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm shadow-sm flex items-center">
            <i class="fas fa-plus fa-fw mr-2"></i>Tambah Item
        </a>
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
                            if($_GET['status'] == 'sukses_tambah') echo 'Item galeri berhasil ditambahkan.';
                            elseif($_GET['status'] == 'sukses_edit') echo 'Item galeri berhasil diperbarui.';
                            elseif($_GET['status'] == 'sukses_hapus') echo 'Item galeri berhasil dihapus.';
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
                                Media
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Judul & Keterangan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                                Tipe
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">
                                Tanggal
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($gallery_items) > 0): ?>
                            <?php foreach ($gallery_items as $row): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-5 py-4 bg-white">
                                    <?php if($row['tipe_media'] == 'foto'): ?>
                                        <div class="h-16 w-24 rounded overflow-hidden shadow-sm border border-gray-200 relative group">
                                            <img src="uploads/<?php echo htmlspecialchars($row['file_media']); ?>" alt="Foto Galeri"
                                                class="h-full w-full object-cover transition transform group-hover:scale-110" 
                                                onerror="this.src='https://placehold.co/100x70?text=No+Image'">
                                        </div>
                                    <?php else: 
                                        // Simple heuristic for YouTube thumbnail
                                        $thumbnail = "https://placehold.co/100x70/1a237e/FFFFFF?text=VIDEO";
                                        if (preg_match('/(v=|\/v\/|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11,11})/i', $row['file_media'], $matches)) {
                                            $video_id = $matches[2];
                                            $thumbnail = "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg";
                                        }
                                    ?>
                                        <div class="h-16 w-24 bg-gray-100 rounded overflow-hidden relative shadow-sm border border-gray-200 group">
                                            <img src="<?php echo $thumbnail; ?>" class="h-full w-full object-cover opacity-80 group-hover:opacity-100 transition" alt="Video Thumbnail">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <i class="fas fa-play-circle text-white text-xl drop-shadow-md group-hover:scale-110 transition"></i>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 bg-white">
                                    <p class="text-gray-900 font-medium text-sm"><?php echo htmlspecialchars($row['judul']); ?></p>
                                    <p class="text-gray-400 text-xs mt-1 truncate max-w-xs"><?php echo htmlspecialchars($row['keterangan']); ?></p>
                                </td>
                                <td class="px-5 py-4 bg-white">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo ($row['tipe_media'] == 'foto') ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'; ?>">
                                        <i class="<?php echo ($row['tipe_media'] == 'foto') ? 'fas fa-camera' : 'fas fa-video'; ?> mr-1.5 mt-0.5"></i>
                                        <?php echo ucfirst($row['tipe_media']); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm text-gray-500">
                                    <?php echo date('d M Y', strtotime($row['tanggal_upload'])); ?>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="edit_galeri.php?id=<?php echo $row['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition shadow-sm" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="proses_galeri.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm" title="Hapus">
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
                                            <i class="fas fa-photo-video text-4xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-600">Belum ada item di galeri.</p>
                                        <a href="tambah_galeri.php" class="mt-2 text-blue-500 hover:underline text-xs">Upload Foto/Video Baru</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>