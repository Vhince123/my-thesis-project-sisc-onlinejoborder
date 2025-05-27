<?php

     class UploadFiles 
     {
          public static function upload_files($file, $targetDestinaton, $mimes)
          {
               $returnResult = array();
               $tempName = $file["tmp_name"];
               $status = true;
               $errorMessage = "";
               $fileExtension = strtolower(pathinfo($targetDestinaton, PATHINFO_EXTENSION));

               if (file_exists($targetDestinaton)) 
               {
                    $status = false;
                    $errorMessage .= "File Already uploaded. ";
               }
     
               if ($file["size"] > 5000000) 
               {
                    $status = false;
                    $errorMessage .= "The File exceeds the maximum size. ";
               }

               if (!in_array($fileExtension, $mimes)) 
               {
                    $status = false;
                    $errorMessage .= "Only ";
                    for ($x = 0; $x < count($mimes); $x++) {
                         $errorMessage .= $mimes[$x] . " ";
                    }
                    $errorMessage .= "are allowed. ";
               }

               if ($status) 
               {
                    if (move_uploaded_file($tempName, $targetDestinaton))
                    {
                         $errorMessage .= basename($file["name"]) . " has been uploaded.";
                    }
                    else 
                    {
                         $errorMessage .= "There was an error encountered while uploading the file.";
                    }
               }
               else 
               {
                    $errorMessage = "File did not upload. " . $errorMessage;
               }

               $returnResult["upload_status"] = $status;
               $returnResult["upload_message"] = $errorMessage;

               return $returnResult;
          }
     }

?>