<?php
    include '../config/config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header("Location: admin_login.php");
        exit;
    }

    // Mengambil informasi pengguna dari database
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt->execute();
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as total_resep FROM resep");
    $stmt->execute();
    $total_recipes = $stmt->fetch(PDO::FETCH_ASSOC)['total_resep'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - Resep Kita</title>
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
</style>
</head>
<body class="text-navy">

<nav class="flex items-center justify-between px-6 md:px-10 py-6 border-b border-navy/10">
    <a href="admin_dashboard.php" class="font-hand text-3xl font-semibold">Resep Kita <span class="font-sans text-xs font-bold text-mustard uppercase tracking-widest align-middle">Admin</span></a>
    <a href="actions/logout.php" class="text-xs font-semibold uppercase tracking-widest hover:text-mustard transition-colors">Logout</a>
</nav>

<div class="max-w-4xl mx-auto px-6 py-12">
    <div class="text-center mb-10">
        <span class="eyebrow">Panel Kontrol</span>
        <h2 class="font-display text-2xl md:text-3xl font-semibold mt-1">Dashboard Admin</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-14">
        <div class="bg-white rounded-lg shadow-md overflow-hidden text-center">
            <div class="bg-navy text-white uppercase tracking-widest text-xs font-bold py-3">Total Pengguna</div>
            <div class="p-6">
                <h5 class="font-display text-4xl font-semibold text-mustard"><?php echo $total_users; ?></h5>
                <p class="text-sm text-navy/60 mt-2">Jumlah pengguna terdaftar di sistem.</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md overflow-hidden text-center">
            <div class="bg-navy text-white uppercase tracking-widest text-xs font-bold py-3">Total Resep</div>
            <div class="p-6">
                <h5 class="font-display text-4xl font-semibold text-mustard"><?php echo $total_recipes; ?></h5>
                <p class="text-sm text-navy/60 mt-2">Jumlah resep yang telah ditambahkan ke sistem.</p>
            </div>
        </div>
    </div>

    <h4 class="font-display text-xl font-semibold text-center mb-6">Menu Admin</h4>
    <div class="space-y-3 max-w-md mx-auto">
        <a href="manage_users.php" class="block bg-white rounded-md shadow-sm px-6 py-4 font-semibold text-navy hover:text-mustard transition-colors">Kelola Pengguna</a>
        <a href="manage_recipes.php" class="block bg-white rounded-md shadow-sm px-6 py-4 font-semibold text-navy hover:text-mustard transition-colors">Kelola Resep</a>
        <a href="view_feedback.php" class="block bg-white rounded-md shadow-sm px-6 py-4 font-semibold text-navy hover:text-mustard transition-colors">Tampilkan Umpan Balik</a>
    </div>
</div>

</body>
</html>