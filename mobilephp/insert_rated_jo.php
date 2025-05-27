<?php

     include "database_manipulator.php";

     $dbConn = DatabaseManipulator::get_db_connection();

     $result = array();

     $jobOrderID = $dbConn->real_escape_string($_GET["jobOrderID"]);
     $userID = $dbConn->real_escape_string($_GET["userID"]);
     $timeliness = $dbConn->real_escape_string($_GET["timeliness"]);
     $accuracy = $dbConn->real_escape_string($_GET["accuracy"]);
     $comments = $dbConn->real_escape_string($_GET["comments"]);

     $result = DatabaseManipulator::insert_rated_jo($jobOrderID, $userID, $timeliness, $accuracy, $comments);

     echo json_encode($result);

?>