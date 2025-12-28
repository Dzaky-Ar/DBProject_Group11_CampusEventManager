<?php
session_start();
require_once "queriesOrganizer.php";

$organizer = new Organizer();
$organizer_id = $organizer->getOrganizerID($_SESSION['Email']);

if (isset($_GET['proposal_id'])) {
    $proposal_id = intval($_GET['proposal_id']);

    if ($organizer->cancelProposal($proposal_id, $organizer_id)) {
        $message = "Submission cancelled successfully!";
        $status = 'success';
    } else {
        $message = "Error cancelling submission.";
        $status = 'error';
    }
} else {
    $message = "Invalid request.";
    $status = 'error';
}

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
} else {
    $_SESSION[$status . '_message'] = $message;
    header("Location: RecordEntries.php");
    exit();
}
?>
