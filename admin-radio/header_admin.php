<?php
// Radio-yuyu-website/admin-radio/header_admin.php

// Deteksi halaman saat ini untuk class 'active'
$currentPage = basename($_SERVER['PHP_SELF']);

// Fallback data admin jika belum ada
if (!isset($admin) && isset($_SESSION['admin_nama'])) {
    $admin = ['nama_lengkap' => $_SESSION['admin_nama']];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - RAKOM Dogiyai</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Custom Scrollbar untuk Sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #2d3748; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #4a5568; border-radius: 20px; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans antialiased">
    <div class="relative min-h-screen md:flex">
        
        <!-- Mobile Header -->
        <div class="bg-gray-800 text-gray-100 flex justify-between md:hidden shadow-md">
            <a href="#" class="block p-4 text-white font-bold text-lg">RAKOM Admin</a>
            <button id="mobile-menu-btn" class="mobile-menu-button p-4 focus:outline-none focus:bg-gray-700">
                <i class="fas fa-bars fa-lg"></i>
            </button>
        </div>

        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-gray-900 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out z-20 flex flex-col h-screen fixed md:sticky top-0 overflow-y-auto sidebar-scroll shadow-xl">
            
            <!-- Brand -->
            <a href="dashboard.php" class="text-white flex items-center space-x-2 px-4 mb-6">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i class="fas fa-broadcast-tower text-xl"></i>
                </div>
                <span class="text-xl font-extrabold tracking-wide">RAYUKOM</span>
            </a>

            <!-- Menu Items -->
            <nav class="flex-1 px-2 space-y-1">
                <a href="dashboard.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'dashboard.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500 shadow-lg' : 'text-gray-400'; ?>">
                    <i class="fas fa-tachometer-alt w-6"></i><span class="mx-2 font-medium">Dashboard</span>
                </a>

                <div class="mt-6 mb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kelola Konten</div>
                
                <!-- === MENU BARU: PROGRAM UNGGULAN === -->
                <a href="kelola_program.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_program.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-star w-6 text-yellow-500"></i><span class="mx-2 font-medium">Program Unggulan</span>
                </a>
                <!-- =================================== -->

                <a href="kelola_berita.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_berita.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-newspaper w-6"></i><span class="mx-2 font-medium">Berita</span>
                </a>
                <a href="kelola_pengumuman.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_pengumuman.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-bullhorn w-6"></i><span class="mx-2 font-medium">Pengumuman</span>
                </a>
                <a href="kelola_agenda.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_agenda.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-calendar-alt w-6"></i><span class="mx-2 font-medium">Agenda</span>
                </a>
                <a href="kelola_galeri.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_galeri.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-images w-6"></i><span class="mx-2 font-medium">Galeri</span>
                </a>

                <div class="mt-6 mb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Siaran & Media</div>
                <a href="kelola_jadwal.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_jadwal.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-clock w-6"></i><span class="mx-2 font-medium">Jadwal</span>
                </a>
                <a href="kelola_podcast.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_podcast.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-podcast w-6"></i><span class="mx-2 font-medium">Podcast</span>
                </a>
                <a href="kelola_chart.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_chart.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-music w-6"></i><span class="mx-2 font-medium">Chart Lagu</span>
                </a>

                <div class="mt-6 mb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Manajemen</div>
                <a href="kelola_tim.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_tim.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-users w-6"></i><span class="mx-2 font-medium">Tim</span>
                </a>
                <a href="kelola_pesan.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_pesan.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-envelope w-6"></i><span class="mx-2 font-medium">Pesan</span>
                </a>
                <a href="kelola_testimoni.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_testimoni.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-comment-alt w-6"></i><span class="mx-2 font-medium">Testimoni</span>
                </a>
                <a href="kelola_newsletter.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_newsletter.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-mail-bulk w-6"></i><span class="mx-2 font-medium">Newsletter</span>
                </a>
                <a href="kelola_forum.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_forum.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-comments w-6"></i><span class="mx-2 font-medium">Forum</span>
                </a>
                <a href="kelola_profil.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-800 hover:text-white <?php echo ($currentPage == 'kelola_profil.php') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-400'; ?>">
                    <i class="fas fa-cog w-6"></i><span class="mx-2 font-medium">Profil Web</span>
                </a>
            </nav>
            
            <div class="px-4 py-4 border-t border-gray-800 mt-auto">
                <a href="logout.php" class="flex items-center text-red-400 hover:text-red-300 transition duration-200 px-4 py-2 rounded hover:bg-gray-800">
                    <i class="fas fa-sign-out-alt w-6"></i><span class="mx-2 font-medium">Logout</span>
                </a>
            </div>
        </div>
