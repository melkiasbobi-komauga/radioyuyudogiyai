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

// Variabel untuk menyimpan data lagu dan pesan error
$lagu = null;
$error_message = '';

// 5. Periksa apakah ID ada di URL dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // 6. Ambil data lagu dari database menggunakan PDO & prepared statement
    try {
        $stmt = $pdo->prepare("SELECT * FROM chart_lagu WHERE id = ?");
        $stmt->execute([$id]);
        $lagu = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika tidak ada lagu dengan ID tersebut
        if (!$lagu) {
            $error_message = "Lagu tidak ditemukan di chart.";
        }
    } catch (PDOException $e) {
        $error_message = "Error mengambil data chart: " . $e->getMessage();
    }
} else {
    $error_message = "ID Lagu tidak valid.";
}

// 7. Sertakan header admin
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-slate-50 h-screen overflow-hidden">
    
    <!-- Header Halaman -->
    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 p-4 flex items-center sticky top-0 z-20">
        <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <div>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight leading-tight">Edit Lagu</h1>
            <p class="text-xs text-gray-500 hidden sm:block">Perbarui data lagu dalam chart.</p>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 scroll-smooth">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_chart.php" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition font-medium text-sm group">
                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center mr-2 shadow-sm group-hover:border-blue-200 group-hover:bg-blue-50">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </div>
                    Kembali ke Daftar Chart
                </a>
            </div>

            <!-- Tampilkan Error -->
            <?php if ($error_message): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                    <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                    <p class="font-medium"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'gagal_upload'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                    <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                    <p class="font-medium">Gagal upload file! Pastikan format dan ukuran sesuai.</p>
                </div>
            <?php endif; ?>

            <?php if ($lagu): ?>
            <!-- Form Edit Modern -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit text-blue-500 mr-2 bg-blue-100 p-2 rounded-lg"></i>
                        Edit Data Lagu
                    </h2>
                    <span class="text-xs font-mono text-gray-400 bg-gray-50 px-2 py-1 rounded border border-gray-200">ID: #<?php echo $lagu['id']; ?></span>
                </div>
                
                <form action="proses_chart.php?aksi=edit" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <input type="hidden" name="id" value="<?php echo $lagu['id']; ?>">
                    <input type="hidden" name="cover_lama" value="<?php echo htmlspecialchars($lagu['cover_album']); ?>">
                    <!-- Input hidden untuk audio lama (PENTING) -->
                    <input type="hidden" name="audio_lama" value="<?php echo htmlspecialchars($lagu['file_audio'] ?? ''); ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Peringkat -->
                        <div>
                            <label for="peringkat" class="block text-sm font-semibold text-gray-700 mb-2">Peringkat <span class="text-red-500">*</span></label>
                            <input type="number" name="peringkat" id="peringkat" 
                                value="<?php echo htmlspecialchars($lagu['peringkat']); ?>"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm bg-gray-50 focus:bg-white" 
                                required>
                        </div>

                        <!-- Judul Lagu -->
                        <div>
                            <label for="judul_lagu" class="block text-sm font-semibold text-gray-700 mb-2">Judul Lagu <span class="text-red-500">*</span></label>
                            <input type="text" name="judul_lagu" id="judul_lagu" 
                                value="<?php echo htmlspecialchars($lagu['judul_lagu']); ?>"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm bg-gray-50 focus:bg-white" 
                                required>
                        </div>
                    </div>

                    <!-- Artis -->
                    <div>
                        <label for="artis" class="block text-sm font-semibold text-gray-700 mb-2">Nama Artis <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-microphone text-gray-400 group-focus-within:text-blue-500 transition"></i>
                            </div>
                            <input type="text" name="artis" id="artis" 
                                value="<?php echo htmlspecialchars($lagu['artis']); ?>"
                                class="pl-10 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm bg-gray-50 focus:bg-white" 
                                required>
                        </div>
                    </div>

                    <!-- Grid Media (Cover & Audio) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        
                        <!-- Cover Album -->
                        <div class="bg-blue-50/50 p-5 rounded-xl border border-blue-100 hover:border-blue-300 transition-colors h-full flex flex-col">
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-image mr-2 text-blue-500"></i> Cover Album
                            </label>
                            
                            <div class="flex items-start gap-4 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="relative group w-24 h-24 rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-white">
                                        <img src="uploads/<?php echo htmlspecialchars($lagu['cover_album']); ?>" 
                                             alt="Cover" 
                                             class="w-full h-full object-cover"
                                             onerror="this.src='https://placehold.co/150x150?text=No+Img'">
                                    </div>
                                    <p class="text-xs text-center text-gray-500 mt-1">Saat Ini</p>
                                </div>
                                <div class="flex-grow">
                                    <input type="file" name="cover_album" id="cover_album" accept="image/*"
                                        class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-xs file:font-semibold
                                        file:bg-blue-100 file:text-blue-700
                                        hover:file:bg-blue-200
                                        cursor-pointer border border-gray-300 rounded-lg bg-white p-1 mb-1
                                    "/>
                                    <p class="text-xs text-gray-500">Ganti (Opsional). Max 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Audio -->
                        <div class="bg-purple-50/50 p-5 rounded-xl border border-purple-100 hover:border-purple-300 transition-colors h-full flex flex-col">
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-music mr-2 text-purple-500"></i> File Audio
                            </label>
                            
                            <div class="flex flex-col gap-3">
                                <?php if (!empty($lagu['file_audio'])): ?>
                                    <div class="bg-white p-2 rounded-lg border border-purple-100 shadow-sm">
                                        <audio controls class="w-full h-8" style="outline: none;">
                                            <source src="uploads/<?php echo htmlspecialchars($lagu['file_audio']); ?>" type="audio/mpeg">
                                        </audio>
                                        <p class="text-xs text-gray-400 mt-1 truncate px-1"><i class="fas fa-check-circle text-green-500 mr-1"></i> File ada: <?php echo htmlspecialchars($lagu['file_audio']); ?></p>
                                    </div>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 italic bg-white p-2 rounded border border-gray-200 text-center">Belum ada file audio.</p>
                                <?php endif; ?>

                                <div class="mt-auto">
                                    <input type="file" name="file_audio" id="file_audio" accept="audio/*"
                                        class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-xs file:font-semibold
                                        file:bg-purple-100 file:text-purple-700
                                        hover:file:bg-purple-200
                                        cursor-pointer border border-gray-300 rounded-lg bg-white p-1 mb-1
                                    "/>
                                    <p class="text-xs text-gray-500">Ganti (Opsional). Max 10MB.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="kelola_chart.php" class="px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-800 font-medium transition duration-200 text-sm shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition duration-200 text-sm flex items-center transform hover:-translate-y-0.5">
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