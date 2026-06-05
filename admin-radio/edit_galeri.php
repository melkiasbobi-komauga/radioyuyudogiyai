<?php 
// 1. Mulai session
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

// 4. Ambil koneksi PDO
$pdo = getDBConnection();

$item = null;
$error_message = '';

// 5. Ambil data galeri berdasarkan ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM galeri WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $error_message = "Item galeri tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error mengambil data: " . $e->getMessage();
    }
} else {
    $error_message = "ID Item Galeri tidak valid.";
}

// 6. Sertakan header admin
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-gray-50">
    
    <!-- Header Halaman -->
    <header class="bg-white shadow-sm p-4 flex items-center sticky top-0 z-10">
        <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Edit Item Galeri</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_galeri.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Galeri
                </a>
            </div>

            <!-- Tampilkan Error jika ada -->
            <?php if ($error_message): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                        <p class="font-medium"><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($item): ?>
                <!-- Form Edit -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-800">Formulir Edit Galeri</h2>
                        <p class="text-xs text-gray-500 mt-1">Perbarui informasi foto atau video.</p>
                    </div>
                    
                    <form action="proses_galeri.php?aksi=edit" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <!-- Field CSRF -->
                        <?php csrfField(); ?>
                        
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="file_media_lama" value="<?php echo htmlspecialchars($item['file_media']); ?>">
                        <input type="hidden" name="tipe_media" value="<?php echo $item['tipe_media']; ?>">

                        <!-- Tipe Media (Read-only untuk konsistensi edit) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Media</label>
                            <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 text-sm font-medium border border-gray-200">
                                <i class="<?php echo ($item['tipe_media'] == 'foto') ? 'fas fa-camera' : 'fas fa-video'; ?> mr-2"></i>
                                <?php echo ucfirst($item['tipe_media']); ?>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 ml-1">Tipe media tidak dapat diubah saat mengedit.</p>
                        </div>

                        <!-- Judul -->
                        <div>
                            <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" id="judul" 
                                value="<?php echo htmlspecialchars($item['judul']); ?>"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                required>
                        </div>

                        <!-- Media Preview & Input -->
                        <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">File Media</label>
                            
                            <div class="flex flex-col gap-4">
                                <!-- Preview Media Lama -->
                                <div class="flex-shrink-0 mb-2">
                                    <p class="text-xs text-gray-500 mb-2">Media Saat Ini:</p>
                                    <?php if ($item['tipe_media'] == 'foto'): ?>
                                        <div class="relative group w-48">
                                            <img src="uploads/<?php echo htmlspecialchars($item['file_media']); ?>" 
                                                 alt="Foto Galeri" 
                                                 class="h-32 w-full object-cover rounded-lg shadow-md border border-gray-200"
                                                 onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                                            <a href="uploads/<?php echo htmlspecialchars($item['file_media']); ?>" target="_blank" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 flex items-center justify-center transition rounded-lg">
                                                <i class="fas fa-external-link-alt text-white opacity-0 group-hover:opacity-100"></i>
                                            </a>
                                        </div>
                                    <?php else: // Video ?>
                                        <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                                            <i class="fab fa-youtube text-red-600 text-2xl mr-3"></i>
                                            <a href="<?php echo htmlspecialchars($item['file_media']); ?>" target="_blank" class="text-sm text-blue-600 hover:underline truncate max-w-xs">
                                                <?php echo htmlspecialchars($item['file_media']); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Input Edit -->
                                <?php if ($item['tipe_media'] == 'foto'): ?>
                                    <div class="w-full">
                                        <label for="file_media_foto" class="block text-xs text-gray-500 mb-2">Ganti Foto (Opsional):</label>
                                        <input type="file" name="file_media_foto" id="file_media_foto" accept="image/*"
                                            class="block w-full text-sm text-slate-500
                                            file:mr-4 file:py-2.5 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-blue-600 file:text-white
                                            hover:file:bg-blue-700
                                            cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                                        "/>
                                        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG. Maks: 5MB.</p>
                                    </div>
                                <?php else: // Video ?>
                                    <div class="w-full">
                                        <label for="file_media_video" class="block text-xs text-gray-500 mb-2">Update Link Video (YouTube):</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-link text-gray-400"></i>
                                            </div>
                                            <input type="text" name="file_media_video" id="file_media_video" 
                                                value="<?php echo htmlspecialchars($item['file_media']); ?>"
                                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                                placeholder="https://www.youtube.com/watch?v=...">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Deskripsi</label>
                            <textarea name="keterangan" id="keterangan" rows="4" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed"><?php echo htmlspecialchars($item['keterangan']); ?></textarea>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                            <a href="kelola_galeri.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition duration-200 text-sm flex items-center transform hover:-translate-y-0.5">
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
