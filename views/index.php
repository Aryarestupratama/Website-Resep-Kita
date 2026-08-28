<?php
    include '../config/config.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $searchKeyword = $_GET['search'] ?? '';

    if ($searchKeyword) {
        $stmt = $pdo->prepare("SELECT resep.*, users.username 
                                FROM resep 
                                JOIN users ON resep.user_id = users.id 
                                WHERE resep.title LIKE :search 
                                ORDER BY resep.created_at DESC");
        $stmt->execute(['search' => '%' . $searchKeyword . '%']);
        $resepList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userId = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM search_history WHERE user_id = ? AND search_term = ?");
        $stmt->execute([$userId, $searchKeyword]);
        $exists = $stmt->fetchColumn() > 0;

        if (!$exists) {
            $stmt = $pdo->prepare("INSERT INTO search_history (user_id, search_term) VALUES (?, ?)");
            $stmt->execute([$userId, $searchKeyword]);
        }
    } else {
        $stmt = $pdo->query("SELECT resep.*, users.username 
                             FROM resep 
                             JOIN users ON resep.user_id = users.id 
                             ORDER BY resep.created_at DESC");
        $resepList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $userId = $_SESSION['user_id'] ?? null;
    if ($userId !== null) {
        $stmt = $pdo->prepare("SELECT search_term FROM search_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$userId]);
        $searchHistory = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $searchHistory = [];
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resep Kita - Temukan Resep Favoritmu</title>
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
    .quote-link{ font-family:'Fraunces', serif; font-style:italic; text-decoration:underline; text-decoration-color:#F5B301; text-underline-offset:4px; color:#211E4B; }
    .dot{ position:absolute; width:7px; height:7px; border-radius:50%; background:#F5B301; }
</style>
</head>
<body class="text-navy">

<!-- Navbar -->
<nav class="max-w-5xl mx-auto flex items-center justify-between px-6 py-8">
    <a href="index.php" class="font-hand text-3xl font-semibold">Resep Kita</a>
    <button id="navToggle" class="md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
    </button>
    <ul id="navMenu" class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest uppercase">
        <li><a class="hover:text-mustard transition-colors" href="index.php">Beranda</a></li>
        <li><a class="hover:text-mustard transition-colors" href="resep.php">Resep Saya</a></li>
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

<!-- Hero -->
<section class="max-w-xl mx-auto text-center px-6 pt-10 pb-16 relative">
    <span class="dot" style="top:10px; left:6%;"></span>
    <span class="dot" style="top:60px; right:8%;"></span>
    <img src="assets/images/hero-illustration.png" alt="Ilustrasi memasak Resep Kita" class="mx-auto w-72 md:w-96 mb-8">
    <h1 class="font-display text-2xl md:text-3xl font-medium leading-snug">
        Selamat datang di Resep Kita
        <span class="block text-base md:text-lg font-normal mt-2 text-navy/60">Masak jadi lebih mudah dan menyenangkan!</span>
    </h1>
    <p class="text-navy/60 text-sm mt-5 max-w-sm mx-auto leading-relaxed">Kumpulkan, bagikan, dan temukan resep favorit dari dapur ke dapur — unggah kreasimu, kasih like, simpan resep yang bikin ngiler.</p>
</section>

<!-- Flat photo strip -->
<section class="max-w-5xl mx-auto px-6 grid grid-cols-2 gap-5 mb-6">
    <img src="assets/images/hero-photo-1.jpg" onerror="this.style.display='none'" alt="" class="w-full h-72 md:h-96 object-cover rounded-md">
    <img src="assets/images/hero-photo-2.jpg" onerror="this.style.display='none'" alt="" class="w-full h-72 md:h-96 object-cover rounded-md">
</section>

<!-- Quote / highlight -->
<section class="max-w-3xl mx-auto px-6 py-14 flex flex-col md:flex-row items-center gap-10 relative">
    <span class="dot" style="top:0; left:40%;"></span>
    <img src="assets/images/about-illustration.png" alt="Ilustrasi komunitas memasak" class="w-64 md:w-80 flex-shrink-0">
    <p class="font-display italic text-lg md:text-xl leading-relaxed text-navy/90 text-center md:text-left">
        Beberapa resep di sini sudah <span class="quote-link">dicoba lebih dari seribu kali</span> oleh anggota komunitas
        (bukan satu-satunya pencapaian, tapi ini murni hasil kerja keras dapur-dapur rumahan seperti punyamu — dan kami sangat senang karenanya).
    </p>
</section>

<!-- Services / Rekomendasi -->
<section class="max-w-4xl mx-auto px-6 py-10">
    <div class="mb-10">
        <span class="eyebrow">Jelajahi</span>
        <h2 class="font-display text-2xl md:text-3xl font-semibold mt-1"><?= $searchKeyword ? 'Hasil Pencarian' : 'Rekomendasi Resep' ?></h2>
    </div>

    <?php if (!empty($resepList)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10" id="resep-list">
            <?php foreach ($resepList as $resep): ?>
                <div>
                    <a href="view_resep.php?id=<?= $resep['id']; ?>" class="block group">
                        <img src="<?= htmlspecialchars($resep['image_url']); ?>" onerror="this.onerror=null;this.src='assets/images/recipe-placeholder.jpg'" class="h-52 w-full object-cover rounded-md mb-3 group-hover:opacity-90 transition-opacity" alt="Gambar Resep">
                        <h5 class="font-display font-semibold text-lg leading-snug"><?= htmlspecialchars($resep['title']); ?></h5>
                        <p class="text-xs text-navy/50 mt-1">Diunggah oleh <?= isset($resep['username']) ? htmlspecialchars($resep['username']) : 'Tidak diketahui'; ?></p>
                    </a>
                    <?php if (isLoggedIn()): ?>
                        <a href="actions/bookmarks_action.php?action=add&resid=<?= $resep['id']; ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-navy/60 hover:text-mustard mt-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg> Simpan
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-navy/40 text-sm">Tidak ada resep yang ditemukan untuk kata kunci "<?= htmlspecialchars($searchKeyword); ?>".</p>
    <?php endif; ?>
</section>

<!-- CTA -->
<section class="text-center py-16">
    <a href="upload.php" class="inline-block bg-mustard hover:bg-navy hover:text-white text-navy rounded-full px-10 py-3 text-xs font-bold uppercase tracking-widest transition-colors">Unggah Resepmu</a>
</section>

<!-- Meet the team style / about -->
<section class="max-w-4xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center gap-10">
    <img src="assets/images/admin-header-illustration.png" onerror="this.style.display='none'" alt="Komunitas Resep Kita" class="w-full md:w-1/2 rounded-md object-cover h-72">
    <div class="md:w-1/2 text-center md:text-left">
        <span class="eyebrow">Tentang Kami</span>
        <p class="font-display italic text-lg md:text-xl leading-relaxed mt-2">
            Kami suka <span class="quote-link">segala hal soal masak-memasak</span>, eksperimen dapur, dan berbagi cerita di balik setiap resep.
        </p>
    </div>
</section>

<!-- Footer -->
<footer class="max-w-xl mx-auto text-center px-6 py-20 relative">
    <span class="dot" style="top:10px; left:20%;"></span>
    <span class="dot" style="bottom:20px; right:15%;"></span>
    <img src="assets/images/hero-illustration.png" alt="" class="w-44 mx-auto mb-6 opacity-70">
    <span class="eyebrow">Hubungi Kami</span>
    <h3 class="font-display text-xl md:text-2xl font-medium mt-2 leading-relaxed">
        Ada pertanyaan, ide kolaborasi, atau sekadar mau say hello?<br>
        <a href="mailto:halo@resepkita.co" class="quote-link">halo@resepkita.co</a>
    </h3>
    <div class="flex justify-center gap-4 mt-8">
        <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-full bg-navy/5 flex items-center justify-center hover:bg-mustard transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
        <a href="#" aria-label="Instagram" class="w-9 h-9 rounded-full bg-navy/5 flex items-center justify-center hover:bg-mustard transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
        </a>
        <a href="#" aria-label="Twitter" class="w-9 h-9 rounded-full bg-navy/5 flex items-center justify-center hover:bg-mustard transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
        </a>
    </div>
</footer>

<script>
    document.getElementById('navToggle').addEventListener('click', () => {
        document.getElementById('navMenuMobile').classList.toggle('hidden');
        document.getElementById('navMenuMobile').classList.toggle('flex');
    });
</script>
</body>
</html>