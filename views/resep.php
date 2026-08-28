<?php
    include '../config/config.php';

    // Cek apakah pengguna sudah login
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // Ambil semua resep dari database untuk user yang login
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM resep WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resep Saya - Resep Kita</title>
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
        <li><a class="text-mustard transition-colors" href="resep.php">Resep Saya</a></li>
        <li><a class="hover:text-mustard transition-colors" href="upload.php">Unggah</a></li>
        <li><a class="hover:text-mustard transition-colors" href="bookmarks.php">Bookmark</a></li>
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

<section class="max-w-5xl mx-auto px-6 pt-6 pb-4 flex flex-wrap items-center justify-between gap-4">
    <div class="text-center sm:text-left">
        <span class="eyebrow">Dapur Pribadi</span>
        <h1 class="font-display text-2xl md:text-3xl font-semibold mt-1">Resep Anda</h1>
    </div>
    <a href="upload.php" class="inline-block bg-mustard hover:bg-navy hover:text-white text-navy rounded-full px-8 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors">+ Unggah Resep</a>
</section>

<div class="max-w-5xl mx-auto px-6 pb-20">
    <?php if (count($recipes) > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            <?php foreach ($recipes as $recipe): ?>
                <div>
                    <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="h-52 w-full object-cover rounded-md mb-3" onerror="this.onerror=null; this.src='assets/images/recipe-placeholder.jpg';">
                    <h5 class="font-display font-semibold text-lg leading-snug"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                    <div class="flex items-center justify-between mt-3">
                        <a href="view_resep.php?id=<?php echo $recipe['id']; ?>" class="text-xs font-semibold text-navy/60 hover:text-mustard transition-colors">Lihat Detail</a>
                        <form action="actions/delete_resep_action.php" method="POST" style="display:inline;">
                            <input type="hidden" name="resep_id" value="<?php echo $recipe['id']; ?>">
                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors" onclick="return confirm('Anda yakin ingin menghapus resep ini?');">
                                <i data-feather="trash-2" style="width:14px;height:14px;"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16 text-navy/40">
            <p class="text-sm mb-4">Kamu belum punya resep. Yuk unggah resep pertamamu!</p>
            <a href="upload.php" class="inline-block bg-mustard hover:bg-navy hover:text-white text-navy rounded-full px-10 py-3 text-xs font-bold uppercase tracking-widest transition-colors">Unggah Resep</a>
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