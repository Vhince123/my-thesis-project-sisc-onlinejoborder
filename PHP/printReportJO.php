<?php
    include ("connect.php");
    $result = Connection();
    require("fpdf186/fpdf.php");

    $month = $_GET["month"];
    $year = $_GET["year"];
    $monthName = date("F", mktime(0, 0, 0, $month, 1));

    $returnArray = array();


    if (isset($result[0])) {
        $conn = $result[0];

        $query1 = "SELECT CONCAT(
            FLOOR(AVG(TIMESTAMPDIFF(MINUTE, dateServed, dateFinished)) / (24 * 60)), ' days, ',
            MOD(FLOOR(AVG(TIMESTAMPDIFF(MINUTE, dateServed, dateFinished)) / 60), 24), ' hours, ',
            FLOOR(MOD(AVG(TIMESTAMPDIFF(MINUTE, dateServed, dateFinished)), 60)), ' minutes'
        ) AS averageLeadTimeFormatted
        FROM 
            joborderdetailstable
        WHERE 
            MONTH(dateFinished) = '$month' 
            AND YEAR(dateFinished) = '$year'
            AND dateServed != 0
            AND dateServed != dateFinished";

        $query2 = "SELECT COUNT(*) ongoing_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND YEAR(dateRequested) = '$year' AND statusTypeID = 1";
        $query3 = "SELECT COUNT(*) waiting_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND YEAR(dateRequested) = '$year' AND statusTypeID = 4";
        $query4 = "SELECT COUNT(*) outsource_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND YEAR(dateRequested) = '$year' AND statusTypeID = 5";
        $query5 = "SELECT COUNT(*) done_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND YEAR(dateRequested) = '$year' AND statusTypeID = 2";
        $query6 = "SELECT COUNT(*) rejected_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND YEAR(dateRequested) = '$year' AND statusTypeID = 3";
        $query8 = "SELECT COUNT(*) incoming_JO FROM joborderdetailstable WHERE MONTH(dateRequested) = '$month' AND YEAR(dateRequested) = '$year' AND statusTypeID = 6";

        $query9 = "SELECT AVG(a.timeliness) AS average_timeliness,
                    AVG(a.accuracy) AS average_accuracy 
                    FROM joratedtable a
                    JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID
                    WHERE MONTH(b.dateServed) = '$month'
                    AND YEAR(b.dateServed) = '$year'";
        
        $result1 = $conn->query($query1);
        $result2 = $conn->query($query2);
        $result3 = $conn->query($query3);
        $result4 = $conn->query($query4);
        $result5 = $conn->query($query5);
        $result6 = $conn->query($query6);
        $result8 = $conn->query($query8);
        $result9 = $conn->query($query9);


        if ($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result8 && $result9) {

            $ongoing_JO = $result2->fetch_assoc()["ongoing_JO"];
            $waiting_JO = $result3->fetch_assoc()["waiting_JO"];
            $outsource_JO = $result4->fetch_assoc()["outsource_JO"];
            $done_JO = $result5->fetch_assoc()["done_JO"];
            $rejected_JO = $result6->fetch_assoc()["rejected_JO"];
            $incoming_JO = $result8->fetch_assoc()["incoming_JO"];

            $average_lead_time = $result1->fetch_assoc()["averageLeadTimeFormatted"];
            // $timeliness = $result9->fetch_assoc()["average_timeliness"];
            // $accuracy = $result9->fetch_assoc()["average_accuracy"];

            // Calculating the sum
            $sum_of_counts =  $ongoing_JO + $waiting_JO + $outsource_JO + $done_JO + $rejected_JO;
            
            $overalltotal = $sum_of_counts + $incoming_JO;

            // Assigning individual counts to return array
            $returnArray["ongoing_JO"] = $ongoing_JO;
            $returnArray["waiting_JO"] = $waiting_JO;
            $returnArray["outsource_JO"] = $outsource_JO;
            $returnArray["done_JO"] = $done_JO;
            $returnArray["rejected_JO"] = $rejected_JO;
            $returnArray["incoming_JO"] = $incoming_JO;
            // Storing the sum in the return array
            $returnArray["total_JO"] = $sum_of_counts;
            $returnArray["overalltotal_JO"] = $overalltotal;
            $returnArray["averageLeadTimeFormatted"] = $average_lead_time;

            // $returnArray["average_timeliness"] = $timeliness;
            // $returnArray["average_accuracy"] = $accuracy;



            //PDF Print ================================================================================

            $excludeCan = number_format($sum_of_counts - $returnArray["rejected_JO"]);

            $pdf = new FPDF('P', 'mm', 'A4');

            $pdf->AddPage();
            $pageWidth = $pdf->GetPageWidth() - 20;
            $contentWidth = 72;
            $cellWidth = 23;
        
            $pdf->SetFont('Arial','B',15);
        
            $x = ($pdf->GetPageWidth() - $pdf->GetStringWidth('Southville International School and Colleges')) / 2;
        
            $pdf->Image('../Images/sisc.png', 75, 12, 10, 11);
            $pdf->Cell(0, 15, 'Southville International School and Colleges', 0, 1, 'R');

            $pdf->Cell(0, 10, 'JOB ORDER MONTHLY REPORT', 0, 1, 'R');

            $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 0, ''.$monthName.'', 0, 1, 'R');

            $pdf->Cell(0, 20, '', 0, 1,);

            $pdf->SetFont('Arial','B',11); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Category', 0, 0, 'L');
            $pdf->Cell(20, 5, 'Total Number', 0, 0 ,'C');
            $pdf->Cell(0, 5, 'Percentage', 0, 1,'C');

            $pdf->Cell(0, 0, '', 0, 1,'C',1);

            $pdf->Cell(0, 3, '', 0, 1);

            //Ongoing JO

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Ongoing', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$returnArray["ongoing_JO"].'', 0, 0 ,'C');

            if($sum_of_counts != 0) {
                $ongoingJO = ($returnArray["ongoing_JO"] / $sum_of_counts ) * 100;
                $waitingJO = ($returnArray["waiting_JO"] / $sum_of_counts ) * 100;
                $outsource = ($returnArray["outsource_JO"] / $sum_of_counts ) * 100;
                $donejo = ($returnArray["done_JO"] / $sum_of_counts ) * 100;
                $rejected = ($returnArray["rejected_JO"] / $sum_of_counts ) * 100;
                $totaljo = ($excludeCan / $sum_of_counts ) * 100;
                $total = ($sum_of_counts / $sum_of_counts ) * 100;
            } 
            else
            {
                $ongoingJO = 0;
                $waitingJO = 0;
                $outsource = 0;
                $donejo = 0;
                $rejected = 0;
                $totaljo = 0;
                $total = 0;
            }

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.number_format($ongoingJO).'%', 0, 1,'C');

            //Waiting for Materials JO

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Waiting for Materials', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$returnArray["waiting_JO"].'', 0, 0,'C');

            

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.number_format($waitingJO).'%', 0, 1,'C');

            //Outsource JO

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Outsource', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$returnArray["outsource_JO"].'', 0, 0,'C');

            

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.number_format($outsource).'%', 0, 1,'C');

            //Done JO

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Done', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$returnArray["done_JO"].'', 0, 0,'C');

            

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.number_format($donejo).'%', 0, 1,'C');

            $pdf->Cell(0, 0, '', 0, 1,'C',1);

            $pdf->Cell(0, 3, '', 0, 1);

            //Total ex in and can  JO

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Total Job Orders (excluded incoming and cancelled)', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$excludeCan.'', 0, 0,'C');

            

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.number_format($totaljo).'%', 0, 1,'C');

            $pdf->Cell(0, 0, '', 0, 1,'C',1);

            $pdf->Cell(0, 3, '', 0, 1);

            //Rejected JO

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Rejected / Cancelled', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$returnArray["rejected_JO"].'', 0, 0,'C');

            

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.number_format($rejected).'%', 0, 1,'C');

            //Total JO including cancelled

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Total Job Orders (including cancelled)', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$sum_of_counts.'', 0, 0,'C');

            

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.number_format($total).'%', 0, 1,'C');

            $pdf->Cell(0, 0, '', 0, 1,'C',1);

            $pdf->Cell(0, 1, '', 0, 1);

            $pdf->SetFont('Arial','I',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Incoming Job Orders', 0, 0, 'L');

            $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$returnArray["incoming_JO"].'', 0, 0,'C');

            $pdf->Cell(0, 8, '', 0, 1);

            //Total Received JO

            $pdf->SetFont('Arial','B',13); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Total Job Orders Received', 0, 0, 'L');

            $pdf->SetFont('Arial','B',13); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20, 5, ''.$overalltotal.'', 0, 0,'C');

            $pdf->Cell(0, 20, '', 0, 1);

            $pdf->SetFont('Arial','B',13); $pdf->SetTextColor(0,0,0);
            $pdf->Cell(100, 5, 'Average Lead Time For the Month:', 0, 0, 'L');

            $pdf->Cell(0, 10, '', 0, 1);

            $pdf->SetFont('Arial','B',13); $pdf->SetTextColor(0,0,255);
            $pdf->Cell(0, 5, ''.$average_lead_time.'', 0, 0,'C');


            $pdf->Output();

        }
        else {
            $returnArray["Error 1"] = "Query Error";
        }
    }


?>