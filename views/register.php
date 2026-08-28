<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar - Resep Kita</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Caveat:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: { extend: {
    colors: { navy: '#211E4B', mustard: '#F5B301', cream: '#FFFCF5' },
    fontFamily: { display: ['Fraunces', 'serif'], hand: ['Caveat', 'cursive'], sans: ['Plus Jakarta Sans', 'sans-serif'] },
  }}
}
</script>
<style> body{ font-family:'Plus Jakarta Sans', sans-serif; } .eyebrow{ font-family:'Caveat', cursive; color:#F5B301; } </style>
</head>
<body class="bg-cream text-navy min-h-screen flex flex-col">

<nav class="max-w-5xl mx-auto flex items-center justify-between px-6 py-8 w-full">
    <a href="index.php" class="font-hand text-3xl font-semibold">Resep Kita</a>
    <ul class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest uppercase">
        <li><a class="hover:text-mustard transition-colors" href="index.php">Beranda</a></li>
        <li><a class="hover:text-mustard transition-colors" href="login.php">Login</a></li>
    </ul>
</nav>

<div class="flex-1 flex flex-col items-center justify-center px-6 py-6">
<div class="w-full max-w-md bg-white rounded-lg shadow-md overflow-hidden">
    <img src="assets/images/auth-illustration.png" alt="Ilustrasi memasak" class="w-40 mx-auto mt-8">
    <div class="px-8 pb-8 pt-4">
        <p class="text-center text-navy/60 mb-2 text-sm">Temukan resep favoritmu dan inspirasi kuliner setiap hari di Resep Kita!</p>
        <h3 class="font-display text-xl font-semibold text-center mb-6">Daftar</h3>
        <form action="actions/register_action.php" method="post" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium mb-1">Username</label>
                <input type="text" id="username" name="username" required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" required class="w-full rounded-md border border-navy/15 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-mustard">
            </div>
            <button type="submit" class="w-full bg-navy hover:bg-navy/90 text-white rounded-md py-2.5 font-semibold transition-colors">Daftar</button>
            <p class="text-center text-sm text-navy/70">Sudah punya akun? <a href="login.php" class="text-mustard font-semibold hover:underline">Login di sini</a></p>
        </form>
    </div>
</div>
<p class="text-center text-navy/50 text-sm mt-2 mb-10">Dengan Resep Kita, masak jadi lebih mudah dan menyenangkan!</p>
</div>

</body>
</html>