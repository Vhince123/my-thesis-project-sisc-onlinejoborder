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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="CSS/onlineJO-sidebar.css" />
    <link rel="stylesheet" href="CSS/modal.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <style>
        .tabs{
            display: flex;
            flex-wrap: wrap;
        }

        .tabs_label{
            padding:10px 16px;
            cursor: pointer;
        }

        .tabs_radio{
            display:none;
        }

        .tabs_content{
            order: 1;
            width: 100%;
            border-bottom: 3px solid #ffff;
            line-height: 1.5;
            font-size: 0.9em;
            display:none;
        }

        .tabs_radio:checked+.tabs_label{
            font-weight: bold;
            color: #009578;
            border-bottom: 2px solid #009578;
        }

        .tabs_radio:checked+.tabs_label+.tabs_content{
            display: initial;
        }
        

        .option-hidden{
            display: none;
        }

        .upload-control{
            border: 1px solid black;
            padding: 8px;
            width: 100%;
        }

        .upload-group{
            margin-top:10px;
        }
    </style>

    <title>Approved JOs</title>
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

<!-- ####################################### VIEW MODAL ############################### -->

    <div id="viewjomodal">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Job Order Details</h5>
            <button type="button" class="btn-close close-viewjomodal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <div class="modal-body">
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
                    <label for="jouserid" class="form-label">Requested by:</label>
                    <input type="text" name="jouserid" id="jouserid" readonly>
                </div>
                <div class="contents-modal1">
                    <label for="photo" class="form-label">Photo:</label>
                    <p id="imageSrc"  style="width:70%; text-align: center;"></p>
                </div>
            </div>
        <div class="modal-footer button-group">
            <button type="button" class="btn close-viewjomodal" data-bs-dismiss="modal">Close</button>
            <?php if($role == "Admin Personnel"){
            ?>
            <button type="button" id="printJO" class="btn btn-yes">Print</button>
            <?php } ?>
        </div>
        </div>
    </div>

<!-- ################################################################################## -->

<!-- ####################################### VIEW EDIT MODAL ############################### -->

    <div id="csmodal">
        <div class="modal1">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Mark as Done?</h5>
                <button type="button" class="btn-close close-csmodal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="PHP/statusChange.php" method="POST" enctype="multipart/form-data">
                <div class = "modal-body">
                    <input type="hidden" name="joid-done" id="joid-done">
                    <h4>Insert Photo</h4>
                    <div class="contents-modal">
                        <label>Printed Job Order Form:</label>
                        <input type="file" name="img-jo1" accept=".jpeg, .jpg, .png" class = "upload-control" required>
                    </div>
                    <div class ="contents-modal">
                        <label>Proof Image:</label>
                        <input type="file" name="img-jo2" accept=".jpeg, .jpg, .png" class = "upload-control" required>
                    </div>
                </div>
                <div class = "button-group">
                    <button type="submit" name="cs-jo" class="btn btn-yes">SUBMIT</button>
                    <button type="button" class="btn close-csmodal" data-dismiss = "modal">NO</button>
                </div>
            </form>
        </div>
    </div>


<!-- ################################################################################## -->


<!-- ################################# REJECT JOB ORDER DETAILS ################################## -->

    <div id="joreject">
        <div class="modal1">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Cancel Job Order</h5>
                <button type="button" class="btn-close close-joreject" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="PHP/rejectJODetails.php" method="POST">
                <div class = "modal-body">
                    <input type="hidden" name="rejectjoid" id="rejectjoid">
                    <div class="contents-modal">
                        <label for="reasontbx">Reason to Cancel</label>
                        <textarea type="text" name="reasontbx" id="reasontbx" required autocomplete="off"></textarea>
                    </div>
                    <div class = "button-group">
                        <button type="submit" name="reject-approvedjo" class="btn btn-yes">Cancel</button>
                        <button type="button" class="btn close-joreject" data-dismiss = "modal">NO</button>
                    </div>

                </div>
                    
            </form>
        </div>
    </div>

<!-- ########################################################################################### -->

<!-- ################################# APPROVE JOB ORDER DETAILS ################################## -->

    <div id="joapprove">
        <div class="modal1">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Change Status</h5>
                <button type="button" class="btn-close close-joapprove" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="PHP/approveJODetails.php" method="POST">
                <div class = "modal-body">
                    <input type="hidden" name="approvejoid" id="approvejoid">
                    <div class="contents-modal">
                        <label for="updatestatus">Select status for approval:</label>
                        <select name = "updatestatus" id = "updatestatus">
                            <option value="4" selected>Waiting For Materials</option>
                            <option value="5">Outsource</option>
                        </select>
                    </div>
                    <div class="contents-modal">
                        <label for="comments">Instructions:</label>
                        <textarea type="text" name ="comments" id="comments" autocomplete="off" style="width: 100%" required></textarea>
                    </div>
                </div>
                <div class = "button-group" style="margin-bottom: 10px;">
                    <button type="submit" name="cstat-jo" class="btn btn-yes">Change Status</button>
                    <button type="button" class="btn btn-secondary close-joapprove" data-dismiss = "modal">NO</button>
                </div>
            </form>
        </div>
    </div>

<!-- ########################################################################################### -->

<!-- ################################################################################################### -->

<div id="wfmmodal">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Other Status</h5>
            <button type="button" class="btn-close close-wfmmodal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <div class = "modal-body">
                <input type="hidden" name="wfmjoid" id="wfmjoid">
                <h4>Does the material/s already obtained?</h4>
            </div>
            <div class="button-group">
                <button type="submit" name="submitWFM" id="submitWFM" class="btn btn-yes">YES</button>
                <button type="button" class="btn close-wfmmodal" data-dismiss = "modal">NO</button>
            </div>
    </div>
</div>

<!-- ##################################################################################################### -->

<div class="wrapper">

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
                                <a href="#" onclick="location.href = 'adminUserView.php'" class="sidebar-link" >View Users</a>
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
                                <a href="#" onclick="location.href = 'joApproved.php'" class="sidebar-link" style="text-decoration: underline;">Approved</a>
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
                    <div class="page-title" style="align-items: center; font-size: 20px;">Approved Job Orders
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
                        <div class="tabs">

                            <!-- ++++++++++++++++++++ONGOING++++++++++++++++++++++++ -->

                            <input type="radio" class="tabs_radio" name = "tabs-approved" id="ongoingtab" checked>
                            <label for="ongoingtab" class="tabs_label">Ongoing</label>
                            <div class="tabs_content">
                                <div class="container p-3 my-5 bg-light ">
                                    <table id="ongoingtable" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>JO No.</th>
                                                <th>Date Approved</th>
                                                <th>Requested By</th>
                                                <th>Approved By</th>
                                                <th>Accountable Person</th>
                                                <th>View</th>
                                                <?php if ($role !== "Administrator") { ?>
                                                    <th>Change Status</th>
                                                    <th>Other Status</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                                if (isset($result[0])) {
                                                    $query = "SELECT a.*, b.*, c.*, d.firstName AS r_fname, d.lastName AS r_lname FROM approvedjotable a JOIN userdetailstable b ON a.userID = b.userID
                                                            JOIN joborderdetailstable c ON a.jobOrderID = c.jobOrderID JOIN userdetailstable d ON d.userID = c.userID WHERE c.statusTypeID = '1' ORDER BY a.jobOrderID ASC";
                                                    $query_run = mysqli_query($result[0], $query);

                                                    if(mysqli_num_rows($query_run) > 0){
                                                        foreach($query_run as $approvedjo)
                                                        {   
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $approvedjo['jobOrderNumber']; ?></td>
                                                                <td><?php echo $approvedjo['dateApproved']; ?></td>
                                                                <td><?php echo $approvedjo['r_fname']. " ". $approvedjo["r_lname"]; ?></td>
                                                                <td><?php echo $approvedjo['firstName']. " ". $approvedjo["lastName"]; ?></td>
                                                                <td><?php echo $approvedjo['accountable'];?></td>
                                                                <td style="text-align: center;">
                                                                    <button type="button" class="btn btn-success open-viewjomodal" data-joview="<?php echo $approvedjo['jobOrderID'];?>"><i class="fa-solid fa-eye"></i></button>
                                                                </td>

                                                                <?php if ($role !== "Administrator") { ?>
                                                                    <td style="text-align: center; width: 400px;" > 
                                                                    <button type="button" class="btn open-csmodal" data-joview="<?php echo $approvedjo['jobOrderID'];?>"><i class="fa-solid fa-check"></i></button>
                                                                    <button type="button" class="btn open-joreject" data-joid="<?php echo $approvedjo['jobOrderID'];?>"><i class="fa-solid fa-xmark"></i></button>
                                                                    
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                    <button type="button" class="btn open-joapprove" data-joid="<?php echo $approvedjo['jobOrderID'];?>"><i class="fa-solid fa-bars"></i></button>
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
                            
                            <!-- ++++++++++++++++++++WAITING FOR MATERIALS++++++++++++++++++++++++ -->

                            <input type="radio" class="tabs_radio" name = "tabs-approved" id="wfmtab">
                            <label for="wfmtab" class="tabs_label">Waiting For Materials</label>
                            <div class="tabs_content">

                                <div class="container p-3 my-5 bg-light ">
                                    <table id="wfmtable" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>JO No.</th>
                                                <th>Date Approved</th>
                                                <th>Requested By</th>
                                                <th>Approved By</th>
                                                <th>Materials</th>
                                                <th>Action</th>
                                                <?php if ($role !== "Administrator") { ?>
                                                    <th>Change Status</th>
                                                    <th>Other Status</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                                if (isset($result[0])) {
                                                    $act = "Materials are already obtained and the job order is on the process...";

                                                    $query1 = "SELECT a.*, b.*, c.*, d.firstName AS r_fname, d.lastName AS r_lname, e.activity FROM approvedjotable a JOIN userdetailstable b ON a.userID = b.userID
                                                            JOIN joborderdetailstable c ON a.jobOrderID = c.jobOrderID JOIN userdetailstable d ON d.userID = c.userID 
                                                            JOIN jotrackingtable e ON e.jobOrderID = c.jobOrderID WHERE c.statusTypeID = '4'";
                                                    $query_run1 = mysqli_query($result[0], $query1);

                                                    if(mysqli_num_rows($query_run1) > 0){
                                                        foreach($query_run1 as $wfmjo)
                                                        {   
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $wfmjo['jobOrderNumber']; ?></td>
                                                                <td><?php echo $wfmjo['dateApproved']; ?></td>
                                                                <td><?php echo $wfmjo['r_fname']. " ". $wfmjo["r_lname"]; ?></td>
                                                                <td><?php echo $wfmjo['firstName']. " ". $wfmjo["lastName"]; ?></td>
                                                                <td><?php if($wfmjo['activity'] == $act){
                                                                    echo "obtained";
                                                                }else{
                                                                    echo "still on waiting";
                                                                }?></td>
                                                                <td style="text-align: center;">
                                                                    <button type="button" class="btn btn-success open-viewjomodal" data-joview="<?php echo $wfmjo['jobOrderID'];?>"><i class="fa-solid fa-eye"></i></button>
                                                                </td>
                                                                <?php if ($role !== "Administrator") { ?>
                                                                    <td style="text-align: center; width: 400px;" >
                                                                    <button type="button" class="btn open-csmodal" data-joview="<?php echo $wfmjo['jobOrderID'];?>"><i class="fa-solid fa-check"></i></button>
                                                                    <button type="button" class="btn open-joreject" data-joid="<?php echo $wfmjo['jobOrderID'];?>"><i class="fa-solid fa-xmark"></i></button>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <button type="button" class="btn open-wfmmodal" style="background-color: blue;" data-joview="<?php echo $wfmjo['jobOrderID'];?>"><i class="fa-solid fa-eye"></i></button>
                                                                    </td>
                                                                <?php } ?>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                    
                                                }else{
                                                    echo "Failed to connect to the database";
                                                }
                                                ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- +++++++++++++++++++++++Outsource Tab++++++++++++++++++++++++++++++++ -->
                
                            <input type="radio" class="tabs_radio" name = "tabs-approved" id="outsourcetab">
                            <label for="outsourcetab" class="tabs_label">Outsource</label>
                            <div class="tabs_content">

                                <div class="container p-3 my-5 bg-light">
                                    <table id="outsourcetable" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>JO No.</th>
                                                <th>Date Approved</th>
                                                <th>Requested By</th>
                                                <th>Approved By</th>
                                                <th>Action</th>
                                                <?php if ($role !== "Administrator") { ?>
                                                    <th>Change Status</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                                if (isset($result[0])) {
                                                    $query2 = "SELECT a.*, b.*, c.*, d.firstName AS r_fname, d.lastName AS r_lname FROM approvedjotable a JOIN userdetailstable b ON a.userID = b.userID
                                                            JOIN joborderdetailstable c ON a.jobOrderID = c.jobOrderID JOIN userdetailstable d ON d.userID = c.userID WHERE c.statusTypeID = '5'";
                                                    $query_run2 = mysqli_query($result[0], $query2);

                                                    if(mysqli_num_rows($query_run2) > 0){
                                                        foreach($query_run2 as $outsource)
                                                        {   
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $outsource['jobOrderNumber']; ?></td>
                                                                <td><?php echo $outsource['dateApproved']; ?></td>
                                                                <td><?php echo $outsource['r_fname']. " ". $outsource["r_lname"]; ?></td>
                                                                <td><?php echo $outsource['firstName']. " ". $outsource["lastName"]; ?></td>
                                                                <td style="text-align: center;">
                                                                    <button type="button" class="btn btn-success open-viewjomodal" data-joview="<?php echo $outsource['jobOrderID'];?>"><i class="fa-solid fa-eye"></i></button>
                                                                </td>
                                                                <?php if ($role !== "Administrator") { ?>
                                                                    <td style="text-align: center; width: 400px;">
                                                                        <button type="button" class="btn open-csmodal" data-joview="<?php echo $outsource['jobOrderID'];?>"><i class="fa-solid fa-check"></i></button>
                                                                        <button type="button" class="btn open-joreject" data-joid="<?php echo $outsource['jobOrderID'];?>"><i class="fa-solid fa-xmark"></i></button>
                                                                    </td>
                                                                <?php } ?>
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
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
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
</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="Javascript/dashboardDisplay.js"></script>

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="Javascript/viewApprovedJO.js"></script>
        <script src="Javascript/changeObtain.js"></script>
        <script src="Javascript/changeStatus.js"></script>
        <script src="Javascript/incomingJOReject.js"></script>
        <script src="Javascript/incomingJOApprove.js"></script>
        <script>
            $(document).ready(function() {
                $('#ongoingtable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": false
                });

                $('#wfmtable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": false
                });

                $('#outsourcetable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": false
                });
            });

            document.getElementById('printJO').addEventListener('click', function(event) {
                var jobno = document.getElementById("jobno");
                let newtab = window.open("PHP/printJobOrder.php?jobno=" + jobno.value, "_blank");

                if (newtab) {
                    newtab.focus();
                }
            });

            function closeModal() {
                    modal.style.display = "none";
                    var inputs = document.querySelectorAll('.modal-content input[type="text"]');
                    inputs.forEach(function(input) {
                    input.value = ''; // Clear input value
                });
            }


        </script>
        <script>
            // Function to clear input fields and text areas
            function clearModalInputs(modalId) {
                var modal = document.getElementById(modalId);
                var inputs = modal.querySelectorAll('input, textarea, select');
                inputs.forEach(function(input) {
                    if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'file')) {
                        input.value = '';
                    } else if (input.tagName === 'TEXTAREA') {
                        input.value = '';
                    } else if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    }
                });
            }

            // Event listener for when modal is hidden
            $('#csmodal, #joreject, #joapprove').on('hidden.bs.modal', function (e) {
                var modalId = e.target.id;
                clearModalInputs(modalId);
            });

            // Event listener for close buttons
            $('.close-csmodal, .close-joreject, .close-joapprove').click(function() {
                var modalId = $(this).closest('.modal1').parent().attr('id');
                clearModalInputs(modalId);
            });

            $('#csmodal form').submit(function() {
                var modalId = $(this).closest('.modal').attr('id');
                clearModalInputs(modalId);
            });

            $('#joreject form').submit(function() {
                var modalId = $(this).closest('.modal').attr('id');
                clearModalInputs(modalId);
            });

            $('#joapprove form').submit(function() {
                var modalId = $(this).closest('.modal').attr('id');
                clearModalInputs(modalId);
            });
        </script>
        <script>
            document.getElementById("submitWFM").addEventListener("click", function() {
                var xml = new XMLHttpRequest();
                var wfmjoid = document.getElementById("wfmjoid");

                xml.addEventListener("readystatechange", function() {
                    if (xml.readyState == 4 && xml.status == 200) {
                        var jsonObj = JSON.parse(xml.responseText);

                        if (jsonObj.status == 1) {
                            alert("Materials status has been updated");
                            window.location.href = "joApproved.php?wfmsuccess";
                        }
                        else if (jsonObj.status == 2) {
                            alert("Materials already obtained");
                        } 
                    }
                });

                xml.open("GET", "PHP/materialObtained.php?wfmjoid=" + wfmjoid.value);
                xml.send();
            });
        </script>
        <script>
        </script>
        
    </body>
</html>