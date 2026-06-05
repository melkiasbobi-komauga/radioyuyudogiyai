<?php
// 1. Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Sertakan file konfigurasi dan auth
// Pastikan path ini benar relatif terhadap lokasi file ini
require_once 'includes/config.php';
require_once 'includes/auth.php';

// 3. Ambil koneksi PDO
$pdo = getDBConnection();

// Tentukan aksi berdasarkan parameter 'aksi' di URL
$aksi = $_GET['aksi'] ?? '';
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

try {
    // --- LOGIKA HAPUS (KHUSUS ADMIN) ---
    if ($aksi == 'hapus' && $id) {
        // Cek Login Admin
        if (!isAdminLoggedIn()) {
            redirect('login.php');
            exit();
        }
        
        $stmt = $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
        $stmt->execute([$id]);
        
        redirect('kelola_newsletter.php?status=sukses_hapus');
        exit();
    } 
    
    // --- LOGIKA TAMBAH SUBSCRIBER (DARI FRONTEND) ---
    elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ambil input email
        $email = trim($_POST['email'] ?? '');
        
        // Sanitisasi input (Hapus tag HTML dan karakter berbahaya)
        $email_sanitized = htmlspecialchars(strip_tags($email), ENT_QUOTES, 'UTF-8');

        // Validasi Format Email
        if (filter_var($email_sanitized, FILTER_VALIDATE_EMAIL)) {
            
            // Cek apakah email sudah terdaftar
            $check_stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
            $check_stmt->execute([$email_sanitized]);
            
            if ($check_stmt->rowCount() > 0) {
                // Email sudah ada -> Redirect ke frontend dengan pesan
                header("Location: ../index.php?status=gagal_double_entry#newsletter");
            } else {
                // Insert email baru
                $insert_stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, tanggal_daftar) VALUES (?, NOW())");
                
                if ($insert_stmt->execute([$email_sanitized])) {
                    // Sukses -> Redirect ke frontend
                    header("Location: ../index.php?status=sukses_newsletter#newsletter");
                } else {
                    // Gagal Query
                    header("Location: ../index.php?status=gagal_sistem#newsletter");
                }
            }
        } else {
            // Format email salah
            header("Location: ../index.php?status=gagal_email#newsletter");
        }
        exit(); // Hentikan eksekusi setelah redirect ke frontend
    }
    
    // Jika tidak ada aksi yang cocok, kembalikan ke halaman kelola (jika admin) atau login
    if (isAdminLoggedIn()) {
        redirect('kelola_newsletter.php');
    } else {
        redirect('login.php');
    }
    
} catch (PDOException $e) {
    // Log error database
    error_log("Database Error (Newsletter): " . $e->getMessage());
    die("Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti.");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

exit();
?>
