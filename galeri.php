<?php 
// Set judul halaman dinamis
$pageTitle = "Galeri Kegiatan";

require_once 'templates/header.php'; 

// === LOGIKA PENGAMBILAN DATA (Lokal & Teroptimasi) ===
$galeri_from_db = fetchAll($pdo, 'galeri', ['orderBy' => 'tanggal_upload DESC']);
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
        <i class="fas fa-images" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
        <i class="fas fa-camera-retro" style="position: absolute; bottom: -10%; left: -5%; font-size: 15rem; transform: rotate(15deg); color: white;"></i>
    </div>

    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif; letter-spacing: 1.5px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    GALERI KEGIATAN
                </h1>
                <p class="lead opacity-90 mx-auto fs-6" style="max-width: 600px; font-weight: 300;">
                    Dokumentasi visual momen-momen terbaik dan kegiatan Radio Kominfo Dogiyai.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN UTAMA -->
<main class="container position-relative" style="margin-top: -40px; z-index: 10; padding-bottom: 60px;">
    
    <!-- Filter Media (Floating) -->
    <div class="row justify-content-center mb-5">
        <div class="col-auto">
            <div class="bg-white rounded-pill shadow-lg p-2 d-flex gap-2 border border-light">
                <button class="btn btn-sm btn-primary rounded-pill px-4 fw-bold active-filter shadow-sm transition-all" onclick="filterGaleri('all')">
                    <i class="fas fa-th-large me-1"></i> Semua
                </button>
                <button class="btn btn-sm btn-outline-light text-muted rounded-pill px-4 fw-bold transition-all hover-primary" onclick="filterGaleri('foto')">
                    <i class="fas fa-camera me-1"></i> Foto
                </button>
                <button class="btn btn-sm btn-outline-light text-muted rounded-pill px-4 fw-bold transition-all hover-primary" onclick="filterGaleri('video')">
                    <i class="fas fa-video me-1"></i> Video
                </button>
            </div>
        </div>
    </div>

    <!-- Grid Galeri -->
    <div class="row g-4">
        <?php if (!empty($galeri_from_db)): ?>
            <?php foreach ($galeri_from_db as $item): ?>
                <?php 
                    // Sanitisasi Judul untuk JS onclick (mencegah error karena kutip satu/dua)
                    // addslashes penting agar judul seperti "Jum'at" tidak merusak script JS
                    $judul_js = htmlspecialchars(addslashes($item['judul']), ENT_QUOTES);
                ?>
                <div class="col-md-6 col-lg-4 gallery-item-col fade-in" data-type="<?php echo htmlspecialchars($item['tipe_media']); ?>">
                    <div class="gallery-card h-100 bg-white rounded-4 shadow-sm overflow-hidden border border-light position-relative group hover-lift">
                        
                        <?php if($item['tipe_media'] == 'foto'): ?>
                            <!-- === ITEM FOTO === -->
                            <a href="javascript:void(0);" onclick="openImageModal('admin-radio/uploads/<?php echo htmlspecialchars($item['file_media']); ?>', '<?php echo $judul_js; ?>'); return false;" class="d-block h-100 text-decoration-none">
                                <div class="gallery-img-wrapper position-relative overflow-hidden" style="height: 250px;">
                                    <img src="admin-radio/uploads/<?php echo htmlspecialchars($item['file_media']); ?>" 
                                         class="gallery-img w-100 h-100 object-fit-cover transition-transform duration-500" 
                                         alt="<?php echo htmlspecialchars($item['judul']); ?>"
                                         loading="lazy"
                                         onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                    
                                    <!-- Overlay Hover -->
                                    <div class="gallery-overlay d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <div class="icon-circle bg-white text-primary rounded-circle mb-3 shadow-lg d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; transform: scale(0); transition: transform 0.3s ease;">
                                            <i class="fas fa-search-plus fa-lg"></i>
                                        </div>
                                        <h6 class="text-white fw-bold mb-1 translate-y-4 transition-all delay-100 font-outfit"><?php echo htmlspecialchars($item['judul']); ?></h6>
                                        <small class="text-white-50 translate-y-4 transition-all delay-200">Klik untuk memperbesar</small>
                                    </div>
                                    
                                    <div class="media-type-badge position-absolute top-0 end-0 m-3 bg-dark bg-opacity-50 text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px; backdrop-filter: blur(4px);">
                                        <i class="fas fa-camera fa-sm"></i>
                                    </div>
                                </div>
                            </a>

                        <?php else: // Tipe Video ?>
                            <!-- === ITEM VIDEO === -->
                            <?php 
                                $video_url = $item['file_media'];
                                $video_id = '';
                                $thumbnail = "https://placehold.co/400x300/1a237e/FFFFFF?text=VIDEO";
                                
                                // Ekstraksi ID YouTube untuk Thumbnail (Hanya untuk preview gambar statis dari server YouTube)
                                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
                                    $video_id = $match[1];
                                    $thumbnail = "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg";
                                }
                                
                                // Kirim URL asli ke JS (biar JS yang handle parsing ID-nya lebih aman)
                                // Gunakan htmlspecialchars untuk mencegah XSS jika URL aneh
                                $js_video_param = htmlspecialchars($video_url, ENT_QUOTES); 
                            ?>
                            
                            <!-- PERBAIKAN PENTING: onclick memanggil openVideoModal dengan parameter URL video -->
                            <a href="javascript:void(0);" onclick="openVideoModal('<?php echo $js_video_param; ?>', '<?php echo $judul_js; ?>'); return false;" class="d-block h-100 text-decoration-none">
                                <div class="gallery-img-wrapper position-relative overflow-hidden" style="height: 250px;">
                                    <img src="<?php echo $thumbnail; ?>" 
                                         class="gallery-img w-100 h-100 object-fit-cover transition-transform duration-500" 
                                         alt="<?php echo htmlspecialchars($item['judul']); ?>"
                                         loading="lazy"
                                         onerror="this.src='https://placehold.co/400x300/1a237e/FFFFFF?text=VIDEO'">
                                    
                                    <!-- Overlay Hover -->
                                    <div class="gallery-overlay d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <div class="icon-circle bg-danger text-white rounded-circle mb-3 shadow-lg d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; transform: scale(0); transition: transform 0.3s ease;">
                                            <i class="fas fa-play fa-lg ps-1"></i>
                                        </div>
                                        <h6 class="text-white fw-bold mb-1 translate-y-4 transition-all delay-100 font-outfit"><?php echo htmlspecialchars($item['judul']); ?></h6>
                                        <small class="text-white-50 translate-y-4 transition-all delay-200">Klik untuk memutar</small>
                                    </div>
                                    
                                    <div class="media-type-badge position-absolute top-0 end-0 m-3 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                        <i class="fas fa-video fa-sm"></i>
                                    </div>
                                </div>
                            </a>
                        <?php endif; ?>

                        <!-- Info Card (Bawah) -->
                        <div class="p-3 bg-white border-top border-light">
                            <h6 class="fw-bold text-dark text-truncate mb-1 font-outfit"><?php echo htmlspecialchars($item['judul']); ?></h6>
                            <small class="text-muted text-truncate d-block" style="font-size: 0.85rem;"><?php echo htmlspecialchars($item['keterangan']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 shadow-sm border border-light mx-auto" style="max-width: 600px;">
                    <div class="mb-4 p-4 bg-light rounded-circle d-inline-block">
                        <i class="fas fa-images fa-4x text-muted opacity-50"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-2 font-outfit">Belum Ada Dokumentasi</h4>
                    <p class="text-muted mb-0">Galeri foto dan video kegiatan akan segera kami perbarui.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- MODAL VIDEO -->
<!-- Pastikan ID ini sesuai dengan yang dipanggil di script.js (videoModal) -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-black py-2 px-3">
                <h6 class="modal-title text-white font-outfit" id="videoModalTitle">Putar Video</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopVideo()"></button>
            </div>
            <div class="modal-body p-0 bg-black">
                <div class="ratio ratio-16x9">
                    <!-- Iframe kosong, src akan diisi oleh Javascript saat modal dibuka -->
                    <iframe id="videoFrame" src="" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Tambahan (Inline) -->
<style>
    /* Font Styling */
    .font-outfit { font-family: 'Poppins', sans-serif; }
    .font-teko { font-family: 'Teko', sans-serif; }

    /* Gallery Card Animations */
    .gallery-img { transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
    .group:hover .gallery-img { transform: scale(1.1); }
    
    .gallery-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.6); /* Dark slate overlay */
        backdrop-filter: blur(2px);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .group:hover .gallery-overlay { opacity: 1; }
    
    .group:hover .icon-circle { transform: scale(1); }
    
    /* Title & Text Animation Logic */
    .translate-y-4 { 
        transform: translateY(20px); 
        opacity: 0; /* Default invisible */
    }
    .group:hover .translate-y-4 { 
        transform: translateY(0); 
        opacity: 1 !important; /* Visible on hover */
    }
    
    /* Helper Delay Classes */
    .delay-100 { transition-delay: 0.1s; }
    .delay-200 { transition-delay: 0.2s; }
    .transition-all { transition: all 0.3s ease; }
    
    .hover-primary:hover {
        background-color: rgba(26, 35, 126, 0.1);
        color: #1a237e !important;
        border-color: #1a237e;
    }
    
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }
    
    .fade-in { transition: opacity 0.3s ease; }
</style>

<?php require_once 'templates/footer.php'; ?>