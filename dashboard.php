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

    if ($role != 'Administrator' && $role != 'Admin Personnel') {
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
    <style>
        /*DASHBOARD*/

        .content .boxes{ 
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content .boxes .box{
            display: flex;
            border-radius: 7px;
            border: rgba(0, 0, 0, 0.2) 1px solid;
            padding: 15px 20px;
            width: calc(100% / 5 - 15px);
            flex-direction: column;
            align-items: center;
            box-shadow:2px 2px 4px rgba(0, 0, 0, 0.3);
            height: 140px;
            justify-content: space-between;
            background-color: white;
        }

        .box .text{
            white-space: inherit;
            overflow: hidden;
            font-size: 15px;
            text-transform: uppercase;
            
        }

        .box .number{
            font-size: 50px;
            text-align: left;
            width: 100%;
            font-weight: 500;
            color: var(--text-color);
        }

        .box i{
            font-size: 20px;
        }

        .boxes .box1{
            border-bottom: 5px solid var(--box1-color);
        }
        .boxes .box2{
            border-bottom: 5px solid var(--box2-color);
        }
        .boxes .box3{
            border-bottom: 5px solid var(--box3-color);
        }

        .boxes .box4{
            border-bottom: 5px solid var(--box4-color);
        }
        .boxes .box5{
            border-bottom: 5px solid var(--box5-color);
        }

        .statusheader{
            display: flex;
            flex-direction: row;
            gap: 10px;
            justify-content: left;
            width: 100%;
        }

        .statusnumber{
            display: flex;
            flex-direction: row;
            gap: 10px;
            justify-content: space-between;
            width: 100%;
        }

        .box:hover{  
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .boxes{
            padding-top: 1rem;
            padding-bottom: 1rem;
            /* background-color: hsl(0, 0%, 75%);
            box-shadow: 0 0 0 1px hsl(0, 0%, 57%),
                inset 2px 2px 5px hsl(0, 0%, 46%);
            border-radius: 7px; */
        }

        .pie-chart, .incoming-log{
            height: 500px;
            border: rgba(0, 0, 0, 0.2) 1px solid;
            border-radius: 7px;
            box-shadow:2px 2px 4px rgba(0, 0, 0, 0.3);
            background-color: white;
        }

        .calculateDone{
            height: 300px;
            width: 100%;
            border: rgba(0, 0, 0, 0.2) 1px solid;
            border-radius: 7px;
            box-shadow:2px 2px 4px rgba(0, 0, 0, 0.3);
            background-color: white;
        }

        .calculateDone-content{
            display: grid;
            height: 85%;
            grid-template-columns: 1fr 1fr 2fr;
        }
        
        .c-box1, .c-box2, .c-box3{
            display: flex;
            justify-content: space-between;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .c_box_header1{
            display: flex;
            height: 50px;
            width: 100%;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: large;
        }

        .c_box_header2{
            display: flex;
            height: 30px;
            width: 80%;    
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 16px;
            border-bottom: purple 1px solid;
        }

        .c-box1 .c_box_content, .c-box1 .c_box_content1{
            display: flex;
            height: 75px;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .c_box_content{
            width: 100%;
        }

        .c-box1 .c_box_content1{
            font-size: 50px;
        }

        .innercontent{
            display: flex; 
            flex-direction: row; 
            width:100%;
            justify-content: space-between;
        }

        .pie-chart{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            overflow: hidden;
        }

        .chart-container {
            display: flex; 
            align-items: center; 
            justify-content: center; 
            width:100%; 
            height: 300px;
            padding-bottom: 1px;
        }

        .pie-chart .details ul {
            list-style: none;
            padding-top: 10px;
            padding-left: 10px;
            padding-right: 10px;
            display: flex; /* Display items horizontally */
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .pie-chart .details ul li {
            font-size: 12px;
            text-transform: uppercase;
        }

        .pie-chart .details .joreport {
            font-weight: 700;
            color: #e63946;
        }

        canvas{
            background-color:transparent;
        }

        .chart-details-container {
            display: flex;
            width: 100%;
            flex-direction: column;
        }

        .chart-below{
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            border-top: 1px rgb(120, 120, 120, 0.3) solid;
            width: 100%;
        }

        #total-jo1{
            width: 100%;
            height: 40%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #total-jo1 i{
            padding: 10px;
            border-radius: 10px;
            width: 60%;
            text-align: center;
            background-color: purple;
            color: white;
        }


        .filter-container{
            display: flex;
            width: 100%;
            background-color: transparent;
            justify-content: center;
            padding-top: 10px;
            padding: 10px;
            border-bottom: 1px rgb(120, 120, 120, 0.3) solid;
        }

        .filter-date{
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            gap: 5px;
            width: 100%;
        }

        #filter-month, #filter-month2{
            border: none;
            background-color: transparent; /* Remove background color */
            font: inherit; /* Inherit font from parent */
            padding: 0; /* Remove padding */
            margin: 0; /* Remove margin */
            cursor: pointer; /* Show pointer cursor */
            color: inherit; /* Inherit color from parent */
            outline: none;
            text-align: right;
            color: purple;
        }

        #filter-year, #filter-year2{
            border: none;
            background-color: transparent; /* Remove background color */
            font: inherit; /* Inherit font from parent */
            padding: 0; /* Remove padding */
            margin: 0; /* Remove margin */
            cursor: pointer; /* Show pointer cursor */
            color: inherit; /* Inherit color from parent */
            outline: none;
            color: purple;
        }

        .incoming-log{
            display: flex;
            left: 0;
            top: 0;
            bottom: 0;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .container-dashboard {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: center;
            gap: 10px;
        }

        .container-dashboard > div {
            width: 50%;
        }

        .content{
            overflow: auto;
        }

        .dashboard-content{
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1.7fr;
            grid-template-rows: auto auto auto auto;
            gap: 10px;
        }
        
        .boxes{
            grid-row: 1;
            grid-column: 1 / span 4;
        }

        .calculateDone{
            grid-row: 3;
            grid-column: 1 / span 4;
        }

        .pie-chart{
            grid-row: 2;
            grid-column: 4 / 4;
        }

        .incoming-log{
            grid-row: 2;
            grid-column: 1 / 4 ;
        }
    </style>
    
    <title>Dashboard</title>
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
                        <a href="#" class="sidebar-link" style="text-decoration: underline;">
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
            <nav class="navbar navbar-expand px-3 border-bottom sample-class">
                <button class="btn" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-collapse">
                    <div class="page-title" style="align-items: center; font-size: 20px;">Dashboard
                    </div>
                    <div class="for-logout">
                        <div class="namedisplay"><button type="button" class="profiledata open-profilemodal"><?php echo $name . " / " . $role;?></button></div>
                        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post"><input type="submit" id=logout class="logout" name="logout" value="Logout"></form>
                    </div>
                </div>
            </nav>

            <?php
                $query1 =  "SELECT 
                                SUM(CASE WHEN statusTypeID = 6 THEN 1 ELSE 0 END) AS incoming_count,
                                SUM(CASE WHEN statusTypeID = 1 THEN 1 ELSE 0 END) AS ongoing_count,
                                SUM(CASE WHEN statusTypeID = 4 THEN 1 ELSE 0 END) AS wfm_count,
                                SUM(CASE WHEN statusTypeID = 5 THEN 1 ELSE 0 END) AS outsource_count,
                                SUM(CASE WHEN statusTypeID = 2 THEN 1 ELSE 0 END) AS done_count,
                                (SELECT COUNT(*) FROM joratedtable) AS rated_count
                            FROM jobOrderDetailsTable";
                $sqlResult1 = mysqli_query($result[0], $query1);
        
                if ($sqlResult1->num_rows > 0 ) {
                    // Fetch the count result
                    $row = $sqlResult1->fetch_assoc();
                    $incoming_count = $row["incoming_count"];
                    $ongoing_count = $row["ongoing_count"];
                    $wfm_count = $row["wfm_count"];
                    $outsource_count = $row["outsource_count"];
                    $done_count = $row["done_count"];

                    $total_count = $incoming_count + $ongoing_count + $wfm_count + $outsource_count + $done_count;
                }

            ?>
            
            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="dashboard-content">
                        <!-- #### Row boxes #### -->
                        <div class="boxes">
                            <a href="joIncoming.php" class="box box1" style="color: black;">
                                <div class="statusheader">
                                    <i class="fa-solid fa-file-arrow-up" style="color: #2BCEFE;"></i>
                                    <span class="text">Incoming</span>
                                </div>
                                <div class="statusnumber">
                                    <span class="number">
                                        <?php echo $incoming_count;?>
                                    </span>
                                    <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                        <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                        <span style="font-size: 25px; color: #2BCEFE;"><?php echo round(($incoming_count / $total_count) * 100)."%";?></span>
                                    </div>
                                </div>
                            </a>
                            <a href="joApproved.php" class="box box2" style="color: black;">
                                <div class="statusheader">
                                    <i class="fa-solid fa-clipboard" style="color: #45FF86;"></i>
                                    <span class="text">Ongoing</span>
                                </div>
                                <div class="statusnumber">
                                    <span class="number">
                                        <?php echo $ongoing_count;?>
                                    </span>
                                    <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                        <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                        <span style="font-size: 25px; color: #45FF86;"><?php echo round(($ongoing_count / $total_count) * 100)."%";?></span>
                                    </div>
                                </div>
                            </a>
                            <a href="joApproved.php" class="box box3">
                                <div class="statusheader">
                                    <i class="fa-solid fa-clock-rotate-left" style="color: #9AFC44;"></i>
                                    <span class="text"  style="color: black;">Waiting For Materials</span>
                                </div>
                                <div class="statusnumber">
                                    <span class="number">
                                        <?php echo $wfm_count;?>
                                    </span>
                                    <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                        <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                        <span style="font-size: 25px; color: #9AFC44;"><?php echo round(($wfm_count / $total_count) * 100)."%";?></span>
                                    </div>
                                </div>
                            </a>
                            <a href="joApproved.php" class="box box4" style="color: black;">
                                <div class="statusheader">
                                    <i class="fa-solid fa-right-from-bracket" style="color: #FFF838;"></i>
                                    <span class="text">Outsource</span>
                                </div>
                                <div class="statusnumber">
                                    <span class="number">
                                        <?php echo $outsource_count;?>
                                    </span>
                                    <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                        <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                        <span style="font-size: 25px; color: #FFF838;"><?php echo round(($outsource_count / $total_count) * 100)."%";?></span>
                                    </div>
                                </div>
                            </a>
                            <a href="joDone.php" class="box box5" style="color: black;">
                                <div class="statusheader">
                                    <i class="fa-solid fa-square-check" style="color: #FF9D35;"></i>
                                    <span class="text">Done</span>
                                </div>
                                <div class="statusnumber">
                                    <span class="number">
                                        <?php echo $done_count;?>
                                    </span>
                                    <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                        <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                        <span style="font-size: 25px; color: #FF9D35;"><?php echo round(($done_count / $total_count) * 100)."%";?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- ############################# -->
                        <div class="calculateDone">
                            <div class="filter-container">
                                <div class="filter-date">
                                    <span style="display: flex; flex-direction: row; align-items: flex-end; font-weight: bold; font-size: 16px; text-align: left; width: 100%;">OTHER REPORT</span>
                                </div>
                            </div>
                            <div class="calculateDone-content">
                                <div class="c-box1" style="border-right: 1px solid rgb(120, 120, 120, 0.3); ">
                                    <div class="c_box_header1">
                                        <span class="text " style="color: purple;">RATED</span>
                                    </div>
                                    <div class="c_box_content1" >
                                        <span class="text" id = "ratedjo" style="width: 100%;"></span>
                                    </div>
                                    <div class="c_box_header2">
                                        <span class="text">Score Averages</span>
                                    </div>
                                    <div class="c_box_content" style = "display: flex; flex-direction: column;">

                                        <div class="innercontent" >
                                            <span class="text" style="width:50%; color: purple; font-weight: bold; font-style: italic;">Timeliness: </span><span class="text" id="avgTimeliness" style="width:50%"> </span>
                                        </div>
                                        <div class="innercontent">
                                            <span class="text" style="width:50%; color: purple; font-weight: bold; font-style: italic;">Accuracy: </span><span class="text" id="avgAccuracy" style="width:50%"></span>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="c-box2" style="border-right: 1px solid rgb(120, 120, 120, 0.3)">
                                    <div class="c_box_header1" >
                                        <span class="text" style="color: purple;">DONE</span>
                                    </div>
                                    <div class="c_box_content" style = "display:flex; justify-content: center; align-items:center; height: 150px">
                                        <span class="text" id="donejo_text" style="font-size: 50px; margin-bottom: 50px;"></span>
                                    </div>
                                    <div class="c_box_header2" style="padding-bottom: 20px;height: auto; border: none; font-size: 10px; font-style: italic; font-weight: bold;">
                                        <span class="text">*Based on requested date (excluding Admin's JO)</span>
                                    </div>
                                </div>
                                <div class="c-box3" style="padding-left: 10px; padding-right:10px;">
                                    <div class="c_box_header1" >
                                        <span style="color: purple;">WORK TIME REPORT (based on Done JOs)</span>
                                    </div>
                                    <div class="c_box_content" style="border-bottom: 1px solid rgb(120, 120, 120, 0.3); padding-bottom: 7px;">
                                        <div class="inner" style = "display: flex; flex-direction: row; justify-content: space-between;">
                                            <div class="innercontent" style="width: 25%; padding-left: 30px; border-right: 1px solid rgb(120, 120, 120, 0.3)">
                                                <span style="font-weight: bold; color: purple;">Status</span>
                                            </div>
                                            <div class="innercontent" style="width: 25%; border-right: 1px solid rgb(120, 120, 120, 0.3)">
                                                <span class="text" style="width: 100%; text-align: center; font-weight: bold; color: purple;">Count</span>
                                            </div>
                                            <div class="innercontent" style="width: 50%; text-align: center; ">
                                                <span  style="width: 100%; text-align: center; font-weight: bold; color: purple;">Average Time</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="c_box_content" style = "display: flex; flex-direction: column; height: 50%; gap: 10px; padding-top: 5px;">
                                        <div class="inner" style = "display: flex; flex-direction: row; justify-content: space-between;">
                                            <div class="innercontent" style="width: 25%; padding-left: 30px; border-right: 1px solid rgb(120, 120, 120, 0.3)">
                                                <span style="border-radius: 5px; font-style: italic; color: white; 
                                                background-color:blue;  padding-left: 7px;padding-right: 7px; padding-top: 3px; padding-bottom: 3px;">Ontime:</span>
                                            </div>
                                            <div class="innercontent" style="width: 25%; border-right: 1px solid rgb(120, 120, 120, 0.3); display: flex; justify-content: center;">
                                                <span class="text" id = "ontime" style="width: 70px; text-align: center;
                                                border-radius: 5px; color: white; 
                                                background-color:blue; padding: 3px;"></span>
                                            </div>
                                            <div class="innercontent" style="width: 50%; text-align: center; display: flex; justify-content: center; ">
                                                <span id= "ontime-leadtime" style="width: auto; text-align: center; border-radius: 5px; color: white; 
                                                background-color:blue; padding: 3px;"></span>
                                            </div>
                                        </div>
                                        <div class="inner" style = "display: flex; flex-direction: row; justify-content: space-between;">
                                            <div class="innercontent" style="width: 25%; padding-left: 30px; border-right: 1px solid rgb(120, 120, 120, 0.3)">
                                                <span style="border-radius: 5px; font-style: italic; color: white; 
                                                background-color:red; padding: 3px;">Overdue:</span>
                                            </div>
                                            <div class="innercontent"  style="width: 25%; border-right: 1px solid rgb(120, 120, 120, 0.3); display: flex; justify-content: center;">
                                                <span class="text" id = "overdue" style=" width: 70px; text-align: center;
                                                border-radius: 5px; color: white; 
                                                background-color:red; padding: 3px;"></span>
                                            </div>
                                            <div class="innercontent" style="width: 50%; text-align: center; display: flex; justify-content: center;">
                                                <span id= "overdue-leadtime" style="width: auto; text-align: center; border-radius: 5px; color: white; 
                                                background-color:red; padding: 3px;"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="c_box_header2" style="padding-bottom: 20px;height: auto; border: none; font-size: 10px; font-style: italic; font-weight: bold;">
                                        <span class="text">Average Time = (date needed - date finished)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- #### Lead time reports 1 #### -->
                        <div class="pie-chart">
                            <div class="piechartcontent" style ="height: 100%; width: 100%;">
                                <div class="filter-container">
                                    <div class="filter-date">
                                        <span style="display: flex; flex-direction: row; align-items: flex-end; font-weight: bold; font-size: 16px; text-align: left; width: 100%;">MONTHLY REPORT </span>
                                        <select name="filter-month" id="filter-month">
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                        
                                        <select name="filter-year" id="filter-year" class="">   
                                            <?php
                                                if (isset($result[0])) {
                                                    $conn = $result[0];
                                        
                                                    $query = "SELECT DISTINCT YEAR(dateRequested) AS year FROM jobOrderDetailsTable WHERE YEAR(dateRequested) != 0
                                                    UNION 
                                                    SELECT DISTINCT YEAR(dateServed) AS year FROM jobOrderDetailsTable WHERE YEAR(dateServed) != 0
                                                    UNION 
                                                    SELECT DISTINCT YEAR(dateFinished) AS year FROM jobOrderDetailsTable WHERE YEAR(dateFinished) != 0";
                                                    $query_run = mysqli_query($conn, $query);
                                        
                                                    if ($query_run) {
                                                        while ($row = mysqli_fetch_assoc($query_run)) {
                                                            echo "<option value='" . $row['year'] . "'>" . $row['year'] . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No years available</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>Database connection failed</option>";
                                                }
                                            ?>

                                        </select>
                                        <?php 
                                        if($role == "Admin Personnel"){
                                            ?>
                                            <button type="button" id = "printReport" style="width: 40px; background-color: purple; color:white; "><i class="fa-solid fa-print" ></i></button>
                                            <?php
                                        }
                                        
                                        ?>
                                    </div>
                                </div>
                                <div class="chart-details-container">
                                    <div class="chart-container" >
                                        <canvas class="myChart" ></canvas>
                                    </div>
                                    
                                    
                                </div>
                                <div class="chart-below">
                                    <div class="details">
                                        <ul>
                                            <li></li>
                                        </ul>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            <span  id='total-jo1'><i class='joreport' id='total-jo'>TOTAL JOB ORDER: </i></span>
                            
                            
                        </div>
                        <!-- ############################# -->
                        <!-- #### Lead time reports 2 #### -->
                        <div class="incoming-log">
                            <div class="bargraphcontainer" style ="height: 100%; width: 100%;">
                                <div class="filter-container">
                                    <div class="filter-date">
                                        <span style="display: flex; flex-direction: row; align-items: flex-end; font-weight: bold; font-size: 16px; text-align: left; width: 100%;">TOTAL SENT JOB ORDERS | Average Lead Time = (∑(date finished - date served) / Number of JOs)
</span>
                                    </div>
                                </div>
                                <div class="chart-container" style="width: 100%;">
                                    <canvas id="lineChart" style="margin-top: 50px; width: 100%; height: 100%; margin-left: 10px; margin-right: 10px;"></canvas>
                                </div>
                            </div>
                            <span  id='total-jo1' style="border-top: 1px rgb(120, 120, 120, 0.3) solid; margin-top: 50px;"><i class='totalleadtime' id='total-lead'>TOTAL JOB ORDER IN A MONTH: </i></span>
                        </div>
                        <!-- ############################# -->
                        
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
    <script src="Javascript/forDashboard.js"></script>
    <script>
        document.getElementById('printReport').addEventListener('click', function(event) {
                var month = document.getElementById("filter-month");
                var year = document.getElementById("filter-year");
                let newtab = window.open("PHP/printReportJO.php?month=" + month.value + "&year=" + year.value, true);

                if (newtab) {
                    newtab.focus();
                }
            });
    </script>
    <script>
        var totallead = document.getElementById("total-lead");

        function updateLeadTime() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.addEventListener("readystatechange", function(e) {
                if (this.readyState === 4 && this.status == 200) {
                    var responseObj = JSON.parse(this.responseText);
                    if(responseObj.average_lead_time == null){
                        totallead.innerHTML = "AVERAGE LEAD TIME: NO DATA AVAILABLE";
                    }else{
                        totallead.innerHTML = "AVERAGE LEAD TIME: " + responseObj.average_lead_time;
                    }
                }
            });

            xmlhttp.open("GET", "PHP/getLeadTime.php?month=" + filterMonth.value + "&year=" + filterYear.value, true);
            xmlhttp.send();
        }

        var xml = new XMLHttpRequest();
        var lineGraph;
        var chartData = {};

        var currentDate = new Date();
        var currentYear = currentDate.getFullYear();
        var currentMonth = currentDate.getMonth() + 1;

        var monthdisplay = document.getElementById("filter-month").options[document.getElementById("filter-month").selectedIndex].text;

        xml.addEventListener("readystatechange", function() {
            if (xml.readyState == 4 && xml.status == 200) {
                var monthData = JSON.parse(xml.responseText);

                chartData = {
                labels: monthData.month_days,
                datasets: [{
                        label: 'Total Sent JOs in a Day',
                        data: monthData.lead_time,
                        fill: false,
                        backgroundColor: 'purple', // Set the bar color to red
                        borderColor: 'purple', // Set the border color to red
                        tension: 0.1
                    }]
                };

                const config = {
                    type: 'bar',
                    data: chartData,
                    options: {
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    color: 'purple',
                                    text: 'DAYS OF THE MONTH'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    color: 'purple',
                                    text: 'NUMBER OF JOs'
                                }
                            }
                        }
                    }
                };

                const ctx = document.getElementById('lineChart').getContext('2d');
                lineGraph = new Chart(ctx, config);
            }
        });

        xml.open("GET", "PHP/getLeadTime.php?month=" + currentMonth + "&year=" + currentYear, true);
        xml.send();

        document.getElementById("filter-month").addEventListener("change", function() {
            var xml1 = new XMLHttpRequest();

            var monthValue = document.getElementById("filter-month").value;
            var yearValue = document.getElementById("filter-year").value;

            xml1.addEventListener("readystatechange", function() {
                if (this.readyState == 4 && this.status == 200) {
                    var dataObj = JSON.parse(this.responseText);

                    lineGraph.data.datasets[0].data = dataObj.lead_time;
                    lineGraph.data.labels = dataObj.month_days;

                    //lineGraph.options.scales.x.title.text = document.getElementById("filter-month").options[document.getElementById("filter-month").selectedIndex].innerHTML.toUpperCase() + " " + document.getElementById("filter-year").value;

                    lineGraph.update();
                }
            });

            xml1.open("GET", "PHP/getLeadTime.php?month=" + monthValue + "&year=" + yearValue, true);
            xml1.send();
        });

        document.getElementById("filter-year").addEventListener("change", function() {
            var xml2 = new XMLHttpRequest();

            var monthValue = document.getElementById("filter-month").value;
            var yearValue = document.getElementById("filter-year").value;

            xml2.addEventListener("readystatechange", function() {
                if (this.readyState == 4 && this.status == 200) {
                    var dataObj = JSON.parse(this.responseText);

                    chartData.datasets[0].data = dataObj.lead_time;
                    chartData.labels = dataObj.month_days;

                    lineGraph.update();
                }
            });

            xml2.open("GET", "PHP/getLeadTime.php?month=" + monthValue + "&year=" + yearValue, true);
            xml2.send();
        });

        function fetchTimeliness() {
            var xml3 = new XMLHttpRequest();
            xml3.addEventListener("readystatechange", function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(xml3.responseText);
                    if(!response.avg_timeliness && !response.avg_accuracy){
                        document.getElementById("avgTimeliness").innerText = "No Rated Job Order";
                        document.getElementById("avgTimeliness").style.fontSize = "12px";
                        document.getElementById("avgAccuracy").innerText = "No Rated Job Order";
                        document.getElementById("avgAccuracy").style.fontSize = "12px";
                    }else{
                        document.getElementById("avgTimeliness").innerText = response.avg_timeliness;
                        document.getElementById("avgAccuracy").innerText = response.avg_accuracy;
                        document.getElementById("avgTimeliness").style.fontSize = "14px";
                        document.getElementById("avgAccuracy").style.fontSize = "14px";
                    }
                    document.getElementById("ratedjo").innerText = response.ratedjo;
                    document.getElementById("donejo_text").innerText = response.donejo;
                    document.getElementById("overdue").innerText = response.status_count["Overdue"].count;
                    document.getElementById("ontime").innerText = response.status_count["On_Time"].count;
                    document.getElementById("ontime-leadtime").innerText = response.status_count["On_Time"].averageDaysDifference;
                    document.getElementById("overdue-leadtime").innerText = response.status_count["Overdue"].averageDaysDifference;
                }
            });

            xml3.open('GET', 'PHP/getLeadTime.php?month=' + filterMonth.value + '&year='+ filterYear.value, true);
            xml3.send();
        }

        window.onload = function() {
            fetchTimeliness();

            filterMonth.addEventListener('change', function() {
                fetchTimeliness();
            });

            filterYear.addEventListener('change', function() {
                fetchTimeliness();
            });
        };

        
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </body>
</html>