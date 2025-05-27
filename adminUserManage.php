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
    <link rel="stylesheet" href="CSS/modal.css" />
    <link rel="stylesheet" href="CSS/onlineJO-sidebar.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <title>Add User</title>
    <link rel="icon" href= "Images/sisc.png" type="image/x-icon"/>
    </head>
    
    <style>

        .regTab{
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            width: 100%;
        }

        #form-container1 label{
            font-style: italic;
        }

        #form-container1{
            display:grid;
            width: 1000px;
            height: auto;
            padding: 4vh;
            grid-template-rows: auto auto auto auto auto;
            grid-template-columns: repeat(6, 1fr);
            gap: 5px;
        }
        
        h3,
        .regtab-row-1,
        .regtab-row-2,
        .regtab-row-3 {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #form-container1 input,
        #form-container1 select{
            width: 100%;
            color: black;
            border: 1px solid black;
            border-radius: 5px;
            padding-left: 5px;
            box-shadow: 2px 2px 4px rgba(118, 12, 224, 0.545);
            overflow: auto;
            height: 40px;
        }

        .regtab-row-1 p,
        .regtab-row-2 p,
        .regtab-row-3 p {
            margin: 0; /* Remove margin */
        }


        #form-container1 h3 {
            grid-row: 1;
            grid-column: 1 / -1;
            text-align: center;
            border-bottom: 1px solid black;
            padding-bottom: 10px;
        }

        .regtab-row-1 {
            grid-row: 2;
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .regtab-row-2 {
            grid-row: 3;
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .regtab-row-3 {
            grid-row: 4;
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        #form-container1 button{
            grid-row: 5;
            grid-column: 1 / -1;
            margin-top: 20px;
            text-align: center;
            justify-content: center;
            width: 100%;
            height: 60px;
        }

        #form-container1 #upload-submit{
            background-color: purple;
            border: none;
            color: white;
            padding: 5px;
            border-radius: 5px;
        }
        
        #form-container1 #upload-submit:hover{
            box-shadow: 3px 3px 6px rgba(118, 12, 224, 1);
            transform: scale(1.01);
            transition: 0.3s ease;
        }

    </style>

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
                        <label for="fNameManage">First Name: </label>
                        <input type="text" id="fNameManage" name="fNameManage" value="<?php echo $first; ?>" readonly />
                    </div>
                    <div class="contents-modal">
                        <label for="mNameManage">Middle Name: </label>
                        <input type="text" id="mNameManage" name="mNameManage" value="<?php echo $middle; ?>" readonly/>
                    </div>
                    <div class="contents-modal">
                        <label for="lNameManage">Last Name: </label>
                        <input type="text" id="lNameManage" name="lNameManage" value="<?php echo $last; ?>" readonly/>
                    </div>
                </div>
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="deptManage" class="form-label">Department:</label>
                        <input type="text" name="deptManage" id="deptManage" value="<?php echo $department; ?>" readonly>
                    </div>
                    <div class="contents-modal">
                        <label for="usertypeManage" class="form-label">User Type:</label>
                        <input type="text" name="usertypeManage" id="usertypeManage" value="<?php echo $role; ?>" readonly>
                    </div>
                </div>
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="usernameManage" class="form-label">Username:</label>
                        <input type="text" name="usernameManage" id="usernameManage" value="<?php echo $username; ?>" readonly>
                    </div>
                    <div class="contents-modal">
                        <label for="emailadManage" class="form-label">Email Address:</label>
                        <input type="text" name="emailadManage" id="emailadManage" value="<?php echo $email; ?>" readonly>
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
                        <a href="#" style="text-decoration: none;">Online Job Order System</a>
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
                            aria-expanded="false" ><i class="fa-regular fa-user pe-2"></i>
                            Users
                        </a>
                        <ul id="users" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="#" onclick="location.href = 'adminUserManage.php'" class="sidebar-link" style="text-decoration: underline;">Add User</a>
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
            <nav class="navbar navbar-expand px-3 border-bottom">
                <button class="btn" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-collapse">
                    <div class="page-title" style="align-items: center; font-size: 20px;">Add User
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
                    <div class="regTab">
                        <form id="form-container1" action="PHP/addUser.php" method="post">
                            <h3>Add Users</h3>
                            <div class="regtab-row-1 ">
                                <label for="fName">
                                    <p>First Name: </p>
                                    <input type="text" name="fName" id="fName" placeholder="First Name..." oninput="validateInput(this)" required>
                                </label>
                                <label for="mName">
                                    <p >Middle Name: </p>
                                    <input type="text" name="mName" id="mName" oninput="validateInput(this)" placeholder="Middle Name...">
                                </label>
                                <label for="lName">
                                    <p>Last Name: </p>
                                    <input type="text" name="lName" id="lName" oninput="validateInput(this)" placeholder="Last Name..." required>
                                </label>
                            </div>
                            <div class="regtab-row-2">
                                <label for="email">
                                    <p>Email: </p>
                                    <input type="email" name="email" id="email" placeholder="Email..." required>
                                </label>
                                <label for="dept">
                                    <p>Department: </p>
                                    <select name="dept" id="dept" onchange="deptChange()">
                                        <option value="Administration">Administration</option>
                                        <option value="Registration">Registration</option>
                                        <option value="MIS">MIS</option>
                                        <option value="Human Resource">Human Resource</option>
                                        <option value="Purchasing">Purchasing</option>
                                        <option value="Stuffshop">Stuffshop</option>
                                    </select>
                                </label>
                            </div>
                            <div class="regtab-row-3">
                                <label for="userType">
                                    <p>User Type: </p>
                                    <select name="userType" id="userType" onchange="myFunction()">
                                        <option value="4" hidden id="dphead">Department Head</option>
                                        <option value="3" id="mstaff">Maintenance Staff</option>
                                        <option value="2" selected>Requisitioner</option>
                                        <option value="1" id = "adminoption">Admin Personnel</option>
                                        <option value="0" hidden>Administrator</option>
                                    </select>
                                </label>
                                <label for="userID">
                                    <p>USERNAME: </p>
                                    <input type="text" name="userID" id="userID" placeholder="REQ-XXX..." required readonly>
                                </label>
                                <label for="pwd">
                                    <p>Password: </p>
                                    <input type="password" name="pwd" id="pwd" placeholder="Password..." required>
                                </label>
                            </div>
                            <button id="upload-submit" name="add-submit" type="submit">ADD USER</button>
                        </form>
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
        <script>

            var x = document.getElementById("userType");
            var y = document.getElementById("userID");
            var dept = document.getElementById("dept");
            var adminoption = document.getElementById("adminoption");
            var mstaff = document.getElementById("mstaff");
            var dphead = document.getElementById("dphead");

            function myFunction() {
                console.log("Selected User Type value: ", x.options[x.selectedIndex].value);
                if(x.options[x.selectedIndex].value == 1){
                y.placeholder = "ADM-XXX...";
                }
                else if(x.options[x.selectedIndex].value== 2){
                y.placeholder = "REQ-XXX...";
                }
                else if(x.options[x.selectedIndex].value== 3){
                y.placeholder = "EMP-XXX...";
                dept.value = "Administration";
                }
                else if(x.options[x.selectedIndex].value== 4){
                y.placeholder = "DPH-XXX...";
                }
            }
            function deptChange(){
                if(dept.value == "Administration"){
                    adminoption.removeAttribute("hidden");
                    mstaff.removeAttribute("hidden");
                }
                else{
                    adminoption.setAttribute("hidden", true);
                    mstaff.setAttribute("hidden", true);
                    dphead.removeAttribute("hidden");
                    x.value = "2";
                    y.placeholder = "REQ-XXX...";    
                }
            }
            
            
        </script>
        <script>
            function validateForm() {
                var email = document.getElementById("email").value;
                var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                
                if (!emailRegex.test(email)) {
                    document.getElementById("email-error").textContent = "Please enter a valid email address.";
                    return false; 
                }
                return true; 
            }

            function validateInput(input) {
                if (/[^a-zA-Z\s]/.test(input.value)) {
                    input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
                    alert("Invalid input!");
                }
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="Javascript/dashboardDisplay.js"></script>

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    </body>
</html>