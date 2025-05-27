<?php

     include "database_manipulator.php";

     $dbConn = DatabaseManipulator::get_db_connection();

     $result = array();

     $workersID = $dbConn->real_escape_string($_GET["workersID"]);
     $jobOrdersID = $dbConn->real_escape_string($_GET["jobOrdersID"]);
     $comments = $dbConn->real_escape_string($_GET["comments"]);

     $result = DatabaseManipulator::update_wfm_jo($workersID, $jobOrdersID, $comments);

     echo json_encode($result);

?>