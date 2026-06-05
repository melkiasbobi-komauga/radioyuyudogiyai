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

// Variabel untuk menyimpan data podcast dan pesan error
$podcast = null;
$error_message = '';

// 5. Periksa apakah ID ada di URL dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // 6. Ambil data podcast dari database menggunakan PDO & prepared statement
    try {
        $stmt = $pdo->prepare("SELECT * FROM podcast WHERE id = ?");
        $stmt->execute([$id]);
        $podcast = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika tidak ada podcast dengan ID tersebut
        if (!$podcast) {
            $error_message = "Podcast tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error mengambil data podcast: " . $e->getMessage();
    }
} else {
    $error_message = "ID Podcast tidak valid.";
}

// 7. Sertakan header admin
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-gray-50">
    
    <!-- Header Halaman -->
    <header class="bg-white shadow-sm p-4 flex items-center sticky top-0 z-10">
        <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Edit Podcast</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_podcast.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Podcast
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
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'gagal_validasi_edit'): ?>
                <!-- PERBAIKAN: Notifikasi jika file WAJIB tidak diupload saat EDIT -->
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Gagal Menyimpan!</p>
                            <p>File Audio dan Cover Image tidak boleh kosong. Harap upload file baru atau pastikan file lama masih ada.</p>
                        </div>
                    </div>
                </div>
            <?php elseif ($podcast): ?>
            <!-- Form Edit -->
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Formulir Edit Podcast</h2>
                        <p class="text-xs text-gray-500 mt-1">Perbarui detail podcast, file audio, atau cover.</p>
                    </div>
                    <div class="text-xs text-gray-400">
                        ID: #<?php echo $podcast['id']; ?>
                    </div>
                </div>
                
                <form action="proses_podcast.php?aksi=edit" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <input type="hidden" name="id" value="<?php echo $podcast['id']; ?>">
                    <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($podcast['file_gambar']); ?>">
                    <input type="hidden" name="audio_lama" value="<?php echo htmlspecialchars($podcast['file_audio']); ?>">

                    <!-- Judul Podcast -->
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul Podcast <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" 
                            value="<?php echo htmlspecialchars($podcast['judul']); ?>"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                            required>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed"><?php echo htmlspecialchars($podcast['deskripsi']); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cover Podcast -->
                        <div class="bg-blue-50 p-5 rounded-xl border border-blue-100 h-full">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Cover Podcast</label>
                            
                            <div class="flex flex-col gap-4">
                                <!-- Preview Gambar Lama -->
                                <div class="flex-shrink-0">
                                    <p class="text-xs text-gray-500 mb-2">Cover Saat Ini:</p>
                                    <div class="relative group w-32">
                                        <img src="uploads/<?php echo htmlspecialchars($podcast['file_gambar']); ?>" 
                                             alt="Cover Podcast" 
                                             class="h-32 w-32 object-cover rounded-lg shadow-md border border-gray-200"
                                             onerror="this.src='https://placehold.co/150x150?text=No+Image'">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 truncate"><?php echo htmlspecialchars($podcast['file_gambar']); ?></p>
                                </div>

                                <!-- Input Upload Baru -->
                                <div class="w-full">
                                    <label for="file_gambar" class="block text-xs text-gray-500 mb-2">Ganti Cover (Opsional):</label>
                                    <input type="file" name="file_gambar" id="file_gambar" accept="image/*"
                                        class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2.5 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-600 file:text-white
                                        hover:file:bg-blue-700
                                        cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                                    "/>
                                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Maks: 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Audio -->
                        <div class="bg-purple-50 p-5 rounded-xl border border-purple-100 h-full">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">File Audio</label>
                            
                            <div class="flex flex-col gap-4">
                                <!-- Preview Audio Lama -->
                                <div class="flex-shrink-0">
                                    <p class="text-xs text-gray-500 mb-2">Audio Saat Ini:</p>
                                    <audio controls class="w-full h-10">
                                        <source src="uploads/<?php echo htmlspecialchars($podcast['file_audio']); ?>" type="audio/mpeg">
                                        Browser Anda tidak mendukung elemen audio.
                                    </audio>
                                    <p class="text-xs text-gray-400 mt-1 truncate"><?php echo htmlspecialchars($podcast['file_audio']); ?></p>
                                </div>

                                <!-- Input Upload Baru -->
                                <div class="w-full mt-auto">
                                    <label for="file_audio" class="block text-xs text-gray-500 mb-2">Ganti Audio (Opsional):</label>
                                    <input type="file" name="file_audio" id="file_audio" accept="audio/*"
                                        class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2.5 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-purple-600 file:text-white
                                        hover:file:bg-purple-700
                                        cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                                    "/>
                                    <p class="text-xs text-gray-500 mt-2">Format: MP3, WAV. Maks: 50MB.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                        <a href="kelola_podcast.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
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
