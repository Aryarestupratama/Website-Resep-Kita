<?php
    include '../config/config.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }

    $resepId = $_GET['id'] ?? null;
    $userId = $_SESSION['user_id'];

    // Ambil detail resep berdasarkan resep_id
    $stmt = $pdo->prepare("SELECT title, ingredients, steps, image_url, user_id AS creator_id FROM resep WHERE id = :resep_id");
    $stmt->execute([':resep_id' => $resepId]);
    $resep = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resep) {
        echo "Resep tidak ditemukan.";
        exit;   
    }

    // Ambil username dari creator resep
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $resep['creator_id']]);
    $creator = $stmt->fetch(PDO::FETCH_ASSOC);

    // Hitung jumlah like untuk resep ini
    $likeStmt = $pdo->prepare("SELECT COUNT(*) as total_likes FROM likes WHERE resep_id = :resep_id");
    $likeStmt->execute([':resep_id' => $resepId]);
    $totalLikes = $likeStmt->fetch(PDO::FETCH_ASSOC)['total_likes'];

    // Cek apakah pengguna sudah memberikan "like" pada resep ini
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE resep_id = :resep_id AND user_id = :user_id");
    $stmt->execute([':resep_id' => $resepId, ':user_id' => $userId]);
    $userLiked = $stmt->fetchColumn() > 0;

    // Ambil semua komentar untuk resep ini
    $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE resep_id = :resep_id ORDER BY comments.created_at DESC");
    $stmt->execute([':resep_id' => $resepId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($resep['title']) ?> - Resep Kita</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    .notif-box{ position:fixed; top:20px; right:20px; z-index:1000; display:flex; align-items:center; gap:8px; background:#211E4B; color:#fff; padding:12px 20px; border-radius:8px; opacity:0; transform:translateY(-10px); transition: all .4s; font-size:0.9rem; }
    .notif-box.show{ opacity:1; transform:translateY(0); }
    .notif-box.error{ background:#B5222A; }
    .liked{ color:#F5B301 !important; }
</style>
</head>
<body class="text-navy">
<!-- Notifikasi -->
<?php
if (isset($_SESSION['notif'])):
    $notif = $_SESSION['notif'];
    $notifClass = $notif['type'] === 'success' ? 'success' : 'error';
?>
    <div class="notif-box <?= $notifClass ?>">
        <span class="icon">
            <?php if ($notif['type'] === 'success'): ?>
                &#10004;
            <?php else: ?>
                &#9888;
             <?php endif; ?>
        </span>
        <span><?= htmlspecialchars($notif['message']) ?></span>
    </div>
    <?php
        unset($_SESSION['notif']);
    endif;
?>

<nav class="max-w-5xl mx-auto flex items-center justify-between px-6 py-8">
    <a href="index.php" class="font-hand text-3xl font-semibold">Resep Kita</a>
    <button id="navToggle" class="md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
    </button>
    <ul id="navMenu" class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest uppercase">
        <li><a class="hover:text-mustard transition-colors" href="index.php">Beranda</a></li>
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

<div class="max-w-2xl mx-auto px-6 pb-20">
    <span class="eyebrow">Resep</span>
    <h1 class="font-display text-2xl md:text-3xl font-semibold mt-1 mb-6"><?= htmlspecialchars($resep['title']) ?></h1>

    <div class="mb-8">
        <?php if (!empty($resep['image_url'])): ?>
            <img src="<?= htmlspecialchars($resep['image_url']) ?>" alt="<?= htmlspecialchars($resep['title']) ?>" class="w-full max-h-[420px] object-cover rounded-md">
        <?php else: ?>
            <p class="text-center text-navy/40 py-16 bg-white rounded-lg">Tidak ada gambar</p>
        <?php endif; ?>
    </div>

    <div class="mb-8">
        <h2 class="font-display font-semibold text-xl mb-2">Bahan-bahan</h2>
        <p class="text-navy/80 leading-relaxed"><?= nl2br(htmlspecialchars($resep['ingredients'])) ?></p>

        <h2 class="font-display font-semibold text-xl mt-6 mb-2">Langkah-langkah</h2>
        <p class="text-navy/80 leading-relaxed"><?= nl2br(htmlspecialchars($resep['steps'])) ?></p>
    </div>

    <div class="text-sm text-navy/60 mb-6">
        <p>Diunggah oleh <strong class="text-navy"><?= htmlspecialchars($creator['username']) ?></strong></p>
    </div>

    <div class="flex items-center gap-8 mb-10 border-t border-b border-navy/10 py-4">
        <form action="actions/like_action.php" method="GET" style="display: inline;">
            <input type="hidden" name="resep_id" value="<?= $resepId ?>">
            <input type="hidden" name="action" value="<?= $userLiked ? 'unlike' : 'like' ?>">
            <button type="submit" class="flex items-center gap-2 text-navy/70 hover:text-mustard transition-colors <?= $userLiked ? 'liked' : '' ?>">
                <i data-feather="heart" style="width:18px;height:18px;"></i>
                <span class="text-sm font-semibold"><?= $totalLikes ?></span>
            </button>
        </form>

        <div class="flex items-center gap-2 text-navy/70 cursor-pointer" onclick="toggleComments()">
            <i data-feather="message-square" style="width:18px;height:18px;"></i>
            <span class="text-sm font-semibold"><?= count($comments) ?></span>
        </div>
    </div>

    <div class="comments-container">
        <h2 class="font-display font-semibold text-xl mb-4">Komentar</h2>
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="bg-white rounded-lg shadow-sm p-4 mb-3">
                    <strong class="text-sm"><?= htmlspecialchars($comment['username']) ?>:</strong>
                    <p class="text-sm text-navy/80 mt-1"><?= htmlspecialchars($comment['comment']) ?></p>
                    <small class="text-navy/40 text-xs"><?= $comment['created_at'] ?></small>

                    <?php if ($comment['user_id'] == $userId || $resep['creator_id'] == $userId): ?>
                    <form id="deleteForm" action="actions/delete_comment_action.php" method="POST" style="display:inline;">
                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                        <input type="hidden" name="resep_id" value="<?= $resepId ?>">
                        <button type="submit" class="block text-xs font-semibold text-red-500 hover:text-red-700 mt-2" id="deleteButton">Hapus</button>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-navy/40 text-sm">Tidak ada komentar untuk resep ini.</p>
        <?php endif; ?>

        <div class="mt-6">
            <form action="actions/comment_action.php" method="POST" class="flex items-center gap-3">
                <textarea name="comment" required placeholder="Tulis komentar..." class="flex-1 rounded-md border border-navy/15 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-mustard" rows="1"></textarea>
                <input type="hidden" name="resep_id" value="<?= $resepId ?>">
                <button type="submit" class="bg-navy hover:bg-navy/90 text-white rounded-md px-6 py-2.5 text-sm font-semibold transition-colors">Kirim</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleComments() {
        const commentsContainer = document.querySelector('.comments-container');
        commentsContainer.style.display = (commentsContainer.style.display === 'none' || commentsContainer.style.display === '') ? 'block' : 'none';
    }
    document.getElementById('navToggle').addEventListener('click', () => {
        document.getElementById('navMenuMobile').classList.toggle('hidden');
        document.getElementById('navMenuMobile').classList.toggle('flex');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notifBox = document.querySelector('.notif-box');
        if (notifBox) {
            setTimeout(() => { notifBox.classList.add('show'); }, 100);
            setTimeout(() => {
                notifBox.classList.remove('show');
                setTimeout(() => { notifBox.remove(); }, 500);
            }, 4000);
        }
    });
</script>
<script>
    const deleteBtn = document.querySelector('#deleteButton');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Anda yakin ingin menghapus komentar ini?',
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#211E4B',
                cancelButtonColor: '#B5222A',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('#deleteForm').submit();
                }
            });
        });
    }
</script>
<script>
    feather.replace();
</script>
</body>
</html>