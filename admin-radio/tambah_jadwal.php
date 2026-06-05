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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Tambah Jadwal Baru</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_jadwal.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jadwal
                </a>
            </div>
            
            <!-- Tampilkan Pesan Error dari URL jika ada -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_validasi'): ?>
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
                    <h2 class="text-lg font-bold text-gray-800">Formulir Jadwal Siaran</h2>
                    <p class="text-xs text-gray-500 mt-1">Tambahkan jadwal program siaran rutin baru.</p>
                </div>
                
                <form action="proses_jadwal.php?aksi=tambah" method="POST" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <!-- Waktu -->
                    <div>
                        <label for="waktu" class="block text-sm font-semibold text-gray-700 mb-2">Waktu Siaran <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="far fa-clock text-gray-400"></i>
                            </div>
                            <input type="text" name="waktu" id="waktu" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Contoh: 08:00 - 10:00" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: HH:MM - HH:MM WIT</p>
                    </div>

                    <!-- Program -->
                    <div>
                        <label for="program" class="block text-sm font-semibold text-gray-700 mb-2">Nama Program <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-broadcast-tower text-gray-400"></i>
                            </div>
                            <input type="text" name="program" id="program" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Contoh: Morning Show, Musik Daerah" required>
                        </div>
                    </div>

                    <!-- Penyiar -->
                    <div>
                        <label for="penyiar" class="block text-sm font-semibold text-gray-700 mb-2">Penyiar <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-microphone text-gray-400"></i>
                            </div>
                            <input type="text" name="penyiar" id="penyiar" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Nama penyiar yang bertugas" required>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="kelola_jadwal.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition duration-200 text-sm flex items-center transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Jadwal
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
