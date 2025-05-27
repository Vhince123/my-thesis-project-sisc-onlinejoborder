<?php

     include "database_manipulator.php";
     $result = array();

     $dbConn = DatabaseManipulator::get_db_connection();

     $loginLogID = $dbConn->real_escape_string($_GET["loginLogID"]);
     $userID = $dbConn->real_escape_string($_GET["userID"]);
     $result = DatabaseManipulator::user_logout($userID, $loginLogID);

     echo json_encode($result);

?>