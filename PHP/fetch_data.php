<?php
include("connect.php");

if (isset($_POST['month']) && isset($_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];

    
    $result = Connection();

    if (isset($result[0])) {
        $conn = $result[0];
        $query = "SELECT a.*, ";

        // Select full name or first name based on user type
        $query .= "CASE 
                        WHEN b.userType = '0' THEN b.firstName
                        ELSE CONCAT(b.lastName, ', ', b.firstName, ' ', LEFT(b.middleName, 1), '.')
                    END AS fullname ";

        $query .= "FROM loginlogtable a
                    JOIN userdetailstable b ON a.userID = b.userID
                    WHERE YEAR(a.loginTime) = $year ";

        if($month == "all"){
            $query .= "";
        }else{
            $query .= " AND MONTH(a.loginTime) = $month";
        }

        $query .= " ORDER BY a.loginTime DESC";

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
