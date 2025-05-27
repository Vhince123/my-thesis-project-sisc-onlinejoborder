<?php

     include "database_manipulator.php";
     include "upload_file.php";

     $dbConn = DatabaseManipulator::get_db_connection();

     $result = array();

     $userID = $dbConn->real_escape_string($_POST["userID"]);
     $jobOrderID = $dbConn->real_escape_string($_POST["jobOrderID"]);
     $workersID = $dbConn->real_escape_string($_POST["workersID"]);
     $comments = $dbConn->real_escape_string($_POST["comments"]);
     $doneImage = $_FILES["doneJOFiles"];

     $allowedExt = array("jpeg", "jpg", "png", "gif", "bmp", "webp");

     $imageFileType = strtolower(pathinfo(basename($doneImage["name"]), PATHINFO_EXTENSION));
     $fileNewName = uniqid("", true) . "." . $imageFileType;

     $targetDestination = "../JOImages/DoneJOImages/" . $fileNewName;
     $uploadedPath = "JOImages/DoneJOImages/" . $fileNewName;

     $uploadResult = UploadFiles::upload_files($doneImage, $targetDestination, $allowedExt);

     if ($uploadResult["upload_status"])
     {
          $resultObj = DatabaseManipulator::update_done_jo($workersID, $jobOrderID, $uploadedPath, $comments);
     }
     else 
     {
          $resultObj["status"] = false;
          $resultObj["message"] = $uploadResult["upload_message"];
     }

     echo json_encode($resultObj);

?>