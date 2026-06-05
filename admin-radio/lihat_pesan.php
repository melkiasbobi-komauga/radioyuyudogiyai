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

$pesan = null;
$error_message = '';

// 5. Ambil detail pesan
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Update status menjadi 'sudah dibaca'
        $update_stmt = $pdo->prepare("UPDATE pesan_kontak SET status = 'sudah dibaca' WHERE id = ?");
        $update_stmt->execute([$id]);

        // Ambil data pesan
        $stmt = $pdo->prepare("SELECT * FROM pesan_kontak WHERE id = ?");
        $stmt->execute([$id]);
        $pesan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pesan) {
            $error_message = "Pesan tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error memproses pesan: " . $e->getMessage();
    }
} else {
    $error_message = "ID Pesan tidak valid.";
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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Detail Pesan</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="kelola_pesan.php" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali ke Kotak Masuk
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

            <?php if ($pesan): ?>
                <!-- Kartu Detail Pesan -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <!-- Header Pesan -->
                    <div class="p-6 border-b border-gray-100 bg-blue-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($pesan['subjek']); ?></h2>
                                <div class="flex items-center text-sm text-gray-600 mt-2">
                                    <span class="font-semibold mr-2">Dari:</span>
                                    <?php echo htmlspecialchars($pesan['nama_pengirim']); ?>
                                    <span class="mx-2 text-gray-300">|</span>
                                    <a href="mailto:<?php echo htmlspecialchars($pesan['email_pengirim']); ?>" class="text-blue-600 hover:underline flex items-center">
                                        <i class="far fa-envelope mr-1"></i> <?php echo htmlspecialchars($pesan['email_pengirim']); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="text-right text-sm text-gray-500">
                                <div class="flex items-center justify-end mb-1">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    <?php echo date('d M Y', strtotime($pesan['tanggal_kirim'])); ?>
                                </div>
                                <div class="flex items-center justify-end">
                                    <i class="far fa-clock mr-1"></i>
                                    <?php echo date('H:i', strtotime($pesan['tanggal_kirim'])); ?> WIT
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Isi Pesan -->
                    <div class="p-8">
                        <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                            <?php echo htmlspecialchars($pesan['isi_pesan']); ?>
                        </div>
                    </div>

                    <!-- Footer Aksi -->
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                        <a href="mailto:<?php echo htmlspecialchars($pesan['email_pengirim']); ?>?subject=Re: <?php echo urlencode($pesan['subjek']); ?>" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                            <i class="fas fa-reply mr-2"></i> Balas Email
                        </a>
                        <a href="proses_pesan.php?aksi=hapus&id=<?php echo $pesan['id']; ?>" 
                           onclick="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')"
                           class="inline-flex items-center px-4 py-2 bg-white border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium rounded-lg shadow-sm transition duration-200">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>