<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php'; 
require_once 'includes/auth.php';
if (!isAdminLoggedIn()) { redirect('login.php'); exit(); }
require_once 'header_admin.php'; 
?>

<div class="flex-1 flex flex-col bg-gray-50">
    <header class="bg-white shadow-sm p-4 flex items-center sticky top-0 z-10">
        <button id="hamburger" class="text-gray-500 hover:text-blue-600 focus:outline-none md:hidden mr-4"><i class="fas fa-bars fa-lg"></i></button>
        <h1 class="text-xl font-bold text-gray-800">Tambah Program Unggulan</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6"><a href="kelola_program.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium"><i class="fas fa-arrow-left"></i> Kembali</a></div>
            
            <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_upload'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm"><p class="font-bold">Gagal Upload!</p><p>Cek format dan ukuran file.</p></div>
            <?php endif; ?>

            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50"><h2 class="text-lg font-bold text-gray-800">Formulir Program</h2></div>
                
                <form action="proses_program.php?aksi=tambah" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <?php csrfField(); ?>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Program <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" class="w-full rounded-lg border-gray-300 focus:border-purple-500 p-3 border shadow-sm" placeholder="Contoh: Suara Pemerintah" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="4" class="w-full rounded-lg border-gray-300 focus:border-purple-500 p-3 border shadow-sm" placeholder="Jelaskan tentang program ini..." required></textarea>
                    </div>

                    <div class="bg-purple-50 p-5 rounded-xl border border-purple-100">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Cover <span class="text-red-500">*</span></label>
                        <input type="file" name="gambar" accept="image/*" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer border border-gray-300 rounded-lg bg-white p-1"/>
                        <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Maks: 2MB.</p>
                    </div>

                    <div class="flex justify-end pt-4"><button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium shadow-lg transition transform hover:-translate-y-0.5"><i class="fas fa-save mr-2"></i> Simpan Program</button></div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once 'footer_admin.php'; ?>
