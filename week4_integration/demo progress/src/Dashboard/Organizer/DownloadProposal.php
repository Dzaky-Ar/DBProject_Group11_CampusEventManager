<?php
session_start();
require_once "OrganizerQueries.php";

if (!isset($_SESSION['User_name'])) {
    header("Location: ../../Main_menu/Login_Page.php");
    exit();
}

if (!isset($_GET['proposal_id'])) {
    die("Proposal ID not provided.");
}

$proposal_id = intval($_GET['proposal_id']);
$organizer = new Organizer();
$organizer_id = $organizer->getOrganizerID($_SESSION['Email']);

// Get the proposal file content
$file_content = $organizer->getProposalFile($proposal_id, $organizer_id);

if ($file_content === null) {
    die("Proposal not found or access denied.");
}

if (!$file_content) {
    die("No file attached to this proposal.");
}

// Set headers for file download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="proposal_' . $proposal_id . '.pdf"');
header('Content-Length: ' . strlen($file_content));

// Output the file content
echo $file_content;
exit();
?>
