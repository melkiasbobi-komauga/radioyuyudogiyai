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

// 5. Siapkan dan jalankan query untuk mengambil semua data tim
try {
    $stmt = $pdo->query("SELECT * FROM tim ORDER BY id ASC");
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Tidak bisa mengambil data tim: " . $e->getMessage());
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
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Kelola Tim</h1>
        </div>
        <a href="tambah_tim.php"
            class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm shadow-sm flex items-center">
            <i class="fas fa-plus fa-fw mr-2"></i>Tambah Anggota
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
                            if($_GET['status'] == 'sukses_tambah') echo 'Anggota tim berhasil ditambahkan.';
                            elseif($_GET['status'] == 'sukses_edit') echo 'Data anggota tim berhasil diperbarui.';
                            elseif($_GET['status'] == 'sukses_hapus') echo 'Anggota tim berhasil dihapus.';
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
                                Foto
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Lengkap
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">
                                Jabatan
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($team_members) > 0): ?>
                            <?php foreach ($team_members as $row): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-5 py-4 bg-white">
                                    <div class="h-12 w-12 rounded-full overflow-hidden shadow-sm border border-gray-200 relative">
                                        <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto Tim"
                                            class="h-full w-full object-cover" 
                                            onerror="this.src='https://placehold.co/100x100?text=User'">
                                    </div>
                                </td>
                                <td class="px-5 py-4 bg-white">
                                    <p class="text-gray-900 font-medium text-base"><?php echo htmlspecialchars($row['nama_lengkap']); ?></p>
                                    <?php if (!empty($row['bio_singkat'])): ?>
                                        <p class="text-gray-400 text-xs mt-1 truncate max-w-xs" title="<?php echo htmlspecialchars($row['bio_singkat']); ?>">
                                            <?php echo htmlspecialchars(substr($row['bio_singkat'], 0, 60)) . (strlen($row['bio_singkat']) > 60 ? '...' : ''); ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 bg-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($row['jabatan']); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 bg-white text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="edit_tim.php?id=<?php echo $row['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition shadow-sm transform hover:scale-105" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="proses_tim.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota tim ini?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm transform hover:scale-105" title="Hapus">
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
                                            <i class="fas fa-users-slash text-4xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-600">Belum ada anggota tim.</p>
                                        <a href="tambah_tim.php" class="mt-2 text-blue-500 hover:underline text-xs">Tambah Anggota Baru</a>
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