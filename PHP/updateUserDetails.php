<?php
include "connect.php";
session_start();
$result = Connection();

$jsonReturn = array();

$fname = $_GET['fName'];
$mname = $_GET['mName'];
$lname = $_GET['lName'];
$dept = $_GET['dept'];
$password = $_GET['pword'];
$userid = $_GET['uName'];
$uEmail = $_GET['uEmail'];
//$updateprof = $_GET["update-data"];

if(isset($result[0])){
    $query = "UPDATE userdetailstable SET firstName = '$fname', middleName = '$mname', lastName='$lname', email = '$uEmail', department = '$dept', password='$password' WHERE userID = '$userid'";
    if (!mysqli_query($result[0], $query)) {
        $jsonReturn["Error1"] = mysqli_error($result[0]);
    }
    else {                        
        $jsonReturn["Success"] = "User details has been updated!";
    }
} else {
    $jsonReturn["Error0"] = "Database connection error";
}   

echo json_encode($jsonReturn);
?>
