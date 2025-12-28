<?php
session_start();
require_once "../../Configuration/config.php";

if (isset($_POST["cancel"])) {
    $registration_id = $_POST["Registration_id"];
    $email = $_SESSION['Email'] ?? '';

    if ($email) {
        $sql = "DELETE FROM Registration WHERE Registration_ID = ? AND Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $registration_id, $email);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Submission cancelled successfully!";
        } else {
            $_SESSION['error_message'] = "Error cancelling submission.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "User not logged in.";
    }

    header("Location: RecordSubmissions.php");
    exit();
}
?>
