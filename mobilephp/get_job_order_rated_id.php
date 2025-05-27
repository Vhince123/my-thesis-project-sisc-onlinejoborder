<?php

     include "database_manipulator.php";

     $dbConn = DatabaseManipulator::get_db_connection();
     $result = DatabaseManipulator::get_job_order_rated_id();

     echo json_encode($result);

?>