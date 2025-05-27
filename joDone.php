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

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <title>Done JOs</title>
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

<!-- #####################################################JOB ORDER VIEW############################################################################ -->

    <div id="viewjomodal">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Job Order Details</h5>
            <button type="button" class="btn-close close-viewjomodal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form>
                <div class="contents-modal1">
                    <label for="jobno" class="form-label">Job Order Number:</label>
                    <input type="text" name ="jobno" id="jobno" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="jobdes" class="form-label">Job Order Description:</label>
                    <textarea type="text" name ="jobdes" id="jobdes" readonly></textarea>
                </div>
                <div class="contents-modal1">
                    <label for="jolocation" class="form-label">Location:</label>
                    <input type="text" name = "jolocation" id="jolocation" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="accountable" class="form-label">Accountable Person:</label>
                    <input type="text" name = "accountable" id="accountable" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="datereq" class="form-label">Date Requested:</label>
                    <input type="text" name = "datereq" id="datereq" readonly>
                </div>
                    <div class="contents-modal1">
                    <label for="dateneed" class="form-label">Date Needed:</label>
                <input type="text" name = "dateneed" id="dateneed" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="dateneed" class="form-label">Date Served:</label>
                    <input type="text" name = "dateser" id="dateser" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="dateneed" class="form-label">Date Finished</label>
                    <input type="text" name = "datefin" id="datefin" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="jouserid" class="form-label">Requested by:</label>
                    <input type="text" name="jouserid" id="jouserid" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="photo" class="form-label">Photo</label>
                    <p id="imageSrc" style="width:70%; text-align: center;" ></p>
                </div>
                
            </form>
            <div class="button-group">
                <button type="button" class="btn btn-secondary close-viewjomodal" data-bs-dismiss="modal"> Close</button>
            </div>  
        </div>
        </div>
    </div>

<!-- ################################################################################################################################# -->

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
                        <a href="#" onclick="location.href = 'adminUserView.php'" class="sidebar-link">View Users</a>
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
                        <a href="#" onclick="location.href = 'joIncoming.php'" class="sidebar-link">Incoming</a>
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
                        <a href="#" onclick="location.href = 'joDone.php'" class="sidebar-link" style="text-decoration: underline;">Done</a>
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
            <div class="page-title" style="align-items: center; font-size: 20px;">Done Job Orders
            </div>
            <div class="for-logout">
                <div class="namedisplay">
                <button type="button" class="profiledata open-profilemodal"><?php echo $name . " / " . $role;?></button></div>
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post"><input type="submit" id=logout class="logout" name="logout" value="Logout"></form>
                
            </div>
        </div>
    </nav>

    
    <main class="content px-3 py-2">
        <div class="container-fluid">
            <div class="contents-modal" style="font-size: 22px; padding-top: 20px;"></div>
                <div class="container p-3 " style="width: 100%;">
                <div class="box" style="width: 100%;" >        
                <div class="filterForm1">
                    <form action="" method="get" style="display: flex; flex-direction: column; gap: 10px ;justify-content: center;">
                        <div class="formcontrol" style="display: flex; flex-direction: row; gap: 10px; justify-content: center; align-items: center;">
                            <label for="">Start Date: </label>
                            <div class="col-md-6" style="width: 40%">
                                
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control" id="startDate" name="startDate" 
                                    value="<?= isset($_GET["startDate"]) == true ? $_GET["startDate"] : '' ?> " readonly>
                                </div>
                            </div>
                            <label for="">End Date: </label>
                            <div class="col-md-6" style="width: 40%">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control" id="endDate" name="endDate" 
                                    value="<?= isset($_GET["endDate"]) == true ? $_GET["endDate"] : '' ?> "readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" style="display: flex; justify-content: center; width: 100%; gap: 10px;">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="joDone.php" class="btn btn-danger">Reset</a>
                        </div>
                    </form>
                </div>
                    
                    <div class="container p-3 my-5 bg-light" style="width: 100%;">
                        <table id="donetable" class="table table-striped" style="width:100%">
                            <thead >
                                <tr>
                                    <th>JO No.</th>
                                    <th>Lead Time</th>
                                    <th>Done By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                    if (isset($result[0])) {
                                                    
                                        if (isset($_GET['startDate']) && $_GET['startDate'] != '' && isset($_GET['endDate']) && $_GET['endDate'] != ''){

                                            $startDate = $_GET['startDate'];
                                            $endDate = $_GET['endDate'];
                                            $query = "SELECT a.* , b.*, b.firstName AS d_fname, b.lastName AS d_lname, c.jobOrderNumber, c.dateFinished,
                                            CONCAT(
                                                TIMESTAMPDIFF(DAY, c.dateServed, c.dateFinished), ' days, ',
                                                TIMESTAMPDIFF(HOUR, c.dateServed, c.dateFinished) % 24, ' hours, ',
                                                TIMESTAMPDIFF(MINUTE, c.dateServed, c.dateFinished) % 60, ' minutes'
                                            ) AS leadTimeFormatted
                                            FROM donejotable a 
                                            JOIN userdetailstable b ON a.userID = b.userID 
                                            JOIN joborderdetailstable c ON a.jobOrderID = c.jobOrderID 
                                            WHERE c.dateFinished BETWEEN '$startDate' AND '$endDate'";

                                        }
                                        else{

                                            $query = "SELECT a.* , b.*, b.firstName AS d_fname, b.lastName AS d_lname, c.jobOrderNumber, c.dateFinished,
                                            CONCAT(
                                                TIMESTAMPDIFF(DAY, c.dateServed, c.dateFinished), ' days, ',
                                                TIMESTAMPDIFF(HOUR, c.dateServed, c.dateFinished) % 24, ' hours, ',
                                                TIMESTAMPDIFF(MINUTE, c.dateServed, c.dateFinished) % 60, ' minutes'
                                            ) AS leadTimeFormatted
                                            FROM donejotable a JOIN userdetailstable b ON a.userID = b.userID 
                                            JOIN joborderdetailstable c ON a.jobOrderID = c.jobOrderID";

                                        }

                                        $query_run = mysqli_query($result[0], $query);
                                        if($query_run){
                                            if(mysqli_num_rows($query_run) > 0){
                                                foreach($query_run as $donejo)
                                                {   
                                                    $leadtime = "";
                                                    if($donejo['leadTimeFormatted'] == 0){
                                                        $leadtime =  "0 days, 0 hours, 0 minutes";
                                                    }
                                                    else{
                                                        $leadtime = $donejo['leadTimeFormatted'];
                                                    }
                                                    
                                                    
                                                    ?>
                                                    <tr>
                                                        <td><?=  $donejo['jobOrderNumber']; ?></td>
                                                        <td><?= $leadtime; ?></td>
                                                        <td><?=  $donejo['d_fname']. " ". $donejo["d_lname"]; ?></td>
                                                        <td style="text-align: center;">
                                                            <button type="button" class="btn btn-success open-viewjomodal" data-donejo="<?php echo $donejo['jobOrderID'];?>">View</button>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        else{
                                            ?>
                                                <tr>
                                                    <td colspan = "7">No Record Found</td>
                                                </tr>
                                            <?php
                                        }  
                                    }
                                    else{
                                        ?>
                                        <tr>
                                            <td colspan = "7">Something Went Wrong</td>
                                        </tr>
                                        <?php
                                    }
                            ?>
                            </tbody>
                        </table>
                    </div>
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
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script>
            $(document).ready(function() {
                $('#donetable').DataTable({
                    "ordering" : false,
                    "columns": [
                        { "width": "400px" }, 
                        { "width": "500px" } ,
                        { "width": "300px" } ,
                        { "width": "20%" }
                    ]
                });
            });

            $(function(){   
                $('#startDate').datepicker({"dateFormat" : "yy-mm-dd", "onSelect" : function(selectedDate) {
                    $('#endDate').datepicker('option', 'minDate', selectedDate);
                    validateDates();
                    }
                });    
                    $('#endDate').datepicker({ "dateFormat" : "yy-mm-dd", "onSelect" : function(selectedDate) {
                        validateDates();
                    }
                });
                
                    function validateDates() {

                        var startDate = $('#startDate').datepicker('getDate');
                        var endDate = $('#endDate').datepicker('getDate');

                        if (endDate <= startDate) {
                            startDate.setDate(startDate.getDate() + 1);
                            $('#endDate').datepicker('setDate', startDate);
                        }
                    }
            });
        </script>
        <script src="Javascript/viewDoneJO.js"></script>
    </body>
</html>