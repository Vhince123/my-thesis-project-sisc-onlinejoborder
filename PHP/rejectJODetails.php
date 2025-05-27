<?php
        include "connect.php";
        include "smtpEmailUpdate.php";
        session_start();
        $result = Connection();

        $forFullName = $_SESSION["firstname"] . " " . $_SESSION["lastname"];

        $jsonReturn = array();

        $joid = $_POST["rejectjoid"];
        $jouserid = $_SESSION["userid"];
        $daterejected = date("Y-m-d H:i:s");
        $comments = $_POST["reasontbx"];
        
        if(isset($result[0])){
            if(isset($_POST["reject-jo"])){
                $query = "UPDATE joborderdetailstable SET statusTypeID = 3 WHERE jobOrderID = '$joid'";

                $sqlResult = mysqli_query($result[0], $query);
                if(!$sqlResult) {
                    $jsonReturn["Error1"] = mysqli_error($result[0]);
                }
                else{                    

                    $query1 = "INSERT INTO rejectjotable (dateFiled, userID, jobOrderID, comments) 
                    VALUES ('$daterejected', '$jouserid', '$joid', '$comments')";
                    $sqlResult1 = mysqli_query($result[0], $query1);

                    if(!$sqlResult1) {
                        $jsonReturn["Error2"] = mysqli_error($result[0]);
                    }
                    else{ 
                        $jsonReturn["Success"] = "Success";
                        $emailQuery = "SELECT b.email, a.jobOrderDescription, c.comments FROM joborderdetailstable a
                                    JOIN userdetailstable b ON a.userID = b.userID
                                    JOIN rejectjotable c ON a.jobOrderID = c.jobOrderID
                                    WHERE a.jobOrderID = '$joid'";
                        $emailResult = mysqli_query($result[0], $emailQuery);
                        if($emailRow = mysqli_fetch_assoc($emailResult)){
                            $email = $emailRow['email'];
                            $joNum = $emailRow['jobOrderDescription'];
                            $cancelcomment = $emailRow['comments'];
                            $body = 'We are sorry to inform you that your job order with the description of : "<b>'. $joNum .'</b>" has been <b>REJECTED</b>!<br><br>' . PHP_EOL . PHP_EOL .

                            'Here is the reason why your job order is rejected:<br><br>' . PHP_EOL . PHP_EOL .

                            '<b>"'. $cancelcomment .'"</b><br><br>' . PHP_EOL . PHP_EOL .

                            'Best Regards, <br><br>' . PHP_EOL .
                            ''. $forFullName .'<br>' . PHP_EOL .
                            'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                            'Administrative Office';
                            $emailMessage = SendEmail::sendEmail($email, $body);

                            echo '<script>alert("Job Order has been rejected!");</script>';
                            echo '<script>window.location.href = "../joIncoming.php?rejectsucess";</script>';
                            exit();
                        }
                    }
                }
            }
            else if(isset($_POST["reject-approvedjo"])) {
                $query = "UPDATE joborderdetailstable SET statusTypeID = 3 WHERE jobOrderID = '$joid'";

                $sqlResult = mysqli_query($result[0], $query);
                if(!$sqlResult) {
                    $jsonReturn["Error1"] = mysqli_error($result[0]);
                }
                else{          
                    $query1 = "INSERT INTO rejectjotable (dateFiled, userID, jobOrderID, comments) 
                    VALUES ('$daterejected', '$jouserid', '$joid', '$comments')";
                    $sqlResult1 = mysqli_query($result[0], $query1);

                    $query2 = "UPDATE jotrackingtable SET activity = 'Job Order has been cancelled...', userID = null WHERE jobOrderID = '$joid'";
                    $sqlResult2 = mysqli_query($result[0], $query2);

                    if(!$sqlResult1 || !$sqlResult2) {
                        $jsonReturn["Error2"] = mysqli_error($result[0]);
                    }
                    else{ 
                        $jsonReturn["Success"] = "Success";
                        $emailQuery = "SELECT b.email, a.jobOrderNumber, c.comments FROM joborderdetailstable a
                                    JOIN userdetailstable b ON a.userID = b.userID
                                    JOIN rejectjotable c ON a.jobOrderID = c.jobOrderID
                                    WHERE a.jobOrderID = '$joid'";
                        $emailResult = mysqli_query($result[0], $emailQuery);
                        if($emailRow = mysqli_fetch_assoc($emailResult)){
                            $email = $emailRow['email'];
                            $joNum = $emailRow['jobOrderNumber'];
                            $cancelcomment = $emailRow['comments'];
                            $body = 'We are sorry to inform you that your job order: <b>'. $joNum .'</b> has been <b>CANCELLED</b>!<br><br>' . PHP_EOL . PHP_EOL .

                            'Here is the reason why your job order is cancelled:<br><br>' . PHP_EOL . PHP_EOL .

                            '<b>"'. $cancelcomment .'"</b><br><br>' . PHP_EOL . PHP_EOL .

                            'Best Regards, <br><br>' . PHP_EOL .
                            ''. $forFullName .'<br>' . PHP_EOL .
                            'SISC-Luxembourg Administrative Personnel<br>' . PHP_EOL .
                            'Administrative Office';
                            $emailMessage = SendEmail::sendEmail($email, $body);

                            echo '<script>alert("Job Order has been cancelled!");</script>';
                            echo '<script>window.location.href = "../joApproved.php?cancelsuccess";</script>';
                            exit();
                        }
                        
                    }
                }
            }
        }
        else {
            $jsonReturn["Error0"] = $result[-1];
        }
        echo json_encode($jsonReturn);
?>