<?php
include_once '../../Configuration/config.php';

class Verificator {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "sample", "Spataz10", "cem");
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Fixed method - returns Verificator_ID directly
    public function getVerificatorIDByEmail($email) {
        if (!$email) return null;
        
        $query = "SELECT Verificator_ID FROM Verificator WHERE Email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['Verificator_ID'];
        }
        return null;
    }

    // Fixed method - uses MySQLi and proper parameter binding
    public function getPendingProposals($verificatorID) {
        if (!$verificatorID) return [];

        $query = "SELECT p.Proposal_ID, p.Description, p.File, p.Status, i.Nama_barang as nama_barang, b.Jumlah_peminjaman as jumlah, b.Tanggal_peminjaman, b.Tanggal_pengembalian,
                         o.Nama_organizer as organizerName, o.Organizer_ID,
                         l.Lokasi, l.Waktu,
                         v.Verificator_ID, v.Nama_PJ
                  FROM Proposal p
                  LEFT JOIN Organizer o ON p.Organizer_ID = o.Organizer_ID
                  LEFT JOIN Event e ON p.Event_ID = e.Event_ID
                  LEFT JOIN Location l ON e.Event_ID = l.Event_ID
                  LEFT JOIN Verificator v ON p.Verificator_ID = v.Verificator_ID
                  LEFT JOIN Inventory i ON p.Kode_barang = i.Kode_barang
                  LEFT JOIN borrows b ON b.Organizer_ID = p.Organizer_ID AND b.Kode_barang = p.Kode_barang
                  WHERE p.Status IN ('pending', 'not approved') AND p.verificator_ID = ?
                  ORDER BY p.Proposal_ID DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $verificatorID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Similarly fix other methods...
    public function getApprovedProposals($verificatorID) {
        if (!$verificatorID) return [];
        
        $query = "SELECT p.Proposal_ID, p.Description, p.File, 
                         o.Nama_organizer as organizerName, o.Organizer_ID,
                         v.Verificator_ID, v.Nama_PJ, p.Status
                  FROM Proposal p
                  LEFT JOIN Organizer o ON p.Organizer_ID = o.Organizer_ID
                  LEFT JOIN Verificator v ON p.Verificator_ID = v.Verificator_ID
                  WHERE p.Status = 'approved' AND p.verificator_ID = ?
                  ORDER BY p.Proposal_ID DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $verificatorID);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getInventaris() {
        $query = "SELECT Kode_barang, Nama_barang, Jumlah_barang FROM Inventory ORDER BY Nama_barang";
        $result = $this->conn->query($query);
        return $result;
    }
        // Update status proposal
    public function updateProposalStatus($proposalID, $status, $verificatorID) {
        $query = "UPDATE Proposal SET Status = ?, Verificator_ID = ?
                  WHERE Proposal_ID = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $status, $verificatorID, $proposalID);

        return $stmt->execute();
    }

    public function getProposalByID($proposalID) {
        $query = "SELECT p.Proposal_ID, p.Description, p.File,
                         o.Nama_organizer as organizerName, o.Organizer_ID,
                         v.Verificator_ID, v.Nama_PJ, p.Status
                  FROM Proposal p
                  LEFT JOIN Organizer o ON p.Organizer_ID = o.Organizer_ID
                  LEFT JOIN Verificator v ON p.Verificator_ID = v.Verificator_ID
                  WHERE p.Proposal_ID = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $proposalID);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function approveProposal($proposalID, $verificatorID) {
        return $this->updateProposalStatus($proposalID, 'approved', $verificatorID);
    }

    public function rejectProposal($proposalID, $verificatorID) {
        return $this->updateProposalStatus($proposalID, 'not approved', $verificatorID);
    }

}
?>