<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/config.php'; 
require_once 'includes/auth.php';
if (!isAdminLoggedIn()) { redirect('login.php'); exit(); }

$pdo = getDBConnection();
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10; $offset = ($page - 1) * $limit;

try {
    $count_sql = "SELECT COUNT(*) FROM program_unggulan WHERE judul LIKE :search";
    $stmt = $pdo->prepare($count_sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->execute();
    $total_pages = ceil($stmt->fetchColumn() / $limit);

    $sql = "SELECT * FROM program_unggulan WHERE judul LIKE :search ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

require_once 'header_admin.php'; 
?>
<div class="flex-1 flex flex-col bg-gray-50">
    <header class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center"><button id="hamburger" class="mr-4 md:hidden"><i class="fas fa-bars"></i></button><h1 class="text-xl font-bold">Kelola Program</h1></div>
        <a href="tambah_program.php" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm"><i class="fas fa-plus mr-2"></i>Tambah</a>
    </header>
    <main class="p-6 overflow-y-auto">
        <?php if(isset($_GET['status'])): ?><div class="bg-green-100 text-green-700 p-4 rounded mb-6">Berhasil diproses!</div><?php endif; ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead><tr class="bg-gray-50 border-b"><th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Program</th><th class="px-5 py-3 text-center w-32">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($programs as $row): ?>
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-5 py-4"><div class="flex items-center"><img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" class="h-12 w-12 rounded object-cover mr-4"><div><p class="font-bold"><?php echo htmlspecialchars($row['judul']); ?></p><p class="text-xs text-gray-500 truncate w-64"><?php echo htmlspecialchars($row['deskripsi']); ?></p></div></div></td>
                        <td class="px-5 py-4 text-center"><a href="edit_program.php?id=<?php echo $row['id']; ?>" class="text-blue-600 mx-2"><i class="fas fa-edit"></i></a><a href="proses_program.php?aksi=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-600 mx-2"><i class="fas fa-trash"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php require_once 'footer_admin.php'; ?>