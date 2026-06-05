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

// Variabel untuk menyimpan data jadwal dan pesan error
$jadwal = null;
$error_message = '';

// 5. Periksa apakah ID ada di URL dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // 6. Ambil data jadwal dari database menggunakan PDO & prepared statement
    try {
        $stmt = $pdo->prepare("SELECT * FROM jadwal WHERE id = ?");
        $stmt->execute([$id]);
        $jadwal = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika tidak ada jadwal dengan ID tersebut
        if (!$jadwal) {
            $error_message = "Jadwal tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error mengambil data jadwal: " . $e->getMessage();
    }
} else {
    $error_message = "ID Jadwal tidak valid.";
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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Edit Jadwal Siaran</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_jadwal.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jadwal
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

            <?php if ($jadwal): ?>
            <!-- Form Edit -->
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Formulir Edit Jadwal</h2>
                        <p class="text-xs text-gray-500 mt-1">Perbarui informasi siaran rutin.</p>
                    </div>
                    <div class="text-xs text-gray-400">
                        ID: #<?php echo $jadwal['id']; ?>
                    </div>
                </div>
                
                <form action="proses_jadwal.php?aksi=edit" method="POST" class="p-6 space-y-6">
                    <!-- Field CSRF -->
                    <?php csrfField(); ?>
                    
                    <input type="hidden" name="id" value="<?php echo $jadwal['id']; ?>">

                    <!-- Waktu -->
                    <div>
                        <label for="waktu" class="block text-sm font-semibold text-gray-700 mb-2">Waktu Siaran <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="far fa-clock text-gray-400"></i>
                            </div>
                            <input type="text" name="waktu" id="waktu" 
                                value="<?php echo htmlspecialchars($jadwal['waktu']); ?>"
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
                                value="<?php echo htmlspecialchars($jadwal['program']); ?>"
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Nama program acara" required>
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
                                value="<?php echo htmlspecialchars($jadwal['penyiar']); ?>"
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm" 
                                placeholder="Nama penyiar yang bertugas" required>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                        <a href="kelola_jadwal.php" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200 text-sm shadow-sm">
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
