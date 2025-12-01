CREATE TABLE User
(
  Email varchar(254) NOT NULL,
  Password varchar(100) NOT NULL,
  Status_user enum('1', '2', '3', '4', '5') NOT NULL,
  User_name varchar(254) NOT NULL,
  PRIMARY KEY (Email)
);

CREATE TABLE Inventory
(
  Jumlah_barang INT NOT NULL,
  Kode_barang INT auto_increment NOT NULL,
  Nama_barang varchar(200) NOT NULL,
  Verificator_ID INT NOT NULL,
  PRIMARY KEY (Kode_barang),
  FOREIGN KEY (Verificator_ID) REFERENCES Verificator(Verificator_ID)
);


CREATE TABLE Verificator
(
  Tingkat enum('1', '2', '3') NOT NULL,
  Verificator_ID INT auto_increment NOT NULL,
  Nama_PJ varchar(254) NOT NULL,
  Email varchar(254) NOT NULL,
  PRIMARY KEY (Verificator_ID),
  FOREIGN KEY (Email) REFERENCES User(Email)
);

CREATE TABLE Organizer
(
  Instansi varchar(100) NOT NULL,
  Nama_organizer varchar(100) NOT NULL,
  Organizer_ID INT auto_increment NOT NULL,
  Email varchar(254) NOT NULL,
  PRIMARY KEY (Organizer_ID),
  FOREIGN KEY (Email) REFERENCES User(Email)
);

CREATE TABLE Event
(
  Event_ID INT auto_increment NOT NULL,
  Nama_event varchar(254) NOT NULL,
  Tipe_event varchar(50),
  Description varchar(254),
  Organizer_ID INT NOT NULL,
  PRIMARY KEY (Event_ID),
  FOREIGN KEY (Organizer_ID) REFERENCES Organizer(Organizer_ID)
);

CREATE TABLE Location
(
  Lokasi varchar(200) NOT NULL,
  Waktu date NOT NULL,
  Event_ID INT NOT NULL,
  PRIMARY KEY (Lokasi, Waktu),
  FOREIGN KEY (Event_ID) REFERENCES Event(Event_ID)
);

CREATE TABLE Registration
(
  Submission blob,
  Judul varchar(254),
  Registration_ID INT auto_increment NOT NULL,
  Email varchar(254) NOT NULL,
  Event_ID INT NOT NULL,
  PRIMARY KEY (Registration_ID),
  FOREIGN KEY (Email) REFERENCES User(Email),
  FOREIGN KEY (Event_ID) REFERENCES Event(Event_ID)
);

CREATE TABLE Proposal
(
  Proposal_ID INT auto_increment NOT NULL,
  Status Enum('approved', 'pending', 'not approved') NOT NULL,
  Description varchar(254),
  File blob,
  Organizer_ID INT NOT NULL,
  Verificator_ID INT,
  Kode_barang INT,
  PRIMARY KEY(Proposal_ID),
  FOREIGN KEY (Organizer_ID) REFERENCES Organizer(Organizer_ID),
  FOREIGN KEY (Verificator_ID) REFERENCES Verificator(Verificator_ID)
);

CREATE TABLE borrows
(
  Tanggal_peminjaman DATE NOT NULL,
  Tanggal_pengembalian DATE NOT NULL,
  Jumlah_peminjaman INT NOT NULL,
  Organizer_ID INT NOT NULL,
  Kode_barang INT NOT NULL,
  FOREIGN KEY (Organizer_ID) REFERENCES Organizer(Organizer_ID),
  FOREIGN KEY (Kode_barang) REFERENCES Inventory(Kode_barang)
);

CREATE TABLE gives
(
  Kode_barang INT NOT NULL,
  Event_ID INT NOT NULL,
  FOREIGN KEY (Kode_barang) REFERENCES Inventory(Kode_barang),
  FOREIGN KEY (Event_ID) REFERENCES Event(Event_ID)
);

DELIMITER //

CREATE TRIGGER trg_update_inventory_before_insert
BEFORE INSERT ON borrows
FOR EACH ROW
BEGIN
    -- Check if stock is enough
    IF (SELECT Jumlah_barang 
        FROM inventory 
        WHERE Kode_barang = NEW.Kode_barang) < NEW.Jumlah_peminjaman THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Not enough stock in inventory.';
    END IF;

    -- Subtract the borrowed amount
    UPDATE inventory
    SET Jumlah_barang = Jumlah_barang - NEW.Jumlah_peminjaman
    WHERE Kode_barang = NEW.Kode_barang;
END //

DELIMITER ;
