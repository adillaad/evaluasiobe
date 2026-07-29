-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Jan 2023 pada 17.42
-- Versi server: 10.4.18-MariaDB
-- Versi PHP: 7.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smmp`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `minggu` varchar(255) NOT NULL,
  `sub_cpmk` mediumtext NOT NULL,
  `indikator` text NOT NULL,
  `kriteria` text DEFAULT NULL,
  `metode_luring` text DEFAULT NULL,
  `metode_daring` text DEFAULT NULL,
  `materi` text NOT NULL,
  `bobot` int(11) DEFAULT NULL,
  `id_rps` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cplmks`
--

CREATE TABLE `cplmks` (
  `id` int(11) NOT NULL,
  `kode_mk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_cpl` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `cplmks`
--

INSERT INTO `cplmks` (`id`, `kode_mk`, `id_cpl`, `created_at`, `updated_at`) VALUES
(32, 'UNI520107', 37, '2022-11-01 15:12:43', '2022-11-01 15:12:43'),
(36, 'COM010101', 52, '2022-11-04 17:43:27', '2022-11-04 17:43:27'),
(42, 'COM616108', 41, '2022-12-28 00:53:34', '2022-12-28 00:53:34'),
(43, 'COM616108', 52, '2022-12-28 00:53:34', '2022-12-28 00:53:34'),
(44, 'COM616108', 57, '2022-12-28 00:53:34', '2022-12-28 00:53:34'),
(45, 'COM616108', 37, '2022-12-28 00:53:34', '2022-12-28 00:53:34'),
(46, 'COM616108', 38, '2022-12-28 00:53:34', '2022-12-28 00:53:34'),
(47, 'COM616108', 40, '2022-12-28 00:53:34', '2022-12-28 00:53:34'),
(48, 'COM616108', 56, '2022-12-28 00:55:40', '2022-12-28 00:55:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cpls`
--

CREATE TABLE `cpls` (
  `id` int(11) NOT NULL,
  `aspek` enum('Sikap','Pengetahuan','Umum','Keterampilan') NOT NULL,
  `kode` varchar(255) NOT NULL,
  `nomor` int(11) NOT NULL,
  `judul` text NOT NULL,
  `kurikulum` int(10) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `cpls`
--

INSERT INTO `cpls` (`id`, `aspek`, `kode`, `nomor`, `judul`, `kurikulum`, `created_at`, `updated_at`) VALUES
(37, 'Pengetahuan', 'P01', 1, 'Menguasai konsep teoritis bidang pengetahuan sistem komputer secara umum dan konsep teoritis bagian khusus dalam bidang  pengetahuan tersebut secara mendalam, serta mampu memformulasikan penyelesaian masalah prosedural.', 2020, '2022-10-05 14:44:13', '2022-10-05 14:44:13'),
(38, 'Pengetahuan', 'P13', 13, 'Menguasai konsep-konsep pemrograman, model-model bahasa pemrograman, serta membandingkan berbagai solusi.', 2020, '2022-10-05 14:44:14', '2022-10-05 14:44:14'),
(40, 'Pengetahuan', 'P29', 29, 'Menguasai konsep menulis kode yang diperlukan untuk digunakan sebagai instruksi dalam membangun aplikasi komputer.', 2020, '2022-10-05 14:44:15', '2022-10-05 14:44:15'),
(41, 'Keterampilan', 'KK19', 19, 'Mampu menjelaskan abstraksi dari eksekusi sebuah program pada sebuah sistem komputer.', 2020, '2022-10-07 00:03:53', '2022-10-07 00:03:53'),
(42, 'Sikap', 'S1', 1, 'Bertakwa kepada Tuhan Yang Maha Esa dan mampu menunjukkan sikap religius.', 2020, '2022-10-14 10:05:23', '2022-12-19 10:40:08'),
(43, 'Sikap', 'S2', 2, 'Menjunjung tinggi nilai kemanusiaan dalam menjalankan tugas berdasarkan agama,moral, dan etika;', 2020, '2022-10-14 10:05:23', '2022-10-14 10:05:23'),
(44, 'Sikap', 'S3', 3, 'Berkontribusi dalam peningkatan mutu kehidupan bermasyarakat, berbangsa, bernegara, dan kemajuan peradaban berdasarkan Pancasila;', 2020, '2022-10-14 10:05:23', '2022-10-14 10:05:23'),
(45, 'Umum', 'KU1', 1, 'Mampu menyelesaikan pekerjaan berlingkup luas dan menganalisis data dengan beragam metode yang sesuai, baik yang belum maupun yang sudah baku;', 2020, '2022-10-14 12:59:17', '2022-10-14 12:59:17'),
(46, 'Umum', 'KU2', 2, 'Mampu menunjukkan kinerja bermutu dan terukur;', 2020, '2022-10-16 00:55:19', '2022-10-16 00:55:19'),
(47, 'Sikap', 'S4', 4, 'Berperan sebagai warga negara yang bangga dan cinta tanah air, memiliki nasionalisme serta rasa tanggungjawab pada negara dan bangsa;', 2020, '2022-10-16 11:57:56', '2022-10-16 11:57:56'),
(48, 'Sikap', 'S5', 5, 'Menghargai keanekaragaman budaya, pandangan, agama, dan kepercayaan, serta pendapat atau temuan orisinal orang lain;', 2020, '2022-10-16 12:01:05', '2022-10-16 12:01:05'),
(49, 'Sikap', 'S6', 6, 'Bekerja sama dan memiliki kepekaan sosial serta kepedulian terhadap masyarakat dan lingkungan;', 2020, '2022-10-16 12:03:56', '2022-10-16 12:03:56'),
(50, 'Sikap', 'S7', 7, 'Taat hukum dan disiplin dalam kehidupan bermasyarakat dan bernegara;', 2020, '2022-10-21 00:49:09', '2022-10-21 00:49:09'),
(52, 'Keterampilan', 'KK01', 1, 'tes', 2020, '2022-11-01 14:14:53', '2022-11-11 11:43:45'),
(56, 'Pengetahuan', 'P31', 31, 'tes aja2', 2016, '2022-11-01 12:18:15', '2022-11-01 12:18:15'),
(57, 'Keterampilan', 'KK30', 30, 'tes', 2016, '2022-11-01 14:14:53', '2022-11-11 11:43:45'),
(65, 'Sikap', 'S8', 8, 'tes', 2020, '2022-11-13 22:44:47', '2022-11-13 22:44:47'),
(66, 'Pengetahuan', 'P01', 1, 'kosong', 2016, '2022-11-18 17:46:21', '2022-11-18 17:46:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cpmks`
--

CREATE TABLE `cpmks` (
  `id` int(11) NOT NULL,
  `id_mk` int(10) UNSIGNED NOT NULL,
  `judul` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `cpmks`
--

INSERT INTO `cpmks` (`id`, `id_mk`, `judul`) VALUES
(1, 6, 'J.620100.017.02 Mengimplementasikan Pemrograman Terstruktur'),
(2, 6, 'J.620100.025.02 Melakukan Debugging'),
(3, 6, 'J.620100.017.02 Mengimplementasikan Pemrograman Terstruktur'),
(4, 9, 'Mempelajari cara menjadi pengusaha'),
(5, 9, 'Mempelajari cara bersedih');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cpmk_soals`
--

CREATE TABLE `cpmk_soals` (
  `id` int(11) NOT NULL,
  `id_cpmk` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `cpmk_soals`
--

INSERT INTO `cpmk_soals` (`id`, `id_cpmk`, `id_soal`, `created_at`, `updated_at`) VALUES
(1, 2, 5, '2022-12-26 11:16:25', '2022-12-26 11:16:25'),
(2, 1, 5, '2022-12-26 11:16:44', '2022-12-26 11:16:44'),
(3, 5, 7, '2022-12-28 07:35:42', '2022-12-28 07:35:42'),
(4, 4, 6, '2022-12-28 07:35:57', '2022-12-28 07:35:57'),
(5, 1, 6, '2023-01-07 19:21:22', '2023-01-07 19:21:22'),
(6, 2, 6, '2023-01-07 20:45:53', '2023-01-07 20:45:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurikulums`
--

CREATE TABLE `kurikulums` (
  `tahun` int(10) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kurikulums`
--

INSERT INTO `kurikulums` (`tahun`, `created_at`, `updated_at`) VALUES
(2016, '2022-11-07 10:58:47', '2022-11-19 17:06:03'),
(2020, '2022-11-07 04:56:40', '2022-11-11 10:02:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2022_09_03_162818_create_users_table', 1),
(2, '2022_09_16_080822_create_mks_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mks`
--

CREATE TABLE `mks` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rumpun` enum('Wajib','Peminatan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `prasyarat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kurikulum` int(10) DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot_teori` int(11) NOT NULL,
  `bobot_praktikum` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mks`
--

INSERT INTO `mks` (`id`, `kode`, `nama`, `rumpun`, `prasyarat`, `kurikulum`, `deskripsi`, `bobot_teori`, `bobot_praktikum`, `created_at`, `updated_at`) VALUES
(6, 'COM616108', 'Pemrograman Terstruktur', 'Peminatan', 'Tidak ada', 2020, 'hehe', 2, 0, '2022-10-05 07:48:37', '2022-12-19 03:51:34'),
(8, 'UNI520107', 'Pendidikan Kewarganegaraan', 'Wajib', 'Tidak ada', 2020, '-', 2, 0, '2022-10-17 12:55:58', '2022-10-17 12:55:58'),
(9, 'COM010101', 'Kelas Nopri', 'Wajib', 'Tidak ada', 2016, 'nop nop', 3, 0, '2022-10-20 16:52:14', '2022-10-20 16:52:14'),
(10, 'COM060820', 'Bucin', 'Peminatan', 'Kelas Nopri', 2020, 'hehe', 2, 0, '2022-11-06 14:27:21', '2022-11-06 14:47:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rpss`
--

CREATE TABLE `rpss` (
  `id` int(11) NOT NULL,
  `id_mk` int(10) UNSIGNED NOT NULL,
  `nomor` varchar(255) NOT NULL,
  `prodi` varchar(255) NOT NULL,
  `dosen` varchar(255) DEFAULT NULL,
  `pengembang` varchar(255) NOT NULL,
  `koordinator` varchar(255) NOT NULL,
  `kaprodi` varchar(255) NOT NULL,
  `kurikulum` int(10) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `materi_mk` text NOT NULL,
  `pustaka_utama` text NOT NULL,
  `pustaka_pendukung` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `rpss`
--

INSERT INTO `rpss` (`id`, `id_mk`, `nomor`, `prodi`, `dosen`, `pengembang`, `koordinator`, `kaprodi`, `kurikulum`, `semester`, `materi_mk`, `pustaka_utama`, `pustaka_pendukung`, `created_at`, `updated_at`) VALUES
(3, 9, '2', 'S1 - Ilmu Komputer', 'Anggie Tamara', 'Anggie Tamara', 'Romdoni', 'Anggie Tamara', 2020, 7, 'haha hihi', 'google.com', 'Tidak ada', '2022-10-30 17:32:24', '2022-10-30 17:32:24'),
(4, 8, '123', 'D3 - Manajemen Informatika', 'Didik Kurniawan S.Si., M.T.', 'Romdoni', 'Anggie Tamara', 'Didik Kurniawan S.Si., M.T.', 2020, 7, 'tes tes', 'google.com', 'Tidak ada', '2022-11-01 11:53:58', '2022-11-01 11:53:58'),
(5, 6, '321', 'S1 - Ilmu Komputer', 'Didik Kurniawan S.Si., M.T.', 'Didik Kurniawan S.Si., M.T.', 'Didik Kurniawan S.Si., M.T.', 'Didik Kurniawan S.Si., M.T.', 2020, 2, 'tes', 'google.com', 'Tidak ada', '2022-11-06 20:23:31', '2022-12-28 01:00:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `soals`
--

CREATE TABLE `soals` (
  `id` int(11) NOT NULL,
  `id_mk` int(10) UNSIGNED NOT NULL,
  `prodi` enum('S1 - Ilmu Komputer','D3 - Manajemen Informatika') NOT NULL,
  `minggu` int(11) NOT NULL,
  `jenis` enum('Kuis ke-1','UTS','UAS','Kuis ke-2') NOT NULL,
  `dosen` varchar(255) NOT NULL,
  `kurikulum` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `status` enum('Valid','Tolak') DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `soals`
--

INSERT INTO `soals` (`id`, `id_mk`, `prodi`, `minggu`, `jenis`, `dosen`, `kurikulum`, `pertanyaan`, `status`, `komentar`, `created_at`, `updated_at`) VALUES
(5, 9, 'S1 - Ilmu Komputer', 1, 'Kuis ke-1', 'Anggie Tamara', 2020, 'hehe?', 'Valid', NULL, '2022-12-26 11:01:48', '2023-01-19 23:39:26'),
(6, 9, 'S1 - Ilmu Komputer', 1, 'Kuis ke-1', 'Anggie Tamara', 2020, 'haha?', 'Valid', NULL, '2022-12-26 11:52:04', '2023-01-19 23:39:50'),
(7, 9, 'S1 - Ilmu Komputer', 16, 'UAS', 'Anggie Tamara', 2020, 'hihi?', 'Tolak', 'soal terlalu sedikit', '2022-12-26 12:05:49', '2023-01-19 23:38:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `otoritas` enum('Dosen','Penjamin Mutu','Admin','') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `img`, `otoritas`, `created_at`, `updated_at`) VALUES
(1, 'achieto', 'achieto.17210@gmail.com', '$2y$10$Jk00BcsHnMJxxMew7WJrUeob8XwZvDGjkNqPdyafkco4mTbdmPH6u', '1672060148606-wp5407058-toilet-bound-hanako-kun-wallpapers.jpg', 'Admin', '2022-09-03 12:05:23', '2022-12-26 13:09:09'),
(2, 'Anggie Tamara', 'anggie.t17210@gmail.com', '$2y$10$Pnv3K5OqtzNhdAwD0SwIj.jeUTBNzRk60arZTH.L11j649le0insu', '1662968664210-Red.(Arknights).full.2831158.jpg', 'Dosen', '2022-09-07 11:22:10', '2022-10-06 08:05:28'),
(3, 'Didik Kurniawan S.Si., M.T.', 'didik.kurniawan@yahoo.com', '$2y$10$JgwclQAI2a3BzazaMM4AO.T0d226tbPuCyZdDcOkpTzoRclSfLzSW', 'User-Profile.png', 'Dosen', '2022-10-05 07:46:44', '2022-10-05 07:46:44'),
(4, 'pm', 'pm@gmail.com', '$2y$10$ULsswBgBqhNWtYKckrputegWESgmNQIKgzg4GCpLgreKxSYGJJOEG', '1667200010262-maxresdefault.jpg', 'Penjamin Mutu', '2022-10-31 06:44:33', '2022-10-31 07:06:50'),
(6, 'Romdoni', 'romdoni@gmail.com', '$2y$10$N5LAWPa63yNsibSwyjtSr.oWv9jbQG76kPkhDGrPCUe24Qy5kxxKi', '1662619345125-wp5488531-jibaku-shounen-hanako-kun-wallpapers.jpg', 'Dosen', '2022-09-07 11:06:40', '2022-11-06 15:42:25');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rps` (`id_rps`);

--
-- Indeks untuk tabel `cplmks`
--
ALTER TABLE `cplmks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cplmks_ibfk_1` (`id_cpl`),
  ADD KEY `cplmks_ibfk_2` (`kode_mk`);

--
-- Indeks untuk tabel `cpls`
--
ALTER TABLE `cpls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulum` (`kurikulum`);

--
-- Indeks untuk tabel `cpmks`
--
ALTER TABLE `cpmks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mk` (`id_mk`);

--
-- Indeks untuk tabel `cpmk_soals`
--
ALTER TABLE `cpmk_soals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cpmk` (`id_cpmk`),
  ADD KEY `id_soal` (`id_soal`);

--
-- Indeks untuk tabel `kurikulums`
--
ALTER TABLE `kurikulums`
  ADD PRIMARY KEY (`tahun`) USING BTREE;

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mks`
--
ALTER TABLE `mks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mks_kode_unique` (`kode`),
  ADD KEY `kurikulum` (`kurikulum`);

--
-- Indeks untuk tabel `rpss`
--
ALTER TABLE `rpss`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rpss_ibfk_2` (`id_mk`),
  ADD KEY `kurikulum` (`kurikulum`),
  ADD KEY `dosen` (`dosen`),
  ADD KEY `koordinator` (`koordinator`),
  ADD KEY `pengembang` (`pengembang`),
  ADD KEY `kaprodi` (`kaprodi`);

--
-- Indeks untuk tabel `soals`
--
ALTER TABLE `soals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dosen` (`dosen`),
  ADD KEY `id_mk` (`id_mk`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `name` (`name`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `cplmks`
--
ALTER TABLE `cplmks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `cpls`
--
ALTER TABLE `cpls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT untuk tabel `cpmks`
--
ALTER TABLE `cpmks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `cpmk_soals`
--
ALTER TABLE `cpmk_soals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `mks`
--
ALTER TABLE `mks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `rpss`
--
ALTER TABLE `rpss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `soals`
--
ALTER TABLE `soals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`id_rps`) REFERENCES `rpss` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `cplmks`
--
ALTER TABLE `cplmks`
  ADD CONSTRAINT `cplmks_ibfk_1` FOREIGN KEY (`id_cpl`) REFERENCES `cpls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cplmks_ibfk_2` FOREIGN KEY (`kode_mk`) REFERENCES `mks` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `cpls`
--
ALTER TABLE `cpls`
  ADD CONSTRAINT `cpls_ibfk_1` FOREIGN KEY (`kurikulum`) REFERENCES `kurikulums` (`tahun`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `cpmks`
--
ALTER TABLE `cpmks`
  ADD CONSTRAINT `cpmks_ibfk_1` FOREIGN KEY (`id_mk`) REFERENCES `mks` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `cpmk_soals`
--
ALTER TABLE `cpmk_soals`
  ADD CONSTRAINT `cpmk_soals_ibfk_1` FOREIGN KEY (`id_cpmk`) REFERENCES `cpmks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cpmk_soals_ibfk_2` FOREIGN KEY (`id_soal`) REFERENCES `soals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mks`
--
ALTER TABLE `mks`
  ADD CONSTRAINT `mks_ibfk_1` FOREIGN KEY (`kurikulum`) REFERENCES `kurikulums` (`tahun`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rpss`
--
ALTER TABLE `rpss`
  ADD CONSTRAINT `rpss_ibfk_2` FOREIGN KEY (`id_mk`) REFERENCES `mks` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rpss_ibfk_3` FOREIGN KEY (`kurikulum`) REFERENCES `kurikulums` (`tahun`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rpss_ibfk_4` FOREIGN KEY (`dosen`) REFERENCES `users` (`name`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `rpss_ibfk_5` FOREIGN KEY (`koordinator`) REFERENCES `users` (`name`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `rpss_ibfk_6` FOREIGN KEY (`pengembang`) REFERENCES `users` (`name`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `rpss_ibfk_7` FOREIGN KEY (`kaprodi`) REFERENCES `users` (`name`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `soals`
--
ALTER TABLE `soals`
  ADD CONSTRAINT `soals_ibfk_2` FOREIGN KEY (`dosen`) REFERENCES `users` (`name`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `soals_ibfk_3` FOREIGN KEY (`id_mk`) REFERENCES `mks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
