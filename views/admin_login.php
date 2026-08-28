<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        // Check if user is an admin
        if ($user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = true;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $loginError = "Anda tidak memiliki akses sebagai admin.";
        }
    } else {
        $loginError = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Resep Kita</title>
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
<body class="text-navy min-h-screen flex flex-col items-center justify-center px-6 py-12">

<a href="../index.php" class="font-hand text-3xl font-semibold mb-6">Resep Kita <span class="font-sans text-xs font-bold text-mustard uppercase tracking-widest align-middle">Admin</span></a>

<div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
    <span class="eyebrow block text-center">Panel Khusus</span>
    <h3 class="font-display text-xl font-semibold text-center mt-1 mb-6">Admin Login</h3>

    <?php if (!empty($loginError)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-md px-4 py-3 mb-4 text-sm text-center">
            <?php echo htmlspecialchars($loginError); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-4">
        <div>
            <label for="username" class="block text-sm font-medium mb-1">Username</label>
            <input type="text" name="username" id="username" required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" id="password" required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard">
        </div>
        <button type="submit" class="w-full bg-navy hover:bg-navy/90 text-white rounded-md py-2.5 font-semibold transition-colors">Login</button>
    </form>
</div>

</body>
</html>