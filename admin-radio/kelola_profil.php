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

// Inisialisasi array profil
$profil = [];

// 5. Ambil data profil
try {
    $stmt = $pdo->query("SELECT * FROM profil_website");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($settings as $row) {
        $profil[$row['nama_pengaturan']] = $row['isi_pengaturan'];
    }
} catch (PDOException $e) {
    die("Tidak bisa mengambil data profil website: " . $e->getMessage());
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
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Pengaturan Profil Website</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Notifikasi Sukses -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_update'): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-xl mr-3"></i>
                        <p class="font-medium">Profil berhasil diperbarui!</p>
                    </div>
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'gagal_upload'): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                        <p class="font-medium">Gagal mengupload logo. Cek format dan ukuran file.</p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="proses_profil.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <!-- Bagian Identitas Utama (BARU) -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-blue-50">
                        <h2 class="text-lg font-bold text-gray-800">Identitas Website</h2>
                        <p class="text-xs text-gray-500 mt-1">Pengaturan judul, nama, dan logo yang tampil di header & footer.</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Judul Website (Title Bar & Header) -->
                        <div>
                            <label for="station_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Stasiun (Judul Utama)</label>
                            <input type="text" name="station_name" id="station_name" 
                                value="<?php echo htmlspecialchars($profil['station_name'] ?? 'RADIO YUYU KOMINFO'); ?>"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm"
                                placeholder="Contoh: RADIO YUYU KOMINFO">
                        </div>

                        <!-- Sub-Judul / Nama Organisasi -->
                        <div>
                            <label for="organization_name" class="block text-sm font-semibold text-gray-700 mb-2">Slogan / Nama Organisasi</label>
                            <input type="text" name="organization_name" id="organization_name" 
                                value="<?php echo htmlspecialchars($profil['organization_name'] ?? '93.6 FM - SUARA DOGIYAI'); ?>"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm"
                                placeholder="Contoh: 93.6 FM - SUARA DOGIYAI">
                        </div>

                        <!-- Copyright Text -->
                        <div>
                            <label for="copyright_text" class="block text-sm font-semibold text-gray-700 mb-2">Teks Copyright Footer</label>
                            <input type="text" name="copyright_text" id="copyright_text" 
                                value="<?php echo htmlspecialchars($profil['copyright_text'] ?? 'Dinas Komunikasi dan Informatika'); ?>"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm"
                                placeholder="Contoh: Dinas Komunikasi dan Informatika">
                        </div>

                        <!-- App Download URL -->
                        <div>
                            <label for="app_download_url" class="block text-sm font-semibold text-gray-700 mb-2">Link Download Aplikasi (APK)</label>
                            <input type="url" name="app_download_url" id="app_download_url" 
                                value="<?php echo htmlspecialchars($profil['app_download_url'] ?? ''); ?>"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm"
                                placeholder="https://example.com/app.apk">
                            <p class="text-xs text-gray-500 mt-1">Link ini akan digunakan pada popup download aplikasi di halaman depan.</p>
                        </div>

                        <!-- Upload Logo -->
                        <div class="border-t border-gray-100 pt-4 mt-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Logo Website</label>
                            <div class="flex items-center gap-6">
                                <div class="flex-shrink-0 text-center">
                                    <p class="text-xs text-gray-500 mb-2">Saat Ini:</p>
                                    <div class="bg-gray-100 p-2 rounded-lg border border-gray-200">
                                        <img src="../images/<?php echo htmlspecialchars($profil['logo_file'] ?? 'logo.png'); ?>" 
                                             alt="Logo Saat Ini" 
                                             class="h-20 w-20 object-contain mx-auto">
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <label for="logo_file" class="block text-xs text-gray-500 mb-2">Ganti Logo (PNG Transparan disarankan):</label>
                                    <input type="file" name="logo_file" id="logo_file" accept="image/png, image/jpeg"
                                        class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-xs file:font-semibold
                                        file:bg-blue-600 file:text-white
                                        hover:file:bg-blue-700
                                        cursor-pointer border border-gray-300 rounded-lg bg-white p-2
                                    "/>
                                    <p class="text-xs text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengubah logo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Profil Umum -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-800">Profil Umum</h2>
                        <p class="text-xs text-gray-500 mt-1">Informasi dasar mengenai stasiun radio.</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Profil Singkat -->
                        <div>
                            <label for="profil_singkat" class="block text-sm font-semibold text-gray-700 mb-2">Profil Singkat</label>
                            <textarea name="profil_singkat" id="profil_singkat" rows="4" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed"
                                placeholder="Deskripsi singkat radio..."><?php echo htmlspecialchars($profil['profil_singkat'] ?? ''); ?></textarea>
                        </div>

                        <!-- Sejarah -->
                        <div>
                            <label for="sejarah" class="block text-sm font-semibold text-gray-700 mb-2">Sejarah</label>
                            <textarea name="sejarah" id="sejarah" rows="6" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed"
                                placeholder="Ceritakan sejarah berdirinya radio..."><?php echo htmlspecialchars($profil['sejarah'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Bagian Visi & Misi -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-800">Visi & Misi</h2>
                        <p class="text-xs text-gray-500 mt-1">Tujuan dan nilai-nilai stasiun radio.</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Visi -->
                        <div>
                            <label for="visi" class="block text-sm font-semibold text-gray-700 mb-2">Visi</label>
                            <textarea name="visi" id="visi" rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed"
                                placeholder="Visi radio..."><?php echo htmlspecialchars($profil['visi'] ?? ''); ?></textarea>
                        </div>

                        <!-- Misi -->
                        <div>
                            <label for="misi" class="block text-sm font-semibold text-gray-700 mb-2">Misi</label>
                            <textarea name="misi" id="misi" rows="5" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed"
                                placeholder="Misi radio..."><?php echo htmlspecialchars($profil['misi'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Bagian Kontak & Peta -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-800">Informasi Kontak & Lokasi</h2>
                        <p class="text-xs text-gray-500 mt-1">Detail kontak dan kode peta yang akan ditampilkan di halaman kontak.</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Alamat -->
                        <div>
                            <label for="alamat_kantor" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Kantor</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="text" name="alamat_kantor" id="alamat_kantor" 
                                    value="<?php echo htmlspecialchars($profil['alamat_kantor'] ?? ''); ?>"
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm"
                                    placeholder="Alamat lengkap studio">
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email_kontak" class="block text-sm font-semibold text-gray-700 mb-2">Email Kontak</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="email_kontak" id="email_kontak" 
                                    value="<?php echo htmlspecialchars($profil['email_kontak'] ?? ''); ?>"
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm"
                                    placeholder="email@radio.com">
                            </div>
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="telepon_kontak" class="block text-sm font-semibold text-gray-700 mb-2">Telepon Kontak</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="text" name="telepon_kontak" id="telepon_kontak" 
                                    value="<?php echo htmlspecialchars($profil['telepon_kontak'] ?? ''); ?>"
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm"
                                    placeholder="(0XXX) XXXXXX">
                            </div>
                        </div>

                        <!-- IFRAME PETA LOKASI BARU -->
                        <div>
                            <label for="iframe_peta" class="block text-sm font-semibold text-gray-700 mb-2">Kode Iframe Peta (Google Maps)</label>
                            <textarea name="iframe_peta" id="iframe_peta" rows="5" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200 p-3 border shadow-sm leading-relaxed"
                                placeholder="Salin seluruh kode iframe dari Google Maps di sini..."><?php echo htmlspecialchars($profil['iframe_peta'] ?? ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Hanya masukkan kode yang dimulai dengan `https://www.google.com/maps/embed...`</p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition duration-200 flex items-center transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Simpan Semua Perubahan
                    </button>
                </div>

            </form>
        </div>
    </main>
</div>

<?php 
// Include Footer
require_once 'footer_admin.php'; 
?>
