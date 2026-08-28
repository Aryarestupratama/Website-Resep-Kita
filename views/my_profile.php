<?php
// Memulai sesi
include '../config/config.php'; // Pastikan jalur ini benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil informasi pengguna dari database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika pengguna tidak ditemukan, redirect ke halaman login
if (!$user) {
    header("Location: login.php");
    exit;
}

// Inisialisasi variabel untuk pesan kesalahan dan sukses
$errors = [];
$success_message = '';

// Proses perubahan profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);

    // Validasi input
    if (empty($new_username) || empty($new_email)) {
        $errors[] = "Username dan email tidak boleh kosong.";
    } else {
        // Update informasi pengguna
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        $stmt->execute(['username' => $new_username, 'email' => $new_email, 'id' => $_SESSION['user_id']]);
        $_SESSION['username'] = $new_username; // Update session username
        $success_message = "Profil berhasil diperbarui.";
        // Redirect setelah berhasil
        header("Refresh: 2; url=my_profile.php"); // Refresh setelah 2 detik
        exit;
    }
}

// Proses ganti password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "Semua field password harus diisi.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Password baru dan konfirmasi password tidak cocok.";
    } else {
        // Verifikasi password saat ini
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashed_password, 'id' => $_SESSION['user_id']]);
            $success_message = "Password berhasil diubah.";
        } else {
            $errors[] = "Password saat ini salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya - Resep Kita</title>
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
    .hidden-form{ display:none; }
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
        <li><a class="hover:text-mustard transition-colors" href="bookmarks.php">Bookmark</a></li>
        <?php if (isset($_SESSION['username'])): ?>
            <li><a class="text-mustard transition-colors" href="my_profile.php">Profil</a></li>
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

<section class="max-w-2xl mx-auto px-6 pt-6 pb-20">
    <div class="text-center mb-8">
        <span class="eyebrow">Akun Saya</span>
        <h2 class="font-display text-2xl md:text-3xl font-semibold mt-1">Profil Saya</h2>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-md px-4 py-3 mb-6 text-sm">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-md px-4 py-3 mb-6 text-sm">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <h5 class="font-display font-semibold text-lg mb-4">Informasi Pengguna</h5>
        <p class="text-sm mb-2"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p class="text-sm mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p class="text-sm"><strong>Terdaftar pada:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <h4 class="font-display font-semibold text-lg mb-3">Ubah Profil</h4>
        <p class="text-navy font-semibold text-sm cursor-pointer inline-block mb-4 border-b-2 border-dashed border-mustard" id="toggle-profile-form">Ubah profil saya</p>
        <form method="POST" id="profile-form" class="hidden-form space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium mb-1">Username</label>
                <input type="text" class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <button type="submit" class="bg-navy hover:bg-navy/90 text-white rounded-md px-6 py-2.5 font-semibold transition-colors" name="update_profile">Simpan Perubahan</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h4 class="font-display font-semibold text-lg mb-3">Ganti Password</h4>
        <p class="text-navy font-semibold text-sm cursor-pointer inline-block mb-4 border-b-2 border-dashed border-mustard" id="toggle-password-form">Ganti password saya</p>
        <form method="POST" id="password-form" class="hidden-form space-y-4">
            <div>
                <label for="current_password" class="block text-sm font-medium mb-1">Password Saat Ini</label>
                <input type="password" class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard" id="current_password" name="current_password" required>
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium mb-1">Password Baru</label>
                <input type="password" class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard" id="new_password" name="new_password" required>
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                <input type="password" class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="bg-navy hover:bg-navy/90 text-white rounded-md px-6 py-2.5 font-semibold transition-colors" name="change_password">Ubah Password</button>
        </form>
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
    document.getElementById('toggle-profile-form').addEventListener('click', function() {
        document.getElementById('profile-form').classList.toggle('hidden-form');
    });
    document.getElementById('toggle-password-form').addEventListener('click', function() {
        document.getElementById('password-form').classList.toggle('hidden-form');
    });
</script>
</body>
</html>