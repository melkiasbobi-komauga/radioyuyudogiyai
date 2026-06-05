<?php 
$pageTitle = "Arsip Berita";
require_once 'templates/header.php'; 

// === LOGIKA PAGINASI (PDO) ===
$limit = 3; // Menampilkan 3 berita per halaman (DIUBAH DARI 9)
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Hitung total berita
$stmt_count = $pdo->query("SELECT COUNT(*) FROM berita");
$total_data = $stmt_count->fetchColumn();
$total_pages = ceil($total_data / $limit);

// Ambil data berita
$stmt = $pdo->prepare("SELECT * FROM berita ORDER BY tanggal_publikasi DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$berita_from_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- HERO SECTION -->
<section class="page-header position-relative d-flex align-items-center justify-content-center" style="width: 100vw; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); margin-top: -25px; padding-top: 100px !important; padding-bottom: 80px !important; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; opacity: 0.05; pointer-events: none;">
        <i class="fas fa-newspaper" style="position: absolute; top: -10%; right: -5%; font-size: 15rem; transform: rotate(-15deg); color: white;"></i>
    </div>
    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif;">ARSIP BERITA</h1>
                <p class="lead opacity-90 mx-auto fs-6">Informasi terkini seputar pembangunan dan kabar dari Kabupaten Dogiyai.</p>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN UTAMA -->
<main class="container position-relative" style="margin-top: -40px; z-index: 10; padding-bottom: 60px;">
    
    <!-- Widget Pencarian -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-pill overflow-hidden bg-white search-bar-wrapper">
                <div class="card-body p-1">
                    <form action="pencarian.php" method="GET" class="d-flex align-items-center">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 shadow-none py-3 px-2" placeholder="Cari berita..." required>
                        <button class="btn btn-primary rounded-pill px-4 fw-bold m-1 shadow-sm" type="submit">Cari</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Berita -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 pb-5">
        <?php if (!empty($berita_from_db)): ?>
            <?php foreach ($berita_from_db as $berita): 
                $slug = create_slug($berita['judul']);
                $url = "baca_berita.php?id=" . $berita['id'] . "&judul=" . $slug;
                $excerpt = strip_tags($berita['konten']);
                if (strlen($excerpt) > 100) $excerpt = substr($excerpt, 0, 100) . '...';
                $tgl = strtotime($berita['tanggal_publikasi']);
            ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm news-card hover-lift overflow-hidden">
                        
                        <!-- Gambar Berita -->
                        <div class="card-img-wrapper position-relative">
                            <a href="<?php echo $url; ?>" class="d-block h-100">
                                <img src="admin-radio/uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" 
                                     class="card-img-top news-img w-100 h-100 object-fit-cover" 
                                     alt="Thumbnail" loading="lazy" 
                                     onerror="this.src='https://placehold.co/400x250?text=No+Image'">
                            </a>
                            <!-- Badge Tanggal -->
                            <div class="date-badge position-absolute top-0 start-0 m-3 bg-white text-center rounded-3 shadow-sm px-2 py-1">
                                <span class="d-block fw-bold text-dark fs-5 lh-1"><?php echo date('d', $tgl); ?></span>
                                <span class="d-block text-uppercase small text-muted fw-bold" style="font-size: 0.65rem;"><?php echo date('M', $tgl); ?></span>
                            </div>
                        </div>

                        <!-- Konten Berita -->
                        <div class="card-body p-4 d-flex flex-column">
                            <!-- Meta Info (Penulis & Waktu) -->
                            <div class="d-flex align-items-center mb-2 text-muted small" style="font-size: 0.75rem;">
                                <span class="me-3"><i class="far fa-user me-1 text-primary"></i> <?php echo htmlspecialchars($berita['penulis']); ?></span>
                                <span><i class="far fa-clock me-1 text-warning"></i> <?php echo date('H:i', $tgl); ?> WIT</span>
                            </div>

                            <h5 class="card-title news-title mb-3 font-outfit fw-bold lh-sm">
                                <a href="<?php echo $url; ?>" class="text-decoration-none text-dark hover-text-primary transition-colors line-clamp-2">
                                    <?php echo htmlspecialchars($berita['judul']); ?>
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted small news-excerpt flex-grow-1 mb-4 line-clamp-3">
                                <?php echo $excerpt; ?>
                            </p>
                            
                            <a href="<?php echo $url; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold align-self-start mt-auto">
                                Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-light rounded-3">
                    <i class="far fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5 class="text-secondary fw-bold">Belum Ada Berita</h5>
                    <p class="text-muted mb-0">Berita terbaru akan segera hadir.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Navigasi Paginasi -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link rounded-pill px-3 me-2 border-0 shadow-sm" href="?page=<?php echo $page - 1; ?>">
                    <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                </a>
            </li>
            
            <?php 
            // Logika Paginasi Pintar
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            if ($start_page > 1) {
                echo '<li class="page-item"><a class="page-link border-0 shadow-sm rounded-circle mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="?page=1">1</a></li>';
                if ($start_page > 2) echo '<li class="page-item disabled"><span class="page-link border-0 bg-transparent">...</span></li>';
            }

            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link border-0 shadow-sm rounded-circle mx-1 d-flex align-items-center justify-content-center fw-bold" 
                       style="width: 38px; height: 38px; <?php echo ($page == $i) ? 'background-color: #1a237e;' : ''; ?>" 
                       href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php 
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link border-0 bg-transparent">...</span></li>';
                echo '<li class="page-item"><a class="page-link border-0 shadow-sm rounded-circle mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="?page='.$total_pages.'">'.$total_pages.'</a></li>';
            }
            ?>

            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link rounded-pill px-3 ms-2 border-0 shadow-sm" href="?page=<?php echo $page + 1; ?>">
                    Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</main>

<style>
    /* CSS Khusus Halaman Berita */
    .font-outfit { font-family: 'Poppins', sans-serif; }
    
    .news-card {
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
        border-radius: 16px; /* Sudut lebih bulat */
    }
    
    .news-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; 
    }

    .card-img-wrapper {
        height: 200px; /* Tinggi gambar konsisten */
        overflow: hidden;
    }

    .news-img {
        transition: transform 0.5s ease;
    }

    .news-card:hover .news-img {
        transform: scale(1.05); /* Zoom in halus saat hover */
    }

    .date-badge {
        min-width: 50px;
        backdrop-filter: blur(4px); /* Efek kaca di belakang tanggal */
        background-color: rgba(255, 255, 255, 0.9);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .hover-text-primary:hover {
        color: #1a237e !important;
        text-decoration: underline !important;
    }

    .pagination .page-link { 
        color: #1a237e; 
        transition: all 0.2s ease;
    }
    
    .pagination .page-item.active .page-link { 
        background-color: #1a237e; 
        color: white; 
    }
    
    .pagination .page-item.disabled .page-link { 
        color: #ccc; 
        background-color: #f8f9fa;
    }

    /* Efek Search Bar */
    .search-bar-wrapper {
        transition: transform 0.3s ease;
    }
    .search-bar-wrapper:focus-within {
        transform: scale(1.02);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
</style>

<?php require_once 'templates/footer.php'; ?>