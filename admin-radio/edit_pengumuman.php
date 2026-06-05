<?php 
// 1. Mulai session & Include Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php'; 
require_once 'includes/auth.php';

// 2. Cek Login
if (!isAdminLoggedIn()) {
    redirect('login.php');
    exit();
}

// 3. Ambil Koneksi
$pdo = getDBConnection();

// 4. Inisialisasi variabel
$pengumuman = null;
$error_message = '';

// 5. Ambil Data Pengumuman berdasarkan ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM pengumuman WHERE id = ?");
        $stmt->execute([$id]);
        $pengumuman = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pengumuman) {
            $error_message = "Pengumuman tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error mengambil data: " . $e->getMessage();
    }
} else {
    $error_message = "ID Pengumuman tidak valid.";
}

// 6. Include Header
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-gray-50">
    
    <!-- Header Halaman -->
    <header class="bg-white shadow-sm p-4 flex items-center sticky top-0 z-10">
        <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Edit Pengumuman</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            
            <!-- Menampilkan Error jika ada -->
            <?php if ($error_message): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                    <a href="kelola_pengumuman.php" class="mt-2 inline-block text-red-600 hover:text-red-800 underline text-sm">Kembali ke Daftar</a>
                </div>
            
            <?php elseif ($pengumuman): ?>
                <!-- Form Edit -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-700">Formulir Perubahan Data</h2>
                        <p class="text-xs text-gray-500 mt-1">Silakan perbarui informasi pengumuman di bawah ini.</p>
                    </div>
                    
                    <form action="proses_pengumuman.php?aksi=edit" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <!-- Field CSRF -->
                        <?php csrfField(); ?>
                        
                        <input type="hidden" name="id" value="<?php echo $pengumuman['id']; ?>">
                        <input type="hidden" name="file_lama" value="<?php echo htmlspecialchars($pengumuman['file_lampiran']); ?>">

                        <!-- Judul -->
                        <div>
                            <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" id="judul" 
                                value="<?php echo htmlspecialchars($pengumuman['judul']); ?>"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border" 
                                required>
                        </div>

                        <!-- Isi Pengumuman -->
                        <div>
                            <label for="isi_pengumuman" class="block text-sm font-medium text-gray-700 mb-1">Isi Pengumuman <span class="text-red-500">*</span></label>
                            <textarea name="isi_pengumuman" id="isi_pengumuman" rows="6" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border" 
                                required><?php echo htmlspecialchars($pengumuman['isi_pengumuman']); ?></textarea>
                        </div>

                        <!-- Grid Dua Kolom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tanggal Berakhir -->
                            <div>
                                <label for="tanggal_berakhir" class="block text-sm font-medium text-gray-700 mb-1">Berlaku Hingga (Opsional)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="far fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <input type="date" name="tanggal_berakhir" id="tanggal_berakhir" 
                                        value="<?php echo $pengumuman['tanggal_berakhir']; ?>"
                                        class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika berlaku selamanya.</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Publikasi</label>
                                <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border bg-white">
                                    <option value="aktif" <?php if($pengumuman['status'] == 'aktif') echo 'selected'; ?>>Aktif (Tampil)</option>
                                    <option value="tidak aktif" <?php if($pengumuman['status'] == 'tidak aktif') echo 'selected'; ?>>Tidak Aktif (Sembunyi)</option>
                                </select>
                            </div>
                        </div>

                        <!-- File Lampiran -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <label class="block text-sm font-medium text-gray-700 mb-2">File Lampiran</label>
                            
                            <!-- Tampilan File Saat Ini -->
                            <?php if (!empty($pengumuman['file_lampiran'])): ?>
                                <div class="flex items-center mb-3 bg-white p-2 rounded border border-blue-200 inline-flex">
                                    <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600 mr-3"><?php echo htmlspecialchars($pengumuman['file_lampiran']); ?></span>
                                    <a href="uploads/<?php echo htmlspecialchars($pengumuman['file_lampiran']); ?>" target="_blank" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition">Lihat</a>
                                </div>
                                <p class="text-xs text-gray-500 mb-2">Upload file baru di bawah jika ingin mengganti.</p>
                            <?php else: ?>
                                <p class="text-xs text-gray-500 mb-2 italic">Belum ada file lampiran.</p>
                            <?php endif; ?>

                            <!-- Input File -->
                            <input type="file" name="file_lampiran" id="file_lampiran" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                            <p class="text-xs text-gray-400 mt-1">Format: PDF, DOC, DOCX, JPG, PNG. Maks: 5MB.</p>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <a href="kelola_pengumuman.php" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-md transition duration-200 text-sm flex items-center">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>
