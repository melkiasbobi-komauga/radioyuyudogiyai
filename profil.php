<?php 
// Set judul halaman dinamis
$pageTitle = "Profil Radio";

require_once 'templates/header.php'; 

// === LOGIKA PENGAMBILAN DATA (Lokal & Teroptimasi - UPDATE PDO) ===

// 1. Ambil Data Profil (Sejarah, Visi, Misi, dll)
$profil_db = [];
if (isset($pdo)) {
    try {
        // Ambil semua pengaturan sekaligus dalam satu query menggunakan PDO
        $stmt = $pdo->query("SELECT * FROM profil_website");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $profil_db[$row['nama_pengaturan']] = $row['isi_pengaturan'];
        }
    } catch (PDOException $e) {
        // Silent error jika gagal load profil
        error_log("Gagal memuat profil: " . $e->getMessage());
    }
}

// Set variabel dengan fallback jika data kosong
$profil_singkat = $profil_db['profil_singkat'] ?? 'Deskripsi singkat belum tersedia.';
$sejarah = $profil_db['sejarah'] ?? 'Sejarah belum tersedia.';
$visi = $profil_db['visi'] ?? 'Visi belum tersedia.';
$misi = $profil_db['misi'] ?? 'Misi belum tersedia.';

// 2. Ambil Data Tim (Menggunakan fungsi fetchAll dari header.php dengan $pdo)
// Mengambil semua anggota tim
$tim_kami = fetchAll($pdo, 'tim');

?>

<!-- HERO SECTION (Header Halaman) -->
<section class="page-header position-relative d-flex align-items-center justify-content-center" style="
    width: 100vw; 
    margin-left: calc(-50vw + 50%); 
    margin-right: calc(-50vw + 50%); 
    background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); 
    margin-top: -25px; 
    padding-top: 100px !important; 
    padding-bottom: 60px !important;
    overflow: hidden;
">
    <!-- Dekorasi Background -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; opacity: 0.05; pointer-events: none;">
        <i class="fas fa-info-circle" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
        <i class="fas fa-building" style="position: absolute; bottom: -10%; left: -5%; font-size: 15rem; transform: rotate(15deg); color: white;"></i>
    </div>

    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif; letter-spacing: 1.5px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    PROFIL RADIO
                </h1>
                <p class="lead opacity-90 mx-auto fs-6" style="max-width: 600px; font-weight: 300;">
                    Mengenal lebih dekat Radio Yuyu Kominfo Dogiyai, media pemersatu dan sumber informasi terpercaya.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN UTAMA -->
<main class="container position-relative" style="margin-top: -40px; z-index: 10; padding-bottom: 60px;">
    
    <!-- Bagian 1: Profil Singkat & Sejarah -->
    <div class="card border-0 shadow-lg rounded-4 mb-5 p-4 p-md-5 bg-white overflow-hidden position-relative hover-lift">
        
        <div class="row align-items-center g-5 position-relative" style="z-index: 1;">
            <div class="col-md-4 text-center">
                <!-- Gambar Bulat Radio Yuyu Kominfo -->
                <div class="position-relative d-inline-block">
                    <div class="position-absolute top-0 start-0 w-100 h-100 rounded-circle border-4 border-warning opacity-50" style="transform: scale(1.1);"></div>
                    <img src="images/logo.png" 
                         class="img-fluid rounded-circle shadow-lg bg-white p-2" 
                         alt="Logo Radio Yuyu Kominfo" 
                         style="width: 220px; height: 220px; object-fit: contain;">
                </div>
            </div>
            <div class="col-md-8">
                <h3 class="fw-bold text-primary mb-3 font-outfit" style="font-size: 2rem;">Profil Stasiun</h3>
                
                <h5 class="fw-bold text-dark mb-2 font-outfit">Tentang Kami</h5>
                <p class="text-secondary" style="line-height: 1.8; font-size: 1.1rem; text-align: justify;">
                    <?php echo htmlspecialchars($profil_singkat); ?>
                </p>

                <h5 class="fw-bold text-dark mt-4 mb-2 font-outfit">Sejarah Singkat</h5>
                <p class="text-muted small" style="line-height: 1.8; text-align: justify;">
                    <?php echo nl2br(htmlspecialchars($sejarah)); ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bagian 2: Visi & Misi -->
    <section class="mt-5 mb-5">
        <div class="text-center mb-5">
            <h3 class="fw-bold text-primary mb-2 font-outfit" style="font-size: 2rem;">Visi & Misi</h3>
            <p class="text-muted mx-auto" style="max-width: 600px;">Fondasi dan tujuan kami dalam melayani masyarakat Dogiyai.</p>
        </div>
        
        <div class="row g-4">
            <!-- Kartu Visi -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 bg-white border-start border-5 border-success hover-lift transition-all">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-square bg-success-subtle text-success rounded-3 p-3 me-3">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                            <h4 class="fw-bold text-success m-0 font-outfit">Visi</h4>
                        </div>
                        <p class="fst-italic text-dark fs-5 mb-0" style="line-height: 1.6;">
                            "<?php echo htmlspecialchars($visi); ?>"
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Kartu Misi -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 bg-white border-start border-5 border-primary hover-lift transition-all">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-square bg-primary-subtle text-primary rounded-3 p-3 me-3">
                                <i class="fas fa-rocket fa-2x"></i>
                            </div>
                            <h4 class="fw-bold text-primary m-0 font-outfit">Misi</h4>
                        </div>
                        <div class="text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                            <?php 
                                $misi_content = htmlspecialchars($misi);
                                // Coba memecah misi jika menggunakan format nomor (1. Misi satu 2. Misi dua)
                                $items = preg_split('/(\d+\.\s*)/', $misi_content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                                
                                if (count($items) > 1 && preg_match('/^\d+\./', $items[0])) {
                                    echo '<ul class="list-unstyled mb-0 d-grid gap-2">';
                                    for ($i = 0; $i < count($items); $i += 2) {
                                        if (isset($items[$i+1])) {
                                            $clean_item = trim($items[$i+1]);
                                            echo "<li class='d-flex align-items-start'><i class='fas fa-check-circle text-primary me-3 mt-1 flex-shrink-0'></i> <span>" . $clean_item . "</span></li>";
                                        }
                                    }
                                    echo '</ul>';
                                } else {
                                    // Tampilkan biasa jika tidak ada format nomor
                                    echo nl2br($misi_content); 
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian 3: Tim Kami -->
    <section class="mt-5 pt-4">
        <div class="text-center mt-5 mb-5">
            <h3 class="fw-bold text-primary mb-2 font-outfit" style="font-size: 2rem;">TIM KAMI</h3>
            <p class="text-muted mx-auto" style="max-width: 600px;">Orang-orang berdedikasi di balik siaran Radio Yuyu Kominfo Dogiyai yang senantiasa menemani hari Anda.</p>
        </div>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 justify-content-center">
            <?php if (!empty($tim_kami)): ?>
                <?php foreach ($tim_kami as $anggota): ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm team-card text-center overflow-hidden rounded-4 hover-lift transition-all">
                            <!-- Foto Anggota -->
                            <div class="team-img-wrapper position-relative mx-auto mt-4 mb-3">
                                <div class="img-container rounded-circle overflow-hidden border-4 border-warning shadow-md mx-auto position-relative" style="width: 140px; height: 140px;">
                                    <img src="admin-radio/uploads/<?php echo htmlspecialchars($anggota['foto']); ?>" 
                                         class="img-fluid w-100 h-100 object-fit-cover transition-transform" 
                                         alt="<?php echo htmlspecialchars($anggota['nama_lengkap']); ?>" 
                                         onerror="this.src='https://placehold.co/150x150?text=User'">
                                </div>
                                <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px; right: 15px;">
                                    <i class="fas fa-microphone fa-xs"></i>
                                </div>
                            </div>
                            
                            <!-- Info Anggota -->
                            <div class="card-body pt-0 pb-4 px-3">
                                <h5 class="card-title fw-bold text-dark mb-1 font-outfit"><?php echo htmlspecialchars($anggota['nama_lengkap']); ?></h5>
                                <p class="card-text text-primary small fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;"><?php echo htmlspecialchars($anggota['jabatan']); ?></p>
                                <div class="px-2">
                                    <p class="card-text text-muted small fst-italic mb-0 line-clamp-3" style="font-size: 0.85rem;">
                                        "<?php echo htmlspecialchars($anggota['bio_singkat']); ?>"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Tampilan Kosong -->
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded-4 shadow-sm border border-light mx-auto" style="max-width: 500px;">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="fw-bold text-secondary mb-1 font-outfit">Tim Belum Ditambahkan</h5>
                        <p class="text-muted small mb-0">Informasi anggota tim akan segera tersedia.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<!-- CSS Tambahan (Inline) -->
<style>
    .font-outfit { font-family: 'Poppins', sans-serif; }
    .font-teko { font-family: 'Teko', sans-serif; }

    /* Card Animations */
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    
    .transition-all { transition: all 0.3s ease; }
    .transition-transform { transition: transform 0.3s ease; }
    
    .team-card:hover .img-fluid {
        transform: scale(1.1);
    }

    /* Icon & Badge Styles */
    .icon-square {
        border-radius: 12px !important;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    
    .bg-success-subtle { background-color: rgba(0, 150, 0, 0.1); color: #0f5132; }
    .bg-primary-subtle { background-color: rgba(26, 35, 126, 0.1); color: #1a237e; }
    
    /* Helper Classes */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<?php require_once 'templates/footer.php'; ?>