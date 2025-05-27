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

    <title>View Users</title>
    <link rel="icon" href= "Images/sisc.png" type="image/x-icon"/>
    
    </head>

    <style>
    .content-contents-modal input[type="checkbox"] {
        margin-left: 10px;
        height: 100%;
        box-shadow: none;
    }

    .content-contents-modal{
        display: flex;
        flex-direction: row;
    }

    #showPasswordText {
    display: none;
    position: absolute; /* Position the message near the checkbox */
    top: -20px; /* Adjust positioning as needed */
    left: 110%; /* Position to the right of the checkbox */
    padding: 5px;
    background-color: #f1f1f1; /* Light background color */
    border: 1px solid #ddd; /* Border for the message */
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
                        <input type="email" name="emailad" id="emailad" value="<?php echo $email; ?>" readonly>
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

<!-- View Modal -->
<div id="modal">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title">View Profile</h5>
            <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div  id="view-joform">
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="fName">First Name: </label>
                        <input type="text" id="fName" name = "fName" oninput="validateInput(this)"/>
                    </div>
                    <div class="contents-modal">
                        <label for="mName" >Middle Name: </label>
                        <input type="text" id="mName" name = "mName" oninput="validateInput(this)"/>
                    </div>
                    <div class="contents-modal">
                        <label for="lName" >Last Name: </label>
                        <input type="text" id="lName" name = "lName" oninput="validateInput(this)"/>
                    </div>
                </div>
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="dept" >Department: </label>
                        <input type="text" id="dept" name = "dept" oninput="validateInput(this)"/>
                    </div>
                    <div class="contents-modal">
                        <label for="uEmail" >Email: </label>
                        <input type="email" id="uEmail" name="uEmail" onblur="validateEmail()">
                        <div id="email-error" style="color: red; width: auto; font-size: 10px; height: 10px;"></div>
                    </div>
                </div>
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="uName" >Username: </label>
                        <input type="text" id="uName" name = "uName"  readonly/>
                    </div>
                    <div class="contents-modal" >
                        <label for="pword" >Password: </label>
                        <div class="content-contents-modal">
                            <input type="password" id="pword" name = "pword" style="width: 100%;"/>
                            <input type="checkbox" id="showPasswordCheckbox" onchange="togglePasswordVisibility()" style="height:100%;" title="Show Password">
                        </div>
                    </div>
                </div>
                <div class="row-inputs">
                    <div class="contents-modal">
                        <label for="dateC" >Date Created: </label>
                        <input type="text" id="dateC" name = "dateC" readonly/>
                    </div>
                    <div class="contents-modal">
                        <label for="uType" >User Type: </label>
                        <input type="text" id="uType" name = "uType" readonly/>
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-secondary btn-update" id = "update-data" name = "update-data">Update</button>
                    <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ################################################################################################################################### -->

<!--- Delete Modal --->

<!-- Modal -->
<div id="deletemodal">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Delete User</h5>
            <button type="button" class="btn-close close-deletemodal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="PHP/deleteUserDetails.php" method="POST">
            <div class = "modal-body">
                <input type="hidden" name="deleteuserid" id="deleteuserid">

                <h4>Do you want to delete this user?</h4>
            </div>
            <div class="button-group">
                <button type="submit" name="delete-data" class="btn btn-yes">YES</button>
                <button type="button" class="btn close-deletemodal" data-dismiss = "modal">NO</button>
            </div>
        </form>
    </div>
</div>

<!-- ################################################################################################################################### -->




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
                                <a href="#" onclick="location.href = 'adminUserView.php'" class="sidebar-link" style="text-decoration: underline;">View Users</a>
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
                    <div class="page-title" style="align-items: center; font-size: 20px;">View Users
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
                        <div class="container p-3 bg-light ">
                            <table id="example" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Date Created</th>
                                        <th scope="col">User Type</th>
                                        <th scope="col">Update / View</th>
                                        <th scope="col">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        if (isset($result[0])) {
                                            $conn = $result[0];
                                            if($role == "Administrator"){
                                            $query = "SELECT * FROM userDetailsTable WHERE userArchieve = '0' ORDER BY dateCreated DESC";
                                            }
                                            else{
                                                $query = "SELECT * FROM userDetailsTable WHERE userArchieve = '0' AND userType != 0 ORDER BY dateCreated DESC";
                                            }
                                            
                                            $query_run = mysqli_query($conn, $query);

                                        if(mysqli_num_rows($query_run) > 0){
                                            foreach($query_run as $user)
                                            {
                                                if($user["userType"] != 0){
                                                    $middleInitial = !empty($user['middleName']) ? strtoupper(substr($user['middleName'], 0, 1)) . '.' : '';
                                                    $fullName = $user['lastName'] . ', ' . $user['firstName'] . ' ' . $middleInitial;
                                                }else{
                                                    $fullName = $user['firstName'];
                                                }
                                                

                                                if($user['userType'] == "1"){
                                                    $user['userType'] = "Admin Personnel";

                                                }
                                                else if($user['userType'] == "2"){
                                                    $user['userType'] = "Requisitioner";
                                                }
                                                else if($user['userType'] == "3"){
                                                    $user['userType'] = "Maintenance Staff";
                                                }
                                                else if($user['userType'] == "0"){
                                                    $user['userType'] = "Administrator";
                                                }else{
                                                    $user['userType'] = "Department Head";
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo $fullName; ?></td>
                                                    <td><?php echo $user['department']; ?></td>
                                                    <td><?php echo $user['userID'];?>
                                                    <td><?php echo $user['email'];?>
                                                    <td><?php echo $user['dateCreated'];?>
                                                    <td><?php echo $user['userType'];?>
                                                    <td>
                                                        <button type="button" class="btn view-btn open-modal" data-userid="<?php echo $user['userID'];?>">Update</button>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn delete-btn open-deletemodal" data-userid="<?php echo $user['userID'];?>">Delete</button>
                                                    </td>
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
    <script src="Javascript/viewUserDetails.js"></script>
    <script src="Javascript/deleteUser.js"></script>
    <script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("pword");
        var checkbox = document.getElementById("showPasswordCheckbox");
        
        if (checkbox.checked) {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    }

    function validateInput(input) {
        if (/[^a-zA-Z\s]/.test(input.value)) {
            input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
            alert("Invalid input!");
        }
    }

    </script>
    
    <script>
        document.getElementById("update-data").addEventListener("click", function() {
            var email = document.getElementById("uEmail").value;
            var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            if (!emailRegex.test(email)) {
                document.getElementById("email-error").textContent = "Please enter a valid email address.";
                return;
            } else {
                document.getElementById("email-error").textContent = ""; 
            }

            var xhr = new XMLHttpRequest();
            xhr.addEventListener("readystatechange", function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    console.log(xhr.responseText);
                    if (response.Success) {
                        alert(response.Success); 
                        window.location.href = window.location.pathname + "?updatesuccess";
                    } else {
                        alert(response.Error1 || response.Error0);
                    }
                }
            });
            xhr.open("GET", "PHP/updateUserDetails.php?uName=" + uName.value + "&fName=" + 
                    fName.value +  "&mName=" + mName.value + "&lName=" + lName.value + "&dept="
                    + dept.value + "&pword=" + pword.value + "&uEmail=" + uEmail.value);
            xhr.send();
        });
    </script>
    <script>
        
        </script>
    </body>
    
</html>