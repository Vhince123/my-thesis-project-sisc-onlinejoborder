<?php
    include "connect.php";
    session_start();
    $result = Connection();

    $jsonReturn = array();

    if(isset($result[0])){
            
        $newpass = $_GET["newpass"];
        $userid = $_SESSION["userid"];

        $query = "UPDATE userdetailstable SET password = '$newpass' WHERE userID = '$userid'";
        if (!mysqli_query($result[0], $query)) {
            $jsonReturn["message"] = "Query Error:";
            $jsonReturn["status"] = 2;
        }
        else {   
            $_SESSION["password"] = $newpass;
            $jsonReturn["message"] = "Success: ";
            $jsonReturn["status"] = 1;
        }

    }

    echo json_encode($jsonReturn);
?>