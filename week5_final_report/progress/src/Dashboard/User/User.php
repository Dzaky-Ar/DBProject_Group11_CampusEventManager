<?php
include_once '../../Configuration/config.php';

class User {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "sample", "Spataz10", "cem");
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    // Update user submission
    public function updateSubmission($registration_id, $judul, $email, $file_content = null) {
        // Check if user owns this registration
        $stmt = $this->conn->prepare("SELECT * FROM Registration WHERE Registration_ID = ? AND Email = ?");
        $stmt->bind_param("is", $registration_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            return false; // Unauthorized
        }
        $stmt->close();

        // Update database
        if ($file_content !== null) {
            $sql = "UPDATE Registration SET Judul = ?, Submission = ? WHERE Registration_ID = ? AND Email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssis", $judul, $file_content, $registration_id, $email);
            $stmt->send_long_data(1, $file_content); // Send blob data
        } else {
            $sql = "UPDATE Registration SET Judul = ? WHERE Registration_ID = ? AND Email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sis", $judul, $registration_id, $email);
        }

        return $stmt->execute();
    }

    // Get user submissions
    public function getUserSubmissions($email) {
        $query = "SELECT r.Registration_ID, r.Judul, r.Submission, r.Email, r.Event_ID,
                         e.Nama_event, e.Tipe_event, e.Description
                  FROM Registration r
                  LEFT JOIN Event e ON r.Event_ID = e.Event_ID
                  WHERE r.Email = ?
                  ORDER BY r.Registration_ID DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Get submission by ID and email
    public function getSubmissionById($registration_id, $email) {
        $query = "SELECT r.Registration_ID, r.Judul, r.Submission, r.Email, r.Event_ID,
                         e.Nama_event, e.Tipe_event, e.Description
                  FROM Registration r
                  LEFT JOIN Event e ON r.Event_ID = e.Event_ID
                  WHERE r.Registration_ID = ? AND r.Email = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $registration_id, $email);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
