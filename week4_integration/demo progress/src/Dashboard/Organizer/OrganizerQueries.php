<?php
require_once "../../Configuration/config.php";

class Organizer {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "sample", "Spataz10", "cem");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getOrganizerID($email) {
        $stmt = $this->conn->prepare("SELECT Organizer_ID FROM organizer WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['Organizer_ID'] : null;
    }

    public function getProposalDetails($proposal_id, $organizer_id) {
        $stmt = $this->conn->prepare("SELECT * FROM proposal WHERE Proposal_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("ii", $proposal_id, $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function getEventDetails($event_id, $organizer_id) {
        $stmt = $this->conn->prepare("SELECT * FROM event WHERE Event_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("si", $event_id, $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function updateEvent($event_id, $organizer_id, $name, $desc) {
        $stmt = $this->conn->prepare("UPDATE event SET Nama_event = ?, Description = ? WHERE Event_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("sssi", $name, $desc, $event_id, $organizer_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateLocation($event_id, $lokasi, $waktu) {
        $stmt = $this->conn->prepare("UPDATE location SET Lokasi = ?, Waktu = ? WHERE Event_ID = ?");
        $stmt->bind_param("sss", $lokasi, $waktu, $event_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateProposal($proposal_id, $organizer_id, $desc, $file_content) {
        if ($file_content !== null) {
            $stmt = $this->conn->prepare("UPDATE proposal SET Description = ?, File = ? WHERE Proposal_ID = ? AND Organizer_ID = ?");
            $stmt->bind_param("sbii", $desc, $file_content, $proposal_id, $organizer_id);
        } else {
            $stmt = $this->conn->prepare("UPDATE proposal SET Description = ? WHERE Proposal_ID = ? AND Organizer_ID = ?");
            $stmt->bind_param("sii", $desc, $proposal_id, $organizer_id);
        }
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function checkEventConflict($lokasi, $waktu, $exclude_event_id = null) {
        $query = "SELECT COUNT(*) as count FROM location WHERE Lokasi = ? AND Waktu = ?";
        if ($exclude_event_id) {
            $query .= " AND Event_ID != ?";
        }
        $stmt = $this->conn->prepare($query);
        if ($exclude_event_id) {
            $stmt->bind_param("sss", $lokasi, $waktu, $exclude_event_id);
        } else {
            $stmt->bind_param("ss", $lokasi, $waktu);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] > 0;
    }

    public function createEventWithLocation($name, $tipe, $desc, $lokasi, $waktu, $organizer_id) {
        $stmt = $this->conn->prepare("INSERT INTO event (Nama_event, Tipe_event, Description, Organizer_ID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $tipe, $desc, $organizer_id);
        $stmt->execute();
        $event_id = $this->conn->insert_id;
        $stmt->close();

        $stmt = $this->conn->prepare("INSERT INTO location (Event_ID, Lokasi, Waktu) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $event_id, $lokasi, $waktu);
        $stmt->execute();
        $stmt->close();

        return $event_id;
    }

    public function submitProposal($desc, $file_content, $organizer_id, $kode_barang) {
        $stmt = $this->conn->prepare("INSERT INTO proposal (Description, File, Organizer_ID, Kode_barang, Status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("sbii", $desc, $file_content, $organizer_id, $kode_barang);
        if ($file_content !== null) {
            $stmt->send_long_data(1, $file_content);
        }
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function createEventWithProposal($name, $tipe, $desc, $lokasi, $waktu, $organizer_id, $file_content, $kode_barang, $jumlah, $tanggal_peminjaman, $tanggal_pengembalian) {
        $this->conn->begin_transaction();
        try {
            // Create event
            $stmt = $this->conn->prepare("INSERT INTO event (Nama_event, Tipe_event, Description, Organizer_ID) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $tipe, $desc, $organizer_id);
            $stmt->execute();
            $event_id = $this->conn->insert_id;
            $stmt->close();

            // Create location
            $stmt = $this->conn->prepare("INSERT INTO location (Event_ID, Lokasi, Waktu) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $event_id, $lokasi, $waktu);
            $stmt->execute();
            $stmt->close();

            // Submit proposal
            $stmt = $this->conn->prepare("INSERT INTO proposal (Description, File, Organizer_ID, Kode_barang, Status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("sbii", $desc, $file_content, $organizer_id, $kode_barang);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return $event_id;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getInventory() {
        $stmt = $this->conn->prepare("SELECT Kode_barang, Nama_barang, Jumlah_barang FROM inventory");
        $stmt->execute();
        $result = $stmt->get_result();
        $inventory = [];
        while ($row = $result->fetch_assoc()) {
            $inventory[] = $row;
        }
        $stmt->close();
        return $inventory;
    }

    public function getItemQuantity($kode_barang) {
        $stmt = $this->conn->prepare("SELECT Jumlah_barang FROM inventory WHERE Kode_barang = ?");
        $stmt->bind_param("s", $kode_barang);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['Jumlah_barang'] : 0;
    }

    public function insertPeminjaman($tanggal_peminjaman, $tanggal_pengembalian, $jumlah, $organizer_id, $nama_barang) {
        $stmt = $this->conn->prepare("INSERT INTO peminjaman (Tanggal_peminjaman, Tanggal_pengembalian, Jumlah, Organizer_ID, Kode_barang) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $tanggal_peminjaman, $tanggal_pengembalian, $jumlah, $organizer_id, $nama_barang);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getProposalList($organizer_id) {
        $stmt = $this->conn->prepare("
            SELECT p.Proposal_ID, p.Description, p.Status, e.Nama_event, l.Lokasi, l.Waktu
            FROM proposal p
            JOIN event e ON p.Kode_barang = e.Kode_barang
            JOIN location l ON p.Kode_barang = l.Kode_barang
            WHERE p.Organizer_ID = ?
        ");
        $stmt->bind_param("i", $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $proposals = [];
        while ($row = $result->fetch_assoc()) {
            $proposals[] = $row;
        }
        $stmt->close();
        return $proposals;
    }

    public function getOrganizerHistory($organizer_id) {
        $stmt = $this->conn->prepare("
            SELECT p.Proposal_ID, p.Description, p.Status, p.Kode_barang,
                   CASE WHEN p.File IS NOT NULL THEN 'Yes' ELSE 'No' END as Proposal
            FROM proposal p
            WHERE p.Organizer_ID = ?
            ORDER BY p.Proposal_ID DESC
        ");
        $stmt->bind_param("i", $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Process results to decode JSON description
        $processed_results = [];
        while ($row = $result->fetch_assoc()) {
            $event_details = json_decode($row['Description'], true);
            if ($event_details) {
                $processed_results[] = [
                    'Proposal_ID' => $row['Proposal_ID'],
                    'Lokasi' => $event_details['lokasi'] ?? '',
                    'Waktu' => $event_details['waktu'] ?? '',
                    'Status' => $row['Status'],
                    'Nama_event' => $event_details['event_name'] ?? '',
                    'Description' => $event_details['description'] ?? '',
                    'Proposal' => $row['Proposal'],
                    'nama_barang' => $this->getItemName($row['Kode_barang']),
                    'jumlah' => $event_details['jumlah'] ?? ''
                ];
            }
        }
        $stmt->close();
        return $processed_results;
    }

    public function getItemName($kode_barang) {
        $stmt = $this->conn->prepare("SELECT Nama_barang FROM inventory WHERE Kode_barang = ?");
        $stmt->bind_param("s", $kode_barang);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['Nama_barang'] : '';
    }

    public function getProposalFile($proposal_id, $organizer_id) {
        $stmt = $this->conn->prepare("SELECT File FROM proposal WHERE Proposal_ID = ? AND Organizer_ID = ?");
        $stmt->bind_param("ii", $proposal_id, $organizer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['File'] : null;
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>
