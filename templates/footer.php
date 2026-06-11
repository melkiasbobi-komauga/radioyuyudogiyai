<?php
// Pastikan koneksi database tersedia ($pdo)
$footer_profil = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM profil_website");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $footer_profil[$row['nama_pengaturan']] = $row['isi_pengaturan'];
        }
    } catch (Exception $e) {
        // Silent error handling
    }
}

// Variabel Data
$f_nama_radio = $stationName ?? "RADIO YUYU KOMINFO";
$f_deskripsi  = $footer_profil['profil_singkat'] ?? "Media pemersatu bangsa.";
$f_alamat     = $footer_profil['alamat_kantor'] ?? "Jl. Trans Papua, Kigamani, Dogiyai";
$f_telepon    = $footer_profil['telepon_kontak'] ?? "(0984) 123-456";
$f_email      = $footer_profil['email_kontak'] ?? "kontak@rakomdogiyai.go.id";

// Potong deskripsi
if (strlen($f_deskripsi) > 200) {
    $f_deskripsi = substr($f_deskripsi, 0, 200) . '...';
}
?>

<!-- Floating Action Buttons -->
<a href="#" class="floating-btn live-chat-btn" id="live-chat-btn" title="Chat Kami">
    <i class="fas fa-comment-dots"></i>
</a>

<a href="#" class="floating-btn back-to-top-btn" id="back-to-top-btn" title="Kembali ke Atas" style="display: none; bottom: 100px; background-color: #1a237e;">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- Image Modal -->
<div id="image-modal" class="image-modal-overlay">
    <span class="close-modal-btn" id="close-modal-btn">&times;</span>
    <div class="modal-content-wrapper">
        <img id="modal-image" class="modal-content-img" src="" alt="Preview">
        <div id="modal-caption" class="modal-caption-text"></div>
    </div>
</div>

<!-- PEMUTAR PODCAST FIXED -->
<div id="podcast-player-container" class="fixed-bottom bg-dark text-white p-2 p-md-3 shadow-lg border-top border-warning" style="display:none; z-index: 1050;">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
            <div class="bg-warning text-dark rounded-circle p-2 me-3 d-flex justify-content-center align-items-center flex-shrink-0" style="width:40px; height:40px;">
                <i class="fas fa-podcast"></i>
            </div>
            <div style="min-width: 0;">
                <small class="text-warning text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Sedang Memutar</small>
                <p id="podcast-title" class="fw-bold mb-0 text-white text-truncate" style="font-size: 0.9rem;">Judul Podcast</p>
            </div>
        </div>
        
        <div class="d-flex align-items-center flex-grow-1 justify-content-end gap-3">
            <audio id="podcast-audio-player" controls class="w-100" style="max-width: 300px; height: 32px; flex-grow: 1;"></audio>
            <button onclick="closePodcastPlayer()" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Tutup Pemutar">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<!-- Footer Utama -->
<footer>
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-5 col-md-6">
                <div class="d-flex align-items-center mb-4">
                    <img src="images/logo.png" alt="Logo" class="me-3 bg-white rounded-circle p-1" style="height: 60px; width: 60px; object-fit: contain;">
                    <div class="brand-text d-flex flex-column justify-content-center">
                        <h5 class="text-white font-teko m-0 lh-1" style="font-size: 1.8rem; letter-spacing: 1px;">
                            <?php echo htmlspecialchars($f_nama_radio); ?>
                        </h5>
                        <small class="text-warning opacity-75 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1.5px; margin-top: 2px;">
                            <?php echo htmlspecialchars($organization ?? 'Suara Dogiyai'); ?>
                        </small>
                    </div>
                </div>
                <p class="small text-white opacity-75 mb-4" style="text-align: justify; line-height: 1.8;">
                    <?php echo htmlspecialchars($f_deskripsi); ?>
                </p>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-4 fw-bold font-outfit position-relative pb-2 d-inline-block border-bottom border-warning">Tautan Cepat</h5>
                <ul class="list-unstyled text-white small mt-2 space-y-2">
                    <li class="mb-2"><a href="./" class="footer-link">Beranda</a></li>
                    <li class="mb-2"><a href="berita.php" class="footer-link">Berita</a></li>
                    <li class="mb-2"><a href="jadwal.php" class="footer-link">Jadwal</a></li>
                    <li class="mb-2"><a href="galeri.php" class="footer-link">Galeri</a></li>
                    <li class="mb-2"><a href="kontak.php" class="footer-link">Kontak</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12">
                <h5 class="text-white mb-4 fw-bold font-outfit position-relative pb-2 d-inline-block border-bottom border-warning">Hubungi Kami</h5>
                <div class="mt-2 text-white opacity-75 small">
                    <p class="mb-3"><i class="fas fa-map-marker-alt me-3 text-warning"></i> <?php echo htmlspecialchars($f_alamat); ?></p>
                    <p class="mb-3"><i class="fas fa-phone me-3 text-warning"></i> <?php echo htmlspecialchars($f_telepon); ?></p>
                    <p class="mb-3"><i class="fas fa-envelope me-3 text-warning"></i> <?php echo htmlspecialchars($f_email); ?></p>
                </div>
            </div>
        </div>
        
        <hr class="my-5 border-white opacity-10">
        
        <div class="row align-items-center pb-4">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="small mb-0 text-white opacity-50 font-outfit">
                    &copy; <span id="current-year"><?php echo date('Y'); ?></span> <strong><?php echo htmlspecialchars($f_nama_radio); ?></strong>. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js?v=<?php echo time(); ?>"></script> 

<script>
    function closePodcastPlayer() {
        const player = document.getElementById('podcast-player-container');
        const audio = document.getElementById('podcast-audio-player');
        if (player) player.style.display = 'none';
        if (audio) { audio.pause(); audio.currentTime = 0; }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const yearSpan = document.getElementById('current-year');
        if(yearSpan && yearSpan.textContent === '') yearSpan.textContent = new Date().getFullYear();
    });
</script>

<style>
    footer { background: linear-gradient(180deg, #0d1346 0%, #000000 100%); color: #cbd5e1; padding-top: 80px; margin-top: 100px; position: relative; }
    .font-teko { font-family: 'Teko', sans-serif; }
    .font-outfit { font-family: 'Poppins', sans-serif; }
    .footer-link { text-decoration: none; color: rgba(255,255,255,0.7); transition: all 0.3s ease; display: block; }
    .footer-link:hover { color: #ffca28 !important; transform: translateX(5px); }
    .image-modal-overlay { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.95); justify-content: center; align-items: center; flex-direction: column; opacity: 0; transition: opacity 0.3s ease; }
    .image-modal-overlay.active { opacity: 1; display: flex; }
    .modal-content-wrapper { max-width: 90%; max-height: 85vh; text-align: center; }
    .modal-content-img { margin: auto; display: block; max-width: 100%; max-height: 80vh; border-radius: 8px; }
    .close-modal-btn { position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer; z-index: 10000; }
    .back-to-top-btn { opacity: 0.8; transition: all 0.3s ease; }
    .back-to-top-btn:hover { opacity: 1; transform: translateY(-5px); }
    
    /* Responsive Podcast Player */
    @media (max-width: 768px) {
        #podcast-player-container {
            padding: 10px !important;
        }
        #podcast-audio-player {
            max-width: 250px !important;
            height: 28px !important;
        }
    }
    
    @media (max-width: 575px) {
        #podcast-player-container {
            padding: 8px !important;
        }
        #podcast-player-container .container-fluid {
            flex-direction: column;
            gap: 8px !important;
        }
        #podcast-audio-player {
            max-width: 100% !important;
            height: 26px !important;
            order: 3;
            width: 100% !important;
        }
        #podcast-title {
            font-size: 0.75rem !important;
        }
    }
</style>

</body>
</html>