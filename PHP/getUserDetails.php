<?php

    include 'connect.php';
    session_start();
    $result = Connection();

    $jsonOutput = array();

    if (isset($result[0])) {
        $userID = $_GET["userID"]; 

        $query = "SELECT * FROM userdetailstable where userID = '$userID'";

        if ($sqlresult = mysqli_query($result[0], $query)) {
            $jsonOutput = mysqli_fetch_assoc($sqlresult);
        }
    }

    echo json_encode($jsonOutput);
    
?>

