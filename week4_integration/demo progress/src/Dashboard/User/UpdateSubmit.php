<?php
session_start();
require_once "../../Configuration/config.php";

if (!isset($_SESSION['User_name'])) {
    header("Location: ../../Main_menu/Login_Page.php");
    exit();
}

if (isset($_POST['update_item'])) {
    $registration_id = intval($_POST['registration_id']);
    $judul = trim($_POST['Judul']);
    $email = $_SESSION['Email'] ?? '';

    // Check if user owns this registration
    $stmt = $conn->prepare("SELECT * FROM registration WHERE Registration_ID = ? AND Email = ?");
    $stmt->bind_param("is", $registration_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $_SESSION['error_message'] = "Unauthorized access.";
        header("Location: RecordSubmissions.php");
        exit();
    }
    $stmt->close();

    // Handle file upload if provided
    $file_content = null;
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
                header("Location: UpdateSubmission.php?id=" . $registration_id);
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Only PDF files are allowed.";
            header("Location: UpdateSubmission.php?id=" . $registration_id);
            exit();
        }
    }

    // Update database
    if ($file_content !== null) {
        $sql = "UPDATE Registration SET Judul = ?, Submission = ? WHERE Registration_ID = ? AND Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $judul, $file_content, $registration_id, $email);
        $stmt->send_long_data(1, $file_content); // Send blob data
    } else {
        $sql = "UPDATE Registration SET Judul = ? WHERE Registration_ID = ? AND Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $judul, $registration_id, $email);
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Submission updated successfully.";
        header("Location: RecordSubmissions.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Database error: " . $stmt->error;
        header("Location: UpdateSubmission.php?id=" . $registration_id);
        exit();
    }
    $stmt->close();
}
?>
