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

$berita = null;
$error_message = '';

// 5. Ambil data berita berdasarkan ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        $berita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$berita) {
            $error_message = "Berita tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error mengambil data berita: " . $e->getMessage();
    }
} else {
    $error_message = "ID Berita tidak valid.";
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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Edit Berita</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_berita.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita
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

            <?php if ($berita): ?>
                <!-- Form Edit -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Formulir Edit Berita</h2>
                            <p class="text-xs text-gray-500 mt-1">Perbarui informasi berita di bawah ini.</p>
                        </div>
                        <div class="text-xs text-gray-400">
                            ID: #<?php echo $berita['id']; ?>
                        </div>
                    </div>
                    
                    <form action="proses_berita.php?aksi=edit" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <!-- Field CSRF -->
                        <?php csrfField(); ?>
                        
                        <input type="hidden" name="id" value="<?php echo $berita['id']; ?>">
                        <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($berita['gambar']); ?>">

                        <!-- Judul -->
                        <div>
                            <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul Berita <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" id="judul" 
                                value="<?php echo htmlspecialchars($berita['judul']); ?>"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Masukkan judul berita yang menarik..." required>
                        </div>

                        <!-- Penulis -->
                        <div>
                            <label for="penulis" class="block text-sm font-semibold text-gray-700 mb-2">Penulis <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="penulis" id="penulis" 
                                    value="<?php echo htmlspecialchars($berita['penulis']); ?>"
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                    placeholder="Nama penulis" required>
                            </div>
                        </div>

                        <!-- Konten (CKEDITOR) -->
                        <div>
                            <label for="konten" class="block text-sm font-semibold text-gray-700 mb-2">Konten Berita <span class="text-red-500">*</span></label>
                            <!-- ID textarea harus 'konten' agar dihubungkan oleh CKEditor -->
                            <textarea name="konten" id="konten" rows="12" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed" 
                                placeholder="Tulis isi berita lengkap di sini..." required><?php echo htmlspecialchars($berita['konten']); ?></textarea>
                            <p class="text-xs text-gray-500 mt-2 text-right">Gunakan editor untuk memformat teks.</p>
                        </div>

                        <!-- Gambar -->
                        <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Gambar Utama</label>
                            
                            <div class="flex flex-col md:flex-row gap-6 items-start">
                                <!-- Preview Gambar Lama -->
                                <div class="flex-shrink-0">
                                    <p class="text-xs text-gray-500 mb-2">Gambar Saat Ini:</p>
                                    <div class="relative group">
                                        <img src="uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" 
                                             alt="Gambar Berita" 
                                             class="h-32 w-48 object-cover rounded-lg shadow-md border border-gray-200"
                                             onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                                        <a href="uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" target="_blank" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 flex items-center justify-center transition rounded-lg">
                                            <i class="fas fa-external-link-alt text-white opacity-0 group-hover:opacity-100"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Input Upload Baru -->
                                <div class="flex-grow w-full">
                                    <label for="gambar" class="block text-xs text-gray-500 mb-2">Ganti Gambar (Opsional):</label>
                                    <input type="file" name="gambar" id="gambar" accept="image/*"
                                        class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2.5 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-600 file:text-white
                                        hover:file:bg-blue-700
                                        cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                                    "/>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i> Format: JPG, JPEG, PNG. Maksimal 2MB.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                            <a href="kelola_berita.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
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

<!-- Load CKEditor dari CDN -->
<script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
<script>
    // Inisialisasi CKEditor pada textarea dengan id 'konten'
    CKEDITOR.replace('konten');
</script>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>
