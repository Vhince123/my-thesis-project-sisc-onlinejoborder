<?php
include("connect.php");

session_start();


if (isset($_POST['month']) && isset($_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];


    $department = $_SESSION['department'];

    $result = Connection();

    if (isset($result[0])) {
        $conn = $result[0];

        $query = "SELECT
                    CONCAT(b.firstName , ' ', b.lastName) AS requisitioner, 
                    a.dateRequested,
                    a.jobOrderNumber,
                    a.statusTypeID,
                    c.statusName,
                    a.jobOrderID,
                    b.department
                    FROM joborderdetailstable a 
                    JOIN userdetailstable b ON a.userID = b.userID
                    JOIN statustypetable c ON a.statusTypeID = c.statusTypeID
                    WHERE b.department = '$department' AND NOT a.statusTypeID = 7 AND ";
        
        if($month == "all"){
            $query .= "";
        }else{
            $query .= " MONTH(a.dateRequested) = '$month' AND";
        }
            $query .= " YEAR(a.dateRequested) = '$year' ORDER BY a.dateRequested DESC";

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
