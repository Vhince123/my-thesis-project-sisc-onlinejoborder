<?php

     include "database_manipulator.php";

     $dbConn = DatabaseManipulator::get_db_connection();

     $result = array();

     //$statusID = $dbConn->real_escape_string($_GET["statusID"]);
     $userID = $dbConn->real_escape_string($_GET["userID"]);

     $result = DatabaseManipulator::get_all_status_jo($userID);

     echo json_encode($result);

?>