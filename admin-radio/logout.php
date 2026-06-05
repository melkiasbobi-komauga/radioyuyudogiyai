<?php
// 1. Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Sertakan config untuk fungsi redirect
require_once 'includes/config.php';

// 3. Hapus semua variabel session
$_SESSION = array();

// 4. Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 5. Hancurkan session
session_destroy();

// 6. Redirect ke halaman login
redirect('login.php');
exit();
?>
