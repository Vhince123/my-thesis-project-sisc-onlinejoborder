<?php

     class DatabaseManipulator
     {
          private static $instance;
          public $connection;

          public function __construct()
          {

          }

          //Database Connection
          public static function db_connection()
          {
               if (null === static::$instance)
               {
                    static::$instance = new static();

                    $hostname = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "dbjoborder";

                    static::$instance->connection = new mysqli($hostname, $username, $password, $database);
                    static::$instance->connection->set_charset("utf8");
               }

               return static::$instance;
          }

          //Connection Function
          public static function get_db_connection() 
          {
               $instance = static::db_connection();
               $dbConn = $instance->connection;

               return $dbConn;
          }

          //User Login
          public static function check_login($username, $password)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $return = array();

               if ($username !== "" || $password !== "")
               {
                    $query = "SELECT * FROM userdetailstable WHERE userID = ? AND password = ?";
                    $stmt = $dbConn->prepare($query);

                    $stmt->bind_param("ss", $username, $password);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0)
                    {
                         $row = $result->fetch_assoc();
                         $userID = $row["userID"];
                         $query2 = "INSERT INTO loginlogtable (loginTime, userID)
                                   VALUES (NOW(), '$userID')";

                         $dbConn->query($query2);

                         $query3 = "SELECT loginLogID FROM loginlogtable ORDER BY loginLogID DESC LIMIT 1";
                         $result3 = $dbConn->query($query3);

                         if ($result3)
                         {
                              $row3 = $result3->fetch_assoc();
                              $loginLogID = $row3["loginLogID"];

                              $row["loginLogID"] = $loginLogID;

                              $return["status"] = true;
                              $return["message"] = "Welcome " . $row["firstName"] . " " . $row["lastName"];
                              $return["user"] = $row;
                         }
                    }
                    else
                    {
                         $return["status"] = false;
                         $return["message"] = "User does not exist";
                    }
               }
               else 
               {
                    $return["status"] = false;
                    $return["message"] = "Please complete your login credentials";
               }
               

               return $return;
          }

          //User Logout
          public static function user_logout($userID, $userloginLogID)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $query = "UPDATE loginlogtable SET logoutTime = NOW() WHERE loginLogID = '$userloginLogID' AND userID = '$userID'";
               $result = $dbConn->query($query);

               if ($result)
               {
                    $returnObj["status"] = true;
                    $returnObj["message"] = "Successfully logout";
               }
               else 
               {
                    $returnObj["status"] = false;
                    $returnObj["message"] = "Error: Check query";
               }

               return $returnObj;
          }

          //Insert new job order
          public static function request_new_jo($jo_description, $jo_location, $jo_needed, $jo_photopath, $urgent, $userId)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $day = date("d");
               $month = date("m");

               $formatedDate = $day . $month;

               $query1 = "SELECT jobordernumber FROM joborderdetailstable ORDER BY jobordernumber DESC LIMIT 1";
               $result1 = $dbConn->query($query1);

               if ($result1) 
               {
                    // $latestId = $result1->fetch_assoc();
                    // $newId = $latestId["jobordernumber"]++;

                    // $formatedNewId = $formatedDate . "-" . str_pad($newId, 4, "0", STR_PAD_LEFT);

                    $query2 = "INSERT INTO joborderdetailstable (jobordernumber, joborderdescription, location, accountable, dateRequested, dateNeeded, photo, userID, statusTypeID)
                              VALUES ('', '$jo_description', '$jo_location', '', NOW(), '$jo_needed', '$jo_photopath', '$userId', '$urgent')";

                    $result2 = $dbConn->query($query2);

                    if($urgent == "7"){
                         $query3 = "INSERT INTO jotrackingtable (activity, jobOrderID, comments) VALUES ('Sending for endorsement', (SELECT jobOrderID FROM joborderdetailstable ORDER BY jobOrderID DESC LIMIT 1), NULL)";
                    }
                    else if ($urgent == "6"){
                         $query3 = "INSERT INTO jotrackingtable (activity, jobOrderID, comments) VALUES ('Sending for approval', (SELECT jobOrderID FROM joborderdetailstable ORDER BY jobOrderID DESC LIMIT 1), NULL)";
                    }

                    $result3 = $dbConn->query($query3);
                    
                    if ($result2 && $result3)
                    {
                         $returnObj["status"] = true;
                         $returnObj["message"] = "Successfully uploaded the JO request";
                    }
                    else 
                    {
                         $returnObj["status"] = false;
                         $returnObj["message"] = mysqli_error($dbConn);
                    }
               }
               else
               {
                    $returnObj["status"] = false;
                    $returnObj["message"] = "Error: Check query1";
               }

               return $returnObj;
          }

          //Get all JO status
          public static function get_status_type()
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $query = "SELECT * FROM statustypetable ORDER BY statusTypeID";

               $result = $dbConn->query($query);

               if ($result)
               {
                    while ($row = $result->fetch_assoc())
                    {
                         $returnObj[$row["statusTypeID"]] = $row;
                    }
               }

               return $returnObj;
          }

          //Get all JO status
          public static function get_all_status_jo($userID)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();
               $doneCancelReturn = array();

               $query1 = "SELECT 
                              b.jobOrderNumber 'number', 
                              b.dateFinished 'joFinished', 
                              CONCAT(d.firstName, ' ', d.lastName) 'fullName', 
                              b.*, 
                              a.donePhoto
                         FROM donejotable a 
                         JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID
                         JOIN jotrackingtable c ON c.jobOrderID = b.jobOrderID
                         JOIN userdetailstable d ON c.userID = d.userID
                         WHERE b.userID = '$userID'
                         ORDER BY b.dateFinished DESC";

               $query2 = "SELECT 
                              b.jobOrderNumber 'number', 
                              a.dateFiled 'joFinished', 
                              CONCAT(d.firstName, ' ', d.lastName) 'fullName', 
                              b.*
                         FROM rejectjotable a 
                         JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID
                         JOIN jotrackingtable c ON c.jobOrderID = b.jobOrderID
                         JOIN userdetailstable d ON c.userID = d.userID
                         WHERE b.userID = '$userID'
                         ORDER BY a.dateFiled DESC";

               $result1 = $dbConn->query($query1);
               $result2 = $dbConn->query($query2);

               if ($result1 && $result2)
               {
                    while ($row = $result1->fetch_assoc())
                    {
                         $joDetails = array();
                         $joDoneCancel = array();

                         $joDoneCancel["status"] = "Done";

                         $x = 0;
                         foreach ($row as $key => $value)
                         {
                              if ($x >= 0 && $x <= 2)
                              {
                                   $joDoneCancel[$key] = $value;
                              }
                              else if ($x >= 3 && $x <= count($row))
                              {
                                   $joDetails[$key] = $value;
                              }
                              $x++;
                         }

                         $joDoneCancel["joDetails"] = $joDetails;

                         array_push($doneCancelReturn, $joDoneCancel);
                    }

                    while ($row = $result2->fetch_assoc())
                    {
                         $joDetails = array();
                         $joDoneCancel = array();

                         $joDoneCancel["status"] = "Cancel";

                         $x = 0;
                         foreach ($row as $key => $value)
                         {
                              if ($x >= 0 && $x <= 2)
                              {
                                   $joDoneCancel[$key] = $value;
                              }
                              else if ($x >= 3 && $x <= count($row))
                              {
                                   $joDetails[$key] = $value;
                              }
                              $x++;
                         }

                         $joDoneCancel["joDetails"] = $joDetails;

                         array_push($doneCancelReturn, $joDoneCancel);
                    }
               }

               usort($doneCancelReturn, function($a, $b) {
                    return strtotime($a["joFinished"]) - strtotime($b["joFinished"]);
               });

               $returnObj["count"] = count($doneCancelReturn);
               $returnObj["tableRow"] = $doneCancelReturn;

               return $returnObj;
          }

          public static function get_jo_tracking($userID)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $query = "SELECT 
                              a.jobTrackingID,
                              CONCAT(c.firstName, ' ', c.lastName) 'fullName',
                              a.activity,
                              b.jobOrderNumber 'JONumber',
                              b.dateRequested 'requestedDate',
                              a.userID 'approvalUserID',
                              d.statusName,
                              a.comments,
                              b.*
                         FROM jotrackingtable a 
                         JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID 
                         JOIN userdetailstable c ON b.userID = c.userID
                         JOIN statustypetable d ON b.statusTypeID = d.statusTypeID
                         WHERE b.userID = '$userID'";

               $result = $dbConn->query($query);

               if ($result)
               {
                    while ($row = $result->fetch_assoc())
                    {
                         $trackingDetails = array();
                         $joDetails = array();

                         $x = 0;
                         foreach ($row as $key => $value) 
                         {
                              if ($x >= 1 && $x <= 7)
                              {
                                   $trackingDetails[$key] = $value;
                              }
                              else if ($x >= 8 && $x < count($row))
                              {
                                   $joDetails[$key] = $value;
                              }
                              $x++;
                         }

                         $returnObj[$row["jobTrackingID"]]["trackingDetails"] = $trackingDetails;
                         $returnObj[$row["jobTrackingID"]]["joDetails"] = $joDetails;
                    }
               }

               return $returnObj;
          }

          //Insert new JO
          public static function insert_rated_jo($jobOrderID, $userID, $timeliness, $accuracy, $comments) 
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $query = "INSERT INTO joratedtable (jobOrderID, userID, timeliness, accuracy, dateRated, comments)
                         VALUES ('$jobOrderID', '$userID', '$timeliness', '$accuracy', NOW(), '$comments')";

               $result = $dbConn->query($query);

               if ($result)
               {
                    $returnObj["status"] = true;
                    $returnObj["message"] = "Successfully rated the Job Order";
               }
               else
               {
                    $returnObj["status"] = false;
                    $returnObj["message"] = "Error: Check Query 1";
               }

               return $returnObj;
          }

          public static function get_job_order_rated_id()
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $query = "SELECT jobOrderID FROM joratedtable";
               $result = $dbConn->query($query);

               if ($result)
               {
                    $x = 0;
                    while ($row = $result->fetch_assoc())
                    {
                         $returnObj[$x] = $row["jobOrderID"];
                         $x++;
                    }
               }

               return $returnObj;

          }

          public static function get_jo_count_per_status($userID)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $statusTypeNames = array("NotYetDone", "Done", "Cancelled", "WaitingMaterials", "Outsource", "Undefined");

               for ($x = 0; $x < count($statusTypeNames); $x++) 
               {
                    $statusID = ($x + 1);
                    $query = "SELECT COUNT(*) '$statusTypeNames[$x]' FROM joborderdetailstable WHERE statusTypeID='$statusID' AND userID='$userID'";
                    $result = $dbConn->query($query);

                    if ($result)
                    {
                         $count = $result->fetch_assoc();
                         $returnObj[$statusTypeNames[$x]] = intval($count[$statusTypeNames[$x]]);
                    }
               }

               return $returnObj;
          }

          public static function get_workers_assigned_jo($userID)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array(); 
               $workersForJO = array();

               $query = "SELECT 
                              b.jobOrderNumber 'number',
                              CONCAT(c.firstName, ' ', c.lastName) 'fullName',
                              b.*
                         FROM jotrackingtable a 
                         JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID 
                         JOIN userdetailstable c ON b.userID = c.userID
                         WHERE a.userID = '$userID' AND b.statusTypeID = '1'";

               $result = $dbConn->query($query);

               if ($result)
               {
                    while ($row = $result->fetch_assoc())
                    {
                         $forJOArray = array();
                         $detailsArray = array();

                         $x = 0;
                         foreach ($row as $key=>$value)
                         {
                              if ($x >= 0 && $x <= 1)
                              {
                                   $forJOArray[$key] = $value;
                              }
                              else if ($x >= 2 && $x <= count($row))
                              {
                                   $detailsArray[$key] = $value;
                              }
                              $x++;
                         }

                         $forJOArray["joDetails"] = $detailsArray;

                         array_push($workersForJO, $forJOArray);
                    }

                    $returnObj["count"] = count($workersForJO);
                    $returnObj["tableRow"] = $workersForJO;
               }

               return $returnObj;
          }

          public static function update_done_jo($workersID, $jobOrderID, $filePath, $comments)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();
               $status = "2";
               $query1 = "INSERT INTO donejotable (userID, jobOrderID, donePhoto)
                         VALUES ('$workersID', '$jobOrderID', '$filePath')";
               $query2 = "UPDATE joborderdetailstable SET statusTypeID = '$status', dateFinished = NOW() WHERE jobOrderID = '$jobOrderID'";
               $query3 = "UPDATE jotrackingtable SET activity = 'Done', comments = '$comments' WHERE jobOrderID = '$jobOrderID'";

               $result1 = $dbConn->query($query1);
               $result2 = $dbConn->query($query2);
               $result3 = $dbConn->query($query3);

               if ($result1 && $result2 && $result3)
               {
                    $returnObj["status"] = true;
                    $returnObj["message"] = "Successfully rated the Job Order";
               }
               else 
               {
                    $returnObj["status"] = false;
                    $returnObj["message"] = "Error: Check queries";
               }

               return $returnObj;
          }

          public static function update_cancelled_jo($workersID, $jobOrderID, $comments)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();
               $status = "3";
               $query1 = "INSERT INTO rejectjotable (dateFiled, userID, jobOrderID, comments)
                         VALUES (NOW(), '$workersID', '$jobOrderID', '$comments')";
               $query2 = "UPDATE joborderdetailstable SET statusTypeID = '$status' WHERE jobOrderID = '$jobOrderID'";
               $query3 = "UPDATE jotrackingtable SET activity = 'Cancelled',  comments = '$comments' WHERE jobOrderID = '$jobOrderID'";

               $result1 = $dbConn->query($query1);
               $result2 = $dbConn->query($query2);
               $result3 = $dbConn->query($query3);

               if ($result1 && $result2 && $result3)
               {
                    $returnObj["status"] = true;
                    $returnObj["message"] = "Successfully rated the Job Order";
               }
               else 
               {
                    $returnObj["status"] = false;
                    $returnObj["message"] = "Error: Check queries";
               }

               return $returnObj;
          }

          public static function update_wfm_jo($workersID, $jobOrderID, $comments)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();
               $status = "4";
               $query1 = "UPDATE joborderdetailstable SET statusTypeID = '$status' WHERE jobOrderID = '$jobOrderID'";
               $query2 = "UPDATE jotrackingtable SET activity = 'Job Order is still waiting for materials',  comments = '$comments' WHERE jobOrderID = '$jobOrderID'";

               $result1 = $dbConn->query($query1);
               $result2 = $dbConn->query($query2);

               if ($result1 && $result2)
               {
                    $returnObj["status"] = true;
                    $returnObj["message"] = "Successfully rated the Job Order";
               }
               else 
               {
                    $returnObj["status"] = false;
                    $returnObj["message"] = "Error: Check queries";
               }

               return $returnObj;
          }

          public static function update_outsourced_jo($workersID, $jobOrderID, $comments)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();
               $status = "5";
               $query1 = "UPDATE joborderdetailstable SET statusTypeID = '$status' WHERE jobOrderID = '$jobOrderID'";
               $query2 = "UPDATE jotrackingtable SET activity = 'Job Order is currently for outsource',  comments = '$comments' WHERE jobOrderID = '$jobOrderID'";

               $result1 = $dbConn->query($query1);
               $result2 = $dbConn->query($query2);

               if ($result1 && $result2)
               {
                    $returnObj["status"] = true;
                    $returnObj["message"] = "Successfully rated the Job Order";
               }
               else 
               {
                    $returnObj["status"] = false;
                    $returnObj["message"] = "Error: Check queries";
               }

               return $returnObj;
          }

          public static function get_for_workers_count($userID)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $query1 = "SELECT COUNT(*) 'doneJO' FROM donejotable WHERE userID = '$userID'";

               $query2 = "SELECT COUNT(*) 'forJO' 
                         FROM jotrackingtable a 
                         JOIN joborderdetailstable b ON a.jobOrderID=b.jobOrderNumber 
                         WHERE a.userID = '$userID' AND b.statusTypeID='1'";

               $result1 = $dbConn->query($query1);
               $result2 = $dbConn->query($query2);

               if ($result1 && $result2) 
               {
                    $row1 = $result1->fetch_assoc();
                    $row2 = $result2->fetch_assoc();
                    
                    $returnObj[0] = $row2["forJO"];
                    $returnObj[1] = $row1["doneJO"];
               }
               return $returnObj;
          }

          public static function get_worker_done_jo($userID)
          {
               $dbConn = DatabaseManipulator::get_db_connection();
               $returnObj = array();

               $query = "SELECT a.doneID, a.donePhoto, b.*
               FROM donejotable a JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID WHERE a.userID = '$userID'";
               $result = $dbConn->query($query);

               if ($result)
               {
                    while ($row = $result->fetch_assoc())
                    {
                         array_push($returnObj, $row);
                    }
               }

               return $returnObj;
          }
     }

?>