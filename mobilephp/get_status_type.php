<?php

     include "database_manipulator.php";

     $dbConn = DatabaseManipulator::get_db_connection();
     $result = DatabaseManipulator::get_status_type();

     echo json_encode($result);

?>