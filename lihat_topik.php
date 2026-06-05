<?php 
// Set judul halaman default (akan diupdate setelah dapat data topik)
$pageTitle = "Lihat Topik";

require_once 'templates/header.php'; 

// --- LOGIKA PENGAMBILAN DATA (Lokal & Aman) ---
// 1. Validasi ID Topik
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect halus jika ID tidak valid
    echo "<script>window.location.href='forum.php';</script>";
    exit;
}

$id_topik = (int)$_GET['id'];
$topik = null;
$balasan = [];

// 2. Ambil Data Topik (PDO)
if (isset($pdo)) {
    // Menggunakan Prepared Statement
    $stmt = $pdo->prepare("SELECT * FROM forum_topik WHERE id = ?");
    $stmt->execute([$id_topik]);
    $topik = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Ambil Data Balasan (Jika topik ditemukan)
    if ($topik) {
        // Update judul halaman untuk SEO
        $pageTitle = "Diskusi: " . htmlspecialchars($topik['judul']);
        
        // Ambil balasan, urutkan dari yang terlama (kronologis)
        $query_balasan = "SELECT * FROM forum_balasan WHERE id_topik = ? ORDER BY tanggal_dibuat ASC";
        $stmt_balasan = $pdo->prepare($query_balasan);
        $stmt_balasan->execute([$id_topik]);
        $balasan = $stmt_balasan->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Jika topik tidak ditemukan di database
if (!$topik) {
    echo "<div class='container mt-5 pt-5 text-center'><div class='alert alert-warning'>Topik tidak ditemukan atau telah dihapus. <a href='forum.php' class='alert-link'>Kembali ke Forum</a></div></div>";
    require_once 'templates/footer.php';
    exit;
}
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
        <i class="fas fa-comments" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
    </div>

    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="badge bg-warning text-dark fw-bold mb-3 px-3 py-2 rounded-pill shadow-sm font-monospace">
                    <i class="fas fa-comments me-1"></i> DISKUSI WARGA
                </span>
                <h1 class="article-title mb-3 fw-bold display-5" style="font-family: 'Teko', sans-serif; letter-spacing: 1px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    <?php echo htmlspecialchars($topik['judul']); ?>
                </h1>
                <div class="article-meta d-flex justify-content-center align-items-center gap-3 text-white opacity-90 small">
                    <span><i class="fas fa-user-circle fa-lg me-1 text-warning"></i> <?php echo htmlspecialchars($topik['nama_pembuat']); ?></span>
                    <span>|</span>
                    <span><i class="far fa-calendar-alt me-1 text-warning"></i> <?php echo date('d F Y, H:i', strtotime($topik['tanggal_dibuat'])); ?> WIT</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN UTAMA -->
<main class="container position-relative" style="margin-top: -20px; z-index: 10; padding-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <!-- Notifikasi Status (DIPERBAIKI) -->
            <?php if (isset($_GET['status'])): ?>
                <?php 
                    // Gunakan trim() untuk membersihkan spasi yang mungkin terbawa di URL
                    $status = trim($_GET['status']);
                    
                    // Default values (Error State)
                    $alert_class = 'alert-danger border-danger text-danger';
                    $alert_icon = 'fas fa-exclamation-triangle';
                    $alert_title = 'Gagal Mengirim!';
                    $alert_message = 'Terjadi kesalahan sistem.';

                    // Logika Switch untuk menangani berbagai status
                    switch ($status) {
                        case 'sukses_topik':
                            $alert_class = 'alert-success border-success text-success';
                            $alert_icon = 'fas fa-check-circle';
                            $alert_title = 'Topik Berhasil Dibuat!';
                            $alert_message = 'Topik diskusi baru berhasil diterbitkan. Silakan mulai berdiskusi.';
                            break;
                            
                        case 'sukses_balasan':
                            $alert_class = 'alert-success border-success text-success';
                            $alert_icon = 'fas fa-check-circle';
                            $alert_title = 'Balasan Terkirim!';
                            $alert_message = 'Terima kasih atas tanggapan Anda.';
                            break;
                            
                        case 'gagal_validasi':
                            $alert_message = 'Mohon lengkapi semua kolom (nama dan isi) sebelum mengirim.';
                            break;
                            
                        case 'gagal_sistem':
                            $alert_message = 'Maaf, terjadi gangguan pada server database.';
                            break;
                    }
                ?>
                <div class="alert <?php echo $alert_class; ?> rounded-4 shadow-sm border p-4 mb-5 d-flex align-items-center mt-4" role="alert">
                    <i class="<?php echo $alert_icon; ?> fa-2x me-3"></i>
                    <div>
                        <h4 class="alert-heading fw-bold mb-1" style="font-size: 1.1rem;"><?php echo $alert_title; ?></h4>
                        <p class="mb-0 small"><?php echo $alert_message; ?></p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Navigasi Kembali -->
            <div class="mb-5 mt-4 d-flex justify-content-start">
                <a href="forum.php" class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold shadow-lg hover-lift-btn transition-all d-inline-flex align-items-center border-0" style="background-color: #ffffff;">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Forum
                </a>
            </div>

            <!-- KONTEN TOPIK UTAMA -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5 bg-white topic-card">
                <div class="card-body p-4 p-md-5">
                    <!-- Header Penulis -->
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-light">
                        <div class="avatar-circle bg-gradient-primary text-white fs-4 fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm me-3" style="width: 60px; height: 60px; border-radius: 50%;">
                            <?php echo strtoupper(substr($topik['nama_pembuat'], 0, 1)); ?>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0 font-outfit"><?php echo htmlspecialchars($topik['nama_pembuat']); ?></h5>
                            <span class="badge bg-primary-subtle text-primary mt-1 rounded-pill border border-primary-subtle" style="font-size: 0.7rem;">Penulis Topik</span>
                        </div>
                    </div>
                    
                    <!-- Isi Konten -->
                    <div class="article-content fs-5 text-secondary font-outfit" style="line-height: 1.8; white-space: pre-line;">
                        <?php echo nl2br(htmlspecialchars($topik['isi'])); ?>
                    </div>
                </div>
                
                <!-- Footer Card (Statistik & Tombol) -->
                <div class="card-footer bg-light border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted fw-bold"><i class="fas fa-reply me-2 text-primary"></i> <?php echo count($balasan); ?> Balasan</small>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold transition-all hover-shadow" onclick="document.getElementById('form-balasan').scrollIntoView({behavior: 'smooth'})">
                        <i class="fas fa-pen me-1"></i> Beri Balasan
                    </button>
                </div>
            </div>

            <!-- DAFTAR BALASAN -->
            <?php if (!empty($balasan)): ?>
                <div class="d-flex align-items-center mb-4">
                    <h5 class="fw-bold text-dark m-0 font-outfit ps-3 border-start border-4 border-primary">Komentar & Diskusi</h5>
                </div>
                
                <div class="vstack gap-4 mb-5">
                    <?php foreach($balasan as $item): ?>
                        <div class="card border-0 shadow-sm rounded-4 reply-card bg-white transition-all">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light text-primary border fw-bold d-flex align-items-center justify-content-center flex-shrink-0 me-3 shadow-sm" style="width: 45px; height: 45px; border-radius: 50%;">
                                            <?php echo strtoupper(substr($item['nama_pembalas'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 font-outfit"><?php echo htmlspecialchars($item['nama_pembalas']); ?></h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                <i class="far fa-clock me-1"></i> <?php echo date('d M Y • H:i', strtotime($item['tanggal_dibuat'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-text text-secondary mb-0 ps-5 ms-2 font-outfit" style="line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($item['isi_balasan'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Tampilan Kosong -->
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light mb-5">
                    <div class="mb-3 p-3 bg-light rounded-circle d-inline-block">
                        <i class="far fa-comment-dots fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-secondary mb-1 font-outfit">Belum Ada Balasan</h5>
                    <p class="text-muted small mb-0">Jadilah yang pertama menanggapi topik ini!</p>
                </div>
            <?php endif; ?>

            <!-- FORM BALASAN -->
            <div id="form-balasan" class="card border-0 shadow-lg rounded-4 bg-white mt-5 overflow-hidden">
                <div class="card-header text-white p-4 border-0" style="background: linear-gradient(135deg, #1a237e 0%, #283593 100%);">
                    <h5 class="m-0 fw-bold font-outfit d-flex align-items-center">
                        <i class="fas fa-pen-nib me-2"></i> Tulis Balasan Anda
                    </h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="proses_forum_frontend.php?aksi=tambah_balasan" method="POST">
                        <input type="hidden" name="id_topik" value="<?php echo $id_topik; ?>">
                        
                        <div class="mb-4">
                            <label for="nama_pembalas" class="form-label small fw-bold text-muted text-uppercase">Nama Anda</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-primary"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2 bg-light shadow-none" id="nama_pembalas" name="nama_pembalas" placeholder="Nama Lengkap" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="isi_balasan" class="form-label small fw-bold text-muted text-uppercase">Isi Komentar</label>
                            <textarea class="form-control bg-light shadow-none" id="isi_balasan" name="isi_balasan" rows="5" placeholder="Tulis tanggapan Anda dengan sopan..." required style="font-size: 0.95rem; resize: none;"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm hover-lift-btn transition-all">
                            <i class="fas fa-paper-plane me-2"></i> KIRIM BALASAN
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- CSS Tambahan (Inline) -->
<style>
    .font-outfit { font-family: 'Poppins', sans-serif; }
    .font-teko { font-family: 'Teko', sans-serif; }

    /* Animations & Effects */
    .hover-lift-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(26, 35, 126, 0.2) !important;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 5px 15px rgba(26, 35, 126, 0.15);
        background-color: #1a237e;
        color: white;
        border-color: #1a237e;
    }

    /* Form Styling */
    .form-control:focus {
        box-shadow: none;
        border-color: #1a237e;
        background-color: #fff !important;
    }
    .input-group-text { border-right: none; }
    
    /* Card Custom Styles */
    .bg-gradient-primary { background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%); }
    
    .bg-primary-subtle {
        background-color: rgba(26, 35, 126, 0.1);
        color: #1a237e !important;
    }
    
    .topic-card {
        border-top: 5px solid #ffca28 !important; /* Aksen Kuning di atas */
    }
    
    .reply-card {
        border-left: 4px solid rgba(26, 35, 126, 0.1) !important;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .reply-card:hover {
        transform: translateX(5px);
        border-left-color: #1a237e !important;
    }
    
    .transition-all { transition: all 0.3s ease; }
</style>

<?php require_once 'templates/footer.php'; ?>