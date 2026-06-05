<?php 
$pageTitle = "Beranda";
require_once 'templates/header.php'; 

// ==========================================================
// 1. PENGAMBILAN DATA DARI DATABASE
// ==========================================================

// A. Jadwal Siaran
$jadwal_siaran = fetchAll($pdo, 'jadwal', ['orderBy' => 'waktu ASC']);
if (empty($jadwal_siaran)) {
    $jadwal_siaran = [
        ['waktu' => '06:00 - 08:00', 'program' => 'Morning Show', 'penyiar' => 'Yohanis D'],
        ['waktu' => '12:00 - 14:00', 'program' => 'Berita Siang', 'penyiar' => 'Yuliana M']
    ];
}

// B. Program Unggulan
$programUnggulan = fetchAll($pdo, 'program_unggulan', ['orderBy' => 'id ASC']);
$isStaticProgram = empty($programUnggulan);
if ($isStaticProgram) {
    $programUnggulan = [
        ['judul' => 'Suara Pemerintah', 'gambar' => 'suarapemerintah.jpg', 'deskripsi' => 'Informasi resmi & kebijakan Pemkab Dogiyai.'],
        ['judul' => 'Musik Noken', 'gambar' => 'musiknoken.jpg', 'deskripsi' => 'Melestarikan musik khas daerah Papua & Dogiyai.'],
        ['judul' => 'Pendidikan Kita', 'gambar' => 'pendidikankita.jpg', 'deskripsi' => 'Diskusi inspiratif seputar dunia pendidikan.'],
        ['judul' => 'Suara Masyarakat', 'gambar' => 'suaramasyarakat.jpg', 'deskripsi' => 'Wadah aspirasi warga untuk pembangunan.'],
    ];
}

// C. Data Lainnya (Berita, Podcast, Chart, Testimoni)
$latest_news = fetchAll($pdo, 'berita', ['orderBy' => 'tanggal_publikasi DESC', 'limit' => 3]);
$podcast_from_db = fetchAll($pdo, 'podcast', ['orderBy' => 'tanggal_upload DESC', 'limit' => 4]);
$chart_from_db = fetchAll($pdo, 'chart_lagu', ['orderBy' => 'peringkat ASC', 'limit' => 10]);
$testimoni_db = fetchAll($pdo, 'testimoni', ['where' => "WHERE status = 'tampil'", 'orderBy' => 'tanggal_kirim DESC', 'limit' => 3]);

// D. Pengaturan Website (Stream URL & No WA)
$final_stream_url = 'https://b.alhastream.com:5049/radio'; 
$whatsapp_number = '628123456789'; // Default

try {
    if (isset($pdo)) {
        // Ambil URL Stream
        $stmt = $pdo->prepare("SELECT isi_pengaturan FROM profil_website WHERE nama_pengaturan = ?");
        $stmt->execute(['stream_url']);
        $resStream = $stmt->fetch();
        if ($resStream && !empty($resStream['isi_pengaturan'])) {
            $final_stream_url = $resStream['isi_pengaturan'];
        }

        // Ambil Nomor WA
        $stmt = $pdo->prepare("SELECT isi_pengaturan FROM profil_website WHERE nama_pengaturan = ?");
        $stmt->execute(['no_wa']);
        $resWa = $stmt->fetch();
        if ($resWa && !empty($resWa['isi_pengaturan'])) {
            $whatsapp_number = $resWa['isi_pengaturan'];
        }
    }
} catch (PDOException $e) {}

// Format Link WhatsApp
$wa_clean = preg_replace('/[^0-9]/', '', $whatsapp_number);
$wa_link = "https://wa.me/" . $wa_clean . "?text=Halo%20Radio%20Yuyu,%20saya%20mau%20request%20lagu...";
?>

<div id="player-section-container">
    <div class="player-section-wrapper">
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="player-card fade-in w-100 mx-auto">
                        <div class="mb-3 d-flex justify-content-center align-items-center flex-wrap gap-3">
                            <img src="images/logokab.gif" alt="Pemkab" style="height: 50px; object-fit: contain;">
                            <img src="images/logo.png" alt="Radio Yuyu" style="height: 50px; object-fit: contain;">
                            <img src="images/logokominfo.png" alt="Kominfo" style="height: 50px; object-fit: contain;">
                        </div>

                        <h2 class="mt-2 font-teko display-6">ON AIR SEKARANG</h2>
                        <p class="lead font-outfit mb-3 opacity-75 fs-6"><?php echo htmlspecialchars($organization ?? 'Radio Pemerintah Kabupaten Dogiyai'); ?></p>
                        
                        <div class="mt-3">
                            <p class="now-playing text-white border border-light d-inline-block px-4 py-2 rounded-pill bg-white bg-opacity-10 backdrop-blur">
                                <i class="fas fa-music me-2 text-warning"></i> <span id="now-playing">Menunggu Siaran...</span>
                            </p>
                        </div>

                        <div class="player-controls mt-4">
                            <audio id="radio-stream" src="<?php echo htmlspecialchars($final_stream_url); ?>" preload="none"></audio>
                            <button class="play-btn shadow-lg px-5" onclick="togglePlay()">
                                <i class="fas fa-play"></i> <span>DENGARKAN LIVE</span>
                            </button>
                        </div>
                        
                        <div class="mt-3 small opacity-75">
                            <i class="fas fa-users me-1"></i> <span id="listeners">-</span> Pendengar Online
                        </div>
                        
                        <canvas id="audio-visualizer" width="300" height="40" class="mt-3 w-100" style="opacity: 0.8;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wa-floating-container">
    <div class="wa-chat-box shadow-lg" id="wa-chat-box">
        <div class="wa-header bg-success text-white p-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fab fa-whatsapp fa-lg"></i>
                <span class="fw-bold font-outfit">Request Lagu / Salam</span>
            </div>
            <button type="button" class="btn-close btn-close-white" onclick="toggleWhatsApp()"></button>
        </div>
        <div class="wa-body p-3 bg-white text-dark">
            <p class="small text-muted mb-3">Halo! Silakan klik tombol di bawah untuk mengirim request lagu atau salam sapa ke penyiar kami.</p>
            <a href="<?php echo $wa_link; ?>" target="_blank" class="btn btn-success w-100 rounded-pill fw-bold hover-scale">
                <i class="fab fa-whatsapp me-1"></i> Kirim Pesan WA
            </a>
        </div>
    </div>

    <button class="wa-float-btn shadow-lg" onclick="toggleWhatsApp()">
        <i class="fab fa-whatsapp"></i>
    </button>
</div>

<main>
    <div class="container">
        <?php if (isset($_GET['status'])): ?>
         <div class="row justify-content-center mb-5 mt-4">
            <div class="col-lg-12">
                <div class="alert alert-info rounded-4 shadow-sm border-0 p-4 d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
                    <div>
                        <h4 class="alert-heading fw-bold mb-1 fs-5">Informasi</h4>
                        <p class="mb-0 small">
                            <?php 
                                if($_GET['status']=='sukses_newsletter') echo "Terima kasih telah berlangganan newsletter.";
                                elseif($_GET['status']=='sukses_testimoni') echo "Testimoni Anda berhasil dikirim.";
                                else echo "Permintaan Anda sedang diproses.";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <section class="section-padding bg-white">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="section-title text-start"><h2><i class="fas fa-calendar-check me-2"></i> JADWAL SIARAN</h2></div>
                    <div class="schedule-card">
                        <div class="table-responsive">
                            <table class="table table-schedule mb-0">
                                <thead><tr><th>WAKTU</th><th>PROGRAM ACARA</th><th>PENYIAR</th></tr></thead>
                                <tbody>
                                    <?php foreach ($jadwal_siaran as $item): ?>
                                    <tr class="schedule-row">
                                        <td class="time-col"><i class="far fa-clock me-1"></i> <?php echo htmlspecialchars($item['waktu']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($item['program']); ?></strong></td>
                                        <td class="announcer-col"><?php echo htmlspecialchars($item['penyiar']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="section-title text-start"><h2><i class="fas fa-chart-line me-2"></i> TOP HITS</h2></div>
                    <div class="chart-wrapper">
                        <div class="chart-list">
                            <?php if (!empty($chart_from_db)): ?> 
                                <?php foreach($chart_from_db as $lagu): 
                                    $audioFile = isset($lagu['file_audio']) ? $lagu['file_audio'] : ''; 
                                ?>
                                <div class="chart-item play-trigger cursor-pointer" 
                                     data-audio="<?php echo htmlspecialchars($audioFile); ?>" 
                                     data-title="<?php echo htmlspecialchars($lagu['judul_lagu']); ?>">
                                    
                                    <div class="chart-rank"><?php echo htmlspecialchars($lagu['peringkat']); ?></div>
                                    <div class="chart-img-wrapper">
                                        <img src="admin-radio/uploads/<?php echo htmlspecialchars($lagu['cover_album']); ?>" class="chart-cover" alt="Cover" onerror="this.src='https://placehold.co/60x60?text=Music'">
                                    </div>
                                    <div class="chart-info">
                                        <div class="chart-title"><?php echo htmlspecialchars($lagu['judul_lagu']); ?></div>
                                        <div class="chart-artist"><?php echo htmlspecialchars($lagu['artis']); ?></div>
                                    </div>
                                    <div class="chart-action">
                                        <i class="fas fa-play-circle"></i>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-music fa-3x mb-3 opacity-25"></i>
                                    <p class="small">Chart lagu belum diperbarui.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="section-padding bg-light-alt">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2>PROGRAM UNGGULAN</h2>
                <p class="text-muted small">Informasi dan kegiatan prioritas kami</p>
            </div>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($programUnggulan as $program): ?>
                    <?php 
                        $imgPath = ($isStaticProgram) ? 'images/' : 'admin-radio/uploads/';
                        $imgSrc = $imgPath . htmlspecialchars($program['gambar']);
                    ?>
                <div class="col">
                    <div class="program-card h-100">
                        <div class="program-img-container">
                            <img src="<?php echo $imgSrc; ?>" class="program-img" alt="<?php echo htmlspecialchars($program['judul']); ?>" onerror="this.src='https://placehold.co/400x300?text=Program'">
                        </div>
                        
                        <div class="card-body">
                            <h5 class="program-title"><?php echo htmlspecialchars($program['judul']); ?></h5>
                            <div class="divider-small mx-auto my-3"></div>
                            <p class="program-desc"><?php echo htmlspecialchars($program['deskripsi']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-padding bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div class="section-title text-start mb-0"><h2 class="mb-0">PODCAST & REKAMAN</h2></div>
            </div>
            <div class="row g-4">
                <?php if (!empty($podcast_from_db)): ?>
                    <?php foreach($podcast_from_db as $podcast): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="podcast-card card-hover h-100">
                            <div class="position-relative play-trigger cursor-pointer"
                                 data-audio="<?php echo htmlspecialchars($podcast['file_audio']); ?>"
                                 data-title="<?php echo htmlspecialchars($podcast['judul']); ?>">
                                <img src="admin-radio/uploads/<?php echo htmlspecialchars($podcast['file_gambar']); ?>" 
                                     class="card-img-top" 
                                     onerror="this.src='https://placehold.co/400x200?text=Podcast'">
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 hover:bg-opacity-50 transition-all">
                                    <i class="fas fa-play-circle text-white fa-4x drop-shadow-md"></i>
                                </div>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold font-outfit text-dark mb-2"><?php echo htmlspecialchars($podcast['judul']); ?></h5>
                                <small class="text-muted mb-3"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y', strtotime($podcast['tanggal_upload'])); ?></small>
                                
                                <button class="btn btn-outline-primary w-100 mt-auto rounded-pill fw-bold btn-sm play-trigger"
                                    data-audio="<?php echo htmlspecialchars($podcast['file_audio']); ?>"
                                    data-title="<?php echo htmlspecialchars($podcast['judul']); ?>">
                                    <i class="fas fa-play me-1"></i> Putar Episode
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 bg-light rounded-4">
                        <i class="fas fa-microphone-slash fa-3x text-muted mb-3 opacity-50"></i>
                        <p class="text-muted mb-0">Belum ada podcast tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section-padding bg-light-alt">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div class="section-title text-start mb-0">
                    <h2 class="mb-0">BERITA TERKINI</h2>
                    <p class="text-muted small mt-1">Kabar terbaru seputar Dogiyai dan sekitarnya</p>
                </div>
                <a href="berita.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Lihat Semua &raquo;</a>
            </div>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php if (!empty($latest_news)): ?>
                    <?php foreach($latest_news as $berita): 
                         $slug = create_slug($berita['judul']);
                         $url = "baca_berita.php?id=" . $berita['id'] . "&judul=" . $slug;
                    ?>
                        <div class="col">
                            <div class="news-card h-100">
                                <a href="<?php echo $url; ?>" class="news-img-wrapper"> 
                                    <div class="badge-date">
                                        <span class="d-block fw-bold fs-5 lh-1"><?php echo date('d', strtotime($berita['tanggal_publikasi'])); ?></span>
                                        <span class="d-block text-uppercase small" style="font-size: 0.7rem;"><?php echo date('M', strtotime($berita['tanggal_publikasi'])); ?></span>
                                    </div>
                                    <img src="admin-radio/uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" class="news-img" loading="lazy" alt="Berita" onerror="this.src='https://placehold.co/400x250?text=Berita'">
                                    <div class="news-overlay"></div>
                                </a>
                                
                                <div class="card-body d-flex flex-column p-4">
                                    <div class="mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 small fw-bold">
                                            <i class="far fa-newspaper me-1"></i> Berita
                                        </span>
                                    </div>
                                    
                                    <h5 class="news-title mb-3">
                                        <a href="<?php echo $url; ?>" class="text-decoration-none text-dark stretched-link">
                                            <?php echo htmlspecialchars($berita['judul']); ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="news-desc text-muted small mb-4 flex-grow-1">
                                        <?php echo strip_tags(substr($berita['isi_berita'], 0, 120)); ?>...
                                    </p>
                                    
                                    <div class="d-flex align-items-center text-primary fw-bold small mt-auto">
                                        Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 text-muted">
                        <p>Belum ada berita terbaru.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container section-padding pb-0">
        <section class="newsletter-section shadow-lg mb-5">
            <div class="position-absolute top-0 end-0 opacity-10 p-4">
                <i class="fas fa-paper-plane fa-10x text-white transform rotate-12"></i>
            </div>
            <div class="row align-items-center position-relative" style="z-index: 10;">
                <div class="col-lg-6 mb-4 mb-lg-0 text-center text-lg-start">
                    <h3 class="fw-bold mb-2 font-outfit">Dapatkan Info Terbaru!</h3>
                    <p class="mb-0 opacity-90">Berlangganan newsletter kami untuk mendapatkan update.</p>
                </div>
                <div class="col-lg-6">
                    <form action="proses_newsletter.php" method="POST" class="d-flex gap-3">
                        <input type="email" name="email" class="form-control rounded-pill py-3 px-4 border-0 shadow-sm w-100" placeholder="Masukkan email Anda..." required>
                        <button type="submit" class="btn btn-warning rounded-pill py-3 px-4 fw-bold text-dark hover-scale shadow-sm text-nowrap">
                            <i class="fas fa-envelope me-2"></i> Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section id="testimonials" class="testimonials rounded-4 shadow-sm px-4 py-5 position-relative overflow-hidden mb-5" 
                 style="background: radial-gradient(circle at center, #1a237e 0%, #0d1346 100%);">
            
            <div class="text-center mb-5 position-relative" style="z-index: 2;">
                <h2 class="fw-bold text-white font-teko display-6">KATA PENDENGAR</h2>
                <button type="button" class="btn btn-outline-warning rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#testimoniModal">
                    <i class="fas fa-pen me-2"></i> Kirim Testimoni
                </button>
            </div>

            <div class="row g-4 justify-content-center position-relative" style="z-index: 2;">
                <?php if (!empty($testimoni_db)): ?>
                    <?php foreach($testimoni_db as $testi): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 rounded-4 p-4 text-center hover-lift" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                            <div class="mb-3">
                                <div class="bg-white text-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center mx-auto fs-4 fw-bold" style="width: 60px; height: 60px; border: 3px solid #ffca28;">
                                    <?php echo strtoupper(substr($testi['nama_pengirim'], 0, 1)); ?>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-1 text-white font-outfit"><?php echo htmlspecialchars($testi['nama_pengirim']); ?></h5>
                            <small class="text-warning fw-bold text-uppercase d-block mb-3" style="font-size: 0.7rem; letter-spacing: 1px;"><?php echo htmlspecialchars($testi['peran']); ?></small>
                            <p class="text-white-50 fst-italic px-3 mb-0 small">"<?php echo htmlspecialchars($testi['pesan']); ?>"</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-white-50 py-4">
                        <p>Belum ada testimoni.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

</main>

<div class="modal fade" id="testimoniModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 p-4">
                <h5 class="modal-title fw-bold font-outfit"><i class="fas fa-comment-dots me-2"></i> Kirim Testimoni</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="proses_testimoni.php" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nama_pengirim" name="nama_pengirim" placeholder="Nama" required>
                        <label for="nama_pengirim">Nama Lengkap</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="peran" name="peran" placeholder="Peran">
                        <label for="peran">Peran / Profesi</label>
                    </div>
                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="pesan" name="pesan" placeholder="Pesan" style="height: 120px" required></textarea>
                        <label for="pesan">Tulis pendapat Anda...</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold py-3">Kirim Masukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="podcast-player-container" class="fixed-bottom bg-white shadow-lg border-top p-3" style="display: none; z-index: 9999;">
    <div class="container d-flex align-items-center justify-content-between flex-column flex-md-row gap-2">
        <div class="d-flex align-items-center gap-3 overflow-hidden w-100">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                <i class="fas fa-music fa-lg"></i>
            </div>
            <div class="text-truncate flex-grow-1">
                <h6 class="mb-0 fw-bold font-outfit" id="podcast-title">Judul Lagu</h6>
                <small class="text-muted">Sedang memutar...</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 w-100 justify-content-end">
            <audio id="podcast-audio-player" controls class="w-100" style="max-width: 400px; height: 40px;"></audio>
            <button class="btn btn-close bg-light rounded-circle p-2" onclick="closePodcastPlayer()"></button>
        </div>
    </div>
</div>

<style>
    /* Utility agar area yang bisa diklik tampak jelas */
    .cursor-pointer { cursor: pointer; }
    .table-schedule tbody tr:last-child td { border-bottom: none; }
</style>

<?php require_once 'templates/footer.php'; ?>