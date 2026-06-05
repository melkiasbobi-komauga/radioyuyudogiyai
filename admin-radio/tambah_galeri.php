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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Tambah Item Galeri</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_galeri.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Galeri
                </a>
            </div>
            
            <!-- Tampilkan Pesan Error dari URL jika ada -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_upload'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Gagal Upload!</p>
                            <p>Terjadi kesalahan saat mengupload file. Pastikan format dan ukuran file sesuai.</p>
                        </div>
                    </div>
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'gagal_validasi'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Data Tidak Lengkap!</p>
                            <p>Mohon lengkapi semua field yang wajib diisi.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Tambah -->
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Formulir Galeri</h2>
                    <p class="text-xs text-gray-500 mt-1">Tambahkan foto atau video kegiatan baru.</p>
                </div>
                
                <form action="proses_galeri.php?aksi=tambah" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                            placeholder="Contoh: Kunjungan Kerja Bupati" required>
                    </div>

                    <!-- Tipe Media -->
                    <div>
                        <label for="tipe_media" class="block text-sm font-semibold text-gray-700 mb-2">Tipe Media <span class="text-red-500">*</span></label>
                        <select name="tipe_media" id="tipe_media" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm bg-white"
                            required>
                            <option value="foto">Foto</option>
                            <option value="video">Video (YouTube)</option>
                        </select>
                    </div>

                    <!-- Input Foto (Tampil jika tipe foto) -->
                    <div id="input_foto" class="bg-blue-50 p-5 rounded-xl border border-blue-100 transition-all duration-300">
                        <label for="file_media_foto" class="block text-sm font-semibold text-gray-700 mb-2">Upload Foto <span class="text-red-500">*</span></label>
                        <input type="file" name="file_media_foto" id="file_media_foto" 
                            class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2.5 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-600 file:text-white
                            hover:file:bg-blue-700
                            cursor-pointer border border-gray-300 rounded-lg bg-white p-1
                        " required>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i> Format: JPG, JPEG, PNG. Maksimal 5MB.
                        </p>
                    </div>

                    <!-- Input Video (Tampil jika tipe video) -->
                    <div id="input_video" class="hidden bg-red-50 p-5 rounded-xl border border-red-100 transition-all duration-300">
                        <label for="file_media_video" class="block text-sm font-semibold text-gray-700 mb-2">URL Video (YouTube) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fab fa-youtube text-red-500"></i>
                            </div>
                            <input type="text" name="file_media_video" id="file_media_video" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Salin link video langsung dari YouTube.</p>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Deskripsi</label>
                        <textarea name="keterangan" id="keterangan" rows="4" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed" 
                            placeholder="Tambahkan keterangan singkat..."></textarea>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="kelola_galeri.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition duration-200 text-sm flex items-center transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
// Script untuk toggle input berdasarkan tipe media
document.addEventListener('DOMContentLoaded', function() {
    const tipeMediaSelect = document.getElementById('tipe_media');
    const inputFoto = document.getElementById('input_foto');
    const inputFileFoto = document.getElementById('file_media_foto');
    const inputVideo = document.getElementById('input_video');
    const inputFileVideo = document.getElementById('file_media_video');

    function toggleInputs() {
        if (tipeMediaSelect.value === 'video') {
            inputVideo.classList.remove('hidden');
            inputFoto.classList.add('hidden');
            
            // Update required attributes
            inputFileFoto.required = false;
            inputFileVideo.required = true;
            
            // Clear value to prevent submission of hidden input
            inputFileFoto.value = ''; 
        } else { // 'foto'
            inputVideo.classList.add('hidden');
            inputFoto.classList.remove('hidden');
            
            inputFileFoto.required = true;
            inputFileVideo.required = false;
            
            inputFileVideo.value = '';
        }
    }

    // Run on change
    tipeMediaSelect.addEventListener('change', toggleInputs);
    
    // Run on load (in case of browser back button or default selection)
    toggleInputs();
});
</script>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>
