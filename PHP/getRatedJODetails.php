<?php

    include 'connect.php';
    session_start();
    $result = Connection();

    $jsonOutput = array();

    if (isset($result[0])) {
        $joID = $_GET["jobOrderID"]; 

        $query = "SELECT a.*, b.*, c.*, d.* FROM joborderdetailstable a 
        JOIN userdetailstable b ON a.userID = b.userID 
        JOIN joratedtable c ON a.jobOrderID = c.jobOrderID
        JOIN donejotable d ON a.jobOrderID = d.jobOrderID
        WHERE a.jobOrderID = '$joID'";

        if ($sqlresult = mysqli_query($result[0], $query)) {
            $jsonOutput = mysqli_fetch_assoc($sqlresult);
            // $imageFilePath = $jsonOutput['photo'];

            // $response = array(
            //     'jobOrderDescription' => $jsonOutput['jobOrderDescription'],
            //     'location' => $jsonOutput['location'],
            //     'userID' => $jsonOutput['userID'],
            //     'imageFilePath' => $imageFilePath
            // );
        }
    }

    echo json_encode($jsonOutput);
    
?>

