<?php 
require_once 'templates/header.php'; 

// 1. Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>window.location.href='404.php';</script>";
    exit;
}

$id_berita = (int)$_GET['id'];

// 2. Ambil Berita (PDO Prepared Statement - AMAN)
try {
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$id_berita]);
    $berita = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Jika berita tidak ditemukan
if (!$berita) {
    echo "<script>window.location.href='404.php';</script>";
    exit;
}

$slug = create_slug($berita['judul']);
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$img_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . '/admin-radio/uploads/' . htmlspecialchars($berita['gambar']);
$meta_desc = substr(strip_tags($berita['konten']), 0, 160) . "...";
?>

<!-- Meta Tags -->
<meta property="og:type" content="article" />
<meta property="og:url" content="<?php echo $currentUrl; ?>" />
<meta property="og:title" content="<?php echo htmlspecialchars($berita['judul']); ?>" />
<meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>" />
<meta property="og:image" content="<?php echo $img_url; ?>" />

<!-- HERO SECTION -->
<section class="page-header position-relative d-flex align-items-center justify-content-center" style="width: 100vw; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); margin-top: -25px; padding-top: 100px !important; padding-bottom: 60px !important; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; opacity: 0.05; pointer-events: none;">
        <i class="fas fa-newspaper" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
    </div>
    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="article-title mb-3 fw-bold display-6" style="font-family: 'Outfit', sans-serif;"><?php echo htmlspecialchars($berita['judul']); ?></h1>
                <div class="article-meta d-flex justify-content-center align-items-center gap-3 text-white opacity-75 small">
                    <span><i class="far fa-calendar-alt text-warning me-1"></i> <?php echo date('d F Y', strtotime($berita['tanggal_publikasi'])); ?></span>
                    <span>|</span>
                    <span><i class="far fa-user text-warning me-1"></i> <?php echo htmlspecialchars($berita['penulis']); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN BERITA -->
<main class="container pb-5" style="margin-top: -40px; position: relative; z-index: 10;">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="article-container bg-white rounded-4 shadow p-4 p-md-5">
                <div class="mb-4"><a href="berita.php" class="back-nav text-decoration-none text-muted small fw-bold hover-primary transition-all"><i class="fas fa-arrow-left me-2"></i> KEMBALI KE ARSIP</a></div>

                <article>
                    <div class="article-image-wrapper mb-4 rounded-3 overflow-hidden position-relative shadow-sm">
                        <img class="article-image w-100" src="admin-radio/uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>" style="max-height: 450px; object-fit: cover;" onerror="this.src='https://placehold.co/800x400?text=Gambar+Tidak+Tersedia'">
                    </div>
                    <div class="article-caption mb-4 text-center text-muted fst-italic small">Dokumentasi: Radio Kominfo Dogiyai</div>
                    
                    <section class="article-content text-dark" style="font-family: 'Outfit', sans-serif; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                        <?php echo $berita['konten']; // Aman karena input admin (biasanya trusted), atau gunakan HTMLPurifier jika perlu lebih ketat ?>
                    </section>
                </article>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?>