<?php
// FILE: admin-radio/includes/config.php

// 1. Robust Root Config Inclusion
// We check multiple possible locations for the root config.php to avoid path errors.

$rootConfig = __DIR__ . '/../../config.php'; // Standard path from includes folder
$documentRootConfig = $_SERVER['DOCUMENT_ROOT'] . '/config.php'; // Direct from document root
// Adjust 'Radio-yuyu-website' if your project folder name is different
$subFolderConfig = $_SERVER['DOCUMENT_ROOT'] . '/Radio-yuyu-website/config.php'; 

if (file_exists($rootConfig)) {
    require_once $rootConfig;
} elseif (file_exists($documentRootConfig)) {
    require_once $documentRootConfig;
} elseif (file_exists($subFolderConfig)) {
    require_once $subFolderConfig;
} else {
    // Fallback: Define DB credentials here if root config is absolutely missing.
    // Ideally, this part should not be reached if the structure is correct.
    if (!defined('DB_HOST')) {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'u347230510_yuyukominfo'); 
        define('DB_PASS', 'Yuyukominfo#08');
        define('DB_NAME', 'u347230510_yuyukominfo');
        
        // Basic PDO connection if root config is missing
        if (!function_exists('getDBConnection')) {
            function getDBConnection() {
                try {
                    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                    return new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                } catch (PDOException $e) { die("DB Error: " . $e->getMessage()); }
            }
        }
    }
}

// 2. Helper Redirect
if (!function_exists('redirect')) {
    function redirect($url) {
        if (!headers_sent()) {
            header("Location: $url");
        } else {
            echo "<script>window.location.href='$url';</script>";
        }
        exit;
    }
}

// 3. Helper CSRF Protection
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
            }
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('csrfField')) {
    function csrfField() {
        echo '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
    }
}
?>