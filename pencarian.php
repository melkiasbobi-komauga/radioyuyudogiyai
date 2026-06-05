<?php 
$pageTitle = "Hasil Pencarian";
require_once 'templates/header.php'; 

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$hasil_pencarian = [];

if (!empty($keyword)) {
    // Pencarian Aman dengan PDO
    try {
        $sql = "SELECT * FROM berita WHERE judul LIKE :key OR konten LIKE :key ORDER BY tanggal_publikasi DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['key' => "%$keyword%"]);
        $hasil_pencarian = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silent fail or log error
    }
}
?>

<section class="page-header position-relative d-flex align-items-center justify-content-center" style="width: 100vw; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); margin-top: -25px; padding-top: 100px !important; padding-bottom: 80px !important; overflow: hidden;">
    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif;">HASIL PENCARIAN</h1>
        <p class="lead opacity-90 mx-auto fs-6">
            <?php if (!empty($keyword)): ?>
                Menampilkan hasil untuk: <strong class="text-warning">"<?php echo htmlspecialchars($keyword); ?>"</strong>
            <?php else: ?>
                Silakan masukkan kata kunci.
            <?php endif; ?>
        </p>
    </div>
</section>

<main class="container position-relative" style="margin-top: -40px; z-index: 10; padding-bottom: 60px;">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-pill overflow-hidden bg-white">
                <div class="card-body p-1">
                    <form action="pencarian.php" method="GET" class="d-flex align-items-center">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 shadow-none py-3 px-2" placeholder="Cari berita lain..." value="<?php echo htmlspecialchars($keyword); ?>" required>
                        <button class="btn btn-primary rounded-pill px-4 fw-bold m-1 shadow-sm hover-lift-btn" type="submit">Cari</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($hasil_pencarian)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 pb-5">
            <?php foreach ($hasil_pencarian as $berita): 
                $slug = create_slug($berita['judul']);
                $url = "baca_berita.php?id=" . $berita['id'] . "&judul=" . $slug;
                $excerpt = substr(strip_tags($berita['konten']), 0, 100) . '...';
            ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm news-card hover-lift transition-all">
                        <a href="<?php echo $url; ?>" class="text-decoration-none text-dark d-block overflow-hidden position-relative img-wrapper" style="border-radius: 16px 16px 0 0;">
                            <img src="admin-radio/uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" class="card-img-top news-img w-100 h-100 object-fit-cover" loading="lazy" onerror="this.src='https://placehold.co/400x250?text=No+Image'">
                        </a>
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="card-title news-title mb-3 font-outfit fw-bold"><a href="<?php echo $url; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($berita['judul']); ?></a></h5>
                            <p class="card-text text-secondary small news-excerpt flex-grow-1 mb-4"><?php echo $excerpt; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="w-100 text-center py-5">
            <h3 class="fw-bold text-dark mb-3 font-outfit">Tidak Ditemukan</h3>
            <p class="text-muted">Maaf, tidak ada berita yang cocok dengan kata kunci tersebut.</p>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'templates/footer.php'; ?>