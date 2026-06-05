<?php 
// 1. Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Sertakan file konfigurasi dan auth
// Ini PENTING agar fungsi getDBConnection() tersedia
require_once 'includes/config.php'; 
require_once 'includes/auth.php';

// 3. Cek Login
if (!isAdminLoggedIn()) {
    redirect('login.php');
    exit();
}

// 4. Ambil koneksi database PDO
$pdo = getDBConnection();

// 5. Siapkan dan jalankan query untuk mengambil semua data pengumuman
try {
    $stmt = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Tidak bisa mengambil data pengumuman: " . $e->getMessage());
}

// 6. Sertakan header admin (berisi sidebar dan navbar bagian atas)
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-gray-50">
    
    <!-- Header Halaman (Judul & Tombol Tambah) -->
    <header class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center">
            <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Kelola Pengumuman</h1>
        </div>
        <a href="tambah_pengumuman.php"
            class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm shadow-sm flex items-center">
            <i class="fas fa-plus fa-fw mr-2"></i>Tambah Baru
        </a>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        
        <!-- Flash Message (Notifikasi Sukses/Gagal) -->
        <?php if(isset($_GET['status'])): ?>
            <div class="mb-6 p-4 rounded-lg shadow-sm <?php echo ($_GET['status'] == 'sukses_hapus' || $_GET['status'] == 'sukses_tambah' || $_GET['status'] == 'sukses_edit') ? 'bg-green-50 text-green-700 border-l-4 border-green-500' : 'bg-red-50 text-red-700 border-l-4 border-red-500'; ?>" role="alert">
                <div class="flex items-center">
                    <div class="py-1"><i class="<?php echo ($_GET['status'] == 'gagal') ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?> mr-3 text-lg"></i></div>
                    <div>
                        <p class="font-bold text-sm">Notifikasi Sistem</p>
                        <p class="text-sm">
                            <?php 
                                if($_GET['status'] == 'sukses_tambah') echo 'Pengumuman berhasil ditambahkan.';
                                elseif($_GET['status'] == 'sukses_edit') echo 'Pengumuman berhasil diperbarui.';
                                elseif($_GET['status'] == 'sukses_hapus') echo 'Pengumuman berhasil dihapus.';
                                else echo 'Terjadi kesalahan saat memproses data.';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabel Data -->
        <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-4 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Judul Pengumuman
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Lampiran
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tanggal Dibuat
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($announcements) > 0): ?>
                            <?php foreach ($announcements as $row): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-5 py-4 bg-white text-sm">
                                    <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($row['judul']); ?></p>
                                    <p class="text-gray-400 text-xs mt-1 truncate w-64"><?php echo htmlspecialchars(substr(strip_tags($row['isi_pengumuman']), 0, 50)) . '...'; ?></p>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm">
                                    <?php if (!empty($row['file_lampiran'])): ?>
                                        <a href="uploads/<?php echo htmlspecialchars($row['file_lampiran']); ?>" target="_blank" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                            <i class="fas fa-paperclip mr-1.5"></i> Lihat File
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs italic">Tidak ada file</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">
                                        <i class="far fa-calendar-alt mr-1 text-gray-400"></i>
                                        <?php echo date('d M Y', strtotime($row['tanggal_dibuat'])); ?>
                                    </p>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold leading-tight <?php echo ($row['status'] == 'aktif') ? 'text-green-900' : 'text-red-900'; ?>">
                                        <span aria-hidden="true" class="absolute inset-0 <?php echo ($row['status'] == 'aktif') ? 'bg-green-100' : 'bg-red-100'; ?> rounded-full opacity-50"></span>
                                        <span class="relative text-xs uppercase tracking-wide"><?php echo htmlspecialchars($row['status']); ?></span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm">
                                    <div class="flex items-center space-x-3">
                                        <a href="edit_pengumuman.php?id=<?php echo $row['id']; ?>" class="text-yellow-500 hover:text-yellow-600 transition transform hover:scale-110" title="Edit">
                                            <i class="fas fa-pen-square fa-lg"></i>
                                        </a>
                                        <a href="proses_pengumuman.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus pengumuman ini? Data yang dihapus tidak dapat dikembalikan.')" class="text-red-500 hover:text-red-600 transition transform hover:scale-110" title="Hapus">
                                            <i class="fas fa-trash-alt fa-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-5 py-10 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                        <p>Belum ada data pengumuman.</p>
                                        <a href="tambah_pengumuman.php" class="mt-2 text-blue-500 hover:underline text-xs">Tambah Pengumuman Baru</a>
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
// Meng-include footer
require_once 'footer_admin.php'; 
?>