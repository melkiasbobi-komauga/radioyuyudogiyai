<?php 
// Set judul halaman dinamis
$pageTitle = "Jadwal Siaran";

require_once 'templates/header.php'; 

// === LOGIKA PENGAMBILAN DATA (Lokal & Teroptimasi) ===
// Mengambil data jadwal dari database, diurutkan berdasarkan waktu (pagi ke malam)
// PERBAIKAN: Menggunakan $pdo (bukan $conn) karena sudah migrasi ke PDO
$jadwal_siaran = fetchAll($pdo, 'jadwal', ['orderBy' => 'waktu ASC']);
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
        <i class="fas fa-calendar-alt" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
        <i class="fas fa-clock" style="position: absolute; bottom: -10%; left: -5%; font-size: 15rem; transform: rotate(15deg); color: white;"></i>
    </div>

    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif; letter-spacing: 1.5px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    JADWAL SIARAN
                </h1>
                <p class="lead opacity-90 mx-auto fs-6" style="max-width: 600px; font-weight: 300;">
                    Jangan lewatkan program-program unggulan kami setiap harinya untuk menemani aktivitas Anda.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN UTAMA -->
<main class="container position-relative" style="margin-top: -40px; z-index: 10; padding-bottom: 60px;">
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Card Jadwal -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header border-0 p-4" style="background: linear-gradient(90deg, #1a237e, #303f9f);">
                    <div class="d-flex align-items-center text-white">
                        <div class="icon-wrapper bg-white text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-broadcast-tower fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold font-outfit">Program Harian</h4>
                            <small class="opacity-75">Waktu dalam WIT (Waktu Indonesia Timur)</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="py-4 ps-4 text-uppercase text-secondary small fw-bold border-0 font-outfit" style="width: 25%;">Waktu</th>
                                    <th scope="col" class="py-4 text-uppercase text-secondary small fw-bold border-0 font-outfit" style="width: 20%;">Hari</th>
                                    <th scope="col" class="py-4 text-uppercase text-secondary small fw-bold border-0 font-outfit" style="width: 30%;">Program Acara</th>
                                    <th scope="col" class="py-4 pe-4 text-uppercase text-secondary small fw-bold border-0 font-outfit" style="width: 25%;">Penyiar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($jadwal_siaran)): ?>
                                    <?php foreach ($jadwal_siaran as $index => $item): ?>
                                        <tr class="schedule-row border-bottom border-light">
                                            <!-- Waktu -->
                                            <td class="ps-4 py-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="time-badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill me-2 font-monospace shadow-sm" style="font-size: 0.9rem;">
                                                        <i class="far fa-clock me-2 opacity-75"></i>
                                                        <?php echo htmlspecialchars($item['waktu']); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <!-- Hari -->
                                            <td class="py-4 border-0">
                                                <span class="badge bg-light text-dark border border-secondary-subtle rounded-pill fw-medium px-3 py-2">
                                                    <?php echo htmlspecialchars($item['hari']); ?>
                                                </span>
                                            </td>
                                            <!-- Program -->
                                            <td class="py-4 border-0">
                                                <h6 class="fw-bold text-dark mb-1 font-outfit" style="font-size: 1.05rem;"><?php echo htmlspecialchars($item['program']); ?></h6>
                                            </td>
                                            <!-- Penyiar -->
                                            <td class="pe-4 py-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-warning text-dark me-2 small fw-bold d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-microphone"></i>
                                                    </div>
                                                    <span class="text-secondary fw-medium font-outfit"><?php echo htmlspecialchars($item['penyiar']); ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="text-muted opacity-50 mb-3">
                                                <i class="fas fa-calendar-times fa-3x"></i>
                                            </div>
                                            <h5 class="fw-bold text-secondary font-outfit">Belum Ada Jadwal</h5>
                                            <p class="text-muted small mb-0">Jadwal siaran belum tersedia saat ini.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Tambahan (Cards Modern) -->
            <div class="row mt-5 g-4 text-center">
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-4 shadow-sm h-100 hover-lift border border-light transition-all">
                        <div class="icon-circle bg-warning-subtle text-warning mb-3 mx-auto rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-music fa-2x"></i>
                        </div>
                        <h6 class="fw-bold font-outfit text-dark">Request Lagu</h6>
                        <p class="text-muted small mb-0">Ingin lagu favoritmu diputar? Hubungi kami lewat WhatsApp atau menu Kontak.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-4 shadow-sm h-100 hover-lift border border-light transition-all">
                        <div class="icon-circle bg-primary-subtle text-primary mb-3 mx-auto rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-bullhorn fa-2x"></i>
                        </div>
                        <h6 class="fw-bold font-outfit text-dark">Pasang Iklan</h6>
                        <p class="text-muted small mb-0">Promosikan usaha atau acara Anda melalui siaran radio kami dengan jangkauan luas.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-4 shadow-sm h-100 hover-lift border border-light transition-all">
                        <div class="icon-circle bg-danger-subtle text-danger mb-3 mx-auto rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-podcast fa-2x"></i>
                        </div>
                        <h6 class="fw-bold font-outfit text-dark">Talkshow Interaktif</h6>
                        <p class="text-muted small mb-0">Bergabunglah dalam diskusi hangat seputar isu terkini di Dogiyai.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- CSS Tambahan (Inline) -->
<style>
    .font-outfit { font-family: 'Poppins', sans-serif; }

    .bg-primary-subtle {
        background-color: rgba(26, 35, 126, 0.1);
        color: #1a237e !important;
    }
    
    .bg-warning-subtle { background-color: #fff3cd; color: #856404; }
    .bg-danger-subtle { background-color: #f8d7da; color: #842029; }
    .border-secondary-subtle { border-color: #e2e3e5 !important; }
    
    .schedule-row {
        transition: background-color 0.2s ease;
    }
    
    .schedule-row:hover {
        background-color: #f8faff; /* Very light blue hover */
    }
    
    /* Mempercantik Tabel */
    .table thead th {
        letter-spacing: 0.5px;
        color: #64748b; /* Slate 500 */
        background-color: #f8f9fa;
    }
    
    /* Efek Card Hover di Info Tambahan */
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table { font-size: 0.85rem; }
        .avatar-circle { width: 25px !important; height: 25px !important; font-size: 0.65rem !important; }
        .icon-circle { width: 50px !important; height: 50px !important; font-size: 1.5rem !important; }
    }
</style>

<?php require_once 'templates/footer.php'; ?>