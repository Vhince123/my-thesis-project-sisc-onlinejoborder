<?php

     include "database_manipulator.php";
     include "upload_file.php";

     $dbConn = DatabaseManipulator::get_db_connection();

     $resultObj = array();

     $userId = $dbConn->real_escape_string($_POST["user-id"]);
     $joDescription = $dbConn->real_escape_string($_POST["jo-description"]);
     $joNeeded = $dbConn->real_escape_string($_POST["jo-needed"]);
     $urgent = $dbConn->real_escape_string($_POST["jo-urgent"]);
     $joLocation = $dbConn->real_escape_string($_POST["jo-location"]);
     $joImage = $_FILES["jo-photo"];

     $allowedExt = array("jpeg", "jpg", "png", "gif", "bmp", "webp");

     $imageFileType = strtolower(pathinfo(basename($joImage["name"]), PATHINFO_EXTENSION));
     $fileNewName = uniqid("", true) . "." . $imageFileType;

     $targetDestination = "../JOImages/OngoingJOImages/" . $fileNewName;
     $uploadedPath = "JOImages/OngoingJOImages/" . $fileNewName;

     $uploadResult = UploadFiles::upload_files($joImage, $targetDestination, $allowedExt);

     if ($uploadResult["upload_status"])
     {
          $resultObj = DatabaseManipulator::request_new_jo($joDescription, $joLocation, $joNeeded, $uploadedPath, $urgent, $userId);
     }
     else 
     {
          $resultObj["status"] = false;
          $resultObj["message"] = $uploadResult["upload_message"];
     }

     echo json_encode($resultObj);

?>