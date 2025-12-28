<?php
session_start();
require_once "../../Configuration/config.php";

if (isset($_GET['id'])) {
    $registration_id = $_GET['id'];
    $email = $_SESSION['Email'] ?? '';

    if ($email) {
        $sql = "SELECT Submission FROM Registration WHERE Registration_ID = ? AND Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $registration_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $file_content = $row['Submission'];
            if ($file_content) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="submission.pdf"');
                header('Content-Length: ' . strlen($file_content));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $file_content;
                exit();
            } else {
                echo "No file found.";
            }
        } else {
            echo "Submission not found or access denied.";
        }
        $stmt->close();
    } else {
        echo "User not logged in.";
    }
} else {
    echo "Invalid request.";
}
?>
