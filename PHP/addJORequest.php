<?php

    include "connect.php";
    session_start();
    $result = Connection();

    $jsonReturn = array();
    
    $jono = NULL;
    $jodes = $_POST["jobOrderDescription"];
    $mainlocation = $_POST["location1"];

    if($mainlocation == "others"){
        $location = $_POST["otherLocation"] . "-" . $_POST["location"];
    }
    else{
        $location = $_POST["location1"] . "-" . $_POST["location"];
    }
    
    $accountable = NULL;
    $dateneed = $_POST["dateNeeded"];
    $dateserve = NULL;
    $datefinish = NULL;
    $photo = $_FILES["insertPhoto"];
    $userID = $_SESSION["userid"];
    $status = 6;

    if (isset($result[0])) {
        
        if(isset($_POST["add-submit"])){
            $totalFiles = count($_FILES['insertPhoto']['name']);
            $filesArray = array();

            $uploaded = true;

            $target_dir = "../JOImages/OngoingJOImages/";
            $fullFilePath = "JOImages/OngoingJOImages/";
            $message = "";

            for($i = 0; $i < $totalFiles; $i++){
                $fileName = $_FILES["insertPhoto"]["name"][$i];
                $tmpName  = $_FILES["insertPhoto"]["tmp_name"][$i];
                $fileSize = $_FILES["insertPhoto"]["size"][$i];
                $fileError = $_FILES["insertPhoto"]["error"][$i];

                $imageFileType = strtolower(pathinfo(basename($fileName), PATHINFO_EXTENSION));
                $fileNewName = uniqid("", true) . "." . $imageFileType;
                $target_file = $target_dir . $fileNewName;
                $full_file_des = $fullFilePath . $fileNewName;

                // if ($fileSize > 10000) {
                //     $message .= "Sorry, your file is too large";
                //     $uploaded = false;
                // }

                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $message = "Sorry, only JPG, JPEG, and PNG files are allowed";
                    $uploaded = false;
                }

                if ($uploaded) {
                    if (move_uploaded_file($tmpName, $target_file)) {
                        $message = "The file ". htmlspecialchars(basename($fileName)). " has been uploaded.";
                        $filesArray[$i] = $full_file_des;
                    }
                    else {
                        $message = "Sorry, there was an error uploading your file";
                    }
                }
                else {
                    $message = "Sorry, your file was not uploaded";
                }
            }

            $joPhotos =  implode(",", $filesArray);

            echo $message;
        }
        

        if ($uploaded) {
            $query = "INSERT INTO joborderdetailstable (jobOrderNumber, jobOrderDescription, location, 
            accountable, dateRequested, dateNeeded, dateServed, dateFinished, photo, userID, statusTypeID)
            VALUES ('$jono', '$jodes', '$location', '$accountable', NOW(), '$dateneed', '$dateserve', 
            '$datefinish', '$joPhotos', '$userID', '$status')";
            
            if (!mysqli_query($result[0], $query)) {
                $jsonReturn["Error1"] = "Check query";
            }
            else {
                $query1 = "INSERT INTO jotrackingtable (activity, jobOrderID) VALUES ('Sending for approval', 
                (SELECT jobOrderID FROM jobOrderDetailsTable ORDER BY dateRequested DESC LIMIT 1))";

                if (!mysqli_query($result[0], $query1)) {
                    $jsonReturn["Error2"] = "Check query";
                }
                else{
                    $jsonReturn["Success"] = "Success";
                    echo '<script>alert("You have sent a job order request!");</script>';
                    echo '<script>window.location.href = "../jobOrderForm.php?uploadsuccess";</script>';
                    exit();
                }
            }
        }
    }
    else {
        $jsonReturn["Error0"] = $result[-1];
    }

    echo json_encode($jsonReturn);
?>