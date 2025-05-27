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

    <title>Rated JOs</title>
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

<div id="viewratedmodal">
    <div class="modal1">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Job Order Details</h5>
            <button type="button" class="btn-close close-viewratedmodal" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary close-viewratedmodal" data-bs-dismiss="modal"> Close</button>
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
                        <a href="#" onclick="location.href = 'joDone.php'" class="sidebar-link">Done</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" onclick="location.href = 'joRated.php'" class="sidebar-link" style="text-decoration: underline;">Rated</a>
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
            <div class="page-title" style="align-items: center; font-size: 20px;">Rated Job Orders
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
            
            <div class="filterForm">
                <label for="month">Select Month:</label>
                <select class="filterDate" id="month">
                    <option value="all">All</option>
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<option value=\"$i\">" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>
                <label for="year">Select Year:</label>
                <select class="filterDate" id="year">
                    <?php
                    if (isset($result[0])) {
                        $conn = $result[0];
            
                        $query = "SELECT DISTINCT YEAR(dateRated) AS year FROM joratedtable";
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
            </div>
            
                <div class="container p-3 my-5 bg-light">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>JO No.</th>
                                <th>Accuracy</th>
                                <th>Timeliness</th>
                                <th>Date Rated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        
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
        <script>
            $(document).ready(function () {

                function loadFilteredData() {
                    var month = $('#month').val();
                    var year = $('#year').val();
                    $.ajax({
                        url: 'PHP/filterRated.php',
                        type: 'POST',
                        data: {
                            month: month,
                            year: year
                        },
                        dataType: 'json',
                        success: function (response) {
                            var table = $('#example').DataTable({
                                "destroy": true,
                                "data": response,
                                "columns": [
                                    { "data": "jobOrderNumber" },
                                    { "data": "accuracy" },
                                    { "data": "timeliness" },
                                    { "data": "dateRated" },
                                    {
                                        "data": "jobOrderID",
                                        "render": function (data, type, row, meta) {
                                            return '<button type="button" class="btn btn-success open-viewratedmodal" data-ratedjoid="' + data + '" onclick="openModal(' + data + ')">View</button>';
                                        }
                                    }
                                ],
                                "paging": true,
                                "pageLength": 10,
                                "ordering": false
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }

                var currentDate = new Date();
                var currentMonth = currentDate.getMonth() + 1; 
                var currentYear = currentDate.getFullYear();

                $('#month').val(currentMonth);
                $('#year').val(currentYear);

                loadFilteredData();

                $('#month, #year').change(function () {
                    loadFilteredData();
                });
                });
        </script>
        <script src="Javascript/viewRatedJO.js"></script>
    </body>
</html>