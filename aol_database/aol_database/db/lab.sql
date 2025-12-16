-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Des 2025 pada 12.33
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lab`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` varchar(10) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
('ADM-001', 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` varchar(20) DEFAULT NULL,
  `id_produk` varchar(10) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `harga_satuan_saat_ini` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_produk`, `qty`, `harga_satuan_saat_ini`, `subtotal`) VALUES
(1, 'TRX-2512-887B16', 'PRD-2A03', 1, 39600.00, 39600.00),
(2, 'TRX-2512-887B16', 'PRD-67AC', 1, 5941.50, 5941.50),
(3, 'TRX-2512-887B16', 'PRD-155F', 1, 70541.50, 70541.50),
(4, 'TRX-2512-529426', 'PRD-9042', 1, 17754.00, 17754.00),
(5, 'TRX-2512-DAE849', 'PRD-9042', 1, 17754.00, 17754.00);

--
-- Trigger `detail_transaksi`
--
DELIMITER $$
CREATE TRIGGER `tg_kurangi_stok` AFTER INSERT ON `detail_transaksi` FOR EACH ROW BEGIN
    UPDATE `stok_produk` 
    SET `jumlah_stok` = `jumlah_stok` - NEW.qty 
    WHERE `id_produk` = NEW.id_produk;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `diskon_produk`
--

CREATE TABLE `diskon_produk` (
  `id_diskon` int(11) NOT NULL,
  `id_transaksi` varchar(20) DEFAULT NULL,
  `id_member` varchar(10) DEFAULT NULL,
  `id_produk` varchar(10) DEFAULT NULL,
  `nama_member` varchar(100) DEFAULT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `jumlah_potongan` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `diskon_produk`
--

INSERT INTO `diskon_produk` (`id_diskon`, `id_transaksi`, `id_member`, `id_produk`, `nama_member`, `nama_produk`, `jumlah_potongan`) VALUES
(1, 'TRX-2512-77C33D', 'MEM-8A441', 'PRD-99A5', 'Budi Santoso', 'pisang', 1000.00),
(2, 'TRX-2512-887B16', 'MEM-7BE48', 'PRD-2A03', 'budi', 'LENGKENG BANGKOK', 9900.00),
(3, 'TRX-2512-887B16', 'MEM-7BE48', 'PRD-67AC', 'budi', 'CIMORY ', 1048.50),
(4, 'TRX-2512-887B16', 'MEM-7BE48', 'PRD-155F', 'budi', 'LOREAL SHPF', 12448.50),
(5, 'TRX-2512-529426', 'MEM-7BE48', 'PRD-9042', 'budi', 'FORMULA S/G ', 11836.00),
(6, 'TRX-2512-DAE849', 'MEM-7BE48', 'PRD-9042', 'budi', 'FORMULA S/G ', 11836.00);

--
-- Trigger `diskon_produk`
--
DELIMITER $$
CREATE TRIGGER `tg_bi_diskon_info` BEFORE INSERT ON `diskon_produk` FOR EACH ROW BEGIN
    DECLARE v_nama_member VARCHAR(100);
    DECLARE v_nama_produk VARCHAR(100);

    SELECT `nama_member` INTO v_nama_member FROM `member` WHERE `id_member` = NEW.id_member;
    
    SELECT `nama_produk` INTO v_nama_produk FROM `produk` WHERE `id_produk` = NEW.id_produk;

    SET NEW.nama_member = v_nama_member;
    SET NEW.nama_produk = v_nama_produk;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_produk`
--

INSERT INTO `kategori_produk` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Makanan Ringan'),
(2, 'Buah Segar'),
(3, 'Bahan Masak'),
(4, 'Perawatan Diri'),
(5, 'Minuman'),
(6, 'Makanan Instan');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `lihat_kategori_dan_produk`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `lihat_kategori_dan_produk` (
`Kategori` varchar(50)
,`ID Produk` varchar(10)
,`Nama Produk` varchar(100)
,`Harga` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `member`
--

CREATE TABLE `member` (
  `id_member` varchar(10) NOT NULL,
  `nama_member` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `member`
--

INSERT INTO `member` (`id_member`, `nama_member`, `email`, `no_telp`, `alamat`) VALUES
('MEM-29ECB', 'Siti Aminah', 'siti.aminah@yahoo.com', '085678901234', 'Komp. Griya Indah Blok A3'),
('MEM-6DC59', 'Rudi Hermawan', 'rudi.gamer@gmail.com', '089876543210', 'Jl. Sudirman Kav. 50'),
('MEM-7BE48', 'budi', 'tes3@gmail.com', '08111111111', 'jl pria tampan, ferro'),
('MEM-8A441', 'Budi Santoso', 'budi.santoso@email.com', '081234567890', 'Jl. Merdeka No. 10, Jakarta'),
('MEM-B9CEE', 'Dewi Lestari', 'dewi.l@outlook.com', '081345678901', 'Apartemen Central Park Lt. 12');

--
-- Trigger `member`
--
DELIMITER $$
CREATE TRIGGER `tg_bi_member_id` BEFORE INSERT ON `member` FOR EACH ROW BEGIN
    IF NEW.id_member IS NULL OR NEW.id_member = '' THEN
        SET NEW.id_member = CONCAT('MEM-', UPPER(SUBSTRING(MD5(UUID()), 1, 5)));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode` int(11) NOT NULL,
  `nama_metode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode`, `nama_metode`) VALUES
(1, 'seabank'),
(2, 'BCA'),
(3, 'Mandiri');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` varchar(10) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `diskon_persen` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `nama_produk`, `harga`, `gambar`, `diskon_persen`) VALUES
('PRD-155F', 4, 'LOREAL SHPF', 82990.00, 'loreal.png', 15),
('PRD-165C', 6, 'INDOMIE GRNG ACEH', 3190.00, 'shop6.png', 0),
('PRD-2A03', 2, 'LENGKENG BANGKOK', 49500.00, 'lengkeng.png', 20),
('PRD-3DC4', 4, 'SELECTION KOREA /karton', 16990.00, 'sedap-selection.png', 0),
('PRD-5AEE', 6, 'SELECTION KOREA', 3190.00, 'sedap-selection.png', 0),
('PRD-5F24', 3, 'SP BIHUN ', 6090.00, 'sp-bihun.png', 0),
('PRD-607B', 6, 'INDOMIE GR ', 4190.00, 'shop3.png', 0),
('PRD-67AC', 5, 'CIMORY ', 6990.00, 'shop5.png', 15),
('PRD-7B6C', 5, 'CIMORY COOKIES', 5990.00, 'milk-cokies.png', 0),
('PRD-7DA1', 6, 'SEDAAP MI WHT CUR', 3190.00, 'sedap-white-curry.png', 0),
('PRD-9042', 4, 'FORMULA S/G ', 29590.00, 'formula-sg-sp.png', 40),
('PRD-934E', 5, 'FRISIAN FLAG', 7290.00, 'frisian-flag.png', 0),
('PRD-99A5', 2, 'pisang', 6000.00, 'shop1.png', 20),
('PRD-9F6B', 2, 'Apel Fuji', 12000.00, 'shop2.png', 0),
('PRD-AADE', 4, 'COLGATE', 51990.00, 'colagate.png', 10),
('PRD-CD6E', 5, 'MILO UHT', 6190.00, 'shop4.png', 10),
('PRD-D5D2', 3, 'PRONAS SPAG P/MIA', 11900.00, 'pronas-spaggeti.jpg', 10),
('PRD-FA14', 1, 'POP ICE CARAMEL', 16900.00, 'pop-ice-caramel.png', 0);

--
-- Trigger `produk`
--
DELIMITER $$
CREATE TRIGGER `tg_bi_produk_id` BEFORE INSERT ON `produk` FOR EACH ROW BEGIN
    IF NEW.id_produk IS NULL OR NEW.id_produk = '' THEN
        SET NEW.id_produk = CONCAT('PRD-', UPPER(SUBSTRING(MD5(UUID()), 1, 4)));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_produk`
--

CREATE TABLE `stok_produk` (
  `id_stok` int(11) NOT NULL,
  `id_produk` varchar(10) DEFAULT NULL,
  `jumlah_stok` int(11) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stok_produk`
--

INSERT INTO `stok_produk` (`id_stok`, `id_produk`, `jumlah_stok`, `last_update`) VALUES
(1, 'PRD-FA14', 99, '2025-12-03 11:38:52'),
(2, 'PRD-2A03', 77, '2025-12-03 11:38:52'),
(3, 'PRD-5F24', 99, '2025-12-03 11:38:52'),
(4, 'PRD-D5D2', 99, '2025-12-03 11:38:52'),
(5, 'PRD-155F', 96, '2025-12-03 11:38:52'),
(6, 'PRD-3DC4', 94, '2025-12-03 11:38:52'),
(7, 'PRD-9042', 97, '2025-12-03 11:38:52'),
(8, 'PRD-AADE', 98, '2025-12-03 11:38:52'),
(9, 'PRD-67AC', 96, '2025-12-03 11:38:52'),
(10, 'PRD-7B6C', 98, '2025-12-03 11:38:52'),
(11, 'PRD-934E', 99, '2025-12-03 11:38:52'),
(12, 'PRD-CD6E', 98, '2025-12-03 11:38:52'),
(13, 'PRD-165C', 95, '2025-12-03 11:38:52'),
(14, 'PRD-5AEE', 96, '2025-12-03 11:38:52'),
(15, 'PRD-607B', 98, '2025-12-03 11:38:52'),
(16, 'PRD-7DA1', 97, '2025-12-07 05:52:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` varchar(20) NOT NULL,
  `id_member` varchar(10) DEFAULT NULL,
  `id_user` varchar(20) DEFAULT NULL,
  `id_admin` varchar(10) DEFAULT NULL,
  `id_metode` int(11) DEFAULT NULL,
  `tanggal_transaksi` datetime DEFAULT current_timestamp(),
  `total_belanja` decimal(10,2) DEFAULT 0.00,
  `total_diskon` decimal(10,2) DEFAULT 0.00,
  `total_bayar` decimal(10,2) DEFAULT 0.00,
  `status_pembayaran` enum('unpaid','pending','approved') NOT NULL DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_member`, `id_user`, `id_admin`, `id_metode`, `tanggal_transaksi`, `total_belanja`, `total_diskon`, `total_bayar`, `status_pembayaran`) VALUES
('TRX-2512-529426', 'MEM-7BE48', 'USR-22B8E', 'ADM-001', 1, '2025-12-14 17:02:37', 29590.00, 11836.00, 17754.00, 'approved'),
('TRX-2512-77C33D', 'MEM-6DC59', 'USR-0B579', 'ADM-001', 2, '2025-12-13 21:48:27', 500000.00, 0.00, 450000.00, 'approved'),
('TRX-2512-887B16', 'MEM-7BE48', 'USR-22B8E', 'ADM-001', 1, '2025-12-14 00:32:23', 139480.00, 23397.00, 116083.00, 'unpaid'),
('TRX-2512-DAE849', 'MEM-7BE48', 'USR-22B8E', 'ADM-001', 2, '2025-12-14 18:22:48', 29590.00, 11836.00, 17754.00, 'approved');

--
-- Trigger `transaksi`
--
DELIMITER $$
CREATE TRIGGER `tg_bi_transaksi_id` BEFORE INSERT ON `transaksi` FOR EACH ROW BEGIN
    IF NEW.id_transaksi IS NULL OR NEW.id_transaksi = '' THEN
        SET NEW.id_transaksi = CONCAT('TRX-', DATE_FORMAT(NOW(), '%y%m'), '-', UPPER(SUBSTRING(MD5(UUID()), 1, 6)));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `Id_user` varchar(20) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Age` tinyint(4) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`Id_user`, `Username`, `Email`, `Age`, `Password`, `CreatedAt`) VALUES
('USR-09E62', 'rudi_hermawan', 'rudi.gamer@gmail.com', 19, '$2y$10$7cKLxmn.EOcEGVEgWM1IQ.NMCaAaRY6biAIDKzgi3fCVpRQOh4g9u', '2025-12-13 14:47:15'),
('USR-0B579', 'budi_santoso', 'budi.santoso@email.com', 25, '$2y$10$7cKLxmn.EOcEGVEgWM1IQ.NMCaAaRY6biAIDKzgi3fCVpRQOh4g9u', '2025-12-13 14:47:15'),
('USR-0C02D', 'donny', 'donny@gmail.com', 45, '$2y$10$sembaranghash0987654321', '2025-12-13 14:37:03'),
('USR-22B8E', 'tes3', 'tes3@gmail.com', 16, '$2y$10$M4rreHYOKCoQI2FtdJ/tMeOOCh4vd1Xl.oaiekq.M04DY/xDbrxG.', '2025-12-13 16:21:39'),
('USR-7B545', 'siti_aminah', 'siti.aminah@yahoo.com', 22, '$2y$10$7cKLxmn.EOcEGVEgWM1IQ.NMCaAaRY6biAIDKzgi3fCVpRQOh4g9u', '2025-12-13 14:47:15'),
('USR-956E1', 'bejo', 'bejo@gmail.com', 19, '$2y$10$sembaranghash1234567890', '2025-12-13 14:37:03'),
('USR-B0B0F', 'penn', 'penpen@gmail.com', 24, '$2y$10$sembaranghash5566778899', '2025-12-13 14:37:03'),
('USR-D5162', 'anton', 'anton@gmail.com', 30, '$2y$10$sembaranghash1122334455', '2025-12-13 14:37:03'),
('USR-F80BD', 'dewi_lestari', 'dewi.l@outlook.com', 30, '$2y$10$7cKLxmn.EOcEGVEgWM1IQ.NMCaAaRY6biAIDKzgi3fCVpRQOh4g9u', '2025-12-13 14:47:15');

--
-- Trigger `users`
--
DELIMITER $$
CREATE TRIGGER `tg_bi_user_id` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.Id_user IS NULL OR NEW.Id_user = '' THEN
        SET NEW.Id_user = CONCAT('USR-', UPPER(SUBSTRING(MD5(UUID()), 1, 5)));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur untuk view `lihat_kategori_dan_produk`
--
DROP TABLE IF EXISTS `lihat_kategori_dan_produk`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `lihat_kategori_dan_produk`  AS SELECT `k`.`nama_kategori` AS `Kategori`, `p`.`id_produk` AS `ID Produk`, `p`.`nama_produk` AS `Nama Produk`, `p`.`harga` AS `Harga` FROM (`kategori_produk` `k` join `produk` `p` on(`k`.`id_kategori` = `p`.`id_kategori`)) ORDER BY `k`.`nama_kategori` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `detail_transaksi_ibfk_1` (`id_transaksi`);

--
-- Indeks untuk tabel `diskon_produk`
--
ALTER TABLE `diskon_produk`
  ADD PRIMARY KEY (`id_diskon`),
  ADD KEY `id_member` (`id_member`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `diskon_produk_ibfk_1` (`id_transaksi`);

--
-- Indeks untuk tabel `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id_member`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD PRIMARY KEY (`id_stok`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_member` (`id_member`),
  ADD KEY `id_metode` (`id_metode`),
  ADD KEY `fk_transaksi_users` (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id_user`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `diskon_produk`
--
ALTER TABLE `diskon_produk`
  MODIFY `id_diskon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kategori_produk`
--
ALTER TABLE `kategori_produk`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `stok_produk`
--
ALTER TABLE `stok_produk`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `diskon_produk`
--
ALTER TABLE `diskon_produk`
  ADD CONSTRAINT `diskon_produk_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `diskon_produk_ibfk_2` FOREIGN KEY (`id_member`) REFERENCES `member` (`id_member`),
  ADD CONSTRAINT `diskon_produk_ibfk_3` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_produk` (`id_kategori`);

--
-- Ketidakleluasaan untuk tabel `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD CONSTRAINT `stok_produk_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`Id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `member` (`id_member`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_metode`) REFERENCES `metode_pembayaran` (`id_metode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
