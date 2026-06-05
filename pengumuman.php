<?php 
// Set judul halaman dinamis
$pageTitle = "Pengumuman";

require_once 'templates/header.php'; 

// === LOGIKA PENGAMBILAN DATA (Lokal & Teroptimasi) ===
// Mengambil semua pengumuman yang statusnya 'aktif', diurutkan dari yang terbaru
// PERBAIKAN: Menggunakan $pdo (bukan $conn) karena sudah migrasi ke PDO
$pengumuman_from_db = fetchAll($pdo, 'pengumuman', [
    'where' => "WHERE status = 'aktif'",
    'orderBy' => 'tanggal_dibuat DESC'
]);
?>

<!-- HERO SECTION (Header Halaman) -->
<section class="page-header position-relative d-flex align-items-center justify-content-center" style="
    width: 100vw; 
    margin-left: calc(-50vw + 50%); 
    margin-right: calc(-50vw + 50%); 
    background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); 
    margin-top: -25px; 
    padding-top: 100px !important; 
    padding-bottom: 80px !important;
    overflow: hidden;
">
    <!-- Dekorasi Background -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; opacity: 0.05; pointer-events: none;">
        <i class="fas fa-bullhorn" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
        <i class="fas fa-clipboard-list" style="position: absolute; bottom: -10%; left: -5%; font-size: 15rem; transform: rotate(15deg); color: white;"></i>
    </div>

    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif; letter-spacing: 1.5px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    PENGUMUMAN
                </h1>
                <p class="lead opacity-90 mx-auto fs-6" style="max-width: 600px; font-weight: 300;">
                    Informasi penting dan pemberitahuan resmi dari Radio Kominfo Dogiyai untuk masyarakat.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN UTAMA -->
<main class="container position-relative" style="margin-top: -40px; z-index: 10; padding-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <?php if (!empty($pengumuman_from_db)): ?>
                <div class="vstack gap-4">
                    <?php foreach ($pengumuman_from_db as $item): ?>
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden pengumuman-card bg-white hover-lift">
                            <div class="card-body p-4 p-md-5">
                                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between mb-4 gap-3">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary mb-2 px-3 py-2 rounded-pill fw-bold border border-primary-subtle" style="font-size: 0.75rem;">
                                            <i class="fas fa-bullhorn me-1"></i> Info Resmi
                                        </span>
                                        <h4 class="card-title fw-bold text-dark mb-1 font-outfit" style="font-size: 1.5rem; line-height: 1.3;">
                                            <?php echo htmlspecialchars($item['judul']); ?>
                                        </h4>
                                        <div class="text-muted small mt-2 d-flex align-items-center">
                                            <i class="far fa-calendar-alt me-2 text-warning"></i> 
                                            <span>Diposting: <?php echo date('d F Y', strtotime($item['tanggal_dibuat'])); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($item['tanggal_berakhir']): ?>
                                        <?php 
                                            $tgl_akhir = strtotime($item['tanggal_berakhir']);
                                            $hari_ini = time();
                                            $sisa_hari = ceil(($tgl_akhir - $hari_ini) / (60 * 60 * 24));
                                            $is_expired = $sisa_hari < 0;
                                            
                                            if ($is_expired) {
                                                $alert_class = 'text-secondary bg-secondary-subtle border-secondary';
                                                $icon = 'fas fa-history';
                                                $text = 'Berakhir';
                                            } elseif ($sisa_hari < 3) {
                                                $alert_class = 'text-danger bg-danger-subtle border-danger';
                                                $icon = 'fas fa-exclamation-circle';
                                                $text = 'Segera Berakhir';
                                            } else {
                                                $alert_class = 'text-success bg-success-subtle border-success';
                                                $icon = 'fas fa-hourglass-half';
                                                $text = 'Masih Berlaku';
                                            }
                                        ?>
                                        <div class="text-start text-md-end ms-md-3 flex-shrink-0">
                                            <div class="d-flex flex-column align-items-start align-items-md-end">
                                                <span class="badge <?php echo $alert_class; ?> px-3 py-2 rounded-pill mb-1 border">
                                                    <i class="<?php echo $icon; ?> me-1"></i> <?php echo $text; ?>
                                                </span>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    s.d. <?php echo date('d M Y', $tgl_akhir); ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-text text-secondary mb-4 content-text" style="line-height: 1.8; font-size: 1rem; white-space: pre-line;">
                                    <?php echo nl2br(htmlspecialchars($item['isi_pengumuman'])); ?>
                                </div>

                                <div class="d-flex align-items-center flex-wrap gap-3 pt-4 border-top border-light mt-auto">
                                    <?php if (!empty($item['file_lampiran'])): ?>
                                        <a href="admin-radio/uploads/<?php echo htmlspecialchars($item['file_lampiran']); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center btn-download transition-transform" download>
                                            <div class="icon-circle bg-white text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                <i class="fas fa-download fa-xs"></i>
                                            </div>
                                            Unduh Lampiran
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic px-2 py-1 bg-light rounded"><i class="fas fa-paperclip me-1"></i> Tidak ada lampiran file</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Footer Card (Branding) -->
                            <div class="card-footer bg-light border-0 py-2 px-4 px-md-5 d-flex justify-content-between align-items-center">
                                <small class="text-muted fst-italic" style="font-size: 0.75rem;">Dinas Komunikasi dan Informatika Kab. Dogiyai</small>
                                <i class="fas fa-rss text-muted opacity-25"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Tampilan Kosong -->
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light mx-auto" style="max-width: 600px;">
                    <div class="mb-4 p-4 bg-light rounded-circle d-inline-block">
                        <i class="fas fa-clipboard-list fa-4x text-muted opacity-50"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-2 font-outfit">Belum Ada Pengumuman</h4>
                    <p class="text-muted mb-0">Saat ini tidak ada informasi atau pengumuman aktif yang perlu ditampilkan.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<!-- CSS Tambahan (Inline) -->
<style>
    .font-outfit { font-family: 'Poppins', sans-serif; }
    .font-teko { font-family: 'Teko', sans-serif; }

    /* Card Styles */
    .pengumuman-card {
        border-left: 5px solid #1a237e !important; /* Primary Color border left */
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }

    /* Badge Colors */
    .bg-primary-subtle {
        background-color: rgba(26, 35, 126, 0.1);
        color: #1a237e !important;
    }
    
    .bg-secondary-subtle { background-color: #e2e3e5; color: #41464b; }
    .bg-success-subtle { background-color: #d1e7dd; color: #0f5132; }
    .bg-danger-subtle { background-color: #f8d7da; color: #842029; }

    /* Button Animation */
    .transition-transform { transition: transform 0.3s ease; }
    .btn-download {
        background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
        border: none;
    }
    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3);
    }
</style>

<?php require_once 'templates/footer.php'; ?>