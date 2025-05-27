<?php

    include 'connect.php';
    session_start();
    $result = Connection();

    $jsonOutput = array();

    if (isset($result[0])) {
        $joID = $_GET["jobOrderID"]; 

        $query = "SELECT a.*, b.* FROM joborderdetailstable a 
        JOIN userdetailstable b ON a.userid = b.userid 
        WHERE a.jobOrderID = '$joID'";

        if ($sqlresult = mysqli_query($result[0], $query)) {
            $jsonOutput = mysqli_fetch_assoc($sqlresult);
            $imageFilePath = $jsonOutput['photo'];

            $response = array(
                'jobOrderDescription' => $jsonOutput['jobOrderDescription'],
                'location' => $jsonOutput['location'],
                'userID' => $jsonOutput['userID'],
                'imageFilePath' => $imageFilePath
            );
        }
    }

    echo json_encode($jsonOutput);
    
?>

