<?php 
$pageTitle = "Agenda Kegiatan";
require_once 'templates/header.php'; 

// === LOGIKA PENGAMBILAN DATA (PDO) ===
$agenda_from_db = fetchAll($pdo, 'agenda', ['orderBy' => 'tanggal_mulai DESC']);

$upcoming_events = [];
$past_events = [];
$today = date('Y-m-d');

if (!empty($agenda_from_db)) {
    foreach ($agenda_from_db as $event) {
        if (!empty($event['tanggal_mulai'])) {
            if ($event['tanggal_mulai'] >= $today) {
                $upcoming_events[] = $event;
            } else {
                $past_events[] = $event;
            }
        }
    }
}

usort($upcoming_events, function($a, $b) { 
    return $a['tanggal_mulai'] <=> $b['tanggal_mulai']; 
});
?>

<!-- HERO SECTION (Header Halaman) -->
<section class="page-header position-relative d-flex align-items-center justify-content-center" style="
    width: 100vw; 
    margin-left: calc(-50vw + 50%); 
    margin-right: calc(-50vw + 50%); 
    background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); 
    margin-top: -25px; 
    padding-top: 100px !important; 
    padding-bottom: 120px !important; /* Ruang lebih untuk elemen melayang (floating) */
    overflow: hidden;
">
    <!-- Dekorasi Background -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; opacity: 0.05; pointer-events: none;">
        <i class="fas fa-calendar-alt" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
        <i class="fas fa-clock" style="position: absolute; bottom: -10%; left: -5%; font-size: 15rem; transform: rotate(15deg); color: white;"></i>
    </div>

    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif;">AGENDA KEGIATAN</h1>
                <p class="lead opacity-90 mx-auto fs-6" style="max-width: 600px; font-weight: 300;">Daftar acara dan kegiatan penting yang akan diselenggarakan oleh Radio Kominfo Dogiyai maupun Pemerintah Daerah.</p>
            </div>
        </div>
    </div>
</section>

<main class="container position-relative" style="margin-top: -80px; z-index: 10; padding-bottom: 60px;">
    
    <!-- Agenda Mendatang (Floating Section) -->
    <div class="row justify-content-center mb-5">
        <div class="col-12">
            <!-- Judul Section -->
             <div class="d-flex justify-content-center mb-4">
                <div class="bg-white shadow-lg px-4 py-3 rounded-pill border border-light d-flex align-items-center gap-3">
                    <h4 class="fw-bold text-primary m-0 font-outfit" style="font-size: 1.1rem;">
                        <i class="fas fa-calendar-star me-2 text-warning"></i> Acara Mendatang
                    </h4>
                    <div class="vr mx-1"></div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-3">
                        <?php echo count($upcoming_events); ?> Kegiatan
                    </span>
                </div>
            </div>

            <?php if (!empty($upcoming_events)): ?>
                <div class="row g-4 justify-content-center">
                    <?php foreach ($upcoming_events as $event): ?>
                        <div class="col-md-6 col-lg-5">
                            <div class="agenda-card bg-white rounded-4 shadow-lg border-0 overflow-hidden h-100 d-flex flex-row align-items-stretch hover-lift position-relative">
                                <div class="agenda-date text-white d-flex flex-column align-items-center justify-content-center p-3 text-center" style="min-width: 90px; background: linear-gradient(180deg, #1a237e 0%, #283593 100%);">
                                    <span class="display-6 fw-bold lh-1 font-teko"><?php echo date('d', strtotime($event['tanggal_mulai'])); ?></span>
                                    <span class="text-uppercase small fw-bold mt-1" style="font-size: 0.75rem; letter-spacing: 1px; opacity: 0.9;"><?php echo date('M', strtotime($event['tanggal_mulai'])); ?></span>
                                    <span class="small opacity-50 mt-1" style="font-size: 0.7rem;"><?php echo date('Y', strtotime($event['tanggal_mulai'])); ?></span>
                                </div>
                                <div class="card-body p-4 d-flex flex-column justify-content-center position-relative">
                                     <div class="position-absolute top-0 end-0 mt-2 me-2 opacity-10 text-primary">
                                        <i class="fas fa-star fa-2x"></i>
                                    </div>

                                    <h5 class="card-title fw-bold text-dark mb-2 font-outfit" style="font-size: 1.1rem; padding-right: 20px;"><?php echo htmlspecialchars($event['nama_kegiatan']); ?></h5>
                                    <p class="card-text text-muted small mb-3 flex-grow-1 line-clamp-3" style="font-size: 0.85rem;"><?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?></p>
                                    <div class="d-flex flex-wrap gap-2 text-secondary small fw-medium mt-auto pt-3 border-top border-light">
                                        <div class="d-flex align-items-center bg-light px-2 py-1 rounded shadow-sm border border-light"><i class="far fa-clock text-warning me-2"></i> <?php echo !empty($event['waktu_mulai']) ? date('H:i', strtotime($event['waktu_mulai'])) . ' WIT' : 'TBA'; ?></div>
                                        <div class="d-flex align-items-center bg-light px-2 py-1 rounded shadow-sm border border-light"><i class="fas fa-map-marker-alt text-danger me-2"></i> <?php echo htmlspecialchars($event['lokasi'] ?? 'TBA'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                 <div class="col-md-8 mx-auto">
                    <div class="alert alert-light border-0 shadow-lg rounded-4 py-5 text-center bg-white">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check text-muted opacity-25 fa-4x"></i>
                        </div>
                        <h5 class="fw-bold mb-2 text-dark font-outfit">Belum Ada Agenda Mendatang</h5>
                        <p class="mb-0 text-muted">Saat ini belum ada acara khusus yang dijadwalkan dalam waktu dekat.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Arsip Agenda (Opsional: Tampilkan agenda lampau jika perlu) -->
    <?php if (!empty($past_events)): ?>
        <div class="row justify-content-center mt-5 pt-4 border-top border-light">
            <div class="col-12">
                 <div class="text-center mb-4">
                    <h4 class="fw-bold text-secondary font-outfit opacity-75">Arsip Kegiatan</h4>
                </div>
                <div class="row g-4 justify-content-center">
                    <?php foreach ($past_events as $event): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card h-100 border-0 bg-light shadow-sm rounded-4 p-3 text-muted opacity-75 hover-opacity-100 transition-all">
                                <div class="card-body p-3">
                                     <small class="d-block mb-1 fw-bold"><i class="far fa-calendar me-1"></i> <?php echo date('d M Y', strtotime($event['tanggal_mulai'])); ?></small>
                                    <h6 class="card-title fw-bold text-dark mb-2 font-outfit text-truncate" title="<?php echo htmlspecialchars($event['nama_kegiatan']); ?>">
                                        <?php echo htmlspecialchars($event['nama_kegiatan']); ?>
                                    </h6>
                                    <p class="card-text small mb-0 line-clamp-2">
                                        <?php echo htmlspecialchars($event['deskripsi']); ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                                    <span class="badge bg-secondary rounded-pill px-3">Selesai</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
     <!-- Bagian Link ke Jadwal Rutin -->
    <div class="row mt-5 pt-3">
        <div class="col-12 text-center">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-light d-inline-block mx-auto" style="max-width: 700px;">
                <h5 class="fw-bold text-primary mb-2 font-outfit">Mencari Jadwal Siaran Harian?</h5>
                <p class="text-muted small mb-3">
                    Halaman ini khusus untuk agenda/acara khusus (event). Untuk jadwal siaran rutin harian (seperti Morning Show, Berita, dll), silakan cek halaman Jadwal.
                </p>
                <a href="jadwal.php" class="btn btn-outline-primary rounded-pill px-5 py-2 fw-bold transition-all hover-shadow">
                    <i class="fas fa-clock me-2"></i> Lihat Jadwal Siaran Rutin
                </a>
            </div>
        </div>
    </div>

</main>

<style>
    .font-outfit { font-family: 'Poppins', sans-serif; }
    .font-teko { font-family: 'Teko', sans-serif; }
    
    /* Agenda Card Style */
    .agenda-card {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important; 
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .hover-opacity-100:hover {
        opacity: 1 !important;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        background-color: #fff !important;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 5px 15px rgba(26, 35, 126, 0.15);
        background-color: #1a237e;
        color: white;
        border-color: #1a237e;
    }
    
     /* Badge Colors - Consistent with other pages */
    .bg-primary-subtle {
        background-color: rgba(26, 35, 126, 0.1);
        color: #1a237e !important;
    }
</style>

<?php require_once 'templates/footer.php'; ?>