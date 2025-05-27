<?php
    include("connect.php");
    $result = Connection();
    require("fpdf186/fpdf.php");

    $jobno = $_GET['jobno'];

    $query = "SELECT a.*, b.*, c.* FROM joborderdetailstable a 
    JOIN userdetailstable b ON a.userID = b.userID
    JOIN statustypetable c ON a.statusTypeID = c.statusTypeID
    WHERE a.jobOrderNumber = '$jobno'";

    if ($sqlresult = mysqli_query($result[0], $query)) {
        $jsonOutput = mysqli_fetch_assoc($sqlresult);

        $dates = array(
            $jsonOutput['dateRequested'],
            $jsonOutput['dateNeeded'],
            $jsonOutput['dateServed']
        );

        $newFormattedDates = array();

        foreach ($dates as $dateString) {
            $date = strtotime($dateString);
            $formattedDate = date("F j, Y", $date);
            array_push($newFormattedDates, $formattedDate);
        }

        $pdf = new FPDF('P', 'mm', 'A4');

        $pdf->AddPage();
        $pageWidth = $pdf->GetPageWidth() - 20;
        $contentWidth = 72;
        $cellWidth = 23;
    
        $pdf->SetFont('Arial','B',15);
    
        $x = ($pdf->GetPageWidth() - $pdf->GetStringWidth('Southville International School and Colleges')) / 2;
    
        $pdf->Image('../Images/sisc.png', 42, 12, 10, 11);
        $pdf->Cell(200, 15, 'Southville International School and Colleges', 0, 1, 'C');
    
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0, 10, 'JOB ORDER FORM', 0, 1, 'C');
        
    
        $pdf->SetFont('Arial','I',8);
        $pdf->Cell($cellWidth, 8, 'Requested by:', 1, 0, 'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell($contentWidth, 8, " ".$jsonOutput['firstName'] . ' ' . $jsonOutput['lastName'], 1,0); 

        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth + 3, 8, 'Date of Request:', 1, 0, 'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell($contentWidth - 3, 8, " ".$newFormattedDates[0], 1,1);
        
        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth, 8, 'Department: ', 1, 0, 'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell($contentWidth, 8, ' ' .$jsonOutput['department'], 1,0);

        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth + 3, 8, 'Status:', 1, 0, 'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell($contentWidth - 3, 8,  ' '.$jsonOutput['statusName'], 1,1);

        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth, 8, 'Date Needed:', 1, 0, 'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell($contentWidth, 8,  ' '.$newFormattedDates[1], 1,0);

        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth + 3, 8, 'Date Served:', 1, 0, 'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell($contentWidth - 3, 8,  ' '.$newFormattedDates[2], 1,1);
        
        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth, 8, 'Location:', 1, 0,'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell(0, 8,  ' '.$jsonOutput['location'], 1,1);
    
        $pdf->Cell(0, 5, '', 0, 1, 'C');

        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell(0, 8, 'JOB ORDER DESCRIPTION', 1, 1, 'C');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(0, 42, '    '.$jsonOutput['jobOrderDescription'].'', 1, 1);


        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 5, '', 0, 1, 'C');
        
        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth, 8, 'Date Printed:', 1, 0, 'R');

        $pdf->SetFont('Arial','B'); $pdf->SetTextColor(255,0,0);
        $pdf->Cell($contentWidth, 8,  ' '.date("F j, Y"), 1,0);



        $pdf->SetFont('Arial','I'); $pdf->SetTextColor(0,0,0);
        $pdf->Cell($cellWidth+3, 8, 'Date Finished:', 1, 0, 'R');
        $pdf->Cell($contentWidth-3, 8,  ' ', 1,1);
        $pdf->Cell($cellWidth, 8, 'Endorsed By: ', 1, 0, 'R');
        $pdf->Cell($contentWidth, 8,  ' ', 1,0);
        $pdf->Cell($cellWidth+3, 8, 'Done By:', 1, 0, 'R');
        $pdf->Cell($contentWidth-3, 8,  ' ', 1,1);

        $photos = explode(',', $jsonOutput['photo']);
        $numPhotos = count($photos);

        $photos = explode(',', $jsonOutput['photo']);
$numPhotos = count($photos);

if (!empty($jsonOutput['photo'])) {
    foreach ($photos as $photo) {
        $pdf->AddPage();

        $image_url = 'http://localhost/Thesis/' . $photo;
        $headers = get_headers($image_url);

        if (strpos($headers[0], '200')) {
            $image_size = getimagesize($image_url);
            $image_width = $image_size[0];
            $image_height = $image_size[1];

            $scale_width = ($pageWidth) / $image_width;
            $scale_height = ($pdf->getPageHeight() - 20) / $image_height;
            $scale = min($scale_width, $scale_height);

            $new_width = $image_width * $scale;
            $new_height = $image_height * $scale;

            $pdf->Image($image_url, 10, 10, $new_width, $new_height);
        } else {
            $pdf->Cell(0, 10, 'Image not located: ' . $photo, 1, 1, 'C');
        }
    }
} else {
    $pdf->Cell(0, 20, '', 0, 1, 'C');
    $pdf->Cell(0, 10, 'NO IMAGE FOUND', 0, 1, 'C');
}

        $pdf->Output();

    }
    
?>


<?php

// if(!$jsonOutput['photo']){
        //     $pdf->Cell(0, 8,  'NO PHOTO ATTACHED', 0,1, 'C');
        // }
        // else{
        //     $pdf->AddPage();
        //     $pdf->Cell($pageWidth, $pageWidth,  ' ', 1,0);
        //     $pdf->Image('http://localhost/Thesis/JOImages/OngoingJOImages/660150f97396f5.13440535.jpg', $pageWidth, $pageWidth, 100, 0);
            
        // // }
        
        // $photoFilePath = $jsonOutput["photo"];
        // $photoFilePathArray = explode(',', $photoFilePath);

        // if (!$photoFilePathArray) {
        //     $pdf->Cell(0, 8, 'NO PHOTO ATTACHED', 0, 1, 'C');
        // } else {
            
        //     $numPhotos = count($photoFilePathArray);
        //     $numPages = ceil($numPhotos / 2); 
        
        //     for ($page = 1; $page <= $numPages; $page++) {
        //         $pdf->AddPage();
        
        //         $startIdx = ($page - 1) * 2;
        //         $endIdx = min($startIdx + 1, $numPhotos - 1);
        
        //         for ($idx = $startIdx; $idx <= $endIdx; $idx++) {
                    
        //             $x = ($idx % 2) == 0 ? 10 : 110; 
        //             $y = 30 + (floor($idx / 2) * 60); 
        
        //             $imageUrl = 'http://localhost/Thesis/' . $photoFilePathArray[$idx];

        //             // Download the image and save it locally
        //             $localImagePath = __DIR__ . '/downloaded_images/' . basename($photoFilePathArray[$idx]);
        //             file_put_contents($localImagePath, file_get_contents($imageUrl));

        //             // Add the image to the PDF
        //             if (file_exists($localImagePath)) {
        //                 $pdf->Image($localImagePath, $x, $y, 80, 0, 'JPEG');
        //             } else {
        //                 // If image download fails or not found, display placeholder
        //                 $pdf->SetXY($x, $y);
        //                 $pdf->SetFillColor(220, 220, 220); // Light gray background
        //                 $pdf->Rect($x, $y, 80, 0, 'F');
        //                 $pdf->SetTextColor(100, 100, 100); // Dark gray text color
        //                 $pdf->SetFont('Arial', '', 10);
        //                 $pdf->Cell(80, 10, 'Image not found', 0, 0, 'C');
        //             }
        //         }
        //     }
        // }
?>