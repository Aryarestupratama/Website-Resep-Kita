<?php
include '../config/config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil daftar bookmark dari database
$stmt = $pdo->prepare("SELECT r.id, r.title, r.image_url FROM bookmarks b JOIN resep r ON b.resep_id = r.id WHERE b.user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$bookmarks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bookmark - Resep Kita</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/feather-icons"></script>
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

<nav class="max-w-5xl mx-auto flex items-center justify-between px-6 py-8">
    <a href="index.php" class="font-hand text-3xl font-semibold">Resep Kita</a>
    <button id="navToggle" class="md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
    </button>
    <ul id="navMenu" class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest uppercase">
        <li><a class="hover:text-mustard transition-colors" href="index.php">Beranda</a></li>
        <li><a class="hover:text-mustard transition-colors" href="resep.php">Resep Saya</a></li>
        <li><a class="hover:text-mustard transition-colors" href="upload.php">Unggah</a></li>
        <li><a class="text-mustard transition-colors" href="bookmarks.php">Bookmark</a></li>
        <?php if (isset($_SESSION['username'])): ?>
            <li><a class="hover:text-mustard transition-colors" href="my_profile.php">Profil</a></li>
            <li><a class="hover:text-mustard transition-colors" href="actions/logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="hover:text-mustard transition-colors" href="login.php">Login</a></li>
            <li><a class="hover:text-mustard transition-colors" href="register.php">Daftar</a></li>
        <?php endif; ?>
    </ul>
</nav>
<ul id="navMenuMobile" class="hidden md:hidden flex-col gap-1 px-6 py-3 text-sm font-semibold">
    <li><a class="block py-2" href="index.php">Beranda</a></li>
    <li><a class="block py-2" href="resep.php">Resep Saya</a></li>
    <li><a class="block py-2" href="upload.php">Unggah Resep</a></li>
    <li><a class="block py-2" href="bookmarks.php">Bookmark</a></li>
    <?php if (isset($_SESSION['username'])): ?>
        <li><a class="block py-2" href="my_profile.php">Profil Saya</a></li>
        <li><a class="block py-2" href="actions/logout.php">Logout</a></li>
    <?php else: ?>
        <li><a class="block py-2" href="login.php">Login</a></li>
        <li><a class="block py-2" href="register.php">Daftar</a></li>
    <?php endif; ?>
</ul>

<section class="max-w-5xl mx-auto px-6 pt-6 pb-4 text-center">
    <span class="eyebrow">Resep Tersimpan</span>
    <h2 class="font-display text-2xl md:text-3xl font-semibold mt-1">Daftar Bookmark</h2>
</section>

<div class="max-w-5xl mx-auto px-6 pb-20">
    <a href="index.php" class="inline-block bg-navy/5 hover:bg-navy hover:text-white text-navy rounded-full px-6 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors mb-8">Kembali ke Beranda</a>

    <?php if ($bookmarks): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            <?php foreach ($bookmarks as $bookmark): ?>
                <div>
                    <img src="<?php echo htmlspecialchars($bookmark['image_url']); ?>" class="h-52 w-full object-cover rounded-md mb-3" alt="<?php echo htmlspecialchars($bookmark['title']); ?>">
                    <h5 class="font-display font-semibold text-lg leading-snug"><?php echo htmlspecialchars($bookmark['title']); ?></h5>
                    <div class="flex items-center justify-between mt-3">
                        <a href="actions/bookmarks_action.php?action=remove&resid=<?php echo $bookmark['id']; ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                            <i data-feather="trash-2" style="width:14px;height:14px;"></i> Hapus
                        </a>
                        <a href="view_resep.php?id=<?php echo $bookmark['id']; ?>" class="text-xs font-semibold text-navy/60 hover:text-mustard transition-colors">Lihat Detail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16 text-navy/40">
            <p class="text-sm mb-4">Tidak ada resep yang dibookmark.</p>
            <a href="index.php" class="inline-block bg-mustard hover:bg-navy hover:text-white text-navy rounded-full px-10 py-3 text-xs font-bold uppercase tracking-widest transition-colors">Jelajahi Resep</a>
        </div>
    <?php endif; ?>
</div>

<footer class="max-w-xl mx-auto text-center px-6 py-16">
    <span class="eyebrow">Hubungi Kami</span>
    <h3 class="font-display text-xl md:text-2xl font-medium mt-2 leading-relaxed">
        Ada pertanyaan, ide kolaborasi, atau sekadar mau say hello?<br>
        <a href="mailto:halo@resepkita.co" class="font-display italic underline decoration-mustard underline-offset-4">halo@resepkita.co</a>
    </h3>
</footer>

<script>
    document.getElementById('navToggle').addEventListener('click', () => {
        document.getElementById('navMenuMobile').classList.toggle('hidden');
        document.getElementById('navMenuMobile').classList.toggle('flex');
    });
    feather.replace();
</script>
</body>
</html>