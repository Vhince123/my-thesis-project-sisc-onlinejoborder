<?php
    include 'connect.php';
    session_start();
    $result = Connection();

    $selectedMonth = $_GET['month'];
    $selectedYear = $_GET['year'];

    $jsonOutput = array();

    if (isset($result[0])) {
        date_default_timezone_set('Asia/Manila');

        $daysOfTheMonth = array();
        $leadTimeInEveryDays = array();

        $numDays = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);

        for ($day = 1; $day <= $numDays; $day++) {
            array_push($daysOfTheMonth, $day);
            array_push($leadTimeInEveryDays, 0);
        }

        // $query1 = "SELECT DAY(dateServed) AS dayOfMonth, AVG(DATEDIFF(dateFinished, dateServed)) AS avgLeadTime
        //             FROM joborderdetailstable
        //             WHERE MONTH(dateFinished) = '$selectedMonth' 
        //                 AND YEAR(dateFinished) = '$selectedYear'
        //                 AND dateServed != 0
        //                 AND dateFinished != 0
        //                 AND dateServed != dateFinished
        //                 AND statusTypeID != 7
        //             GROUP BY DAY(dateServed)";
        $query1 = "SELECT DAY(dateRequested) AS dayOfMonth, COUNT(*) AS jobOrderCount
                    FROM joborderdetailstable
                    WHERE MONTH(dateRequested) = '$selectedMonth' 
                        AND YEAR(dateRequested) = '$selectedYear'
                    GROUP BY DAY(dateRequested);";

        $result1 = mysqli_query($result[0], $query1);   

        $query2 = "SELECT CONCAT(
                    FLOOR(AVG(TIMESTAMPDIFF(MINUTE, dateServed, dateFinished)) / (24 * 60)), ' days, ',
                    MOD(FLOOR(AVG(TIMESTAMPDIFF(MINUTE, dateServed, dateFinished)) / 60), 24), ' hours, ',
                    FLOOR(MOD(AVG(TIMESTAMPDIFF(MINUTE, dateServed, dateFinished)), 60)), ' minutes'
                ) AS averageLeadTimeFormatted
            FROM 
                joborderdetailstable
            WHERE 
                MONTH(dateFinished) = '$selectedMonth' 
                AND YEAR(dateFinished) = '$selectedYear'
                AND dateServed != 0
                AND dateFinished != 0
                AND dateServed != dateFinished";

        $result2 = mysqli_query($result[0], $query2);

        $query3 = "SELECT
                        status,
                        COUNT(*) AS count,
                        CONCAT(
                            FLOOR(AVG(daysDifference) / (24)), ' days, ',
                            MOD(FLOOR(AVG(daysDifference) % (24)), 24), ' hours, ',
                            FLOOR(AVG(daysDifference % 60)), ' minutes'
                        ) AS averageDaysDifference
                    FROM (
                        SELECT 
                            b.dateNeeded,
                            b.dateFinished,
                            b.dateRequested,
                            CASE 
                                WHEN b.dateFinished > b.dateNeeded THEN 'Overdue'
                                WHEN b.dateFinished <= b.dateNeeded THEN 'On_Time'
                            END AS status,
                            DATEDIFF(b.dateFinished, b.dateNeeded) AS daysDifference
                        FROM donejotable a
                            JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID
                            JOIN userdetailstable c ON c.userID = b.userID
                        WHERE 
                            MONTH(b.dateRequested) = '$selectedMonth'
                            AND YEAR(b.dateRequested) = '$selectedYear'
                            AND c.userType != 1
                    ) AS subquery
                    GROUP BY status";

        $result3 = mysqli_query($result[0], $query3);

        $query4 = "SELECT 
            (SELECT COUNT(*) FROM joratedtable a JOIN joborderdetailstable b ON a.joborderID = b.jobOrderID WHERE MONTH(b.dateRequested) = '$selectedMonth' AND YEAR(b.dateRequested) = '$selectedYear') AS ratedjo,
            (SELECT ROUND(AVG(timeliness), 2) FROM joratedtable a JOIN joborderdetailstable b ON a.joborderID = b.jobOrderID WHERE MONTH(b.dateRequested) = '$selectedMonth' AND YEAR(b.dateRequested) = '$selectedYear') AS avgTimeliness,
            (SELECT ROUND(AVG(accuracy), 2) FROM joratedtable a JOIN joborderdetailstable b ON a.joborderID = b.jobOrderID WHERE MONTH(b.dateRequested) = '$selectedMonth' AND YEAR(b.dateRequested) = '$selectedYear') AS avgAccuracy
        ";

        $result4 = mysqli_query($result[0], $query4);

        $query5 = "SELECT COUNT(*) AS donejo FROM joborderdetailstable a
        JOIN userdetailstable b ON a.userID = b.userID
        WHERE MONTH(a.dateRequested) = '$selectedMonth' 
        AND YEAR(a.dateRequested) = '$selectedYear'
        AND a.statusTypeID = 2
        AND b.userType != 1";

        $result5 = mysqli_query($result[0], $query5);
        
        if ($result1 && $result2 && $result3 && $result4 && $result5) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $day = $row['dayOfMonth'];
                $leadTime = round($row['jobOrderCount']);

                if (in_array($day, $daysOfTheMonth)) {
                    $leadTimeInEveryDays[$day - 1] = intval($leadTime);
                }
            }

            $jsonOutput["month_days"] = $daysOfTheMonth;
            $jsonOutput["lead_time"] = $leadTimeInEveryDays;

            $row = mysqli_fetch_assoc($result2);
            $jsonOutput["average_lead_time"] = $row["averageLeadTimeFormatted"];

            $statusCount = array();
            $numRows = mysqli_num_rows($result3);

            if($numRows == 0) {
                $statusCount["On_Time"] = array(
                    "count" => 0,
                    "averageDaysDifference" => "No Record"
                );
                $statusCount["Overdue"] = array(
                    "count" => 0,
                    "averageDaysDifference" => "No Record"
                );
            } elseif ($numRows == 1) {
                $row = mysqli_fetch_assoc($result3);
                $status = $row['status'];
                $count = $row['count'];
                $averageDaysDifference = $row['averageDaysDifference'];
                
                if($status == "On_Time") {
                    $statusCount[$status] = array(
                        "count" => $count,
                        "averageDaysDifference" => $averageDaysDifference
                    );
                    $statusCount["Overdue"] = array(
                        "count" => 0,
                        "averageDaysDifference" => "No Record"
                    );
                } elseif ($status == "Overdue") {
                    $statusCount["On_Time"] = array(
                        "count" => 0,
                        "averageDaysDifference" => "No Record"
                    );
                    $statusCount[$status] = array(
                        "count" => $count,
                        "averageDaysDifference" => $averageDaysDifference
                    );
                }
            } elseif ($numRows == 2) {
                while ($row = mysqli_fetch_assoc($result3)) {
                    $status = $row['status'];
                    $count = $row['count'];
                    $averageDaysDifference = $row['averageDaysDifference'];
                    $statusCount[$status] = array(
                        "count" => $count,
                        "averageDaysDifference" => $averageDaysDifference
                    );
                }
            }


            $jsonOutput["status_count"] = $statusCount;

            $row = mysqli_fetch_assoc($result4);
            $jsonOutput["ratedjo"] = $row["ratedjo"];
            $jsonOutput["avg_timeliness"] = $row["avgTimeliness"];
            $jsonOutput["avg_accuracy"] = $row["avgAccuracy"];

            $row = mysqli_fetch_assoc($result5);
            $jsonOutput["donejo"] = $row["donejo"];
        }
    }

    echo json_encode($jsonOutput);
?>