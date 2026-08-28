<?php

include '../config/config.php'; // Pastikan jalur ini benar

// Periksa jika admin sudah login
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit;
}

// Ambil data likes dan comments untuk setiap resep
$stmt = $pdo->prepare("
    SELECT likes.resep_id, likes.user_id, likes.created_at AS like_time, 
           comments.comment, comments.created_at AS comment_time, 
           users.username, users.email, resep.title AS resep_title
    FROM likes
    LEFT JOIN comments ON likes.resep_id = comments.resep_id AND likes.user_id = comments.user_id
    LEFT JOIN users ON likes.user_id = users.id
    LEFT JOIN resep ON likes.resep_id = resep.id
");
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Umpan Balik Pengguna - Resep Kita</title>
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
    <a href="manage_users.php">Kelola Pengguna</a>
    <a href="manage_recipes.php">Kelola Resep</a>
    <a href="view_feedback.php" class="text-mustard" style="opacity:1;">Tampilkan Umpan Balik</a>
    <a href="actions/logout.php">Logout</a>
</div>

<div class="admin-main">
    <div class="max-w-5xl mx-auto">
        <span class="eyebrow block text-center">Panel Admin</span>
        <h2 class="font-display text-2xl md:text-3xl font-semibold text-center mt-1 mb-6">Umpan Balik Pengguna</h2>
        <a href="admin_dashboard.php" class="inline-block bg-navy/5 hover:bg-navy hover:text-white text-navy rounded-full px-6 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors mb-6">Kembali ke Dashboard</a>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy text-white uppercase text-xs tracking-widest">
                        <th class="text-left px-4 py-3">Judul Resep</th>
                        <th class="text-left px-4 py-3">Nama Pengguna</th>
                        <th class="text-left px-4 py-3">Email Pengguna</th>
                        <th class="text-left px-4 py-3">Waktu Like</th>
                        <th class="text-left px-4 py-3">Komentar</th>
                        <th class="text-left px-4 py-3">Waktu Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <tr class="border-b border-navy/10">
                            <td class="px-4 py-3 font-semibold"><?php echo htmlspecialchars($feedback['resep_title']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($feedback['username']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($feedback['email']); ?></td>
                            <td class="px-4 py-3 text-navy/60"><?php echo htmlspecialchars($feedback['like_time']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($feedback['comment'] ?? 'Tidak ada komentar'); ?></td>
                            <td class="px-4 py-3 text-navy/60"><?php echo htmlspecialchars($feedback['comment_time'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>