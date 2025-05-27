<?php

    function Connection() {
        $result = array();
        $server = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dbjoborder";

        $conn = mysqli_connect($server, $username, $password, $dbname);

        if (!$conn) {
            $result[-1] = "Failed to Connect to the database";
        }
        else {
            $result[0] = $conn;
        }

        return $result;
}
?>