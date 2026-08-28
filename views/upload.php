<?php
include '../config/config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proses form saat dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $title = $_POST['title'];
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];
    $imageUrl = ''; // Default image URL

    // Cek apakah ada file gambar yang diupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/"; // Folder untuk menyimpan gambar
        $imageUrl = $targetDir . basename($_FILES['image']['name']);

        // Pindahkan file yang diupload ke folder yang ditentukan
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imageUrl)) {
            // Berhasil mengupload gambar
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    // Simpan resep ke database
    $stmt = $pdo->prepare("INSERT INTO resep (user_id, title, ingredients, steps, image_url, created_at) VALUES (:user_id, :title, :ingredients, :steps, :image_url, NOW())");
    $stmt->execute([
        ':user_id' => $userId,
        ':title' => $title,
        ':ingredients' => $ingredients,
        ':steps' => $steps,
        ':image_url' => $imageUrl
    ]);

    header("Location: resep.php"); // Redirect setelah sukses
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Unggah Resep - Resep Kita</title>
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

<nav class="max-w-5xl mx-auto flex items-center justify-between px-6 py-8">
    <a href="index.php" class="font-hand text-3xl font-semibold">Resep Kita</a>
    <button id="navToggle" class="md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
    </button>
    <ul id="navMenu" class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest uppercase">
        <li><a class="hover:text-mustard transition-colors" href="index.php">Beranda</a></li>
        <li><a class="hover:text-mustard transition-colors" href="resep.php">Resep Saya</a></li>
        <li><a class="text-mustard transition-colors" href="upload.php">Unggah</a></li>
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

<section class="max-w-xl mx-auto px-6 pt-10 pb-16">
    <div class="text-center mb-8">
        <span class="eyebrow">Bagikan Kreasimu</span>
        <h2 class="font-display text-2xl md:text-3xl font-semibold mt-1">Unggah Resep Baru</h2>
        <p class="text-navy/60 text-sm mt-3">Bagikan resep favoritmu ke komunitas Resep Kita</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium mb-1">Judul Resep</label>
                <input type="text" id="title" name="title" placeholder="Contoh: Rendang Daging Sapi" required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard">
            </div>
            <div>
                <label for="ingredients" class="block text-sm font-medium mb-1">Bahan-bahan</label>
                <textarea id="ingredients" name="ingredients" rows="5" placeholder="Tuliskan satu bahan per baris..." required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard"></textarea>
            </div>
            <div>
                <label for="steps" class="block text-sm font-medium mb-1">Langkah-langkah</label>
                <textarea id="steps" name="steps" rows="5" placeholder="Tuliskan langkah memasak secara berurutan..." required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard"></textarea>
            </div>
            <div>
                <label for="image" class="block text-sm font-medium mb-1">Foto Resep</label>
                <input type="file" id="image" name="image" accept="image/*" class="w-full text-sm text-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-mustard file:text-navy hover:file:bg-navy hover:file:text-white file:transition-colors">
            </div>
            <button type="submit" class="w-full bg-navy hover:bg-navy/90 text-white rounded-md py-2.5 font-semibold transition-colors">Unggah Resep</button>
        </form>
        <a href="index.php" class="block text-center mt-4 text-sm font-semibold text-navy/60 hover:text-mustard transition-colors">Kembali ke Beranda</a>
    </div>
</section>

<footer class="max-w-xl mx-auto text-center px-6 pb-16">
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
</script>
</body>
</html>