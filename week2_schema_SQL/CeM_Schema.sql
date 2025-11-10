-- Buat database
CREATE DATABASE IF NOT EXISTS cem;
USE cem;

-- Tabel user
CREATE TABLE user (
    email VARCHAR(254) PRIMARY KEY,
    password VARCHAR(100) NOT NULL,
    username VARCHAR(254),
    statusUser ENUM('1','2','3','4','5')
);

-- Tabel verificator
CREATE TABLE verificator (
    verificatorID INT AUTO_INCREMENT PRIMARY KEY,
    tingkat ENUM('1','2','3'),
    namaPJ VARCHAR(254),
    email VARCHAR(254) NOT NULL,
    FOREIGN KEY (email) REFERENCES user(email)
);

-- Tabel organizer
CREATE TABLE organizer (
    organizerID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    instansi VARCHAR(100),
    email VARCHAR(250) NOT NULL,
    FOREIGN KEY(email) REFERENCES user(email)
);

-- Tabel proposal
CREATE TABLE proposal(
    proposalID INT AUTO_INCREMENT PRIMARY KEY,
    status ENUM('approved','not approved','pending'),
    description VARCHAR(254),
    file BLOB,
    organizerID INT,
    verificationID INT,
    FOREIGN KEY(organizerID) REFERENCES organizer(organizerID),
    FOREIGN KEY(verificationID) REFERENCES verificator(verificatorID)
);

-- Tabel event
CREATE TABLE event(
    eventID INT AUTO_INCREMENT PRIMARY KEY,
    nameEvent VARCHAR(254),
    tipeEvent VARCHAR(50),
    description VARCHAR(254),
    organizerID INT NOT NULL,
    FOREIGN KEY(organizerID) REFERENCES organizer(organizerID)
);

-- Tabel inventaris
CREATE TABLE inventaris(
    kodeBarang INT AUTO_INCREMENT PRIMARY KEY,
    namaBarang VARCHAR(200) NOT NULL,
    jumlah INT NOT NULL
);

-- Tabel location
CREATE TABLE location(
    lokasi VARCHAR(200) NOT NULL,
    waktu DATE NOT NULL,
    eventID INT NOT NULL,
    PRIMARY KEY(lokasi, waktu),
    FOREIGN KEY(eventID) REFERENCES event(eventID)
);

-- Tabel registration
CREATE TABLE registration(
    registrationID INT AUTO_INCREMENT PRIMARY KEY,
    submission BLOB,
    judul VARCHAR(254),
    eventID INT NOT NULL,
    userEmail VARCHAR(254) NOT NULL,
    FOREIGN KEY(eventID) REFERENCES event(eventID),
    FOREIGN KEY(userEmail) REFERENCES user(email)
);

-- Tabel gives
CREATE TABLE gives(
    kodeBarang INT,
    eventID INT,
    FOREIGN KEY(kodeBarang) REFERENCES inventaris(kodeBarang),
    FOREIGN KEY(eventID) REFERENCES event(eventID)
);

-- Tabel borrows
CREATE TABLE borrows(
    kodeBarang INT NOT NULL,
    organizerID INT NOT NULL,
    tanggalPeminjaman DATE,
    tanggalPengembalian DATE,
    jumlah INT NOT NULL,
    FOREIGN KEY(kodeBarang) REFERENCES inventaris(kodeBarang),
    FOREIGN KEY(organizerID) REFERENCES organizer(organizerID)
);