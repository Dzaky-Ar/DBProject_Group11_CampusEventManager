<?php
$proposal = filter_input(INPUT_POST, 'proposal', FILTER_SANITIZE_STRING);
$org = filter_input(INPUT_POST, 'organizer', FILTER_SANITIZE_STRING);

require_once 'config.php';

$target_dir = "C:\\xampp\\htdocs\\cem\\uploads\\";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
move_uploaded_file($_FILES["file"]["tmp_name"],$target_file);


if($conn-> connect_error){
    die('Connect Error ('. $conn->connect_errno .')' .$conn->connect_error);
} else{
    $stmt = $conn->prepare("Insert ignore into proposal_try(description,organizerID) values (?,?)");
    if($stmt === false){
        die('Prepare Failed: ' . $conn->error);
    }

    $stmt->bind_param("si",$proposal,$org);
   
}

if($stmt->execute()){
    //echo "Success" . "<br>  ";
} else{
    echo "error" . $stmt->error;
}
$stmt->close();

$conn->close();
header("Location: form.php");
?>




