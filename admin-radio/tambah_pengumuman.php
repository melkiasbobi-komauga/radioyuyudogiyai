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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Tambah Pengumuman Baru</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            
            <!-- Tampilkan Pesan Error dari URL jika ada -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_upload'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-bold">Gagal Upload!</p>
                    <p>Terjadi kesalahan saat mengupload file lampiran. Pastikan format dan ukuran file sesuai.</p>
                </div>
            <?php endif; ?>

            <!-- Form Tambah -->
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-700">Formulir Pengumuman</h2>
                    <p class="text-xs text-gray-500 mt-1">Isi detail pengumuman baru yang akan dipublikasikan.</p>
                </div>
                
                <form action="proses_pengumuman.php?aksi=tambah" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border" 
                            placeholder="Masukkan judul pengumuman..." required>
                    </div>

                    <!-- Isi Pengumuman -->
                    <div>
                        <label for="isi_pengumuman" class="block text-sm font-medium text-gray-700 mb-1">Isi Pengumuman <span class="text-red-500">*</span></label>
                        <textarea name="isi_pengumuman" id="isi_pengumuman" rows="6" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border" 
                            placeholder="Tuliskan isi lengkap pengumuman di sini..." required></textarea>
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
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika pengumuman berlaku selamanya.</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Publikasi</label>
                            <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-2.5 border bg-white">
                                <option value="aktif" selected>Aktif (Langsung Tampil)</option>
                                <option value="tidak aktif">Tidak Aktif (Simpan Draf)</option>
                            </select>
                        </div>
                    </div>

                    <!-- File Lampiran -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors duration-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Lampiran (Opsional)</label>
                        <input type="file" name="file_lampiran" id="file_lampiran" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-white hover:file:bg-blue-600 transition cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Format yang diizinkan: PDF, DOC, DOCX, JPG, PNG. Ukuran maksimal: 5MB.
                        </p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="kelola_pengumuman.php" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-md transition duration-200 text-sm flex items-center transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Simpan Pengumuman
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
