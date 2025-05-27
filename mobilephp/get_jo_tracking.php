<?php

     include "database_manipulator.php";

     $dbConn = DatabaseManipulator::get_db_connection();

     $result = array();

     $userID = $dbConn->real_escape_string($_GET["userID"]);

     $result = DatabaseManipulator::get_jo_tracking($userID);

     echo json_encode($result);

?>