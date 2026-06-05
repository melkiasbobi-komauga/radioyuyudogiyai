<?php
// Set header HTTP 404 Not Found agar mesin pencari tahu halaman ini tidak ada
http_response_code(404);

// Deteksi Base URL agar aset (gambar, css) bisa dimuat dengan benar dari folder manapun
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// PENTING: Sesuaikan '/Radio-yuyu-website/' dengan nama folder root proyek Anda di server.
// Jika di hosting root domain (public_html), ubah jadi '/' atau kosongkan.
// Contoh lokal: $base_url = $protocol . "://" . $host . "/Radio-yuyu-website/";
// Contoh hosting: $base_url = $protocol . "://" . $host . "/";

// Kita coba deteksi otomatis folder tempat skrip ini berada
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base_url = $protocol . "://" . $host . $path . "/";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Halaman Tidak Ditemukan</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>images/logo.png">
    
    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Teko:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a237e;
            --secondary: #ff6f00;
            --text: #1e293b;
            --bg: #f1f5f9;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0;
        }

        .error-container {
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 3rem;
            max-width: 600px;
            width: 90%;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.5);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .icon-404 {
            font-size: 4rem;
            color: var(--secondary);
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .error-code {
            font-family: 'Teko', sans-serif;
            font-size: 8rem;
            line-height: 0.8;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #283593);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 1rem 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .error-title {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .error-desc {
            color: #64748b;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--primary), #283593);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            color: white;
            background: linear-gradient(135deg, #283593, var(--primary));
        }

        .btn-help {
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-help:hover {
            background-color: var(--primary);
            color: white;
        }

        /* Background decoration */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            opacity: 0.1;
            z-index: 1;
        }
        .c1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .c2 { width: 200px; height: 200px; bottom: -50px; right: -50px; }

    </style>
</head>
<body>
    
    <!-- Background Decor -->
    <div class="bg-circle c1"></div>
    <div class="bg-circle c2"></div>

    <div class="error-container">
        <div class="error-card">
            <div class="icon-404">
                <i class="fas fa-satellite-dish"></i>
            </div>
            
            <h1 class="error-code">404</h1>
            <h2 class="error-title">Halaman Tidak Ditemukan</h2>
            
            <p class="error-desc">
                Maaf, halaman yang Anda cari mungkin telah dihapus, dipindahkan, atau alamat URL yang Anda masukkan salah.
            </p>
            
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="<?php echo $base_url; ?>" class="btn-home">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                <a href="<?php echo $base_url; ?>kontak.php" class="btn-help">
                    <i class="fas fa-question-circle"></i> Bantuan
                </a>
            </div>
        </div>
        
        <div class="mt-4 text-muted small">
            &copy; <?php echo date('Y'); ?> Radio Kominfo Dogiyai
        </div>
    </div>

</body>
</html>