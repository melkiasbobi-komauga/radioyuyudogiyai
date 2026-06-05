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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Tambah Berita Baru</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_berita.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita
                </a>
            </div>
            
            <!-- Tampilkan Pesan Error dari URL jika ada -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_upload'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Gagal Upload!</p>
                            <p>Terjadi kesalahan saat mengupload gambar. Pastikan format (JPG/PNG) dan ukuran file (max 2MB) sesuai.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Tambah -->
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Formulir Berita</h2>
                    <p class="text-xs text-gray-500 mt-1">Tulis berita terbaru untuk dipublikasikan.</p>
                </div>
                
                <form action="proses_berita.php?aksi=tambah" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" 
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
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Nama penulis" required>
                        </div>
                    </div>

                    <!-- Konten -->
                    <div>
                        <label for="konten" class="block text-sm font-semibold text-gray-700 mb-2">Konten Berita <span class="text-red-500">*</span></label>
                        <textarea name="konten" id="editor" rows="12" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed" 
                            placeholder="Tulis isi berita lengkap di sini..."></textarea>
                        <p class="text-xs text-gray-500 mt-2 text-right">Gunakan editor untuk memformat teks.</p>
                    </div>

                    <!-- Gambar -->
                    <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Utama <span class="text-red-500">*</span></label>
                        <input type="file" name="gambar" id="gambar" accept="image/*" required
                            class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2.5 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-600 file:text-white
                            hover:file:bg-blue-700
                            cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                        "/>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i> Format yang diizinkan: JPG, JPEG, PNG, GIF. Ukuran maksimal: 2MB.
                        </p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="kelola_berita.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition duration-200 text-sm flex items-center transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Terbitkan Berita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- Load CKEditor dari CDN -->
<script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
<script>
    // Inisialisasi CKEditor pada textarea dengan id 'editor'
    CKEDITOR.replace('editor');
</script>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>
