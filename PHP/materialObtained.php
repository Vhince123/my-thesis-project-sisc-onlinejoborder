<?php

    include "connect.php";
    include "smtpEmailUpdate.php";
    session_start();
    $result = Connection();

    $forFullName = $_SESSION["firstname"] . " " . $_SESSION["lastname"];

    $returnArray = array();

    $today = date("Y-m-d H:i:s");
    $wfmjoid = $_GET["wfmjoid"];

    $act = "Materials are already obtained and the job order is on the process...";

    $query_check = "SELECT activity FROM jotrackingtable WHERE jobOrderID = '$wfmjoid'";
    $result_check = mysqli_query($result[0], $query_check);
    $row_check = mysqli_fetch_assoc($result_check);

    $current_activity = $row_check['activity'];

    if ($current_activity != $act) {
        $query1 = "UPDATE jotrackingtable SET activity = '$act' WHERE jobOrderID = '$wfmjoid'";
        $sqlResult1 = mysqli_query($result[0], $query1);

        if(!$sqlResult1){
            $returnArray["message"] = "Query Error:";
            $returnArray["status"] = 0;
        }
        else{
            $returnArray["message"] = "Success";
            $returnArray["status"] = 1;
            $emailQuery = "SELECT b.email, a.jobOrderNumber FROM joborderdetailstable a
            JOIN userdetailstable b ON a.userID = b.userID
            WHERE a.jobOrderID = '$wfmjoid'";
            $emailResult = mysqli_query($result[0], $emailQuery);
            if($emailRow = mysqli_fetch_assoc($emailResult)){
                $email = $emailRow['email'];
                $joNum = $emailRow['jobOrderNumber'];
                $body = 'We like to inform you that your job order: <b>'. $joNum .'</b> materials has been obtained and your job order
                is currently on process!<br><br>' . PHP_EOL . PHP_EOL .

                'Best Regards, <br><br>' . PHP_EOL .
                ''. $forFullName .'<br>' . PHP_EOL .
                'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                'Administrative Office';
                $emailMessage = SendEmail::sendEmail($email, $body);
            }
        }
    } 
    else {
        $returnArray["message"] = "Already obtained";
        $returnArray["status"] = 2;
    }

    echo json_encode($returnArray);

?>