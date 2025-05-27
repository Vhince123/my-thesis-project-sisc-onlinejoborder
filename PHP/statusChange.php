<?php
        include "connect.php";
        include "smtpEmailUpdate.php";
        session_start();
        $result = Connection();

        $forFullName = $_SESSION["firstname"] . " " . $_SESSION["lastname"];

        $jsonReturn = array();
        $approver = $_SESSION["userid"];
        $dateFinished =  date("Y-m-d H:i:s");

        if(isset($result[0])){
            if(isset($_POST["cs-jo"])){
                $joid = $_POST["joid-done"];

                $allowedExtensions = array('jpg', 'jpeg', 'png');
                $uploadedFiles = array();
                $error ='';

                for ($i = 1; $i <= 2; $i++) {
                    $file = $_FILES["img-jo$i"]["name"];
                    $tmpName = $_FILES["img-jo$i"]["tmp_name"];
                    $fileSize = $_FILES["img-jo$i"]["size"];
                    $fileError = $_FILES["img-jo$i"]["error"];

                    $imageExtension = explode('.', $file);
                    $imageActualExt = strtolower(end($imageExtension));

                    if (in_array($imageActualExt, $allowedExtensions)) {

                        if ($fileError === 0 || $fileError === 4) {
                                $fileNewName = uniqid('', true) . "." . $imageActualExt;
                                $fileDestination = '../JOImages/DoneJOImages/' . $fileNewName;
                                $fullFilePath = "JOImages/DoneJOImages/" . $fileNewName;

                                if (move_uploaded_file($tmpName, $fileDestination)) {
                                    $uploadedFiles[] = $fullFilePath;
                                }
                                else {
                                    $error = "Failed to move uploaded file.";
                                }
                        } else {
                            $error = "File upload error.";
                        }
                    } else {
                        $error = "Invalid file extension.";
                    }
                }

                
                if(empty($error)) {
                    
                    $donePhoto = implode(',', $uploadedFiles);
                    
                    $query = "INSERT INTO donejotable (userID, jobOrderID, donePhoto) 
                    VALUES ('$approver', '$joid', '$donePhoto')";
                    $sqlResult = mysqli_query($result[0], $query);

                    if(!$sqlResult) {
                        $jsonReturn["Error2"] = mysqli_error($result[0]);
                    } else {

                            $query1 = "SELECT * FROM userdetailstable  WHERE userID='$approver'";
                            $sqlResult1 = mysqli_query($result[0], $query1);
                            if($row = $sqlResult1->fetch_assoc()){
                                $aName = $row['firstName']." ".$row['lastName'];
                                $query2 = "UPDATE joborderdetailstable SET accountable = '$aName', statusTypeID = 2, dateFinished = '$dateFinished' WHERE jobOrderID = '$joid' ";
                                $sqlResult2 = mysqli_query($result[0], $query2);
                            }
    
                            $query3 = "UPDATE jotrackingtable SET activity = 'Job Order is already done...', userID = '$approver' WHERE jobOrderID = '$joid'";
                            $sqlResult3 = mysqli_query($result[0], $query3);
                            if(!$sqlResult2 || !$sqlResult3){
                                $jsonReturn["Error3"] = mysqli_error($result[0]);
                            }
                            else{
                                $jsonReturn["Success"] = "Success";
                                $emailQuery = "SELECT b.email, a.jobOrderNumber FROM joborderdetailstable a
                                    JOIN userdetailstable b ON a.userID = b.userID  WHERE a.jobOrderID = '$joid'";
                                $emailResult = mysqli_query($result[0], $emailQuery);
                                if($emailRow = mysqli_fetch_assoc($emailResult)){
                                    $email = $emailRow['email'];
                                    $joNum = $emailRow['jobOrderNumber'];
                                    $body = 'We like to inform you that your job order: '. $joNum .' is already <b>DONE</b><br><br>' . PHP_EOL . PHP_EOL .

                                    'Best Regards, <br><br>' . PHP_EOL .
                                    ''. $forFullName .'<br>' . PHP_EOL .
                                    'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                                    'Administrative Office';
                                    $emailMessage = SendEmail::sendEmail($email, $body);

                                    echo '<script>alert("Job Order has been mark as done!");</script>';
                                    echo '<script>window.location.href = "../joApproved.php?concludesuccess";</script>';
                                    exit();
                                }
                                
                            }
                        }
                    }
                }
                else{
                    echo '<script>alert("'.$error.'");</script>';
                }
        }
        else {
            $jsonReturn["Error0"] = $result[-1];
        }
        echo json_encode($jsonReturn);
?>