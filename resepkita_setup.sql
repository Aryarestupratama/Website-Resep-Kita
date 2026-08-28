-- =========================================================
--  RESEP KITA — DATABASE SETUP + DUMMY DATA
--  Jalankan file ini di HeidiSQL / phpMyAdmin (Laragon)
-- =========================================================

CREATE DATABASE IF NOT EXISTS resepkita_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE resepkita_db;

-- ---------------------------------------------------------
-- Hapus tabel lama kalau sudah ada (biar bisa run ulang aman)
-- ---------------------------------------------------------
DROP TABLE IF EXISTS search_history;
DROP TABLE IF EXISTS bookmarks;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS resep;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------
-- Tabel: users
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: resep
-- ---------------------------------------------------------
CREATE TABLE resep (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    ingredients TEXT NOT NULL,
    steps TEXT NOT NULL,
    image_url VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: likes
-- ---------------------------------------------------------
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resep_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (resep_id, user_id),
    FOREIGN KEY (resep_id) REFERENCES resep(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: comments
-- ---------------------------------------------------------
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resep_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resep_id) REFERENCES resep(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: bookmarks
-- ---------------------------------------------------------
CREATE TABLE bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resep_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (user_id, resep_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resep_id) REFERENCES resep(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: search_history
-- ---------------------------------------------------------
CREATE TABLE search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    search_term VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================
--  DATA DUMMY
-- =========================================================

-- ---------------------------------------------------------
-- Users
-- Password untuk SEMUA akun di bawah ini adalah: password123
-- (hash dibuat dengan password_hash($pass, PASSWORD_BCRYPT))
-- ---------------------------------------------------------
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@resepkita.com', '$2b$10$MuNHPe5gdDJgOvzMLf1B/.1znRo33L.0PYgNXyYBvm4atOlDFNZZy', 'admin'),
('siti_dapur', 'siti@example.com', '$2b$10$MuNHPe5gdDJgOvzMLf1B/.1znRo33L.0PYgNXyYBvm4atOlDFNZZy', 'user'),
('budi_masak', 'budi@example.com', '$2b$10$MuNHPe5gdDJgOvzMLf1B/.1znRo33L.0PYgNXyYBvm4atOlDFNZZy', 'user'),
('rina_kitchen', 'rina@example.com', '$2b$10$MuNHPe5gdDJgOvzMLf1B/.1znRo33L.0PYgNXyYBvm4atOlDFNZZy', 'user'),
('andi_koki', 'andi@example.com', '$2b$10$MuNHPe5gdDJgOvzMLf1B/.1znRo33L.0PYgNXyYBvm4atOlDFNZZy', 'user');

-- ---------------------------------------------------------
-- Resep (pakai gambar dari folder views/uploads/ yang sudah ada)
-- ---------------------------------------------------------
INSERT INTO resep (user_id, title, ingredients, steps, image_url, created_at) VALUES
(2, 'Bakso Ayam Udang',
'500 gr daging ayam giling
200 gr udang cincang
100 gr tepung tapioka
2 butir putih telur
3 siung bawang putih, haluskan
1 sdt garam
1/2 sdt merica bubuk
Es batu secukupnya',
'1. Campur ayam giling, udang cincang, bawang putih halus, garam, dan merica dalam wadah.
2. Masukkan putih telur dan es batu, aduk rata.
3. Tambahkan tepung tapioka sedikit demi sedikit sambil diuleni.
4. Bentuk adonan menjadi bulatan bakso.
5. Rebus bakso dalam air mendidih hingga mengapung, angkat dan tiriskan.
6. Sajikan dengan kuah kaldu panas.',
'uploads/bakso-ayam-udang-foto-resep-utama.webp', '2025-01-05 10:00:00'),

(2, 'Bakso Tenggiri Kuah Cuanki',
'300 gr ikan tenggiri fillet
100 gr tepung sagu
1 butir telur
3 siung bawang putih, haluskan
1 sdt garam
1/2 sdt kaldu bubuk
Kuah kaldu sapi secukupnya',
'1. Haluskan ikan tenggiri hingga lembut.
2. Campurkan dengan tepung sagu, telur, bawang putih, garam, dan kaldu bubuk.
3. Uleni hingga adonan kalis dan bisa dibentuk.
4. Bentuk bulat-bulat kecil, rebus hingga matang dan mengapung.
5. Siapkan kuah kaldu sapi panas, masukkan bakso.
6. Sajikan dengan taburan seledri dan bawang goreng.',
'uploads/bakso-tenggiri-kuah-cuanky-foto-resep-utama.webp', '2025-01-10 14:30:00'),

(3, 'Gulai Ayam Kuliner Aceh',
'1 ekor ayam, potong 8 bagian
500 ml santan kental
3 lembar daun jeruk
2 batang serai, memarkan
1 ruas lengkuas, memarkan
Bumbu halus: 8 siung bawang merah, 5 siung bawang putih, 4 buah cabai merah, 3 butir kemiri, 1 sdt ketumbar, kunyit secukupnya',
'1. Tumis bumbu halus bersama daun jeruk, serai, dan lengkuas hingga harum.
2. Masukkan ayam, aduk hingga berubah warna.
3. Tuang santan sedikit demi sedikit sambil terus diaduk agar tidak pecah.
4. Masak dengan api kecil hingga ayam empuk dan kuah mengental.
5. Koreksi rasa dengan garam dan gula secukupnya.
6. Sajikan hangat dengan nasi putih.',
'uploads/gulai-ayam-kuliner-aceh-foto-resep-utama.webp', '2025-01-15 09:15:00'),

(3, 'Ketoprak Juhi',
'200 gr bihun, seduh air panas
2 buah tahu, goreng dan potong
100 gr taoge, seduh sebentar
2 buah lontong, potong-potong
Bumbu kacang: 200 gr kacang tanah goreng, 3 siung bawang putih, cabai rawit sesuai selera, kecap manis, air asam jawa',
'1. Haluskan bumbu kacang bersama bawang putih dan cabai.
2. Tambahkan kecap manis dan air asam jawa, aduk rata hingga jadi saus kental.
3. Tata lontong, bihun, tahu goreng, dan taoge di piring.
4. Siram dengan bumbu kacang.
5. Taburi dengan bawang goreng dan kerupuk.
6. Sajikan segera selagi segar.',
'uploads/ketoprak-juhi-foto-resep-utama.webp', '2025-01-18 12:00:00'),

(4, 'Pempek Dos Tanpa Ikan',
'250 gr tepung tapioka
150 gr tepung terigu
200 ml air panas
2 siung bawang putih, haluskan
1 sdt garam
1/2 sdt kaldu jamur',
'1. Campurkan air panas dengan bawang putih halus, garam, dan kaldu jamur.
2. Masukkan tepung terigu, aduk hingga menjadi adonan licin.
3. Tambahkan tepung tapioka sedikit demi sedikit sambil diuleni.
4. Bentuk adonan memanjang sesuai selera.
5. Rebus dalam air mendidih hingga mengapung.
6. Goreng sebentar sebelum disajikan dengan cuko pempek.',
'uploads/pempek-dos-tanpa-ikan-foto-resep-utama.webp', '2025-01-20 16:45:00'),

(4, 'Pempek Ikan Tenggiri',
'500 gr ikan tenggiri giling
250 gr tepung tapioka
200 ml air es
1 butir telur
1 sdt garam
1/2 sdt kaldu bubuk',
'1. Campur ikan tenggiri giling dengan air es, garam, dan kaldu bubuk.
2. Masukkan telur, aduk rata.
3. Tambahkan tepung tapioka sedikit demi sedikit, uleni hingga kalis.
4. Bentuk sesuai selera (lenjer, kapal selam, atau bulat).
5. Rebus dalam air mendidih hingga mengapung, angkat.
6. Goreng sebelum disajikan bersama cuko dan mentimun.',
'uploads/pempek-ikan-tenggiri-foto-resep-utama.webp', '2025-01-22 08:20:00'),

(5, 'Pempek Lenggang',
'Adonan pempek ikan tenggiri secukupnya
3 butir telur
Daun pisang untuk membungkus
Garam dan merica secukupnya',
'1. Siapkan adonan pempek ikan tenggiri yang sudah direbus dan dipotong dadu kecil.
2. Kocok telur bersama garam dan merica.
3. Campurkan potongan pempek ke dalam kocokan telur.
4. Bungkus dengan daun pisang, semat dengan lidi.
5. Panggang di atas teflon atau kukus hingga matang.
6. Sajikan dengan cuko pempek.',
'uploads/pempek-lenggang-foto-resep-utama.webp', '2025-01-25 11:10:00'),

(5, 'Pepes Tempe Telur Bebek',
'2 papan tempe, potong dadu
3 butir telur bebek
Daun pisang untuk membungkus
Bumbu halus: 5 siung bawang merah, 3 siung bawang putih, 3 buah cabai merah, kemiri, kunyit
Daun kemangi secukupnya',
'1. Haluskan semua bumbu, tumis sebentar hingga harum.
2. Campurkan tempe, telur bebek kocok, dan bumbu halus dalam wadah.
3. Tambahkan daun kemangi, aduk rata.
4. Bungkus adonan dengan daun pisang.
5. Kukus selama 20-30 menit hingga matang.
6. Bisa dibakar sebentar sebelum disajikan agar lebih harum.',
'uploads/pepes-tempe-telur-bebek-foto-resep-utama.webp', '2025-01-28 13:30:00'),

(2, 'Pepes Usus Kemangi',
'500 gr usus ayam, bersihkan
2 lembar daun salam
Daun pisang untuk membungkus
Bumbu halus: bawang merah, bawang putih, cabai merah, kemiri, kunyit, jahe
Daun kemangi secukupnya',
'1. Bersihkan usus ayam hingga benar-benar bersih dan tidak berbau.
2. Rebus sebentar usus ayam bersama daun salam.
3. Haluskan bumbu, campurkan dengan usus ayam yang sudah direbus.
4. Tambahkan daun kemangi, aduk rata.
5. Bungkus dengan daun pisang, kukus selama 20 menit.
6. Bakar sebentar sebelum disajikan.',
'uploads/pepes-usus-kemangi-foto-resep-utama.webp', '2025-02-01 15:00:00'),

(3, 'Sayur Lontong Labu Siam',
'2 buah labu siam, potong dadu
200 ml santan
2 lembar daun salam
1 batang serai, memarkan
Bumbu halus: bawang merah, bawang putih, kemiri, ketumbar
Lontong secukupnya',
'1. Tumis bumbu halus bersama daun salam dan serai hingga harum.
2. Masukkan labu siam, aduk rata.
3. Tuang santan, masak dengan api kecil hingga labu siam empuk.
4. Koreksi rasa dengan garam dan gula.
5. Sajikan hangat bersama lontong.
6. Taburi bawang goreng sebagai pelengkap.',
'uploads/sayur-lontong-labu-siam-foto-resep-utama.webp', '2025-02-03 09:45:00'),

(4, 'Serabi Ayam Balado',
'250 gr tepung beras
500 ml santan
1 sdt garam
200 gr ayam suwir
Bumbu balado: cabai merah, bawang merah, bawang putih, tomat',
'1. Campur tepung beras, santan, dan garam, aduk hingga rata dan tidak bergerindil.
2. Diamkan adonan selama 30 menit.
3. Tuang adonan ke cetakan serabi panas, masak hingga berlubang-lubang dan matang.
4. Untuk topping, tumis bumbu balado hingga matang, masukkan ayam suwir.
5. Sajikan serabi dengan topping ayam balado di atasnya.
6. Nikmati selagi hangat.',
'uploads/serabi-ayam-balado-foto-resep-utama.webp', '2025-02-05 17:20:00'),

(5, 'Tongseng Ayam',
'500 gr daging ayam, potong sesuai selera
100 gr kol, iris
2 buah tomat, potong
2 batang daun bawang, iris
Bumbu halus: bawang merah, bawang putih, kemiri, ketumbar, jahe, lengkuas
Kecap manis secukupnya',
'1. Tumis bumbu halus hingga harum.
2. Masukkan ayam, masak hingga berubah warna.
3. Tambahkan air secukupnya, masak hingga ayam empuk.
4. Masukkan kol, tomat, dan kecap manis, aduk rata.
5. Terakhir masukkan daun bawang, masak sebentar.
6. Sajikan hangat dengan nasi putih.',
'uploads/tongseng-ayam-foto-resep-utama.webp', '2025-02-08 12:50:00');

-- ---------------------------------------------------------
-- Likes (dummy interaksi antar user)
-- ---------------------------------------------------------
INSERT INTO likes (resep_id, user_id) VALUES
(1, 3), (1, 4), (1, 5),
(2, 2), (2, 5),
(3, 2), (3, 4), (3, 5),
(4, 3),
(5, 2), (5, 3),
(6, 4),
(7, 2), (7, 3), (7, 4),
(8, 5),
(9, 2),
(10, 3), (10, 4),
(11, 5),
(12, 2), (12, 3);

-- ---------------------------------------------------------
-- Comments (dummy interaksi antar user)
-- ---------------------------------------------------------
INSERT INTO comments (resep_id, user_id, comment, created_at) VALUES
(1, 3, 'Baksonya kenyal banget, keluarga suka semua!', '2025-01-06 09:00:00'),
(1, 4, 'Sudah coba, enak tapi aku tambah bawang goreng biar makin gurih.', '2025-01-06 10:30:00'),
(2, 5, 'Kuahnya mantap, cocok buat cuaca dingin.', '2025-01-11 08:00:00'),
(3, 4, 'Gulainya otentik banget rasa Acehnya, mantap!', '2025-01-16 11:00:00'),
(3, 5, 'Santannya pas, ga terlalu berat.', '2025-01-17 13:20:00'),
(4, 2, 'Ketopraknya seger, bumbu kacangnya juara.', '2025-01-19 15:00:00'),
(7, 3, 'Pempek lenggangnya unik, baru tahu ada versi ini.', '2025-01-26 09:30:00'),
(9, 4, 'Pepes usus kemanginya wangi banget pas dibakar.', '2025-02-02 14:00:00'),
(12, 5, 'Tongseng ayamnya pas banget buat makan malam.', '2025-02-09 18:00:00');

-- ---------------------------------------------------------
-- Bookmarks (dummy)
-- ---------------------------------------------------------
INSERT INTO bookmarks (user_id, resep_id) VALUES
(2, 3), (2, 7), (2, 12),
(3, 1), (3, 5),
(4, 2), (4, 9),
(5, 4), (5, 11);

-- ---------------------------------------------------------
-- Search history (dummy)
-- ---------------------------------------------------------
INSERT INTO search_history (user_id, search_term, created_at) VALUES
(2, 'bakso', '2025-02-10 08:00:00'),
(2, 'gulai ayam', '2025-02-10 08:05:00'),
(3, 'pempek', '2025-02-11 10:00:00'),
(4, 'tongseng', '2025-02-12 09:00:00'),
(5, 'pepes', '2025-02-13 11:00:00');

-- =========================================================
--  SELESAI
-- =========================================================
-- Akun untuk login:
--   Admin  -> username: admin       | password: password123
--   User   -> username: siti_dapur  | password: password123
--   User   -> username: budi_masak  | password: password123
--   User   -> username: rina_kitchen| password: password123
--   User   -> username: andi_koki   | password: password123
-- =========================================================
