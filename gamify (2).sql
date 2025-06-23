-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 01:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamify`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(64) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `content`, `created_at`) VALUES
(3, 'Tria Yunita Krismiyanto', 'Aduh', '2024-12-18 06:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `informasipromo`
--

CREATE TABLE `informasipromo` (
  `id` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `promo_type` enum('discount','bonus') NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `bonus_item` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `informasipromo`
--

INSERT INTO `informasipromo` (`id`, `id_produk`, `title`, `description`, `photo_url`, `photo`, `promo_type`, `start_date`, `end_date`, `discount_percentage`, `bonus_item`, `created_at`) VALUES
(44, 0, 'PC DENGAN BONUS MONITOR!!!', 'Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', '../uploads/fotosiswa.jpeg', NULL, 'bonus', '2024-12-20', '2024-12-27', 0, 4, '2024-12-20 07:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'CONTROLER', '', '2024-11-11 21:37:50'),
(2, 'PC', '', '2024-11-11 21:38:00'),
(3, 'AKSESORIS', '', '2024-11-11 21:38:01'),
(4, 'GAMING', '', '2024-11-11 21:38:03'),
(5, 'CONSOLE', '', '2024-12-09 13:29:15');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id_keranjang` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `komentar`
--

CREATE TABLE `komentar` (
  `id_komentar` int(11) NOT NULL,
  `id_topik` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `isi_komentar` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komentar`
--

INSERT INTO `komentar` (`id_komentar`, `id_topik`, `id_user`, `isi_komentar`, `created_at`) VALUES
(1, 1, 5, 'Anjay', '2024-12-18 08:05:46'),
(2, 2, 5, 'Ada budget berapa kak?', '2024-12-18 10:09:12'),
(3, 1, 5, '', '2024-12-18 10:43:21'),
(4, 3, 4, 'Hai', '2024-12-18 13:02:16'),
(5, 3, 5, 'Keren\r\n', '2024-12-18 13:03:48'),
(6, 3, 6, 'Apa nih yang keren\r\n', '2024-12-18 13:04:05'),
(7, 3, 6, 'Kabarin dong\r\n', '2024-12-18 13:04:14'),
(8, 3, 6, 'Baru nimbrung nih', '2024-12-18 13:04:21'),
(9, 3, 5, 'Nih mending PS 5 atau 4\r\n', '2024-12-18 13:04:49'),
(10, 3, 5, 'Yah menurutku aku PS 5 aja gak sih', '2024-12-18 13:05:00'),
(11, 3, 5, 'Aku pengennya itu', '2024-12-18 13:05:05'),
(12, 3, 4, 'Yah kalau menurut admin sih,mana baiknya buat kamu', '2024-12-18 13:05:34'),
(13, 3, 4, 'Semua barang dikita ready yah,jangan lupa checkout', '2024-12-18 13:05:49'),
(14, 3, 4, 'Semangatt', '2024-12-18 13:05:53'),
(15, 3, 2, 'haduh', '2024-12-19 17:21:15'),
(16, 6, 6, 'Mending PS sih kalo mau ngegame doang\r\n', '2024-12-19 17:41:57'),
(17, 6, 1, 'Betul sekali mas asep prayogi\r\n', '2024-12-19 19:02:39');

-- --------------------------------------------------------

--
-- Table structure for table `komunitas`
--

CREATE TABLE `komunitas` (
  `id_komunitas` int(11) NOT NULL,
  `nama_komunitas` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `dibuat_oleh` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komunitas`
--

INSERT INTO `komunitas` (`id_komunitas`, `nama_komunitas`, `deskripsi`, `dibuat_oleh`, `created_at`, `gambar`) VALUES
(1, 'PS 5', 'Kelz bang', 5, '2024-12-18 06:18:29', NULL),
(2, 'CONSOLE', 'MANTAP', 5, '2024-12-18 06:48:30', NULL),
(3, 'PC', 'MANTAP', 5, '2024-12-18 06:57:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('admin','user') NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id_message`, `user_id`, `sender`, `message`, `timestamp`) VALUES
(6, 1, 'user', 'Hallo', '2024-10-29 00:04:33'),
(7, 1, 'admin', 'Hai rafi', '2024-10-29 00:04:43'),
(8, 1, 'user', 'Aku ada masalah nih', '2024-10-29 00:04:53'),
(9, 1, 'admin', 'Masalah apa tuh kalau boleh tau', '2024-10-29 00:05:03'),
(10, 1, 'user', 'Waduh apa yah', '2024-10-29 00:05:13'),
(11, 1, 'admin', 'Jangan ragu kalau mau ceita', '2024-10-29 00:05:27'),
(12, 1, 'user', 'Waduh apa yah', '2024-10-29 00:05:36'),
(13, 1, 'admin', 'Jangan ragu kalau mau ceita', '2024-10-29 00:05:42'),
(14, 1, 'admin', 'Hai', '2024-11-17 23:56:39'),
(15, 1, 'user', 'Hallo min', '2024-11-17 23:57:01'),
(16, 1, 'user', '', '2024-11-17 23:57:01'),
(17, 5, 'user', 'Hai', '2024-11-18 01:26:59'),
(18, 2, 'user', 'Halo', '2024-12-15 08:13:16'),
(19, 2, 'admin', 'Iya ada apa ya? apakah ada yang bisa kami bantu?', '2024-12-15 08:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `type` enum('admin','user') NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `id_user`, `id_produk`, `type`, `title`, `message`, `image`, `created_at`, `is_read`) VALUES
(155, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-14 15:40:25', 1),
(156, 2, NULL, 'user', 'Pesanan Selesai', 'Pesanan Anda telah selesai. Terima kasih!', NULL, '2024-12-14 16:20:40', 1),
(157, 2, NULL, 'user', 'Pesanan Selesai', 'Pesanan Anda telah selesai. Terima kasih!', NULL, '2024-12-14 16:20:41', 1),
(159, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-15 08:05:34', 1),
(163, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-16 04:45:50', 1),
(165, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(166, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(167, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(168, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(169, 2, NULL, 'user', 'Promo Tersedia', 'Promo test sedang berlangsung! Diskon hingga 20%. test', NULL, '2024-12-17 10:34:08', 1),
(170, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 5.720.000', NULL, '2024-12-19 15:09:51', 1),
(171, NULL, 2, 'admin', 'Stok Produk Rendah', 'Produk XBOX Series X tersisa 1 unit', NULL, '2024-12-19 15:11:22', 1),
(172, NULL, 3, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk Dualshock Controller dengan rating 5/5', NULL, '2024-12-19 15:48:20', 0),
(173, 2, NULL, 'user', 'Pesanan Selesai', 'Pesanan Anda telah selesai. Terima kasih!', NULL, '2024-12-19 16:21:08', 1),
(174, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 16:38:45', 0),
(175, NULL, 3, 'admin', 'Review Baru', 'Review baru dari 2 untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-19 16:38:45', 0),
(176, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 16:51:02', 0),
(177, NULL, 3, 'admin', 'Review Baru', 'Review baru dari 2 untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-19 17:02:34', 0),
(178, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2024-12-19 17:05:27', 0),
(179, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 6.020.000', NULL, '2024-12-19 17:05:27', 1),
(180, NULL, 3, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-19 17:10:26', 0),
(181, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:24', 0),
(182, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:24', 1),
(183, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:24', 0),
(184, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:24', 1),
(185, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 18:57:25', 0),
(186, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-19 18:57:25', 1),
(187, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 18:57:25', 0),
(188, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-19 18:57:25', 1),
(189, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:25', 0),
(190, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:25', 1),
(191, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:25', 0),
(192, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:25', 1),
(193, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 18:57:25', 0),
(194, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-19 18:57:25', 1),
(195, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:26', 0),
(196, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:26', 1),
(197, 2, NULL, 'user', 'Promo Tersedia', 'Promo Promo Binus sedang berlangsung! Dapatkan bonus Test. Bonus Promo Baru', NULL, '2024-12-20 06:41:04', 1),
(198, 2, NULL, 'user', 'Promo Tersedia', 'Promo PC DENGAN BONUS MONITOR!!! sedang berlangsung! Dapatkan bonus 4. Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', NULL, '2024-12-20 07:12:19', 1),
(199, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 55.520.000', NULL, '2024-12-20 07:42:23', 0),
(200, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 55.520.000', NULL, '2024-12-20 07:42:24', 0),
(201, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 55.520.000', NULL, '2024-12-20 07:42:24', 0),
(202, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 55.520.000', NULL, '2024-12-20 07:42:24', 0),
(203, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-20 07:42:24', 0),
(204, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-20 07:42:24', 0),
(205, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-20 09:50:33', 0),
(206, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-20 09:50:33', 0),
(207, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:33', 0),
(208, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(209, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(210, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(211, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(212, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(213, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(214, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(215, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(216, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(217, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:35', 0),
(218, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pembatalan_pesanan`
--

CREATE TABLE `pembatalan_pesanan` (
  `id_pembatalan` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `alasan_pembatalan` enum('berubah_pikiran','harga_lebih_murah','barang_salah','pengiriman_lama','masalah_pembayaran','lainnya') NOT NULL,
  `deskripsi_pembatalan` text DEFAULT NULL,
  `tanggal_pembatalan` datetime NOT NULL,
  `catatan_admin` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status_pembayaran` enum('Belum Dibayar','Dibayar') DEFAULT 'Belum Dibayar',
  `tanggal_pembayaran` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`) VALUES
(37, 41, 'Transfer Bank', 'Dibayar', '2024-12-17 17:54:53'),
(38, 42, 'Kartu Kredit', 'Dibayar', '2024-12-18 17:21:08'),
(39, 43, 'Kartu Kredit', 'Dibayar', '2024-12-18 17:21:55'),
(40, 44, 'Kartu Kredit', 'Dibayar', '2024-12-18 17:27:17'),
(41, 45, 'Transfer Bank', 'Dibayar', '2024-12-18 17:40:59'),
(42, 46, 'Transfer Bank', 'Dibayar', '2024-12-18 17:51:22'),
(43, 47, 'COD', 'Dibayar', '2024-12-18 18:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman_pesanan`
--

CREATE TABLE `pengiriman_pesanan` (
  `id_pengiriman` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nomor_resi` varchar(50) NOT NULL,
  `nama_kurir` varchar(100) NOT NULL,
  `alamat_pengiriman` text NOT NULL,
  `tanggal_kirim` datetime NOT NULL,
  `perkiraan_tiba` datetime DEFAULT NULL,
  `tanggal_tiba` datetime DEFAULT NULL,
  `status_pengiriman` enum('dalam_pengiriman','sudah_sampai','terlambat') NOT NULL,
  `biaya_kirim` decimal(10,2) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman_pesanan`
--

INSERT INTO `pengiriman_pesanan` (`id_pengiriman`, `id_pesanan`, `id_user`, `nomor_resi`, `nama_kurir`, `alamat_pengiriman`, `tanggal_kirim`, `perkiraan_tiba`, `tanggal_tiba`, `status_pengiriman`, `biaya_kirim`, `dibuat_pada`, `diperbarui_pada`) VALUES
(37, 41, 2, 'RSI20241217115453184', 'J&T', 'Koperindag Blok C No 3', '2024-12-17 11:54:53', '2024-12-20 11:54:53', NULL, 'dalam_pengiriman', 20000.00, '2024-12-17 10:54:53', '2024-12-17 10:54:53'),
(38, 42, 2, 'RSI20241218112108107', 'JNE', 'Koperindag Blok C No 3', '2024-12-18 11:21:08', '2024-12-21 11:21:08', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:21:08', '2024-12-18 10:21:08'),
(39, 43, 2, 'RSI20241218112155840', 'J&T', 'Koperindag Blok C No 3', '2024-12-18 11:21:55', '2024-12-21 11:21:55', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:21:55', '2024-12-18 10:21:55'),
(40, 44, 2, 'RSI20241218112717277', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-18 11:27:17', '2024-12-21 11:27:17', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:27:17', '2024-12-18 10:27:17'),
(41, 45, 2, 'RSI20241218114059775', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-18 11:40:59', '2024-12-21 11:40:59', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:40:59', '2024-12-18 10:40:59'),
(42, 46, 2, 'RSI20241218115122358', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-18 11:51:22', '2024-12-21 11:51:22', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:51:22', '2024-12-18 10:51:22'),
(43, 47, 2, 'RSI20241218120110807', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-18 12:01:10', '2024-12-21 12:01:10', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 11:01:10', '2024-12-18 11:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(64) NOT NULL,
  `tanggal_pesanan` datetime DEFAULT current_timestamp(),
  `status_pesanan` enum('Menunggu Pembayaran','Diproses','Dikirim','Selesai','Dibatalkan') DEFAULT 'Menunggu Pembayaran',
  `total_harga` decimal(10,2) NOT NULL,
  `notifikasi_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `stok`, `id_kategori`, `created_at`, `updated_at`, `gambar`) VALUES
(2, 'XBOX Series X', 'Xbox ', 6000000.00, 600, 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Xbox Series X - 1TB Digital Edition (White).png'),
(3, 'Dualshock Controller', 'Controller Dual Shock Terbaru', 500000.00, 121, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PS 5DualSense Wireless Controller.png'),
(5, 'Playstasion 5', 'PLaystation 5 ORI', 5000000.00, 500, 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '685915e91100d_PS 5.jpg'),
(6, 'Nintendo Switch', 'Nintendo Switch ORI', 3000000.00, 500, 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '6859161f1fc7a_Nintendo Switch™ -OLED Model.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `discount_type` enum('fixed','percentage') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `usage_limit` int(11) NOT NULL DEFAULT 1,
  `times_used` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`id`, `code`, `discount_type`, `discount_value`, `usage_limit`, `times_used`, `created_at`) VALUES
(10, 'IVRKH', 'percentage', 50.00, 0, 20, '2024-12-17 06:17:10'),
(11, 'ESOUK', 'fixed', 15000.00, 0, 20, '2024-12-17 06:17:23'),
(12, 'ACD5Y', 'fixed', 15000.00, 0, 20, '2024-12-17 06:28:02'),
(13, '2HSLK', 'fixed', 300000.00, 105, 45, '2024-12-19 13:19:49'),
(14, '14J60', 'fixed', 200000.00, 39, 11, '2024-12-19 19:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `review_produk`
--

CREATE TABLE `review_produk` (
  `id_review` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `komentar_admin` varchar(255) DEFAULT NULL,
  `tanggal_review` datetime DEFAULT current_timestamp(),
  `notifikasi_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_produk`
--

INSERT INTO `review_produk` (`id_review`, `id_user`, `id_produk`, `id_pesanan`, `rating`, `komentar`, `komentar_admin`, `tanggal_review`, `notifikasi_status`) VALUES
(3, 2, 2, 8, 5, 'GEGE', NULL, '2024-12-14 15:17:46', 1),
(4, 2, 2, 6, 5, 'BAGUS', NULL, '2024-12-14 15:48:28', 1),
(5, 2, 3, 9, 5, 'Mantap', NULL, '2024-12-14 16:58:03', 1),
(6, 2, 2, 12, 5, 'Sangat Bagus', NULL, '2024-12-14 23:20:30', 1),
(7, 2, 3, 10, 5, 'Sangat Baik', NULL, '2024-12-14 23:32:30', 1),
(8, 2, 2, 11, 3, 'Kualitas Barang Sesuai Harga', NULL, '2024-12-14 23:32:48', 1),
(9, 2, 2, 13, 1, 'Foto produknya jelek', 'Mang Ngapa', '2024-12-16 11:47:47', 1),
(10, 2, 3, 60, 5, 'DISKON', NULL, '2024-12-19 22:48:05', 1),
(11, 2, 3, 61, 5, 'MANTAP', NULL, '2024-12-19 23:37:51', 1),
(12, 2, 3, 63, 5, 'i', NULL, '2024-12-20 00:02:20', 1),
(13, 2, 3, 56, 5, 'tes', NULL, '2024-12-20 00:10:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `topik`
--

CREATE TABLE `topik` (
  `id_topik` int(11) NOT NULL,
  `id_komunitas` int(11) NOT NULL,
  `judul_topik` varchar(255) NOT NULL,
  `deskripsi_topik` text NOT NULL,
  `dibuat_oleh` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topik`
--

INSERT INTO `topik` (`id_topik`, `id_komunitas`, `judul_topik`, `deskripsi_topik`, `dibuat_oleh`, `created_at`) VALUES
(1, 3, 'Saran PC ', 'Butuh PC SPEK GEMING BUDGET 1 JT', 5, '2024-12-18 07:12:12'),
(2, 2, 'Saran Console', 'Saran dong gez', 5, '2024-12-18 10:08:57'),
(3, 1, 'PS 5?', 'Saran PS4 atau PS 5 nih', 5, '2024-12-18 10:45:14'),
(6, 1, 'Bingung', 'PC apa PS?', 2, '2024-12-19 17:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_tlp` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `level` enum('admin','user') NOT NULL,
  `foto` varchar(255) NOT NULL,
  `active` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `no_tlp`, `alamat`, `level`, `foto`, `active`) VALUES
(1, 'Hajjid Rafi Mumtaz', 'rafimumtaz86@gmail.com', '123', '081513099954', 'Bekasi City', 'admin', '1734507173_messages-3.jpg', '2025-06-23 15:46:38'),
(2, 'Tria Yunita Krismiyanto', 'triayunita07@gmail.com', '123', '0888883838383', 'Bangkalan Halim Perdana Kusuma 2\r\n', 'user', '1734507541_messages-2.jpg', '2024-12-20 16:50:56'),
(5, 'Tria Krismiyanto Yunita', 'triayunita02@gmail.com', '123', '08587617989', 'Koperindag Blok C No 3', 'user', '', '2024-12-18 13:48:06'),
(6, 'asep prayogi', 'asepyogi@gmail.com', '123', '123456', 'Medan', 'user', '', '2024-12-20 00:41:38'),
(7, 'jauhari', 'jauhari@gmail.com', '123', '085311999046', 'Koperindag Blok C No 3', 'user', '1750668866_messages-3.jpg', '2025-06-23 15:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_promo_codes`
--

CREATE TABLE `user_promo_codes` (
  `id_user_promo_code` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `promo_id` int(11) NOT NULL,
  `promo_code` varchar(255) NOT NULL,
  `times_used` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishlist` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notifikasi_restock` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id_wishlist`, `user_id`, `id_produk`, `created_at`, `updated_at`, `notifikasi_restock`) VALUES
(2, 1, 2, '2024-12-19 18:00:41', '2024-12-19 18:00:41', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `informasipromo`
--
ALTER TABLE `informasipromo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`id_komentar`),
  ADD KEY `id_topik` (`id_topik`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `komunitas`
--
ALTER TABLE `komunitas`
  ADD PRIMARY KEY (`id_komunitas`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  ADD PRIMARY KEY (`id_pembatalan`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`) USING BTREE,
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_user` (`id_user`) USING BTREE;

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `review_produk`
--
ALTER TABLE `review_produk`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `topik`
--
ALTER TABLE `topik`
  ADD PRIMARY KEY (`id_topik`),
  ADD KEY `id_komunitas` (`id_komunitas`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  ADD PRIMARY KEY (`id_user_promo_code`),
  ADD UNIQUE KEY `promo_code` (`promo_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `informasipromo`
--
ALTER TABLE `informasipromo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `komentar`
--
ALTER TABLE `komentar`
  MODIFY `id_komentar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `komunitas`
--
ALTER TABLE `komunitas`
  MODIFY `id_komunitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  MODIFY `id_pembatalan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  MODIFY `id_pengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `review_produk`
--
ALTER TABLE `review_produk`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `topik`
--
ALTER TABLE `topik`
  MODIFY `id_topik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  MODIFY `id_user_promo_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `komunitas`
--
ALTER TABLE `komunitas`
  ADD CONSTRAINT `komunitas_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  ADD CONSTRAINT `pembatalan_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `pembatalan_pesanan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  ADD CONSTRAINT `pengiriman_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pengiriman_pesanan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `review_produk`
--
ALTER TABLE `review_produk`
  ADD CONSTRAINT `review_produk_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `review_produk_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `topik`
--
ALTER TABLE `topik`
  ADD CONSTRAINT `topik_ibfk_1` FOREIGN KEY (`id_komunitas`) REFERENCES `komunitas` (`id_komunitas`),
  ADD CONSTRAINT `topik_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
