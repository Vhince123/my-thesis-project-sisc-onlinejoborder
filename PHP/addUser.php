     <?php

          include "connect.php";
          session_start();

          use PHPMailer\PHPMailer\PHPMailer;
          use PHPMailer\PHPMailer\Exception;

          $result = Connection();

          $jsonReturn = array();
               
          $fName = $_POST["fName"];
          $mName = $_POST["mName"];
          $lName = $_POST["lName"];
          $dept = $_POST["dept"];
          $email = $_POST["email"];
          $pwd = $_POST["pwd"];
          $dateCreate = date("Y-m-d H:i:s");
          $uType = $_POST["userType"];
          $uArchieve = NULL;

          $empty = true;
          $userID = "";

          if ($uType == "1" || $uType == "2" || $uType == "3") {
               $query1 = "SELECT COUNT(userType) AS usertype_count FROM userdetailstable WHERE userType = '$uType'";
               $queryResult = $result[0]->query($query1);
               
               if ($queryResult && $row = $queryResult->fetch_assoc()) {
                    $rowcount = $row["usertype_count"] + 1;
                    
                    $prefix = ($uType == "1") ? "ADM" : (($uType == "2") ? "REQ" : "EMP");
                    $userID = $prefix . "-00" . $rowcount;
               } else {
                    $jsonReturn["error"] = ($queryResult) ? "No records found." : mysqli_error($result[0]);
               }

          } elseif ($uType == "4") {
               
               $checkQuery = "SELECT * FROM userdetailstable WHERE userType = '4' AND department = '$dept'";
               $checkQueryResult = mysqli_query($result[0], $checkQuery);
               //$count = ($checkQueryResult) ? mysqli_fetch_assoc($checkQueryResult)['count'] : 0;

               if ($checkQueryResult) {
                    if (mysqli_num_rows($checkQueryResult) > 0) {
                         $isDeptHead = "A department head for ".$dept." already existed!";
                         $empty = false;
                    }
                    else {
                         $query1 = "SELECT COUNT(userType) AS usertype_count FROM userdetailstable WHERE userType = '4'";
                         $queryResult = $result[0]->query($query1);
                    
                         if ($queryResult && $row = $queryResult->fetch_assoc()) {
                              $rowcount = $row["usertype_count"] + 1;
                              $userID = "DPH-00" . $rowcount;
                         } 
                         else {
                              $jsonReturn["error"] = ($queryResult) ? "No records found." : mysqli_error($result[0]);
                         }
                    }
               }
               else {
                    $jsonReturn["error"] = mysqli_error($result[0]);
               }
          }

          if($empty){
               if (isset($result[0])) {
                    $query = "INSERT INTO userdetailstable (userID, firstName, middleName, lastName, department, email, password, dateCreated, userType, userArchieve)
                    VALUES ('$userID', '$fName', '$mName', '$lName', '$dept', '$email', '$pwd', '$dateCreate', '$uType', '$uArchieve')";
          
                    if (!mysqli_query($result[0], $query)) {
                         $jsonReturn["Error1"] = mysqli_error($result[0]);
                         echo '<script>alert("'.$jsonReturn["Error1"].'");</script>';
                    }
                    else {
                         // ====================PHPMAILER=======================
                              try{
                                   if(isset($_POST["add-submit"])){
               
                                   require 'phpmailer/src/Exception.php';
                                   require 'phpmailer/src/PHPMailer.php';
                                   require 'phpmailer/src/SMTP.php';
               
                                   $mail = new PHPMailer(true);
               
                                   $mail->isSMTP();
                                   $mail->Host       = 'smtp.gmail.com';
                                   $mail->SMTPAuth   = true;
                                   $mail->Username   ='marcvincentvitto@gmail.com';
                                   $mail->Password   = 'elqjirtkbzmukmlr';
                                   $mail->SMTPSecure = 'tls';
                                   // $mail->SMTPDebug = 2;
                                   $mail->Port = 587;
               
                                   $mail->setFrom('marcvincentvitto@gmail.com');
               
                                   $mail->addAddress($_POST["email"]);
               
                                   $mail->isHTML(true);
               
                                   $mail->Subject = "Welcome to the Online Job Order Request System!";
                                   $mail->Body = '<b>We are excited to welcome you to the Online Job Order Request System.</b><br><br>' . PHP_EOL .
               
                                                  'Your account has been successfully created, initiating a smooth road towards efficiently managing job orders.<br><br>' . PHP_EOL .
               
                                                  'Here are some essential details to begin:<br><br>' . PHP_EOL .
               
                                                  '<b>Name: </b>'.$fName.' '.preg_match('/^?[A-Z]$/i', $mName).' '.$lName.'<br>' . PHP_EOL .
                                                  '<b>UserID: </b>'.$userID.'<br>' . PHP_EOL .
                                                  '<b>Temporary Password: </b>'.$pwd.'<br><br>'. PHP_EOL . PHP_EOL .
               
                                                  'Once logged in, we recommend updating your password for security purposes<br><br>' . PHP_EOL . PHP_EOL .
                                                  'If you encounter any issues or have questions, our support team is here to assist you. ' . PHP_EOL .
                                                  'You can reach them by replying or emailing this email address';
               
                                   $mail->send();
                                   
                                   }
                              } catch (Exception $e){
                                   echo '<script>alert("Email does not exist!");</script>';
                              }
                              $jsonReturn["Success"] = "Success";
                              echo '<script>alert("Account has been created!");</script>';
                              echo '<script>window.location.href = "../adminUserManage.php?uploadsuccess";</script>';
                              exit();
                         
                    }
               }
               else {
                    $jsonReturn["Error0"] = $result[-1];
               }
          }
          else {
               $isDeptHead = addslashes($isDeptHead);
               echo "<script>alert('" . htmlspecialchars($isDeptHead) . "');</script>";
               echo '<script>window.location.href = "../adminUserManage.php";</script>';
               exit();
          }

          echo json_encode($jsonReturn);


     ?>