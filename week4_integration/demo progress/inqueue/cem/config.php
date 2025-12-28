<?php
$host = "localhost";
$dbusername = "root";
$dbpassword="";
$dbname = "cem";

$conn = new mysqli($host, $dbusername,$dbpassword,$dbname);

if($conn-> connect_error){
    die('Connect Error ('. $conn->connect_errno .')' .$conn->connect_error);
} 
?>