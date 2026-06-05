<?php 
$pageTitle = "Hubungi Kami";
require_once 'templates/header.php'; 

// Ambil data kontak & peta dari database
$kontak_db = [];
if (isset($pdo)) {
    $stmt = $pdo->query("SELECT * FROM profil_website WHERE nama_pengaturan IN ('alamat_kantor', 'email_kontak', 'telepon_kontak', 'iframe_peta')");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $kontak_db[$row['nama_pengaturan']] = $row['isi_pengaturan'];
    }
}

// Default peta jika kosong (bisa diganti koordinat default kantor)
$iframe_peta = $kontak_db['iframe_peta'] ?? '';
?>

<!-- HERO SECTION -->
<section class="page-header position-relative d-flex align-items-center justify-content-center" style="width: 100vw; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); margin-top: -25px; padding-top: 100px !important; padding-bottom: 80px !important; overflow: hidden;">
    <div class="container text-center text-white position-relative" style="z-index: 2;">
        <h1 class="article-title mb-2 fw-bold display-5" style="font-family: 'Teko', sans-serif;">HUBUNGI KAMI</h1>
        <p class="lead opacity-90 mx-auto fs-6">Kami siap mendengar masukan dan saran Anda.</p>
    </div>
</section>

<!-- KONTEN UTAMA -->
<main class="container position-relative" style="z-index: 10; padding-bottom: 60px; margin-top: -40px;">
    
    <!-- Notifikasi -->
    <?php if (isset($_GET['status'])): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="alert alert-info rounded-4 shadow-sm border p-3 bg-white text-center">
                    <p class="mb-0 small fw-bold">
                        <?php 
                            if($_GET['status']=='sukses') echo "<i class='fas fa-check-circle text-success me-2'></i>Pesan Anda berhasil terkirim.";
                            elseif($_GET['status']=='gagal_validasi') echo "<i class='fas fa-exclamation-triangle text-warning me-2'></i>Mohon lengkapi semua data wajib.";
                            else echo "<i class='fas fa-times-circle text-danger me-2'></i>Terjadi kesalahan sistem.";
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="row g-4 justify-content-center mt-5"> 
        
        <!-- KOLOM KIRI: INFO KONTAK (Diperlebar sedikit agar seimbang) -->
        <div class="col-lg-7"> 
            <!-- 1. Card Info Kontak -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4 hover-lift h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-4 font-outfit border-bottom pb-2">Informasi Kantor</h5>
                    <ul class="list-unstyled d-grid gap-3">
                        <li class="d-flex align-items-start">
                            <div class="icon-box bg-primary-subtle text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Alamat</small>
                                <p class="text-dark mb-0 fw-medium"><?php echo htmlspecialchars($kontak_db['alamat_kantor'] ?? '-'); ?></p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start">
                            <div class="icon-box bg-primary-subtle text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-envelope"></i></div>
                            <div>
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Email</small>
                                <p class="text-dark mb-0 fw-medium"><?php echo htmlspecialchars($kontak_db['email_kontak'] ?? '-'); ?></p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start">
                            <div class="icon-box bg-primary-subtle text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Telepon</small>
                                <p class="text-dark mb-0 fw-medium"><?php echo htmlspecialchars($kontak_db['telepon_kontak'] ?? '-'); ?></p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: FORM KONTAK (Diubah menjadi col-lg-4 agar ramping seperti forum) -->
        <div class="col-lg-4"> 
            <div class="card border border-light shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-header text-white p-4 border-0" style="background: linear-gradient(135deg, #1a237e 0%, #283593 100%);">
                    <h4 class="card-title m-0 fw-bold font-outfit"><i class="fas fa-paper-plane me-2"></i> Kirim Pesan</h4>
                    <p class="mb-0 small opacity-75 mt-1">Kami akan membalas pesan Anda melalui email.</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="proses_kontak.php" method="POST">
                        <div class="row g-3">
                            <!-- Nama Pengirim -->
                            <div class="col-12">
                                <label for="nama" class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light border-light" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
                            </div>
                            
                            <!-- Email Pengirim -->
                            <div class="col-12">
                                <label for="email" class="form-label small fw-bold text-muted">Alamat Email</label>
                                <input type="email" class="form-control rounded-3 py-2 bg-light border-light" id="email" name="email" placeholder="nama@email.com" required>
                            </div>

                            <!-- Subjek -->
                            <div class="col-12">
                                <label for="subjek" class="form-label small fw-bold text-muted">Subjek Pesan</label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light border-light" id="subjek" name="subjek" placeholder="Topik pesan..." required>
                            </div>

                            <!-- Isi Pesan -->
                            <div class="col-12">
                                <label for="pesan" class="form-label small fw-bold text-muted">Isi Pesan</label>
                                <textarea class="form-control rounded-3 bg-light border-light" id="pesan" name="pesan" rows="5" placeholder="Tuliskan pesan, saran, atau pertanyaan Anda di sini..." required style="resize: none;"></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm transition-transform hover-scale">
                                    KIRIM PESAN SEKARANG
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- PETA LOKASI (Di Bawah Form, Full Width) -->
    <div class="row mt-5 pt-4 justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white hover-lift">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold text-primary font-outfit mb-0"><i class="fas fa-map-marked-alt me-2"></i> Lokasi Kami</h5>
                </div>
                <div class="card-body p-0">
                    <?php if(!empty($iframe_peta)): ?>
                        <!-- Wrapper responsif untuk iframe -->
                        <div class="ratio ratio-21x9" style="max-height: 450px;">
                            <!-- Menggunakan iframe dari database secara langsung -->
                            <?php echo $iframe_peta; ?>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted p-5" style="height: 350px;">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt fa-3x mb-2 opacity-25"></i>
                                <p class="small m-0">Peta lokasi belum diatur oleh admin.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-top border-light p-3 text-center">
                    <a href="https://maps.google.com" target="_blank" class="text-decoration-none small fw-bold text-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>

</main>

<style>
    .font-outfit { font-family: 'Poppins', sans-serif; }
    
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
    }
    
    .bg-primary-subtle { background-color: rgba(26, 35, 126, 0.1); color: #1a237e !important; }
    
    /* Styling khusus iframe peta agar pas di container */
    .ratio iframe {
        border-radius: 0;
        border: 0;
        width: 100%;
        height: 100%;
    }
</style>

<?php require_once 'templates/footer.php'; ?>