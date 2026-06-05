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
<div class="flex-1 flex flex-col bg-slate-50 h-screen overflow-hidden">
    
    <!-- Header Halaman -->
    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 p-4 flex items-center sticky top-0 z-20">
        <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4 transition duration-200">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <div>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight leading-tight">Tambah Lagu</h1>
            <p class="text-xs text-gray-500 hidden sm:block">Tambahkan entri baru ke dalam chart musik.</p>
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
            
            <!-- Notifikasi Error -->
            <?php if (isset($_GET['status'])): ?>
                <div class="mb-6 p-4 rounded-xl shadow-sm border-l-4 bg-red-50 border-red-500 text-red-700 flex items-center animate-fade-in-down" role="alert">
                    <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                    <div>
                        <p class="font-bold text-sm">Gagal!</p>
                        <p class="text-sm">
                            <?php 
                                if($_GET['status'] == 'gagal_upload') echo 'File cover atau audio tidak valid/terlalu besar.';
                                elseif($_GET['status'] == 'gagal_validasi') echo 'Data tidak lengkap. Mohon isi semua field.';
                                else echo 'Terjadi kesalahan sistem.';
                            ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Tambah Modern -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-music text-blue-500 mr-2 bg-blue-100 p-2 rounded-lg"></i>
                        Formulir Chart Lagu
                    </h2>
                </div>
                
                <form action="proses_chart.php?aksi=tambah" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Peringkat -->
                        <div>
                            <label for="peringkat" class="block text-sm font-semibold text-gray-700 mb-2">Peringkat <span class="text-red-500">*</span></label>
                            <input type="number" name="peringkat" id="peringkat" 
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm bg-gray-50 focus:bg-white" 
                                placeholder="Contoh: 1" required min="1">
                        </div>

                        <!-- Judul Lagu -->
                        <div>
                            <label for="judul_lagu" class="block text-sm font-semibold text-gray-700 mb-2">Judul Lagu <span class="text-red-500">*</span></label>
                            <input type="text" name="judul_lagu" id="judul_lagu" 
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm bg-gray-50 focus:bg-white" 
                                placeholder="Judul lagu..." required>
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
                                class="pl-10 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm bg-gray-50 focus:bg-white" 
                                placeholder="Nama artis atau band" required>
                        </div>
                    </div>

                    <!-- Grid Upload File (Cover & Audio) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <!-- Cover Album -->
                        <div class="bg-blue-50/50 p-5 rounded-xl border border-blue-100 hover:border-blue-300 transition-colors group">
                            <label for="cover_album" class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-image mr-2 text-blue-500"></i> Cover Album
                            </label>
                            <input type="file" name="cover_album" id="cover_album" accept="image/*"
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-100 file:text-blue-700
                                hover:file:bg-blue-200
                                cursor-pointer border border-gray-300 rounded-lg bg-white p-1 shadow-sm
                            ">
                            <p class="text-xs text-gray-500 mt-2">Max 2MB (JPG, PNG)</p>
                        </div>

                        <!-- File Audio (BARU: Ditambahkan Kembali) -->
                        <div class="bg-purple-50/50 p-5 rounded-xl border border-purple-100 hover:border-purple-300 transition-colors group">
                            <label for="file_audio" class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-music mr-2 text-purple-500"></i> File Audio (Opsional)
                            </label>
                            <input type="file" name="file_audio" id="file_audio" accept="audio/*"
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-purple-100 file:text-purple-700
                                hover:file:bg-purple-200
                                cursor-pointer border border-gray-300 rounded-lg bg-white p-1 shadow-sm
                            ">
                            <p class="text-xs text-gray-500 mt-2">Max 10MB (MP3, WAV)</p>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="kelola_chart.php" class="px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-800 font-medium transition duration-200 text-sm shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition duration-200 text-sm flex items-center transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Lagu
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