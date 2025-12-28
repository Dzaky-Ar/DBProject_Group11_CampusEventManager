-- Database
CREATE DATABASE IF NOT EXISTS cem;
USE cem;

-- =====================================================
-- TABLE: user
-- =====================================================
CREATE TABLE `user` (
  `Email` VARCHAR(254) NOT NULL,
  `Password` VARCHAR(100) NOT NULL,
  `Status_user` ENUM('1','2','3') DEFAULT NULL,
  `User_name` VARCHAR(254) NOT NULL,
  PRIMARY KEY (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: verificator
-- =====================================================
CREATE TABLE `verificator` (
  `Verificator_ID` INT NOT NULL AUTO_INCREMENT,
  `Nama_PJ` VARCHAR(254) NOT NULL,
  `Email` VARCHAR(254) NOT NULL,
  PRIMARY KEY (`Verificator_ID`),
  KEY `Email` (`Email`),
  CONSTRAINT `verificator_ibfk_1`
    FOREIGN KEY (`Email`) REFERENCES `user` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: organizer
-- =====================================================
CREATE TABLE `organizer` (
  `Organizer_ID` INT NOT NULL AUTO_INCREMENT,
  `Nama_organizer` VARCHAR(100) NOT NULL,
  `Instansi` VARCHAR(100) NOT NULL,
  `Email` VARCHAR(254) NOT NULL,
  PRIMARY KEY (`Organizer_ID`),
  KEY `Email` (`Email`),
  CONSTRAINT `organizer_ibfk_1`
    FOREIGN KEY (`Email`) REFERENCES `user` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: event
-- =====================================================
CREATE TABLE `event` (
  `Event_ID` INT NOT NULL AUTO_INCREMENT,
  `Nama_event` VARCHAR(254) NOT NULL,
  `Tipe_event` VARCHAR(50) DEFAULT NULL,
  `Description` VARCHAR(254) DEFAULT NULL,
  `Organizer_ID` INT DEFAULT NULL,
  PRIMARY KEY (`Event_ID`),
  KEY `Organizer_ID` (`Organizer_ID`),
  CONSTRAINT `event_ibfk_1`
    FOREIGN KEY (`Organizer_ID`) REFERENCES `organizer` (`Organizer_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: inventory
-- =====================================================
CREATE TABLE `inventory` (
  `Kode_barang` INT NOT NULL AUTO_INCREMENT,
  `Nama_barang` VARCHAR(200) NOT NULL,
  `Jumlah_barang` INT NOT NULL,
  `Verificator_ID` INT NOT NULL,
  PRIMARY KEY (`Kode_barang`),
  KEY `Verificator_ID` (`Verificator_ID`),
  CONSTRAINT `inventory_ibfk_1`
    FOREIGN KEY (`Verificator_ID`) REFERENCES `verificator` (`Verificator_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: proposal
-- =====================================================
CREATE TABLE `proposal` (
  `Proposal_ID` INT NOT NULL AUTO_INCREMENT,
  `Status` ENUM('approved','pending','not approved') NOT NULL DEFAULT 'pending',
  `Description` VARCHAR(254) DEFAULT NULL,
  `File` BLOB DEFAULT NULL,
  `Organizer_ID` INT DEFAULT NULL,
  `Verificator_ID` INT DEFAULT NULL,
  `Kode_barang` INT DEFAULT NULL,
  PRIMARY KEY (`Proposal_ID`),
  KEY `Organizer_ID` (`Organizer_ID`),
  KEY `Verificator_ID` (`Verificator_ID`),
  KEY `Kode_barang` (`Kode_barang`),
  CONSTRAINT `proposal_ibfk_1`
    FOREIGN KEY (`Organizer_ID`) REFERENCES `organizer` (`Organizer_ID`),
  CONSTRAINT `proposal_ibfk_2`
    FOREIGN KEY (`Verificator_ID`) REFERENCES `verificator` (`Verificator_ID`),
  CONSTRAINT `proposal_ibfk_3`
    FOREIGN KEY (`Kode_barang`) REFERENCES `inventory` (`Kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: registration
-- =====================================================
CREATE TABLE `registration` (
  `Registration_ID` INT NOT NULL AUTO_INCREMENT,
  `Submission` BLOB DEFAULT NULL,
  `Judul` VARCHAR(254) DEFAULT NULL,
  `Email` VARCHAR(254) NOT NULL,
  `Event_ID` INT NOT NULL,
  PRIMARY KEY (`Registration_ID`),
  KEY `Email` (`Email`),
  KEY `Event_ID` (`Event_ID`),
  CONSTRAINT `registration_ibfk_1`
    FOREIGN KEY (`Email`) REFERENCES `user` (`Email`),
  CONSTRAINT `registration_ibfk_2`
    FOREIGN KEY (`Event_ID`) REFERENCES `event` (`Event_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: location
-- =====================================================
CREATE TABLE `location` (
  `Lokasi` VARCHAR(200) NOT NULL,
  `Waktu` DATE NOT NULL,
  `Event_ID` INT NOT NULL,
  PRIMARY KEY (`Lokasi`, `Waktu`),
  KEY `Event_ID` (`Event_ID`),
  CONSTRAINT `location_ibfk_1`
    FOREIGN KEY (`Event_ID`) REFERENCES `event` (`Event_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: gives
-- =====================================================
CREATE TABLE `gives` (
  `Kode_barang` INT NOT NULL,
  `Event_ID` INT NOT NULL,
  KEY `Kode_barang` (`Kode_barang`),
  KEY `Event_ID` (`Event_ID`),
  CONSTRAINT `gives_ibfk_1`
    FOREIGN KEY (`Kode_barang`) REFERENCES `inventory` (`Kode_barang`),
  CONSTRAINT `gives_ibfk_2`
    FOREIGN KEY (`Event_ID`) REFERENCES `event` (`Event_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: borrows
-- =====================================================
CREATE TABLE `borrows` (
  `Tanggal_peminjaman` DATE NOT NULL,
  `Tanggal_pengembalian` DATE NOT NULL,
  `Jumlah_peminjaman` INT NOT NULL,
  `Organizer_ID` INT NOT NULL,
  `Kode_barang` INT NOT NULL,
  KEY `Organizer_ID` (`Organizer_ID`),
  KEY `Kode_barang` (`Kode_barang`),
  CONSTRAINT `borrows_ibfk_1`
    FOREIGN KEY (`Organizer_ID`) REFERENCES `organizer` (`Organizer_ID`),
  CONSTRAINT `borrows_ibfk_2`
    FOREIGN KEY (`Kode_barang`) REFERENCES `inventory` (`Kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
