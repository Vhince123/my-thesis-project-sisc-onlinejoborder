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
    else if ($role == '1'){
        $role = "Administration Staff";
    }
    else {
        $role = "Department Head";
    }

    if ($role !== "Department Head") {
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


    <title>Endorse JOs</title>
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
                    <p id="imageSrc" style="width:70%; text-align: center;" ></p>
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
            <h5 class="modal-title fs-5" id="exampleModalLabel">Endorse Job Order</h5>
            <button type="button" class="btn-close close-joapprove" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="PHP/approveJODetails.php" method="POST">
            <div class = "modal-body">
                <input type="hidden" name="approvejoid" id="approvejoid">
                <div class="contents-modal">
                        <label for="reasontbx">Endorse this job order?</label>
                </div>
            </div>
            <div class = "button-group">
                <button type="submit" name="endorse-jo" class="btn btn-secondary btn-yes">YES</button>
                <button type="button" class="btn btn-secondary close-joapprove" data-dismiss = "modal">NO</button>
            </div>
        </form>
    </div>
</div>

<!-- ########################################################################################### -->

<!-- ################################# REJECT JOB ORDER DETAILS ################################## -->

<!-- <div id="joreject">
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
</div> -->

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
                        <a href="#" onclick="location.href = 'dpDashboard.php'"  class="sidebar-link" >
                            <i class="fa-solid fa-list pe-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Job Orders
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-target="#approvals" style="text-decoration: underline;"
                            aria-expanded="false"><i class="fa-solid fa-file-import pe-2"></i>
                            For Endorse
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-target="#approvals" onclick="location.href = 'dpjoTrack.php'"
                            aria-expanded="false"><i class="fa-solid fa-chart-column pe-2"></i>
                            JO Tracking
                        </a>
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
            <div class="page-title" style="align-items: center; font-size: 20px;">Endorse Job Orders
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
                <div class="container p-3 my-5 bg-light" >
                    <table id="example" class="table table-striped " data-bs-sortable="false" style="width:100%; table-layout: fixed; overflow: hidden;">
                        <thead>
                            <tr>
                                <th>Requested by</th>
                                <th>Date Filed</th>
                                <th>Date Needed</th>
                                <th>Action</th>
                                <th>Approval</th>
                                <!-- <th>User Type</th>
                                <th>User Archieve</th> -->
                            </tr>
                        </thead>
                        <tbody>
                                <?php 
                                    if (isset($result[0])) {
                                        $query = "SELECT a.*, b.*, 
                                        b.lastName AS lname, 
                                        b.firstName AS fname 
                                        FROM joborderdetailstable a 
                                        JOIN userdetailstable b ON a.userID = b.userID 
                                        WHERE a.statusTypeID = '7' AND b.department = '$department'";
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
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <button type="button" class="btn open-joapprove" data-joid="<?php echo $joborders['jobOrderID'];?>">Endorse</button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    else{
                                        echo "Failed to connect to the database";
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
                }
                else{
                    forDone.style = "display: none;";
                    forOthers.style = "display: block;";

                }
            });
        </script>
    </body>
</html>