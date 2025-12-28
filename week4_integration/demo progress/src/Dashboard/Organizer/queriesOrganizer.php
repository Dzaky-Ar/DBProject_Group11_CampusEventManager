<?php
class Organizer {
    private $conn; 

    public function __construct() {
        $this->conn = new mysqli("localhost", "sample", "Spataz10", "cem");

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    // Example Query 2: Get all users
    public function getInventory() {
        $result = $this->conn->query("SELECT * FROM Inventory");
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function submitProposal($desc, $file_content, $organizer_id, $kode_barang) {
        $sql = "INSERT INTO Proposal (Status, Description, File, Organizer_ID, Kode_barang)
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $status = 'pending';
        $stmt->bind_param("ssbii", $status, $desc, $file_content, $organizer_id, $kode_barang);

        $stmt->send_long_data(2, $file_content);
        return $stmt->execute();

    }

    public function createEvents($name, $type, $desc, $organizer_id) {
        $stmt = $this->conn->prepare("SELECT u.Status_user FROM organizer o 
        JOIN user u ON o.Email = u.Email WHERE o.Organizer_ID = ?");

        $stmt->bind_param("i", $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $row = $result->fetch_assoc();
        if ($row['Status_user'] != '4') {
            return false;
        }

        $stmt2 = $this->conn->prepare("INSERT INTO Event (Nama_event, Tipe_event, Description, Organizer_ID)
                                    VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("sssi", $name, $type, $desc, $organizer_id);
        $stmt2->execute();

        return $this->conn->insert_id;
    }

    public function getOrganizerID($email) {
        $stmt = $this->conn->prepare("SELECT Organizer_ID FROM organizer WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['Organizer_ID'];
        }
        return null;
    }

    public function getOrganizerHistory($organizer_id) {
        $stmt = $this->conn->prepare("SELECT p.Proposal_ID, l.Lokasi, l.Waktu, p.Status, e.Nama_event, e.Description, CASE WHEN p.File IS NOT NULL THEN 'Yes' ELSE 'No' END AS Proposal, i.Nama_barang FROM Proposal p JOIN Event e ON p.Kode_barang = e.Event_ID JOIN Location l ON e.Event_ID = l.Event_ID JOIN Inventory i ON p.Kode_barang = i.Kode_barang WHERE p.Organizer_ID = ?");
        $stmt->bind_param("i", $organizer_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function createEventWithLocation($name, $tipe, $desc, $lokasi, $waktu, $organizer_id) {
    // 1️⃣ Insert into Event
    $stmt1 = $this->conn->prepare(
        "INSERT INTO event (Nama_event, Tipe_event, Description, Organizer_ID)
         VALUES (?, ?, ?, ?)"
    );
    $stmt1->bind_param("sssi", $name, $tipe, $desc, $organizer_id);
    $stmt1->execute();

    $event_id = $this->conn->insert_id; // get Event_ID of newly created event

    if (!$event_id) return false;

    // 2️⃣ Insert into Location
    $stmt2 = $this->conn->prepare(
        "INSERT INTO location (Lokasi, Waktu, Event_ID) VALUES (?, ?, ?)"
    );
    $stmt2->bind_param("ssi", $lokasi, $waktu, $event_id);
    $stmt2->execute();

    return $event_id; // return Event_ID for further use
}

    public function getProposalDetails($proposal_id, $organizer_id) {
        $stmt = $this->conn->prepare("SELECT * FROM Proposal WHERE Proposal_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("ii", $proposal_id, $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateProposal($proposal_id, $organizer_id, $new_desc, $file_content = null) {
        if ($file_content !== null) {
            $stmt = $this->conn->prepare("UPDATE Proposal SET Description = ?, File = ? WHERE Proposal_ID = ? AND Organizer_ID = ?");
            $stmt->bind_param("sbii", $new_desc, $file_content, $proposal_id, $organizer_id);
            $stmt->send_long_data(1, $file_content);
        } else {
            $stmt = $this->conn->prepare("UPDATE Proposal SET Description = ? WHERE Proposal_ID = ? AND Organizer_ID = ?");
            $stmt->bind_param("sii", $new_desc, $proposal_id, $organizer_id);
        }
        return $stmt->execute();
    }

    public function updateEvent($event_id, $organizer_id, $name, $desc) {
        $stmt = $this->conn->prepare("UPDATE Event SET Nama_event = ?, Description = ? WHERE Event_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("ssii", $name, $desc, $event_id, $organizer_id);
        return $stmt->execute();
    }

    public function updateLocation($event_id, $lokasi, $waktu) {
        $stmt = $this->conn->prepare("UPDATE Location SET Lokasi = ?, Waktu = ? WHERE Event_ID = ?");
        $stmt->bind_param("ssi", $lokasi, $waktu, $event_id);
        return $stmt->execute();
    }

    public function getEventDetails($event_id, $organizer_id) {
        $stmt = $this->conn->prepare("SELECT e.Nama_event, e.Description, l.Lokasi, l.Waktu FROM Event e JOIN Location l ON e.Event_ID = l.Event_ID WHERE e.Event_ID = ? AND e.Organizer_ID = ?");
        $stmt->bind_param("ii", $event_id, $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function checkEventConflict($lokasi, $waktu, $exclude_event_id = null) {
        $query = "SELECT COUNT(*) as count FROM Location WHERE Lokasi = ? AND Waktu = ?";
        $params = [$lokasi, $waktu];
        $types = "ss";

        if ($exclude_event_id) {
            $query .= " AND Event_ID != ?";
            $params[] = $exclude_event_id;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    public function cancelProposal($proposal_id, $organizer_id) {
        $stmt = $this->conn->prepare("DELETE FROM Proposal WHERE Proposal_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("ii", $proposal_id, $organizer_id);
        return $stmt->execute();
    }

    public function getProposalFile($proposal_id, $organizer_id) {
        $stmt = $this->conn->prepare("SELECT File FROM Proposal WHERE Proposal_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("ii", $proposal_id, $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['File'];
        }
        return null;
    }

    public function insertPeminjaman($tanggal_peminjaman, $tanggal_pengembalian, $jumlah, $organizer_id, $kode_barang) {
        $stmt = $this->conn->prepare("INSERT INTO peminjaman (Tanggal_peminjaman, Tanggal_pengembalian, Jumlah_peminjaman, Organizer_ID, Kode_barang) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $tanggal_peminjaman, $tanggal_pengembalian, $jumlah, $organizer_id, $kode_barang);
        return $stmt->execute();
    }






    

}
?>
