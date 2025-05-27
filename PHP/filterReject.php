<?php
include("connect.php");

if (isset($_POST['month']) && isset($_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];

    $result = Connection();

    if (isset($result[0])) {
        $conn = $result[0];

        $query = "SELECT a.*, b.*, c.*, CONCAT(d.lastName, ', ', d.firstName, ' ', LEFT(d.middleName, 1), '.') AS fullname
                FROM rejectjotable a 
                JOIN joborderdetailstable b ON a.jobOrderID=b.jobOrderID 
                JOIN userdetailstable c ON b.userID=c.userID 
                JOIN userdetailstable d ON a.userID = d.userID
                WHERE";
        
        if($month == "all"){
            $query .= "";
        }else{
            $query .= " MONTH(a.dateFiled) = '$month' AND";
        }
            $query .= " YEAR(a.dateFiled) = '$year' ORDER BY a.dateFiled DESC";

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
