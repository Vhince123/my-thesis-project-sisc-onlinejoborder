<?php
        include "connect.php";
        include "smtpEmailUpdate.php";
        session_start();
        $result = Connection();

        $forFullName = $_SESSION["firstname"] . " " . $_SESSION["lastname"];

        $jsonReturn = array();

        $updatedStatus = $_POST["updatestatus"];
        $joid = $_POST["approvejoid"];
        $today = date("Y-m-d H:i:s");
        $userID = $_SESSION["userid"];
        $comments = $_POST["comments"];

        if(isset($result[0])){
            if(isset($_POST["approve-jo"])){

                $currentDate = date("my");

                if ($updatedStatus == "1") {
                    $staff = $_POST["available-staff"];
                } else {
                    $staff = $userID;
                }

                $query2 = "SELECT COUNT(jobOrderNumber) AS jonum_count FROM joborderdetailstable WHERE jobOrderNumber LIKE '%$currentDate%'";
                $query2Result = $result[0]->query($query2);

                if ($row = $query2Result->fetch_assoc()) {
                    $joCount = $row["jonum_count"];
                    $joCount++;
                    $formattedJOCount = sprintf("%04d", $joCount);
                    $joNumber = date("my") . '-' . $formattedJOCount;
                }

                $query = "UPDATE joborderdetailstable SET jobOrderNumber = '$joNumber',dateServed = '$today', statusTypeID = '$updatedStatus', ";
                if ($updatedStatus == '1') {
                    $query .= "accountable = (SELECT CONCAT(firstName, ' ', lastName) FROM userdetailstable WHERE userid = '$staff')";
                } else {
                    $query .= "accountable = 'not applicable'";
                }
                $query .= " WHERE jobOrderID = '$joid'";
                $sqlResult = mysqli_query($result[0], $query);

                if (!$sqlResult) {
                    $jsonReturn["Error1"] = mysqli_error($result[0]);
                } else {
                    $query3 = "INSERT INTO approvedjotable (dateApproved, userID, jobOrderID) VALUES ('$today', '$userID', '$joid')";

                    $activity = array('Job Order is now ongoing...', 'Job Order is already done...', 'Job Order is still waiting for materials...', 'Job Order needs to be outsourced...');

                    if ($updatedStatus == '1') {
                        $query4 = "UPDATE jotrackingtable SET activity = '$activity[0]', userID = '$staff', comments = '$comments' WHERE jobOrderID = '$joid'";
                    } else if ($updatedStatus == '2') {
                        $query4 = "UPDATE jotrackingtable SET activity = '$activity[1]', userID = '$staff', comments = NULL WHERE jobOrderID = '$joid'";
                    } else if ($updatedStatus == '4') {
                        $query4 = "UPDATE jotrackingtable SET activity = '$activity[2]', userID = '$staff', comments = '$comments' WHERE jobOrderID = '$joid'";
                    } else if ($updatedStatus == '5') {
                        $query4 = "UPDATE jotrackingtable SET activity = '$activity[3]', userID = '$staff', comments = '$comments' WHERE jobOrderID = '$joid'";
                    }

                    $sqlResult3 = mysqli_query($result[0], $query3);
                    $sqlResult4 = mysqli_query($result[0], $query4);

                    if (!$sqlResult3 || !$sqlResult4) {
                        $jsonReturn["Error2"] = mysqli_error($result[0]);
                    } else {

                        if ($updatedStatus == '2') {
                            $allowedExtensions = array('jpg', 'jpeg', 'png');
                            $uploadedFiles = array();
                            $maxFileSize = 100000000;
                            $error = "";

                            for ($i = 1; $i <= 2; $i++) {
                                $file = $_FILES["doneSec$i"]["name"];
                                $tmpName = $_FILES["doneSec$i"]["tmp_name"];
                                $fileSize = $_FILES["doneSec$i"]["size"];
                                $fileError = $_FILES["doneSec$i"]["error"];

                                $imageExtension = explode('.', $file);
                                $imageActualExt = strtolower(end($imageExtension));

                                if (in_array($imageActualExt, $allowedExtensions)) {

                                    if ($fileError === 0 || $fileError === 4) {
                                        if ($fileSize < $maxFileSize) {
                                            $fileNewName = uniqid('', true) . "." . $imageActualExt;
                                            $fileDestination = '../JOImages/DoneJOImages/' . $fileNewName;
                                            $fullFilePath = "JOImages/DoneJOImages/" . $fileNewName;

                                            if (move_uploaded_file($tmpName, $fileDestination)) {
                                                $uploadedFiles[] = $fullFilePath;
                                            } else {
                                                $error = "Failed to move uploaded file.";
                                            }
                                        } else {
                                            $error = "File size exceeds maximum limit.";
                                        }
                                    } else {
                                        $error = "File upload error.";
                                    }
                                } else {
                                    $error = "Invalid file extension.";
                                }
                            }

                            if (empty($errors)) {
                                $donePhoto = implode(',', $uploadedFiles);

                                $query5 = "INSERT INTO donejotable (userID, jobOrderID, donePhoto) 
                                VALUES ('$staff', '$joid', '$donePhoto')";
                                $sqlResult5 = mysqli_query($result[0], $query5);

                                $query6 = "UPDATE joborderdetailstable SET dateFinished = '$today' 
                                WHERE jobOrderID = '$joid'";
                                $sqlResult6 = mysqli_query($result[0], $query6);

                                if (!$sqlResult5 || !$sqlResult6) {
                                    $jsonReturn["Error3"] = mysqli_error($result[0]);
                                } else {
                                    $jsonReturn["Success"] = "Success";
                                    $emailQuery = "SELECT b.email, a.jobOrderNumber FROM joborderdetailstable a
                                        JOIN userdetailstable b ON a.userID = b.userID  WHERE a.jobOrderID = '$joid'";
                                    $emailResult = mysqli_query($result[0], $emailQuery);
                                    if($emailRow = mysqli_fetch_assoc($emailResult)){
                                        $email = $emailRow['email'];
                                        $joNum = $emailRow['jobOrderNumber'];
                                        $body = 'We like to inform you that your job order: <b>'. $joNum .'</b> has been marked as <b>DONE</b>!<br><br>' . PHP_EOL . PHP_EOL .

                                        'Best Regards, <br><br>' . PHP_EOL .
                                        ''. $forFullName .'<br>' . PHP_EOL .
                                        'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                                        'Administrative Office';
                                        $emailMessage = SendEmail::sendEmail($email, $body);

                                        echo '<script>alert("Job Order has been marked as done!");</script>';
                                        echo '<script>window.location.href = "../joIncoming.php?approved";</script>';
                                        exit();
                                    }
                                    
                                }
                            } else {
                                echo '<script>alert("' . $error . '");</script>';
                            }
                        } else {
                            $jsonReturn["Success"] = "Success";
                            $emailQuery = "SELECT b.email, a.jobOrderNumber FROM joborderdetailstable a
                                        JOIN userdetailstable b ON a.userID = b.userID  WHERE a.jobOrderID = '$joid'";
                            $emailResult = mysqli_query($result[0], $emailQuery);
                            if($emailRow = mysqli_fetch_assoc($emailResult)){
                                $email = $emailRow['email'];
                                $joNum = $emailRow['jobOrderNumber'];
                                $body = 'We like to inform you that your job order: <b>'. $joNum .'</b> has been <b>APPROVED</b>!<br><br>' . PHP_EOL . PHP_EOL .

                                'Best Regards, <br><br>' . PHP_EOL .
                                ''. $forFullName .'<br>' . PHP_EOL .
                                'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                                'Administrative Office';
                                $emailMessage = SendEmail::sendEmail($email, $body);

                                echo '<script>alert("Job Order has been approved!");</script>';
                                echo '<script>window.location.href = "../joIncoming.php?approved";</script>';
                                exit();
                            }
                        }
                    }
                }
            }
            else if(isset($_POST["cstat-jo"])){
                
                $query = "UPDATE joborderdetailstable SET statusTypeID = '$updatedStatus', accountable = 'not applicable' WHERE jobOrderID = '$joid'";
                $sqlResult = mysqli_query($result[0], $query);
                
                if(!$sqlResult) {
                    $jsonReturn["Error1"] = mysqli_error($result[0]);
                }
                else {
                    
                    $activity = array('Job Order is needs to wait for materials...', 'Job Order needs to be outsourced...');
                    $$joStatus = "";
                    if($updatedStatus == '4'){
                        $query2 = "UPDATE jotrackingtable SET activity = '$activity[0]', userID = NULL, comments = '$comments' WHERE jobOrderID = '$joid'";
                        $joStatus = "WAITING FOR MATERIALS";
                    }
                    else if($updatedStatus == '5'){
                        $query2 = "UPDATE jotrackingtable SET activity = '$activity[1]', userID = NULL, comments = '$comments' WHERE jobOrderID = '$joid'";
                        $joStatus = "OUTSOURCED";
                    }

                    $sqlResult2 = mysqli_query($result[0], $query2);
                    
                    if (!$sqlResult2) {
                        $jsonReturn["Error2"] = mysqli_error($result[0]);
                    }
                    else {
                        $jsonReturn["Success"] = "Success";
                        $emailQuery = "SELECT b.email, a.jobOrderNumber FROM joborderdetailstable a
                                        JOIN userdetailstable b ON a.userID = b.userID  WHERE a.jobOrderID = '$joid'";
                        $emailResult = mysqli_query($result[0], $emailQuery);
                        if($emailRow = mysqli_fetch_assoc($emailResult)){
                            $email = $emailRow['email'];
                            $joNum = $emailRow['jobOrderNumber'];
                            $body = 'We like to inform you that your job order: <b>'. $joNum .'</b> is currently <b>'.$joStatus.'!</b><br><br>' . PHP_EOL . PHP_EOL .

                            'Best Regards, <br><br>' . PHP_EOL .
                            ''. $forFullName .'<br>' . PHP_EOL .
                            'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                            'Administrative Office';
                            $emailMessage = SendEmail::sendEmail($email, $body);

                            echo '<script>alert("Job Order status has been updated!");</script>';
                            echo '<script>window.location.href = "../joApproved.php?concludesuccess";</script>';
                            exit();
                        }
                    }
                }
            }
            else if(isset($_POST["endorse-jo"])){
                $query = "UPDATE joborderdetailstable SET statusTypeID = 6 WHERE jobOrderID = '$joid'";
                $sqlResult = mysqli_query($result[0], $query);
                
                if(!$sqlResult) {
                    $jsonReturn["Error1"] = mysqli_error($result[0]);
                }
                else {
                    $query1 = "UPDATE jotrackingtable SET activity = 'Sending for Approval' WHERE jobOrderID = '$joid'";
                    $sqlResultt= mysqli_query($result[0], $query1);

                    $jsonReturn["Success"] = "Success";
                    $emailQuery = "SELECT b.email, a.jobOrderNumber FROM joborderdetailstable a
                                    JOIN userdetailstable b ON a.userID = b.userID  WHERE a.jobOrderID = '$joid'";
                    $emailResult = mysqli_query($result[0], $emailQuery);
                    if($emailRow = mysqli_fetch_assoc($emailResult)){
                        $email = $emailRow['email'];
                        $joNum = $emailRow['jobOrderNumber'];
                        $body = 'We like to inform you that your job order: <b>'. $joNum .'</b> has been <b>ENDORSED</b> by your department head.<br><br>' . PHP_EOL . PHP_EOL .

                        'Best Regards, <br><br>' . PHP_EOL .
                        'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                        'Administrative Office';
                        $emailMessage = SendEmail::sendEmail($email, $body);

                        echo '<script>alert("The Job Order has been endorsed!");</script>';
                        echo '<script>window.location.href = "http://localhost/Thesis/dpjoEndorse.php";</script>';
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