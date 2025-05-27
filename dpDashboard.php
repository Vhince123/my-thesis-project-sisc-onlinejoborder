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

    $department = $_SESSION["department"];

    if ($role == '0'){
        $role = "Administrator";
    }
    else if ($role == '1'){
        $role = "Administration Staff";
    }
    else {
        $role = "Department Head";
    }

    if ($role !== 'Department Head') {
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

        .content .boxes-container-row .box{
            display: flex;
            border-radius: 7px;
            border: rgba(0, 0, 0, 0.2) 1px solid;
            padding: 15px 20px;
            width: calc(100% / 5 - 15px);
            flex-direction: column;
            align-items: center;
            box-shadow:2px 2px 4px rgba(0, 0, 0, 0.3);
            height: 140px;
            width: 100%;
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
            transform: scale(1.01);
            transition: all 0.3s ease;
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


        .title-control{
            display: flex;
            width: 100%;
            background-color: transparent;
            justify-content: center;
            padding-top: 10px;
            padding: 10px;
            border-bottom: 1px rgb(120, 120, 120, 0.3) solid;
        }

        .activity-title{
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            gap: 5px;
            width: 100%;
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
            padding-top: 50px;
            display: grid;
            grid-template-columns: 100%;
            grid-gap: 10px;
        }

        .boxes-container{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            gap: 10px;
        }

        .boxes-container-row{
            width: 100%;
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
                    <li class="sidebar-header">
                        Job Orders
                    </li>
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'dpjoEndorse.php'" class="sidebar-link" data-bs-target="#approvals" 
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
                    SUM(CASE WHEN a.statusTypeID = 6 THEN 1 ELSE 0 END) AS incoming_count,
                    SUM(CASE WHEN a.statusTypeID = 1 THEN 1 ELSE 0 END) AS ongoing_count,
                    SUM(CASE WHEN a.statusTypeID = 4 THEN 1 ELSE 0 END) AS wfm_count,
                    SUM(CASE WHEN a.statusTypeID = 5 THEN 1 ELSE 0 END) AS outsource_count,
                    SUM(CASE WHEN a.statusTypeID = 2 THEN 1 ELSE 0 END) AS done_count,
                    (SELECT COUNT(*) FROM joratedtable WHERE b.department = '$department') AS rated_count
                    FROM joborderdetailstable a 
                    JOIN userdetailstable b ON a.userID = b.userID
                    WHERE  b.department = '$department'";

                $sqlResult1 = mysqli_query($result[0], $query1);
        
                if ($sqlResult1->num_rows > 0 ) {
                    $row = $sqlResult1->fetch_assoc();
                    $incoming_count = $row["incoming_count"];
                    $ongoing_count = $row["ongoing_count"];
                    $wfm_count = $row["wfm_count"];
                    $outsource_count = $row["outsource_count"];
                    $rated_count = $row["rated_count"];
                    $done_count = abs($row["done_count"] - $rated_count);
                    
                    $total_count = $incoming_count + $ongoing_count + $wfm_count + $outsource_count + $done_count + $rated_count;
                }

            ?>
            
            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="dashboard-content">
                        <div class="boxes-container">
                            <div class="boxes-container-row">
                                <a class="box box1" style="color: black;">
                                    <div class="statusheader">
                                        <i class="fa-solid fa-file-arrow-up" style="color: #2BCEFE;"></i>
                                        <span class="text">Incoming</span>
                                    </div>
                                    <div class="statusnumber">
                                        <span class="number">
                                            <?php 
                                                if($incoming_count == 0){
                                                    echo "0";
                                                }
                                                else
                                                {
                                                    echo $incoming_count;
                                                }?>
                                        </span>
                                        <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                            <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                            <span style="font-size: 25px; color: #2BCEFE;"><?php 
                                            
                                            if($incoming_count == 0){
                                                echo "0%";
                                            }
                                            else
                                            {
                                                echo round(($incoming_count / $total_count) * 100)."%";
                                            }?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="boxes-container-row">
                                <a class="box box2" style="color: black;">
                                    <div class="statusheader">
                                        <i class="fa-solid fa-clipboard" style="color: #45FF86;"></i>
                                        <span class="text">Ongoing</span>
                                    </div>
                                    <div class="statusnumber">
                                        <span class="number">
                                            <?php 
                                            if($ongoing_count == 0){
                                                echo "0";
                                            }
                                            else
                                            {
                                                echo $ongoing_count;
                                            }?>
                                        </span>
                                        <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                            <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                            <span style="font-size: 25px; color: #45FF86;"><?php 
                                            
                                            if($ongoing_count == 0){
                                                echo "0%";
                                            }
                                            else
                                            {
                                                echo round(($ongoing_count / $total_count) * 100)."%";
                                            }?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="boxes-container">
                            <div class="boxes-container-row">
                                <a class="box box3">
                                    <div class="statusheader">
                                        <i class="fa-solid fa-clock-rotate-left" style="color: #9AFC44;"></i>
                                        <span class="text"  style="color: black;">Waiting For Materials</span>
                                    </div>
                                    <div class="statusnumber">
                                        <span class="number">
                                            <?php 
                                            if($wfm_count == 0){
                                                echo "0";
                                            }
                                            else
                                            {
                                                echo $wfm_count;
                                            }?>
                                        </span>
                                        <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                            <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                            <span style="font-size: 25px; color: #9AFC44;"><?php 
                                            
                                            if($wfm_count == 0){
                                                echo "0%";
                                            }
                                            else
                                            {
                                                echo round(($wfm_count / $total_count) * 100)."%";
                                            }?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="boxes-container-row">
                                <a class="box box4" style="color: black;">
                                    <div class="statusheader">
                                        <i class="fa-solid fa-right-from-bracket" style="color: #FFF838;"></i>
                                        <span class="text">Outsource</span>
                                    </div>
                                    <div class="statusnumber">
                                        <span class="number">
                                            <?php 
                                            if($outsource_count == 0){
                                                echo "0";
                                            }
                                            else
                                            {
                                                echo $outsource_count;
                                            }?>
                                        </span>
                                        <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                            <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                            <span style="font-size: 25px; color: #FFF838;"><?php 
                                            
                                            if($outsource_count == 0){
                                                echo "0%";
                                            }
                                            else
                                            {
                                                echo round(($outsource_count / $total_count) * 100)."%";
                                            }?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="boxes-container">
                            <div class="boxes-container-row">
                                <a class="box box5" style="color: black;">
                                    <div class="statusheader">
                                        <i class="fa-solid fa-square-check" style="color: #FF9D35;"></i>
                                        <span class="text">Done</span>
                                    </div>
                                    <div class="statusnumber">
                                        <span class="number">
                                            <?php 
                                            if($done_count == 0){
                                                echo "0";
                                            }
                                            else
                                            {
                                                echo $done_count;
                                            }?>
                                        </span>
                                        <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                            <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                            <span style="font-size: 25px; color: #FF9D35;"><?php 
                                            
                                            if($done_count == 0){
                                                echo "0%";
                                            }
                                            else
                                            {
                                                echo round(($done_count / $total_count) * 100)."%";
                                            }?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="boxes-container-row">
                                <a class="box box6" style="color: black;">
                                    <div class="statusheader">
                                        <i class="fa-solid fa-star" style="color: purple;"></i>
                                        <span class="text">Rated</span>
                                    </div>
                                    <div class="statusnumber">
                                        <span class="number">
                                            <?php 
                                                if($rated_count == 0){
                                                    echo "0";
                                                }
                                                else
                                                {
                                                    echo $rated_count;
                                                }?>
                                        </span>
                                        <div class="statuspercent" style="width: 100%; display: flex; flex-direction: row; align-items: flex-end;">
                                            <span style="width: 100%; color: rgb(120,120,120)">Total %:</span>
                                            <span style="font-size: 25px; color: purple;"><?php 
                                            
                                            if($rated_count == 0){
                                                echo "0%";
                                            }
                                            else
                                            {
                                                echo round(($rated_count / $total_count) * 100)."%";
                                            }?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
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
    </body>
</html>