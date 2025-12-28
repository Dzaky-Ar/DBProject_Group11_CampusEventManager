<?php
session_start();
require_once "../../Configuration/config.php";

if (isset($_POST["submit_item"])) {
    $judul = $_POST["Judul"] ?? '';
    $event_data = $_POST["event_id"];
    list($event_id, $event_name) = explode('|', $event_data);
    $event = $event_id;

    $email = $_SESSION['Email'] ?? ''; // Assuming email is stored in session
    $file_content = null;

    // Handle file upload if provided
    if (isset($_FILES["submission_pdf"]) && $_FILES["submission_pdf"]["error"] == 0) {
        $file_name = $_FILES["submission_pdf"]["name"];
        $file_tmp = $_FILES["submission_pdf"]["tmp_name"];
        $file_size = $_FILES["submission_pdf"]["size"];
        $file_type = $_FILES["submission_pdf"]["type"];

        // Check if file is PDF
        if ($file_type == "application/pdf") {
            $file_content = file_get_contents($file_tmp);
            if ($file_content === false) {
                $_SESSION['error_message'] = "Failed to read file.";
                header("Location: RecordSubmissions.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Only PDF files are allowed.";
            header("Location: RecordSubmissions.php");
            exit();
        }
    }

    // Insert into database
    $sql = "INSERT INTO Registration (Judul, Submission, Email, Event_ID) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $judul, $file_content, $email, $event);
    if ($file_content !== null) {
        $stmt->send_long_data(1, $file_content); // Send blob data
    }
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Registration successful!";
        header("Location: RegisterEvent.php?event_id=" . $event);
        exit();
    } else {
        $_SESSION['error_message'] = "Database error: " . $stmt->error;
        header("Location: RegisterEvent.php?event_id=" . $event);
        exit();
    }
    $stmt->close();
}
?>
