<?php
include_once '../../Configuration/config.php';

class Event {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "sample", "Spataz10", "cem");

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Ambil semua jadwal event lengkap dengan lokasi + organizer
    public function getAllEvents() {
        $query = "SELECT
                    e.Event_ID,
                    e.Nama_event,
                    e.Tipe_event,
                    e.Description,
                    l.Lokasi,
                    l.Waktu,
                    o.Nama_organizer AS organizerName,
                    e.Organizer_ID
                  FROM event e
                  LEFT JOIN Location l ON e.Event_ID = l.Event_ID
                  LEFT JOIN organizer o ON e.Organizer_ID = o.Organizer_ID
                  ORDER BY l.Waktu ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Ambil event berdasarkan ID
    public function getEventById($eventID) {
        $query = "SELECT
                    e.Event_ID,
                    e.Nama_event,
                    e.Tipe_event,
                    e.Description,
                    l.Lokasi,
                    l.Waktu,
                    o.Nama_organizer AS organizerName,
                    e.Organizer_ID
                  FROM event e
                  LEFT JOIN Location l ON e.Event_ID = l.Event_ID
                  LEFT JOIN organizer o ON e.Organizer_ID = o.Organizer_ID
                  WHERE e.Event_ID = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Ambil event upcoming
    public function getUpcomingEvents() {
        $query = "SELECT 
                    e.Event_ID, 
                    e.Nama_event, 
                    e.Tipe_event, 
                    e.Description,
                    l.Lokasi, 
                    l.Waktu, 
                    o.Nama_organizer AS organizerName
                  FROM event e
                  LEFT JOIN Location l ON e.Event_ID = l.Event_ID
                  LEFT JOIN organizer o ON e.Organizer_ID = o.Organizer_ID
                  WHERE l.Waktu >= CURDATE()
                  ORDER BY l.Waktu ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
