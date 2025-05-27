<?php

    include "database_manipulator.php";
    $result = array();

    $dbConn = DatabaseManipulator::get_db_connection();
    $userID = $dbConn->real_escape_string($_GET["userID"]);
    $result = DatabaseManipulator::get_worker_done_jo($userID);

    echo json_encode($result);

?>