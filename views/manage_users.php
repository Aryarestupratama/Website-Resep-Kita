<?php

include '../config/config.php'; // Pastikan jalur ini benar

// Periksa jika admin sudah login
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit;
}

// Operasi hapus pengguna (hanya untuk admin)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header("Location: manage_action.php");
    exit;
}

// Ambil semua data pengguna untuk ditampilkan
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Pengguna - Resep Kita</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: { navy: '#211E4B', mustard: '#F5B301', cream: '#FFFCF5' },
      fontFamily: {
        display: ['Fraunces', 'serif'],
        hand: ['Caveat', 'cursive'],
        sans: ['Plus Jakarta Sans', 'sans-serif'],
      },
    }
  }
}
</script>
<style>
    body{ font-family:'Plus Jakarta Sans', sans-serif; background:#FFFCF5; }
    .eyebrow{ font-size:0.72rem; font-weight:700; letter-spacing:.18em; text-transform:uppercase; color:#F5B301; }
    .admin-sidebar{ width:220px; background:#211E4B; color:#fff; min-height:100vh; padding:30px 20px; position:fixed; top:0; left:0; }
    .admin-sidebar h3{ font-family:'Fraunces',serif; margin-bottom:30px; }
    .admin-sidebar a{ display:block; color:#fff; opacity:0.75; padding:10px 0; font-size:0.9rem; font-weight:600; text-decoration:none; }
    .admin-sidebar a:hover{ opacity:1; color:#F5B301; }
    .admin-main{ margin-left:220px; padding: 40px 30px; }
    @media (max-width: 767px){ .admin-sidebar{ position:static; width:100%; min-height:auto; } .admin-main{ margin-left:0; } }
</style>
</head>
<body class="text-navy">

<div class="admin-sidebar">
    <h3 class="text-lg">Resep Kita</h3>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="manage_users.php" class="text-mustard" style="opacity:1;">Kelola Pengguna</a>
    <a href="manage_recipes.php">Kelola Resep</a>
    <a href="view_feedback.php">Tampilkan Umpan Balik</a>
    <a href="actions/logout.php">Logout</a>
</div>

<div class="admin-main">
    <div class="max-w-5xl mx-auto">
        <span class="eyebrow block text-center">Panel Admin</span>
        <h2 class="font-display text-2xl md:text-3xl font-semibold text-center mt-1 mb-6">Daftar Pengguna</h2>
        <a href="admin_dashboard.php" class="inline-block bg-navy/5 hover:bg-navy hover:text-white text-navy rounded-full px-6 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors mb-6">Kembali ke Dashboard</a>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy text-white uppercase text-xs tracking-widest">
                        <th class="text-left px-4 py-3">ID</th>
                        <th class="text-left px-4 py-3">Username</th>
                        <th class="text-left px-4 py-3">Email</th>
                        <th class="text-left px-4 py-3">Password (Hash)</th>
                        <th class="text-left px-4 py-3">Role</th>
                        <th class="text-left px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-b border-navy/10">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['id']); ?></td>
                            <td class="px-4 py-3 font-semibold"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-4 py-3"><small class="text-navy/40"><?php echo htmlspecialchars($user['password']); ?></small></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['role']); ?></td>
                            <td class="px-4 py-3">
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <a href="actions/manage_action.php?action=delete&id=<?php echo $user['id']; ?>"
                                       class="text-red-500 hover:text-red-700 font-semibold text-xs uppercase tracking-widest"
                                       onclick="return confirm('Yakin ingin menghapus pengguna ini?');">
                                       Hapus
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>