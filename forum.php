<?php 
// Set judul halaman dinamis
$pageTitle = "Forum Komunitas";

require_once 'templates/header.php'; 

// === LOGIKA PENGAMBILAN DATA (Lokal & Teroptimasi) ===
// Mengambil semua topik forum, diurutkan dari yang terbaru
// Menggunakan PDO ($pdo)
$forum_from_db = fetchAll($pdo, 'forum_topik', ['orderBy' => 'tanggal_dibuat DESC']);

// --- LOGIKA STATUS NOTIFIKASI ---
// Menggunakan trim() untuk membersihkan spasi yang tidak sengaja terbawa di URL
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
?>

<!-- OPTIMASI: Preconnect ke CDN -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- HERO SECTION (Header Halaman) -->
<!-- Z-Index 1 agar berada di bawah konten yang melayang -->
<section class="page-header position-relative d-flex align-items-center justify-content-center" style="
    width: 100vw; 
    margin-left: calc(-50vw + 50%); 
    margin-right: calc(-50vw + 50%); 
    background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); 
    margin-top: -25px; 
    padding-top: 100px !important; 
    padding-bottom: 100px !important; /* Dikurangi sedikit agar proporsional */
    overflow: hidden;
    position: relative;
    z-index: 1; 
">
    <!-- Dekorasi Background -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; opacity: 0.05; pointer-events: none;">
        <i class="fas fa-comments" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
        <i class="fas fa-users" style="position: absolute; bottom: -10%; left: -5%; font-size: 15rem; transform: rotate(15deg); color: white;"></i>
    </div>

    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif; letter-spacing: 1.5px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    FORUM KOMUNITAS
                </h1>
                <p class="lead opacity-90 mx-auto fs-6" style="max-width: 600px; font-weight: 300;">
                    Wadah diskusi, berbagi ide, dan aspirasi warga Dogiyai untuk kemajuan bersama.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN UTAMA -->
<!-- Z-Index 10 agar elemen di dalamnya PASTI di atas header -->
<main class="container position-relative" style="z-index: 10; padding-bottom: 60px;">
    
    <!-- Bagian Notifikasi & Info Topik (Floating Section) -->
    <!-- Margin-top negatif (-50px) menarik elemen ini ke atas menutupi batas header -->
    <div class="row justify-content-center mb-5" style="margin-top: -50px;">
        <div class="col-lg-10 col-md-12">
            <?php if (!empty($status)): ?>
                <!-- Tampilkan Notifikasi jika ada status -->
                <?php 
                    // Default values (Kondisi Error/Umum)
                    $alert_class = 'alert-danger border-danger text-danger';
                    $alert_icon = 'fas fa-exclamation-triangle';
                    $alert_title = 'Gagal!';
                    $alert_message = 'Terjadi kesalahan.';

                    // Logika Switch Case yang lebih rapi
                    switch ($status) {
                        case 'sukses_topik':
                            $alert_class = 'alert-success border-success text-success';
                            $alert_icon = 'fas fa-check-circle';
                            $alert_title = 'Topik Berhasil Dibuat!';
                            $alert_message = 'Topik diskusi Anda telah berhasil diterbitkan.';
                            break;
                            
                        case 'gagal_validasi':
                            $alert_title = 'Data Tidak Lengkap';
                            $alert_message = 'Harap lengkapi Nama, Judul, dan Isi Topik sebelum mengirim.';
                            break;
                            
                        case 'gagal_sistem':
                            $alert_title = 'Kesalahan Sistem';
                            $alert_message = 'Maaf, terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                            break;
                    }
                ?>
                <div class="alert <?php echo $alert_class; ?> rounded-4 shadow-lg border p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <i class="<?php echo $alert_icon; ?> fa-2x me-3"></i>
                        <div>
                            <h4 class="alert-heading fw-bold mb-1" style="font-size: 1.1rem;"><?php echo $alert_title; ?></h4>
                            <p class="mb-0 small"><?php echo $alert_message; ?></p>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Badge Statistik (Floating Pill) -->
                <div class="d-flex justify-content-center">
                    <div class="bg-white shadow-lg px-5 py-3 rounded-pill border border-light d-flex align-items-center gap-3 position-relative">
                        <h4 class="fw-bold text-primary m-0 font-outfit d-flex align-items-center" style="font-size: 1.1rem;">
                            <i class="fas fa-comments me-2 text-warning fa-lg"></i> Topik Terbaru
                        </h4>
                        <div class="vr mx-2 bg-secondary opacity-25" style="height: 20px;"></div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-3 py-2">
                            <?php echo count($forum_from_db); ?> Diskusi Aktif
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-5">
        <!-- Kolom Kiri: Daftar Topik -->
        <div class="col-lg-8">
            <div class="vstack gap-4">
                <?php if (!empty($forum_from_db)): ?>
                    <?php foreach($forum_from_db as $topik): ?>
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden forum-card bg-white hover-lift transition-all">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-1 font-outfit lh-sm">
                                        <!-- Link ke detail topik -->
                                        <a href="lihat_topik.php?id=<?php echo $topik['id']; ?>" class="text-decoration-none text-dark forum-title-link stretched-link">
                                            <?php echo htmlspecialchars($topik['judul']); ?>
                                        </a>
                                    </h5>
                                    <!-- Tanggal Post -->
                                    <small class="text-muted text-nowrap ms-3 bg-light px-2 py-1 rounded d-flex align-items-center" style="font-size: 0.75rem;">
                                        <i class="far fa-clock me-1 text-secondary"></i> <?php echo date('d M Y', strtotime($topik['tanggal_dibuat'])); ?>
                                    </small>
                                </div>
                                
                                <!-- Cuplikan Isi Topik -->
                                <p class="card-text text-muted small text-truncate mb-3" style="max-width: 95%;">
                                    <?php 
                                        $excerpt = strip_tags($topik['isi']);
                                        echo (strlen($excerpt) > 120) ? substr($excerpt, 0, 120) . '...' : $excerpt; 
                                    ?>
                                </p>
                                
                                <!-- Footer Card (Info Pembuat & Link) -->
                                <div class="d-flex align-items-center border-top border-light pt-3 mt-3">
                                    <div class="avatar-circle bg-light text-primary border fw-bold d-flex align-items-center justify-content-center flex-shrink-0 me-2 shadow-sm" style="width: 35px; height: 35px; border-radius: 50%;">
                                        <!-- Inisial Pembuat -->
                                        <?php echo strtoupper(substr($topik['nama_pembuat'], 0, 1)); ?>
                                    </div>
                                    <div class="d-flex flex-column lh-1">
                                        <small class="text-muted" style="font-size: 0.65rem;">Dibuat oleh</small>
                                        <span class="fw-bold text-dark small"><?php echo htmlspecialchars($topik['nama_pembuat']); ?></span>
                                    </div>
                                    
                                    <span class="ms-auto text-primary small fw-bold d-flex align-items-center group-hover-arrow transition-colors">
                                        Lihat Diskusi <i class="fas fa-arrow-right ms-2 transition-transform"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Tampilan Kosong -->
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
                        <div class="mb-3 p-3 bg-light rounded-circle d-inline-block">
                            <i class="far fa-comment-dots fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="fw-bold text-secondary mb-1 font-outfit">Belum Ada Topik</h5>
                        <p class="text-muted small">Jadilah yang pertama memulai diskusi!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Form Buat Topik (Sticky) -->
        <div class="col-lg-4">
            <!-- Card Form dengan Sticky Position agar tetap terlihat saat scroll -->
            <div class="sticky-top" style="top: 100px; z-index: 5;">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header text-white p-4 border-0" style="background: linear-gradient(135deg, #1a237e 0%, #283593 100%);">
                        <h5 class="card-title m-0 fw-bold font-outfit d-flex align-items-center">
                            <i class="fas fa-plus-circle me-2"></i> Buat Topik Baru
                        </h5>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <form action="proses_forum_frontend.php?aksi=tambah_topik" method="POST">
                            <!-- Input Nama -->
                            <div class="mb-3">
                                <label for="nama_pembuat" class="form-label small fw-bold text-muted text-uppercase">Nama Anda</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 text-primary"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-2 bg-light shadow-none" id="nama_pembuat" name="nama_pembuat" placeholder="Tulis nama Anda..." required>
                                </div>
                            </div>
                            
                            <!-- Input Judul -->
                            <div class="mb-3">
                                <label for="judul" class="form-label small fw-bold text-muted text-uppercase">Judul Topik</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 text-primary"><i class="fas fa-heading"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-2 bg-light shadow-none" id="judul" name="judul" placeholder="Topik apa yang ingin dibahas?" required>
                                </div>
                            </div>
                            
                            <!-- Input Isi -->
                            <div class="mb-4">
                                <label for="isi" class="form-label small fw-bold text-muted text-uppercase">Isi Diskusi</label>
                                <textarea class="form-control bg-light shadow-none" id="isi" name="isi" rows="5" placeholder="Jelaskan detail topik, pertanyaan, atau aspirasi Anda..." required style="font-size: 0.9rem; resize: none;"></textarea>
                            </div>
                            
                            <!-- Tombol Submit -->
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm hover-lift-btn transition-transform">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Topik
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Info Tambahan Kecil -->
                <div class="mt-4 p-3 bg-light rounded-3 border border-light text-center">
                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">
                        <i class="fas fa-info-circle me-1 text-info"></i> Pastikan topik diskusi sopan dan tidak mengandung SARA.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- CSS Tambahan (Inline) -->
<style>
    /* Font Styling */
    .font-outfit { font-family: 'Poppins', sans-serif; }
    .font-teko { font-family: 'Teko', sans-serif; }

    /* Card Animations */
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }

    /* Link Hover Effect */
    .forum-title-link {
        transition: color 0.2s ease;
    }
    .forum-card:hover .forum-title-link {
        color: #1a237e !important; /* Primary Color */
        text-decoration: underline !important;
    }

    /* Arrow Animation */
    .transition-transform {
        transition: transform 0.3s ease;
    }
    .forum-card:hover .group-hover-arrow i {
        transform: translateX(5px);
    }

    /* Button Animation */
    .hover-lift-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3) !important;
    }

    /* Form Styles */
    .form-control:focus {
        border-color: #1a237e;
        background-color: #fff !important;
    }
    .input-group-text {
        border-right: none;
    }
    
    /* Badge Colors */
    .bg-primary-subtle {
        background-color: rgba(26, 35, 126, 0.1);
        color: #1a237e !important;
    }
    
    /* Scrollbar for Sticky Sidebar on small heights if needed */
    @media (max-height: 800px) {
        .sticky-top {
            position: static !important; /* Disable sticky on short screens to avoid cutting content */
        }
    }
</style>

<?php require_once 'templates/footer.php'; ?>