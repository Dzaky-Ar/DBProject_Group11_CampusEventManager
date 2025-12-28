-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2025 at 01:17 PM
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
-- Database: `cem`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrows`
--

CREATE TABLE `borrows` (
  `Tanggal_peminjaman` date NOT NULL,
  `Tanggal_pengembalian` date NOT NULL,
  `Jumlah_peminjaman` int(11) NOT NULL,
  `Organizer_ID` int(11) NOT NULL,
  `Kode_barang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `borrows`
--
DELIMITER $$
CREATE TRIGGER `trg_update_inventory_before_insert` BEFORE INSERT ON `borrows` FOR EACH ROW BEGIN
    
    IF (SELECT Jumlah_barang 
        FROM inventory 
        WHERE Kode_barang = NEW.Kode_barang) < NEW.Jumlah_peminjaman THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Not enough stock in inventory.';
    END IF;

    
    UPDATE inventory
    SET Jumlah_barang = Jumlah_barang - NEW.Jumlah_peminjaman
    WHERE Kode_barang = NEW.Kode_barang;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `Event_ID` int(11) NOT NULL,
  `Nama_event` varchar(254) NOT NULL,
  `Tipe_event` varchar(50) DEFAULT NULL,
  `Description` varchar(254) DEFAULT NULL,
  `Organizer_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gives`
--

CREATE TABLE `gives` (
  `Kode_barang` int(11) NOT NULL,
  `Event_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `Jumlah_barang` int(11) NOT NULL,
  `Kode_barang` int(11) NOT NULL,
  `Nama_barang` varchar(200) NOT NULL,
  `Verificator_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`Jumlah_barang`, `Kode_barang`, `Nama_barang`, `Verificator_ID`) VALUES
(100, 4, 'Kursi', 1),
(20, 5, 'Meja', 1),
(2, 6, 'Sound System', 1);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `Lokasi` varchar(200) NOT NULL,
  `Waktu` date NOT NULL,
  `Event_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizer`
--

CREATE TABLE `organizer` (
  `Instansi` varchar(100) NOT NULL,
  `Nama_organizer` varchar(100) NOT NULL,
  `Organizer_ID` int(11) NOT NULL,
  `Email` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizer`
--

INSERT INTO `organizer` (`Instansi`, `Nama_organizer`, `Organizer_ID`, `Email`) VALUES
('kampus', 'beta', 18, 'beta@gmail.com'),
('Chandra Institute', 'chandra', 19, 'chandra@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `proposal`
--

CREATE TABLE `proposal` (
  `Proposal_ID` int(11) NOT NULL,
  `Status` enum('approved','pending','not approved') NOT NULL DEFAULT 'pending',
  `Description` varchar(254) DEFAULT NULL,
  `File` blob DEFAULT NULL,
  `Organizer_ID` int(11) DEFAULT NULL,
  `Verificator_ID` int(11) DEFAULT NULL,
  `Kode_barang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `Submission` blob DEFAULT NULL,
  `Judul` varchar(254) DEFAULT NULL,
  `Registration_ID` int(11) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Event_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Email` varchar(254) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Status_user` enum('1','2','3') DEFAULT NULL,
  `User_name` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Email`, `Password`, `Status_user`, `User_name`) VALUES
('alpha@gmail.com', '$2y$10$SOih.VrelzSplJrJb4n1E.aAxGTevY9tBokRb0thzLXFSNQu0WfHe', '3', 'alpha'),
('beta@gmail.com', '$2y$10$IFZLEP.UIKXFth7SYNZDB.q.RxwRDU3ZPPByq5STHhPZFT8rKgKzu', '2', 'beta'),
('chandra@gmail.com', '$2y$10$bKceBI5UaZz4XAKskIieUOqMG0buOJ1L4VS3SGY8BVwLYRZzyoL8.', '2', 'chandra'),
('gamma@gmail.com', '$2y$10$DREAS3r48.QR/EVNjPsQqunjSkvh8M0lK7vGKPdGSvQBGGPACQz2e', '1', 'gamma');

-- --------------------------------------------------------

--
-- Table structure for table `verificator`
--

CREATE TABLE `verificator` (
  `Verificator_ID` int(11) NOT NULL,
  `Nama_PJ` varchar(254) NOT NULL,
  `Email` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verificator`
--

INSERT INTO `verificator` (`Verificator_ID`, `Nama_PJ`, `Email`) VALUES
(1, 'candra', 'candra@gmail.com'),
(3, 'gamma', 'gamma@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrows`
--
ALTER TABLE `borrows`
  ADD KEY `fk_Organizer_ID` (`Organizer_ID`),
  ADD KEY `fk_Kode_barang` (`Kode_barang`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`Event_ID`),
  ADD KEY `Organizer_ID` (`Organizer_ID`);

--
-- Indexes for table `gives`
--
ALTER TABLE `gives`
  ADD KEY `Kode_barang` (`Kode_barang`),
  ADD KEY `Event_ID` (`Event_ID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`Kode_barang`),
  ADD KEY `Verificator_ID` (`Verificator_ID`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`Lokasi`,`Waktu`),
  ADD KEY `Event_ID` (`Event_ID`);

--
-- Indexes for table `organizer`
--
ALTER TABLE `organizer`
  ADD PRIMARY KEY (`Organizer_ID`),
  ADD KEY `Email` (`Email`);

--
-- Indexes for table `proposal`
--
ALTER TABLE `proposal`
  ADD PRIMARY KEY (`Proposal_ID`),
  ADD KEY `Organizer_ID` (`Organizer_ID`),
  ADD KEY `Verificator_ID` (`Verificator_ID`),
  ADD KEY `fk_inventory_proposal` (`Kode_barang`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`Registration_ID`),
  ADD KEY `Email` (`Email`),
  ADD KEY `Event_ID` (`Event_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Email`);

--
-- Indexes for table `verificator`
--
ALTER TABLE `verificator`
  ADD PRIMARY KEY (`Verificator_ID`),
  ADD KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `Event_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `Kode_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `organizer`
--
ALTER TABLE `organizer`
  MODIFY `Organizer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `proposal`
--
ALTER TABLE `proposal`
  MODIFY `Proposal_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `Registration_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `verificator`
--
ALTER TABLE `verificator`
  MODIFY `Verificator_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrows`
--
ALTER TABLE `borrows`
  ADD CONSTRAINT `borrows_ibfk_1` FOREIGN KEY (`Organizer_ID`) REFERENCES `organizer` (`Organizer_ID`),
  ADD CONSTRAINT `borrows_ibfk_2` FOREIGN KEY (`Kode_barang`) REFERENCES `inventory` (`Kode_barang`),
  ADD CONSTRAINT `fk_Kode_barang` FOREIGN KEY (`Kode_barang`) REFERENCES `inventory` (`Kode_barang`),
  ADD CONSTRAINT `fk_Organizer_ID` FOREIGN KEY (`Organizer_ID`) REFERENCES `organizer` (`Organizer_ID`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Organizer_ID`) REFERENCES `organizer` (`Organizer_ID`);

--
-- Constraints for table `gives`
--
ALTER TABLE `gives`
  ADD CONSTRAINT `gives_ibfk_1` FOREIGN KEY (`Kode_barang`) REFERENCES `inventory` (`Kode_barang`),
  ADD CONSTRAINT `gives_ibfk_2` FOREIGN KEY (`Event_ID`) REFERENCES `event` (`Event_ID`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`Verificator_ID`) REFERENCES `verificator` (`Verificator_ID`);

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`Event_ID`) REFERENCES `event` (`Event_ID`);

--
-- Constraints for table `organizer`
--
ALTER TABLE `organizer`
  ADD CONSTRAINT `organizer_ibfk_1` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`);

--
-- Constraints for table `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `fk_inventory_proposal` FOREIGN KEY (`Kode_barang`) REFERENCES `inventory` (`Kode_barang`),
  ADD CONSTRAINT `proposal_ibfk_1` FOREIGN KEY (`Organizer_ID`) REFERENCES `organizer` (`Organizer_ID`),
  ADD CONSTRAINT `proposal_ibfk_2` FOREIGN KEY (`Verificator_ID`) REFERENCES `verificator` (`Verificator_ID`);

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`),
  ADD CONSTRAINT `registration_ibfk_2` FOREIGN KEY (`Event_ID`) REFERENCES `event` (`Event_ID`);

--
-- Constraints for table `verificator`
--
ALTER TABLE `verificator`
  ADD CONSTRAINT `verificator_ibfk_1` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
