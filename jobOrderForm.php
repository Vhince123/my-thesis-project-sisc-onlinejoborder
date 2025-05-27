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

    if ($role !== 'Admin Personnel') {
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/016938eade.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="CSS/onlineJO-sidebar.css" />
    <link rel="stylesheet" href="CSS/modal.css" />


    <title>Job Order Form</title>
    <link rel="icon" href= "Images/sisc.png" type="image/x-icon"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <style>
        #massAdd, #insertP {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            justify-content: center;
            padding: 20px;
            width: 100%;
            height: 200px;
            border: 2px dashed #212121;
        }

        :is(#massAdd, #insertP):hover {
            background-color: #b8b8b8;
        }

        :is(#massAdd, #insertP) p {
            font-size: 25px;
        }

        :is(#massAdd, #insertP) input {
            width: auto;
        }

        .formTab{
            background-color: rgba(255, 255, 255, 0.5); 
            border-radius: 40px; 
            width: 70%; 
            border: 1px solid black; 
            flex-direction: column; 
            align-items: center; 
            overflow: hidden;
        }

        .formTab .contents-modal label{
            font-size: 20px;
        }

        .formTab h3, .formTab h6{
            color: black;
        }

        .formTab .contents-modal input{
            padding: 5px;
        }

        #form-container1{
            width: 100%; 
            flex-direction: column;
        }

        #location1{
            color: black;
            border: 1px solid black;
            border-radius: 5px;
            padding-left: 5px;
            box-shadow: 2px 2px 4px rgba(118, 12, 224, 0.545);
            overflow: auto;
        }

        .formTab #dateRequested{
            font-size: 20px;
        }

        .joform-header{
            flex-direction: column;
            align-items: center;
            background-color: purple;
            padding: 10px;
        }
        .joform-header h3{
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, .545);
        }

        .formTab label{
            font-weight: none;
        }

        .modal-body label{
            font-family: 'Poppins';
            background-color: transparent;
            color: black;
            font-weight: bold;
        }

        #form-container1 h6{
            padding-top: 20px;
        }
    </style>



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
            <li class="sidebar-item">
                <a href="#" style="text-decoration: underline;" class="sidebar-link">
                    <i class="fa-solid fa-file-circle-plus pe-2"></i>
                    Job Order Form
                </a>
            </li>
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
    <nav class="navbar navbar-expand px-3 border-bottom">
        <button class="btn" id="sidebar-toggle" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse">
            <div class="page-title" style="align-items: center; font-size: 20px;">Job Order Form
            </div>
            <div class="for-logout">
                <div class="namedisplay"><button type="button" class="profiledata open-profilemodal"><?php echo $name . " / " . $role;?></button></div>
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post"><input type="submit" id=logout class="logout" name="logout" value="Logout"></form>
            </div>
        </div>
    </nav>

    
    <main class="content px-3 py-2">
        <div class="container-fluid">
            <div class="mb-3" style="font-size: 22px; padding-top: 20px;"></div>
                <div class="container">
                    <div class="formTab">
                        <form id="form-container1" action="PHP/addJORequest.php" method="post" enctype="multipart/form-data" >
                            <div class="joform-header">
                                <h3 style="font-weight: bold; text-align: center;">Online Job Order Request Form</h3>
                            </div>
                            <h6 style=" text-align: center;">Please input the necessary details for your job order request.</h6>
                            <div class="modal-body" style="padding-inline: 20px;">
                                <div class="contents-modal current">
                                    <label>Date of Request:</label>
                                    <span id="dateRequested" name="dateRequested"></span>
                                </div>

                                <!-- </label> -->

                                <div class="contents-modal">
                                    <label>Date Needed:</label>
                                    <input type="date" id="dateNeeded" name="dateNeeded" required>
                                </div>
                                <div class="contents-modal">
                                    <label>Job Order Description:</label>
                                    <textarea style="width: 100%"; type="text" name="jobOrderDescription" id="jobOrderDescription" placeholder="" required></textarea>
                                </div>
                                <div class="contents-modal">
                                    <label>Location:</label>
                                    <div style="display: flex; flex-direction: row; gap: 10px; margin: none;">
                                        <select id="location1" name="location1" style="width: 30%;">
                                            <option value="Luxemburg">Luxemburg</option>
                                            <option value="Tropical">Tropical</option>
                                            <option value="Munich">Munich</option>
                                            <option value="College">College</option>
                                            <option value="others">Others</option>
                                        </select>
                                        <input type="text" id="otherLocation" name="otherLocation"  style="display: none;">
                                        <input style="width: 100%;" type="text" name="location" id="location" placeholder="" required>
                                    </div>
                                </div>
                                <label id="insertP">
                                    <p>Photo:</p>
                                    <input style="border:none; box-shadow: none;" type="file" name="insertPhoto[]" accept=".jpeg, .jpg, .png" multiple> 
                                </label>
                                <div class="button-group">
                                    <button class = "btn btn-yes" id="create-submit" name="add-submit" type="submit">SUBMIT</button>
                                </div>
                            </div>
                        </form>
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
                
        <script src="Javascript/realtime.js"></script>
        <script src="Javascript/restrictPastDate.js"></script>

        <script>
            var mainloc = document.getElementById("location1");
            var otherLocation = document.getElementById("otherLocation");

            mainloc.addEventListener("change", function(){
                var mOption = mainloc.value;
                if(mOption == "others"){
                    otherLocation.style = "display: block;";
                    otherLocation.setAttribute("required", "true");
                }
                else{
                    otherLocation.style = "display: none;";
                    otherLocation.removeAttribute("required");
                }
            });
        </script>
    </body>
</html>