-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 22, 2026 at 05:57 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpuskita`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `user_id`, `nama_lengkap`, `nis`, `alamat`) VALUES
(1, 2, 'huda', '12345', 'sepaten'),
(2, 3, 'nuril', '122345', 'kajoran'),
(3, 4, 'zian', '345', 'sidowangi');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int NOT NULL,
  `judul` varchar(150) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `stok` int DEFAULT '0',
  `isi_buku` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sampul` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `judul`, `penulis`, `stok`, `isi_buku`, `updated_at`, `sampul`) VALUES
(1, 'Langkah Mudah Belajar Pemrograman PHP Menggunakan CodeIgniter 4 Untuk Pemula', ' Randi Adrika Putra', 6, 'Buku ini dirancang sebagai panduan praktis bagi para pemula yang ingin menguasai pembuatan aplikasi web menggunakan framework populer, CodeIgniter 4. Di dalamnya, pembaca akan diajak mengenal dasar-dasar bahasa pemrograman PHP hingga implementasi desain pola MVC (Model-View-Controller) yang menjadi standar industri saat ini.\r\n\r\nFokus utama buku ini adalah memberikan pemahaman yang sistematis mengenai:\r\n- Persiapan lingkungan kerja (local server).\r\n- Dasar-dasar routing, controller, dan view di CodeIgniter 4.\r\n- Interaksi database menggunakan sistem Query Builder dan Model.\r\n- Pembuatan fitur aplikasi nyata seperti pengelolaan data (CRUD).\r\n\r\nDengan bahasa yang sederhana dan contoh kode yang aplikatif, buku ini bertujuan membantu siapa saja agar mampu membangun aplikasi web yang rapi, cepat, dan aman dari nol.', '2026-04-22 04:09:30', '1776670118_sampul belajar php.jpg'),
(3, 'Navigation to Global', 'Naho', 139, 'Di era ketika peta dunia masih dipenuhi dengan area kosong yang misterius, seorang navigator muda menemukan sebuah kompas kuno yang tidak menunjuk ke arah utara, melainkan ke sebuah koordinat yang tidak tercatat dalam sejarah.\r\n\r\n\"Navigation to Global\" mengisahkan perjalanan epik melintasi samudra yang tak terjamah dan daratan tersembunyi. Sang tokoh utama harus menghadapi badai dahsyat, persaingan antar penjelajah, dan rahasia kuno yang terkunci di balik simbol-simbol kompas tersebut. Ini bukan sekadar pencarian wilayah baru, melainkan sebuah misi untuk menyatukan potongan-potongan sejarah dunia yang hilang demi masa depan peradaban global.\r\n\r\nSetiap putaran jarum kompas membawa mereka lebih dekat pada kebenaran: bahwa dunia jauh lebih luas dan lebih ajaib daripada yang pernah mereka bayangkan.', '2026-04-22 04:09:31', '1776670421_buku.jpg'),
(5, ' Rekayasa Perangkat Lunak', 'Indah Purnama Sari, S.T., M.Kom.', 10, 'Buku ini merupakan panduan ajar yang membahas secara sistematis konsep dan praktik pengembangan perangkat lunak bagi mahasiswa teknologi informasi. Materinya mencakup:\r\n\r\n- Definisi dan karakteristik perangkat lunak.\r\n- Prinsip-prinsip dan metodologi pengembangan (seperti Waterfall, Agile, dll).\r\n- Tahapan manajemen proyek, mulai dari perencanaan, analisis kebutuhan, perancangan antarmuka (UI), hingga desain basis data.\r\n- Teknis penulisan kode program, pengujian (testing), serta pemeliharaan (maintenance) perangkat lunak.', '2026-04-22 03:40:05', '1776829205_rekayasa.jpg'),
(6, ' Cuan dari Investasi Saham & Reksa Dana (Mulai Rp100K)', 'Nofiandi Riawan', 15, 'Buku ini adalah panduan praktis yang ditujukan bagi investor pemula yang ingin mulai membangun aset masa depan dengan modal terjangkau. Poin-poin utamanya meliputi:\r\n\r\n- Langkah Awal: Panduan mendaftar di sekuritas atau memilih Manajer Investasi.\r\n- Strategi Modal Kecil: Memaparkan cara membeli saham atau reksa dana hanya dengan modal Rp100.000.\r\n- Pemilihan Produk: Tips memilih jenis instrumen investasi yang tepat dan aman agar tidak salah langkah.\r\n- Tujuan Finansial: Memotivasi pembaca untuk menjadikan investasi sebagai gaya hidup demi mencapai financial freedom di masa depan.', '2026-04-22 03:44:31', '1776829471_img20210628_16241951.jpg'),
(7, 'Laravel Untuk Pemula', 'Fika Ridaul Maulayya', 155, 'Ebook ini disusun khusus untuk pengembang pemula yang ingin mempelajari framework Laravel dari dasar secara terstruktur dan sistematis. Materi yang dibahas meliputi:\r\n\r\n- Pengenalan Dasar: Memahami apa itu Laravel dan persiapan alat (tools) yang dibutuhkan.\r\n- Konsep Dasar Web: Pembahasan mengenai Routing, Middleware, Controllers, dan Requests/Responses.\r\n- Manajemen Database: Teknik penggunaan Migrations, Query Builder, dan Eloquent ORM (termasuk Relationships).\r\n- Fitur Lanjutan: Validasi data, Accessors, Mutators, serta Seeding database.', '2026-04-22 03:46:39', '1776829599_Tanpa Judul.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `identitas`
--

CREATE TABLE `identitas` (
  `id` int NOT NULL,
  `nama_kepsek` varchar(100) DEFAULT NULL,
  `nama_pustakawan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `identitas`
--

INSERT INTO `identitas` (`id`, `nama_kepsek`, `nama_pustakawan`) VALUES
(1, 'Drs. Nama Kepala Sekolah', 'Nama Petugas Perpus, S.IIP');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `anggota_id` int NOT NULL,
  `buku_id` int NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `status` enum('pinjam','kembali') DEFAULT 'pinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `anggota_id`, `buku_id`, `tgl_pinjam`, `tgl_kembali`, `status`) VALUES
(1, 1, 1, '2026-04-20', '2026-04-20', 'kembali'),
(2, 2, 1, '2026-04-20', '2026-04-20', 'kembali'),
(3, 2, 1, '2026-04-20', '2026-04-20', 'kembali'),
(4, 2, 3, '2026-04-20', '2026-04-20', 'kembali'),
(5, 2, 1, '2026-04-20', '2026-04-20', 'kembali'),
(6, 2, 3, '2026-04-20', '2026-04-20', 'kembali'),
(7, 2, 2, '2026-04-20', '2026-04-27', 'pinjam'),
(8, 3, 3, '2026-04-20', '2026-04-20', 'kembali'),
(9, 3, 1, '2026-04-20', '2026-04-27', 'kembali'),
(10, 2, 3, '2026-04-20', '2026-04-20', 'kembali'),
(11, 2, 1, '2026-04-20', '2026-04-20', 'kembali'),
(12, 2, 3, '2026-04-20', '2026-04-27', 'kembali'),
(13, 2, 3, '2026-04-21', '2026-04-28', 'kembali'),
(14, 2, 3, '2026-04-22', '2026-04-29', 'kembali'),
(15, 2, 1, '2026-04-22', '2026-04-29', 'kembali'),
(16, 2, 3, '2026-04-22', '2026-04-29', 'kembali'),
(17, 2, 1, '2026-04-22', '2026-04-29', 'kembali'),
(18, 3, 3, '2026-04-22', '2026-04-29', 'kembali'),
(19, 3, 1, '2026-04-22', '2026-04-29', 'kembali'),
(20, 3, 1, '2026-04-22', '2026-04-29', 'kembali'),
(21, 3, 1, '2026-04-22', '2026-04-29', 'kembali'),
(22, 3, 1, '2026-04-22', '2026-04-29', 'kembali');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin123', 'admin'),
(2, 'siswa', 'siswa123', 'siswa'),
(3, 'huda', '123', 'siswa'),
(4, 'zian', '123', 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `identitas`
--
ALTER TABLE `identitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `identitas`
--
ALTER TABLE `identitas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
