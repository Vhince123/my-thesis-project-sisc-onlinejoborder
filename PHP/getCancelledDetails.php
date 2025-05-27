<?php

    include 'connect.php';
    session_start();
    $result = Connection();

    $jsonOutput = array();

    if (isset($result[0])) {
        $joID = $_GET["jobOrderID"]; 

        $query = "SELECT c.comments, CONCAT(b.firstName, ' ',b.lastName) AS fullname,
        a.jobOrderDescription, a.location, a.photo
        FROM joborderdetailstable a 
        JOIN userdetailstable b ON a.userid = b.userid 
        JOIN rejectjotable c ON a.jobOrderID = c.jobOrderID
        WHERE c.jobOrderID = '$joID'";

        if ($sqlresult = mysqli_query($result[0], $query)) {
            $jsonOutput = mysqli_fetch_assoc($sqlresult);
        }
    }

    echo json_encode($jsonOutput);
    
?>

