<?php 
$pageTitle = "Tentang Kami";
require_once 'templates/header.php'; 

// === PENGAMBILAN DATA (PDO) ===
$profil_db = [];
if (isset($pdo)) {
    $stmt = $pdo->query("SELECT * FROM profil_website WHERE nama_pengaturan IN ('profil_singkat', 'visi', 'misi', 'sejarah')");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $profil_db[$row['nama_pengaturan']] = $row['isi_pengaturan'];
    }
}

$profil_singkat = $profil_db['profil_singkat'] ?? 'Belum tersedia.';
$sejarah = $profil_db['sejarah'] ?? 'Belum tersedia.';
$visi = $profil_db['visi'] ?? 'Belum tersedia.';
$misi = $profil_db['misi'] ?? 'Belum tersedia.';

$tim_kami = fetchAll($pdo, 'tim');
?>

<section class="page-header position-relative d-flex align-items-center justify-content-center" style="width: 100vw; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); margin-top: -25px; padding-top: 100px !important; padding-bottom: 80px !important; overflow: hidden;">
    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif;">TENTANG KAMI</h1>
    </div>
</section>

<main class="container position-relative" style="margin-top: -40px; z-index: 10; padding-bottom: 60px;">
    <div class="card border-0 shadow-lg rounded-4 mb-5 p-4 p-md-5 bg-white overflow-hidden position-relative hover-lift">
        <div class="row align-items-center g-5 position-relative" style="z-index: 1;">
            <div class="col-md-4 text-center">
                <img src="images/logo.png" class="img-fluid rounded-circle shadow-lg bg-white p-2" style="width: 220px; height: 220px; object-fit: contain;">
            </div>
            <div class="col-md-8">
                <h3 class="fw-bold text-primary mb-3 font-outfit">Radio Yuyu Kominfo Dogiyai</h3>
                <h5 class="fw-bold text-dark mb-2 font-outfit">Profil Singkat</h5>
                <p class="text-secondary" style="line-height: 1.8; text-align: justify;"><?php echo htmlspecialchars($profil_singkat); ?></p>
                <h5 class="fw-bold text-dark mt-4 mb-2 font-outfit">Sejarah</h5>
                <p class="text-muted small" style="line-height: 1.8; text-align: justify;"><?php echo nl2br(htmlspecialchars($sejarah)); ?></p>
            </div>
        </div>
    </div>
    
    <section class="mt-5 mb-5">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 bg-white border-start border-5 border-success p-4">
                    <h4 class="fw-bold text-success m-0 font-outfit mb-3">Visi</h4>
                    <p class="fst-italic text-dark mb-0">"<?php echo htmlspecialchars($visi); ?>"</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 bg-white border-start border-5 border-primary p-4">
                    <h4 class="fw-bold text-primary m-0 font-outfit mb-3">Misi</h4>
                    <div class="text-secondary"><?php echo nl2br(htmlspecialchars($misi)); ?></div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-5 pt-4">
        <div class="text-center mt-5 mb-5"><h3 class="fw-bold text-primary mb-2 font-outfit">TIM KAMI</h3></div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 justify-content-center">
            <?php if (!empty($tim_kami)): ?>
                <?php foreach ($tim_kami as $anggota): ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm team-card text-center overflow-hidden rounded-4 hover-lift">
                            <div class="team-img-wrapper position-relative mx-auto mt-4 mb-3">
                                <img src="admin-radio/uploads/<?php echo htmlspecialchars($anggota['foto']); ?>" class="img-fluid rounded-circle border-4 border-warning shadow-md" style="width: 140px; height: 140px; object-fit: cover;" onerror="this.src='https://placehold.co/150x150?text=User'">
                            </div>
                            <div class="card-body pt-0 pb-4 px-3">
                                <h5 class="card-title fw-bold text-dark mb-1 font-outfit"><?php echo htmlspecialchars($anggota['nama_lengkap']); ?></h5>
                                <p class="card-text text-primary small fw-bold text-uppercase mb-3"><?php echo htmlspecialchars($anggota['jabatan']); ?></p>
                                <p class="card-text text-muted small fst-italic mb-0"><?php echo htmlspecialchars($anggota['bio_singkat']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>.font-outfit { font-family: 'Poppins', sans-serif; } .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }</style>
<?php require_once 'templates/footer.php'; ?>