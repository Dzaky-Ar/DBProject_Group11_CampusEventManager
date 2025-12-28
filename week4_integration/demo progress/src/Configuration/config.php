<?php
    $conn = mysqli_connect("localhost","sample","Spataz10","cem");

    if (!$conn) {
        die("connection failed!". mysqli_connect_error());
    }
?>