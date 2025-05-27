<?php

    include ("connect.php");
    $result = Connection();

    $month = $_GET["month"];

    $returnArray = array();

    if (isset($result[0])) {
        $conn = $result[0];

        $query2 = "SELECT COUNT(*) ongoing_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND statusTypeID = 1";
        $query3 = "SELECT COUNT(*) waiting_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND statusTypeID = 4";
        $query4 = "SELECT COUNT(*) outsource_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND statusTypeID = 5";
        $query5 = "SELECT COUNT(*) done_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND statusTypeID = 2";
        $query6 = "SELECT COUNT(*) rejected_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND statusTypeID = 3";

        $result2 = $conn->query($query2);
        $result3 = $conn->query($query3);
        $result4 = $conn->query($query4);
        $result5 = $conn->query($query5);
        $result6 = $conn->query($query6);

        if ( $result2 && $result3 && $result4 && $result5 && $result6) {
            $ongoing_JO = $result2->fetch_assoc()["ongoing_JO"];
            $waiting_JO = $result3->fetch_assoc()["waiting_JO"];
            $outsource_JO = $result4->fetch_assoc()["outsource_JO"];
            $done_JO = $result5->fetch_assoc()["done_JO"];
            $rejected_JO = $result6->fetch_assoc()["rejected_JO"];

            // Calculating the sum
            $sum_of_counts =  $ongoing_JO + $waiting_JO + $outsource_JO + $done_JO + $rejected_JO;

            $returnArray["ongoing_JO"] = $ongoing_JO;
            $returnArray["waiting_JO"] = $waiting_JO;
            $returnArray["outsource_JO"] = $outsource_JO;
            $returnArray["done_JO"] = $done_JO;
            $returnArray["rejected_JO"] = $rejected_JO;
            $returnArray["sum_of_counts"] = $sum_of_counts;

        }
        else {
            $returnArray["Error 1"] = "Query Error";
        }
    }

    echo json_encode($returnArray);

?>