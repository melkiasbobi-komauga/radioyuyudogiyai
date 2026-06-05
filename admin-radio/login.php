<?php
/**
 * Halaman Login Admin dengan Proteksi CSRF
 */

// Output buffering
ob_start();

// 1. Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Sertakan file konfigurasi & auth
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Jika sudah login, redirect ke dashboard
if (isAdminLoggedIn()) {
    redirect('dashboard.php');
    exit();
}

// 3. Proses Login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error = 'Sesi kadaluarsa atau permintaan tidak valid. Silakan refresh halaman.';
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            $error = 'Username dan password wajib diisi!';
        } else {
            // Coba login
            if (adminLogin($username, $password)) {
                // Regenerasi ID session untuk keamanan (mencegah session fixation)
                session_regenerate_id(true);
                redirect('dashboard.php');
                exit();
            } else {
                $error = 'Username atau password salah!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Radio Yuyu Kominfo</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        .bg-gradient-custom { background: linear-gradient(135deg, #1a237e 0%, #0d1346 100%); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 bg-[url('../images/background.jpeg')] bg-cover bg-center relative">
    
    <div class="absolute inset-0 bg-blue-900 bg-opacity-80 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-4xl flex rounded-2xl overflow-hidden shadow-2xl mx-4 bg-white">
        
        <!-- Kiri: Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center z-10">
            <div class="text-center mb-8">
                <img src="../images/logo.png" alt="Logo" class="w-20 h-20 mx-auto mb-4 md:hidden object-contain">
                <h2 class="text-3xl font-bold text-gray-800">Selamat Datang</h2>
                <p class="text-gray-500 text-sm mt-2">Login ke Panel Admin Radio Yuyu.</p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm flex items-center" role="alert">
                <i class="fas fa-exclamation-circle mr-2 text-lg"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <!-- CSRF Token Field -->
                <?php csrfField(); ?>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" id="username" name="username" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                            placeholder="Username" required>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-gray-400"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-900 text-white font-bold py-3 rounded-lg hover:bg-blue-800 transition duration-300 shadow-lg transform hover:-translate-y-1">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-gray-400">
                &copy; <?php echo date('Y'); ?> Radio Kominfo Dogiyai.
            </div>
        </div>

        <!-- Kanan: Branding (Desktop) -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-custom items-center justify-center p-12 text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <i class="fas fa-broadcast-tower absolute text-9xl -top-10 -left-10 transform rotate-12"></i>
                <i class="fas fa-music absolute text-8xl bottom-10 right-10 transform -rotate-12"></i>
            </div>
            <div class="relative z-10 text-center">
                <img src="../images/logo.png" alt="Logo Radio" class="w-48 h-48 object-contain mx-auto mb-6 drop-shadow-2xl">
                <h3 class="text-3xl font-bold mb-2">Radio Yuyu Kominfo</h3>
                <p class="text-blue-200 text-sm font-light tracking-wider">93.6 FM - SUARA DOGIYAI</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>
