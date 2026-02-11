-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 07:56 AM
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
-- Database: `lapor_polres_banjar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_detail`
--

CREATE TABLE `admin_detail` (
  `id_detail` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `pangkat` varchar(50) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `no_telpon` varchar(20) DEFAULT NULL,
  `tgl_dibuat` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_detail`
--

INSERT INTO `admin_detail` (`id_detail`, `id_admin`, `nip`, `pangkat`, `jabatan`, `unit_kerja`, `no_telpon`, `tgl_dibuat`) VALUES
(1, 1, '198812312010011001', 'AKP', 'Kepala Unit Reskrim', 'Polres Banjar', '081234567800', '2026-02-11 06:48:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin_log`
--

CREATE TABLE `admin_log` (
  `id` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `aktivitas` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `waktu` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_log`
--

INSERT INTO `admin_log` (`id`, `id_admin`, `aktivitas`, `ip_address`, `user_agent`, `waktu`) VALUES
(1, 1, 'Login ke sistem', '127.0.0.1', 'Mozilla/5.0', '2026-02-11 06:48:08');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id_berita` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT curdate(),
  `penulis` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id_berita`, `judul`, `isi`, `gambar`, `tanggal`, `penulis`) VALUES
(2, 'Polres Banjar Tingkatkan Patroli Malam', 'Dalam rangka meningkatkan keamanan wilayah, Polres Banjar melakukan patroli rutin setiap malam.', NULL, '2026-02-11', 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int(11) NOT NULL,
  `id_pengaduan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `komentar` text DEFAULT NULL,
  `tanggal` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `tampil` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `id_pengaduan`, `id_user`, `rating`, `komentar`, `tanggal`, `status`, `tampil`) VALUES
(1, 8, 4, 5, 'Respon cepat, terima kasih. Semoga rutin patroli.', '2026-02-11 06:56:10', 'diterima', 1),
(2, 7, 2, 4, 'Sudah ditindaklanjuti, mohon update perkembangan.', '2026-02-11 06:56:10', 'pending', 0);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Kriminal'),
(2, 'Lalu Lintas'),
(3, 'Narkoba'),
(4, 'Kekerasan'),
(5, 'Penipuan');

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id_lokasi` int(11) NOT NULL,
  `nama_lokasi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id_lokasi`, `nama_lokasi`) VALUES
(1, 'Banjarbaru Utara'),
(2, 'Banjarbaru Selatan'),
(3, 'Martapura'),
(4, 'Landasan Ulin'),
(5, 'Cempaka');

-- --------------------------------------------------------

--
-- Table structure for table `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id_pengaduan` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `notif_admin` tinyint(1) NOT NULL DEFAULT 0,
  `id_kategori` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `isi_aduan` text NOT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Public') DEFAULT 'Menunggu',
  `tanggal_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_lokasi` int(11) DEFAULT NULL,
  `bukti_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaduan`
--

INSERT INTO `pengaduan` (`id_pengaduan`, `id_user`, `notif_admin`, `id_kategori`, `judul`, `isi_aduan`, `lokasi`, `bukti`, `status`, `tanggal_selesai`, `created_at`, `updated_at`, `id_lokasi`, `bukti_file`) VALUES
(7, 2, 1, 1, 'Pencurian Motor', 'Telah terjadi pencurian motor di parkiran pasar.', 'Banjarbaru Selatan', NULL, 'Menunggu', NULL, '2026-02-11 06:50:33', '2026-02-11 06:50:33', 2, NULL),
(8, 4, 1, 2, 'Balap Liar', 'Sering terjadi balap liar pada malam hari.', 'Banjarbaru Utara', NULL, 'Diproses', NULL, '2026-02-11 06:50:33', '2026-02-11 06:50:33', 1, NULL),
(9, 5, 1, 5, 'Penipuan Online', 'Saya menjadi korban penipuan transfer.', 'Martapura', NULL, 'Selesai', NULL, '2026-02-11 06:50:33', '2026-02-11 06:50:33', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_title` varchar(255) NOT NULL DEFAULT 'Website Dalam Perawatan',
  `maintenance_msg` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tanggapan`
--

CREATE TABLE `tanggapan` (
  `id_tanggapan` int(11) NOT NULL,
  `id_pengaduan` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `isi_tanggapan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tanggapan`
--

INSERT INTO `tanggapan` (`id_tanggapan`, `id_pengaduan`, `id_admin`, `isi_tanggapan`, `tanggal`, `created_at`) VALUES
(1, 8, 1, 'Laporan diterima. Petugas akan melakukan patroli dan penertiban di lokasi.', '2026-02-11 06:56:02', '2026-02-11 06:56:02'),
(2, 9, 1, 'Kasus diproses. Silakan lengkapi bukti transfer dan kronologi untuk penyelidikan.', '2026-02-11 06:56:02', '2026-02-11 06:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','public') DEFAULT 'user',
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `role`, `no_hp`, `alamat`, `created_at`, `foto_profil`) VALUES
(1, 'Administrator', 'admin@mail.com', '0192023a7bbd73250516f069df18b500', 'admin', '-', '-', '2026-02-09 21:16:56', NULL),
(2, 'Dhera', 'user@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '-', '-', '2026-02-09 21:13:17', NULL),
(4, 'Ahmad Fauzi', 'ahmad@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567801', 'Banjarbaru', '2026-02-11 06:48:08', NULL),
(5, 'Siti Rahma', 'siti@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567802', 'Martapura', '2026-02-11 06:48:08', NULL),
(6, 'Budi Santoso', 'budi@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567803', 'Landasan Ulin', '2026-02-11 06:48:08', NULL),
(7, 'Rina Putri', 'rina@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567804', 'Cempaka', '2026-02-11 06:48:08', NULL),
(8, 'Andi Wijaya', 'andi@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567805', 'Liang Anggang', '2026-02-11 06:48:08', NULL),
(9, 'Dewi Lestari', 'dewi@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567806', 'Sungai Ulin', '2026-02-11 06:48:08', NULL),
(10, 'Rudi Hartono', 'rudi@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567807', 'Kertak Hanyar', '2026-02-11 06:48:08', NULL),
(11, 'Lina Marlina', 'lina@mail.com', '6ad14ba9986e3615423dfca256d04e3f', 'user', '081234567808', 'Gambut', '2026-02-11 06:48:08', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_detail`
--
ALTER TABLE `admin_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `admin_log`
--
ALTER TABLE `admin_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id_berita`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`),
  ADD KEY `id_pengaduan` (`id_pengaduan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id_lokasi`);

--
-- Indexes for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id_pengaduan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_lokasi` (`id_lokasi`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `tanggapan`
--
ALTER TABLE `tanggapan`
  ADD PRIMARY KEY (`id_tanggapan`),
  ADD KEY `id_pengaduan` (`id_pengaduan`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_detail`
--
ALTER TABLE `admin_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_log`
--
ALTER TABLE `admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id_berita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id_lokasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id_pengaduan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tanggapan`
--
ALTER TABLE `tanggapan`
  MODIFY `id_tanggapan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_detail`
--
ALTER TABLE `admin_detail`
  ADD CONSTRAINT `admin_detail_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD CONSTRAINT `pengaduan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengaduan_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengaduan_ibfk_3` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id_lokasi`);

--
-- Constraints for table `tanggapan`
--
ALTER TABLE `tanggapan`
  ADD CONSTRAINT `tanggapan_ibfk_1` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE,
  ADD CONSTRAINT `tanggapan_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
