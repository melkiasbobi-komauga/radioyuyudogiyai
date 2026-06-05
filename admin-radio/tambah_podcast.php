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

// 4. Include Header Admin
require_once 'header_admin.php'; 
?>

<!-- Konten utama halaman -->
<div class="flex-1 flex flex-col bg-gray-50">
    
    <!-- Header Halaman -->
    <header class="bg-white shadow-sm p-4 flex items-center sticky top-0 z-10">
        <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Tambah Podcast Baru</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_podcast.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Podcast
                </a>
            </div>
            
            <!-- Tampilkan Pesan Error dari URL jika ada -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_upload'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Gagal Upload!</p>
                            <p>Terjadi kesalahan saat mengupload file media. Pastikan format dan ukuran file sesuai.</p>
                        </div>
                    </div>
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'gagal_validasi'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Data Tidak Lengkap!</p>
                            <p>Mohon lengkapi judul podcast.</p>
                        </div>
                    </div>
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'gagal_validasi_file'): ?>
                <!-- PERBAIKAN: Notifikasi jika file WAJIB tidak diupload -->
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">File Wajib Hilang!</p>
                            <p>Anda harus mengupload File Audio dan Cover Image untuk podcast baru.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Tambah -->
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Formulir Podcast</h2>
                    <p class="text-xs text-gray-500 mt-1">Upload episode podcast atau rekaman baru.</p>
                </div>
                
                <form action="proses_podcast.php?aksi=tambah" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul Podcast <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                            placeholder="Masukkan judul podcast..." required>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed" 
                            placeholder="Jelaskan isi podcast ini..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Input Gambar Cover -->
                        <div class="bg-blue-50 p-5 rounded-xl border border-blue-100 transition-all duration-300">
                            <label for="file_gambar" class="block text-sm font-semibold text-gray-700 mb-2">Cover Image <span class="text-red-500">*</span></label>
                            <input type="file" name="file_gambar" id="file_gambar" accept="image/*"
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700
                                cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                            " required>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG. Maks: 2MB.
                            </p>
                        </div>

                        <!-- Input File Audio -->
                        <div class="bg-purple-50 p-5 rounded-xl border border-purple-100 transition-all duration-300">
                            <label for="file_audio" class="block text-sm font-semibold text-gray-700 mb-2">File Audio <span class="text-red-500">*</span></label>
                            <input type="file" name="file_audio" id="file_audio" accept="audio/*"
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-purple-600 file:text-white
                                hover:file:bg-purple-700
                                cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                            " required>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-music mr-1"></i> Format: MP3, WAV. Maks: 50MB.
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="kelola_podcast.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition duration-200 text-sm flex items-center transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Podcast
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>
