<?php
    include ("PHP/connect.php");
    session_start();
    $result = Connection();


    if (!isset($_SESSION["userid"])) {
        header('Location: index.php');
    }

    $first = $_SESSION["firstname"];
    $last = $_SESSION["lastname"];
    $middle = $_SESSION["middlename"];
    $name = $first . " " . $last ;
    $role = $_SESSION["userType"];
    $department = $_SESSION["department"];
    $username = $_SESSION["userid"];
    $password = $_SESSION["password"];
    $email = $_SESSION["email"];

    if ($role == '0'){
        $role = "Administrator";
    }
    else if ($role == '1') {
        $role = "Admin Personnel";
    }

    if ($role !== 'Administrator' && $role !== 'Admin Personnel') {
        echo "<script>alert('Access Denied. You are not authorized to view this page.'); window.history.back();</script>";
        exit();
    }

    if (isset($_POST['logout'])) {
        
        $error = "";
        $today = date("Y-m-d H:i:s");
        $loginLogID = $_SESSION["loginLogID"];

        $query = "UPDATE loginlogtable SET logoutTime = '$today' WHERE loginLogID = '$loginLogID'";
        if(!mysqli_query($result[0], $query)){
            $error = "Check query: ".  mysqli_error($result[0]);
        }
        else{
            session_unset();
            session_destroy();
            header('Location: index.php');
            exit();
        }

        if (!empty($error)) {
            echo '<script>alert("' . $error . '")</script>';
            exit();
        }
    }
        
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/016938eade.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="CSS/onlineJO-sidebar.css" />
    <link rel="stylesheet" href="CSS/modal.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">


    <title>Incoming JOs</title>
    <link rel="icon" href= "Images/sisc.png" type="image/x-icon"/>
    </head>

    <body>

    <!-- #####################################################PROFILE MODAL############################################################################ -->

    <div id="profilemodal">
        <div class="modal1">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Profile Details</h5>
                <button type="button" class="btn-close close-profilemodal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding-left: 10px; padding-right: 10px;">
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="fName">First Name: </label>
                        <input type="text" id="fName" name="fName" value="<?php echo $first; ?>" readonly />
                    </div>
                    <div class="contents-modal">
                        <label for="mName">Middle Name: </label>
                        <input type="text" id="mName" name="mName" value="<?php echo $middle; ?>" readonly/>
                    </div>
                    <div class="contents-modal">
                        <label for="lName">Last Name: </label>
                        <input type="text" id="lName" name="lName" value="<?php echo $last; ?>" readonly/>
                    </div>
                </div>
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="dept" class="form-label">Department:</label>
                        <input type="text" name="dept" id="dept" value="<?php echo $department; ?>" readonly>
                    </div>
                    <div class="contents-modal">
                        <label for="usertype" class="form-label">User Type:</label>
                        <input type="text" name="usertype" id="usertype" value="<?php echo $role; ?>" readonly>
                    </div>
                </div>
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" id="username" value="<?php echo $username; ?>" readonly>
                    </div>
                    <div class="contents-modal">
                        <label for="emailad" class="form-label">Email Address:</label>
                        <input type="text" name="emailad" id="emailad" value="<?php echo $email; ?>" readonly>
                    </div>
                </div>
                <div style="width: 100%; display: flex; justify-content: center;">Change Password?</div>
                <input type="text" name="origpass" id="origpass" value="<?php echo $password?>" hidden/>
                <div class="contents-modal1">
                    <label for="oldpass" class="form-label">Old Password:</label>
                    <input type="password" name="oldpass" id="oldpass" require>
                </div>
                <div class="contents-modal1">
                    <label for="newpass" class="form-label">New Password:</label>
                    <input type="password" name="newpass" id="newpass" require>
                </div>
                <div class="contents-modal1">
                    <label for="confirmpass" class="form-label">Confirm Password:</label>
                    <input type="password" name="confirmpass" id="confirmpass" require>
                </div>
                <div class="button-group">
                    <button type="submit" name = "updateprofile" id="updateprofile" class="btn btn-yes">Update</button>
                    <button type="button" class="btn close-profilemodal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<!-- ########################################################################################################################################### -->

<!-- ################################# VIEW JOB ORDER DETAILS ################################## -->

    <div id="jomodal">
        <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Job Order Details</h5>
            <button type="button" class="btn-close close-jomodal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                <div class="contents-modal1">
                    <label for="jobdes" class="form-label">Job Order Description:</label>
                    <textarea type="text" name ="jobdes" id="jobdes" readonly></textarea>
                </div>
                <div class="contents-modal1">
                    <label for="jolocation" class="form-label" style="text-align: right;">Location:</label>
                    <input type="text" name = "jolocation" id="jolocation" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="jouserid" class="form-label">Requested by:</label>
                    <input type="text" name="jouserid" id="jouserid" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="jophoto" class="form-label">Photo:</label>
                    <p id="imageSrc" style="width:70%; text-align: left;"></p>
                </div>
        </div>
        <div class="modal-footer" style="height: 40px;"></div>
        </div>  
    </div>

<!-- ##################################################################################################### -->

<!-- ################################# APPROVE JOB ORDER DETAILS ################################## -->

<div id="joapprove">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Approve Job Order</h5>
            <button type="button" class="btn-close close-joapprove" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="PHP/approveJODetails.php" method="POST" enctype="multipart/form-data">
            <div class = "modal-body">
                <input type="hidden" name="approvejoid" id="approvejoid">
                
                <?php 

                $query1 = "SELECT * FROM userdetailstable WHERE  usertype = '3' AND userarchieve = 0";
                $sqlResult1 = mysqli_query($result[0], $query1);


                ?>
                <div class="contents-modal">
                    <label for="updatestatus">Select status for approval:</label>
                    <select name = "updatestatus" id = "updatestatus" onchange="change()">
                        <option value="1" selected>Ongoing</option>
                        <option value="2">Done</option>
                        <option value="4">Waiting For Materials</option>
                        <option value="5">Outsource</option>
                    </select>
                </div>
                
                <div class="contents-modal">
                    <label for ="available-staff">Select Maintenance Staff to do the Job Order:</label>
                    <select name="available-staff" id="available-staff">
                        <?php

                        if($sqlResult1->num_rows >0){
                            while($row = $sqlResult1->fetch_assoc()){
                                $uid = $row["userID"];
                                $query2 = "SELECT COUNT(a.userID) 'assignjo' FROM jotrackingtable a 
                                JOIN joborderdetailstable b ON a.jobOrderID = b.jobOrderID
                                WHERE a.userID = '$uid' AND b.statusTypeID = 1";
                                $queryResult2 = $result[0]->query($query2);

                                while ($row1 = $queryResult2->fetch_assoc()) {
                                    echo "<option value='" . $uid . "'>" . $row["firstName"] . " " .  $row['lastName'] . " - ". $row1['assignjo'] . " assigned JO</option>";
                                }
                            }
                        }
                        
                        ?>
                        <option value="0" style="display: none;">Not Applicable</option>
                    </select>
                </div>
                <div class="contents-modal" id="forOthers">
                    <label for="comments">Instructions:</label>
                    <textarea type="text" name ="comments" id="comments" style="width: 100%" required></textarea>
                </div>
                <div id="forDone-Hide" style="display: none;">
                    <div class="contents-modal" id="forDone">
                        <label>Upload Photo for Done:</label>
                        <input type="file" name ="doneSec1" id="doneSec1" style="padding: 3px; margin-top: 5px; margin-bottom: 10px;"></input>
                        <input type="file" name ="doneSec2" id="doneSec2" style="padding: 3px;"></input>
                    </div>
                </div>
                
            </div>
            <div class = "button-group">
                <button type="submit" name="approve-jo" class="btn btn-secondary btn-yes">YES</button>
                <button type="button" class="btn btn-secondary close-joapprove" data-dismiss = "modal">NO</button>
            </div>
        </form>
    </div>
</div>

<!-- ########################################################################################### -->

<!-- ################################# REJECT JOB ORDER DETAILS ################################## -->

<div id="joreject">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Reject Job Order</h5>
            <button type="button" class="btn-close close-joreject" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="PHP/rejectJODetails.php" method="POST">
            <div class = "modal-body">
                <input type="hidden" name="rejectjoid" id="rejectjoid">
                <div class="contents-modal" style="gap: 10px;">
                    <label for="reasontbx">Reason to Reject:</label>
                    <textarea type="text" name="reasontbx" id="reasontbx" style="width: 100%;" required></textarea>
                </div>
                <div class = "button-group">
                    <button type="submit" name="reject-jo" class="btn btn-yes">Reject</button>
                    <button type="button" class="btn close-joreject" data-dismiss = "modal">NO</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ########################################################################################### -->

<div class="wrapper">

<!-- SIDEBAR -->

<aside id="sidebar" class="js-sidebar">
    <div class="h-100">
        <div class="container">
            <div class="img-logo">
                    <img id="logo-img" src="Images/sisc.png" alt="">
            </div>
            <div class="sidebar-logo">
                <a href="#">Online Job Order System</a>
            </div>
        </div>
        
        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Admin Elements
            </li>
            <li class="sidebar-item">
                <a href="#" onclick="location.href = 'dashboard.php'" class="sidebar-link">
                    <i class="fa-solid fa-list pe-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed" data-bs-target="#users" data-bs-toggle="collapse"
                    aria-expanded="false"><i class="fa-regular fa-user pe-2"></i>
                    Users
                </a>
                <ul id="users" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'adminUserManage.php'" class="sidebar-link">Add User</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'adminUserView.php'" class="sidebar-link active">View Users</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'userLoginSummary.php'" class="sidebar-link">Login Summary</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-header">
                Job Orders
            </li>
            <?php if($role !== 'Administrator'){ ?>
                <li class="sidebar-item">
                <a href="#" onclick="location.href = 'jobOrderForm.php'" class="sidebar-link">
                    <i class="fa-solid fa-file-circle-plus pe-2"></i>
                    Job Order Form
                </a>
                </li>
            <?php } ?>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed" data-bs-target="#approvals" data-bs-toggle="collapse"
                    aria-expanded="false"><i class="fa-solid fa-file-import pe-2"></i>
                    Approvals
                </a>
                <ul id="approvals" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'joIncoming.php'" class="sidebar-link" style="text-decoration: underline;">Incoming</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'joApproved.php'" class="sidebar-link">Approved</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'joRejected.php'" class="sidebar-link">Rejected</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed" data-bs-target="#finished" data-bs-toggle="collapse"
                    aria-expanded="false"><i class="fa-solid fa-file-circle-check pe-2"></i>
                    Finished
                </a>
                <ul id="finished" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'joDone.php'" class="sidebar-link">Done</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'joRated.php'" class="sidebar-link">Rated</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>
<div class="main">
    <nav class="navbar navbar-expand px-3 border-bottom">
        <button class="btn" id="sidebar-toggle" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse">
            <div class="page-title" style="align-items: center; font-size: 20px;">Incoming Job Orders
            </div>
            <div class="for-logout">
                <div class="namedisplay"><button type="button" class="profiledata open-profilemodal"><?php echo $name . " / " . $role;?></button></div>
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post"><input type="submit" id=logout class="logout" name="logout" value="Logout"></form>
                
            </div>
        </div>
    </nav>

    
    <main class="content px-3 py-2">
        <div class="container-fluid">
            <div class="contents-modal" style="font-size: 22px; padding-top: 20px;"></div>
                <div class="container p-3 my-5 bg-light">
                    <table id="example" class="table table-striped " data-bs-sortable="false" style="width:100%">
                        <thead>
                            <tr>
                                <th >Requested by</th>
                                <th>Date Filed</th>
                                <th>Date Needed</th>
                                <th>Action</th>
                                <?php
                                    // Check if the user role is Administrator
                                    if ($role !== "Administrator") {
                                        echo "<th>Approval</th>";
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                                <?php 
                                    if (isset($result[0])) {
                                        $query = "SELECT a.*, b.*, b.lastName AS lname, b.firstName AS fname FROM joborderdetailstable a JOIN userdetailstable b ON a.userID = b.userID WHERE a.statusTypeID = '6'";
                                        $query_run = mysqli_query($result[0], $query);

                                        if(mysqli_num_rows($query_run) > 0){
                                            foreach($query_run as $joborders)
                                            {   
                                                ?>
                                                <tr>
                                                    <td><?php echo $joborders['fname'] . " " . $joborders['lname']; ?></td>
                                                    <td><?php echo $joborders['dateRequested']; ?></td>
                                                    <td><?php echo $joborders['dateNeeded']; ?></td>
                                                    <td style="text-align: center;">
                                                        <button type="button" class="btn btn-success open-jomodal" data-joid="<?php echo $joborders['jobOrderID'];?>">View</button>
                                                    </td><?php
                                                    if ($role !== "Administrator") { ?>
                                                        <td style="text-align: center;">
                                                        <button type="button" class="btn open-joapprove" data-joid="<?php echo $joborders['jobOrderID'];?>">Approve</button>
                                                        <button type="button" class="btn open-joreject" data-joid="<?php echo $joborders['jobOrderID'];?>">Reject</button>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                            }
                                        }else{
                                            echo "Failed to connect to the database";
                                        }
                                    }
                                ?>
                        </tbody>
                    </table>
                </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row text-muted">
                <div class="col-6 text-start">
                    <p class="mb-0">
                        <a href="#" class="text-muted">
                            <strong>Online Job Order System</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>
</div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="Javascript/dashboardDisplay.js"></script>


        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="Javascript/dataTable.js"></script>
        <script src="Javascript/viewJobOrderDetails.js"></script>
        <script src="Javascript/incomingJOReject.js"></script>
        <script src="Javascript/incomingJOApprove.js"></script>
        <script>
            var updatestatus = document.getElementById("updatestatus");
            var availablestaff = document.getElementById("available-staff");
            var forOthers = document.getElementById("forOthers");
            var forDone = document.getElementById("forDone-Hide");
            var doneSec1 = document.getElementById("doneSec1");
            var doneSec2 = document.getElementById("doneSec2");
            var comments = document.getElementById("comments");

            updatestatus.addEventListener("change", function() {
                let updatestatusOptions = updatestatus.options[updatestatus.selectedIndex];

                if (updatestatusOptions.value === '1') {
                    for (let y = 0; y < availablestaff.options.length - 1; y++) {
                        availablestaff.options[y].style = "display: block;";
                    }

                    availablestaff.options[availablestaff.options.length - 1].style = "display: none;";
                    availablestaff.selectedIndex = 0;
                }
                else {
                    for (let y = 0; y < availablestaff.options.length - 1; y++) {
                        availablestaff.options[y].style = "display: none;";
                    }

                    availablestaff.options[availablestaff.options.length - 1].style = "display: block;";
                    availablestaff.selectedIndex = availablestaff.options.length - 1;
                }

                if(updatestatusOptions.value === '2'){
                    forDone.style = "display: block;";
                    forOthers.style = "display: none;";
                    doneSec1.setAttribute("required", "");
                    doneSec2.setAttribute("required", "");
                    comments.removeAttribute("required");
                }
                else{
                    forDone.style = "display: none;";
                    forOthers.style = "display: block;";
                    doneSec1.removeAttribute("required");
                    doneSec2.removeAttribute("required"); 
                    comments.setAttribute("required", "");
                }
            });
        </script>
    </body>
</html>