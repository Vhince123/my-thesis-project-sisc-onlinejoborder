<?php

     include "database_manipulator.php";

     $dbConn = DatabaseManipulator::get_db_connection();

     $result = array();

     $username = $dbConn->real_escape_string($_GET["username"]);
     $password = $dbConn->real_escape_string($_GET["password"]);

     $result = DatabaseManipulator::check_login($username, $password);

     echo json_encode($result);

?>