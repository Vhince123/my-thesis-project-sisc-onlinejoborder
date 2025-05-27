<?php
include("connect.php");

if (isset($_POST['month']) && isset($_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];

    $result = Connection();

    if (isset($result[0])) {
        $conn = $result[0];

        $query = "SELECT a.*, b.*, c.*, CONCAT(c.lastName, ', ', c.firstName, ' ', LEFT(c.middleName, 1), '.') AS fullname
                FROM joratedtable a 
                JOIN joborderdetailstable b ON a.jobOrderID=b.jobOrderID 
                JOIN userdetailstable c ON b.userID=c.userID 
                WHERE";
        
        if($month == "all"){
            $query .= "";
        }else{
            $query .= " MONTH(a.dateRated) = '$month' AND";
        }
            $query .= " YEAR(a.dateRated) = '$year' ORDER BY a.dateRated ASC";

        $result = mysqli_query($conn, $query);

        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            $data = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            echo json_encode($data);
        } else {
            echo json_encode(["error" => "Failed to execute query"]);
        }
    } else {
        echo json_encode(["error" => "Failed to connect to the database"]);
    }
} else {
    echo json_encode(["error" => "Month and year parameters are missing"]);
}
?>
