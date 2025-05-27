<?php
        include "connect.php";
        session_start();
        $result = Connection();

        $jsonReturn = array();

        $userid = $_POST['deleteuserid'];
        
        if(isset($result[0])){
            if(isset($_POST["delete-data"])){
                $query1 = "UPDATE userdetailstable SET userArchieve = '1' WHERE userID = '$userid'";
                if(!mysqli_query($result[0], $query1)) {
                    $jsonReturn["Error1"] = mysqli_error($result[0]);
                }
                else{                        
                    $jsonReturn["Success"] = "Success";
                    header("location: http://localhost/Thesis/adminUserView.php");
                    exit();
                }
            }   
        }
        else {
            $jsonReturn["Error0"] = $result[-1];
        }
        echo json_encode($jsonReturn);
?>