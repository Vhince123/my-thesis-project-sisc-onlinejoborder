<?php
    include('connect.php');  
    session_start();
    $result = Connection();

    $error = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $username = $_POST['user'];  
        $password = $_POST['pass'];
        $today = date("Y-m-d H:i:s");

        $query = "SELECT * FROM userdetailstable WHERE BINARY userID = '$username' AND BINARY password = '$password'";
        $sqlResult = mysqli_query($result[0], $query); 

        if($sqlResult->num_rows == 1){
            while($row = $sqlResult->fetch_assoc()){
                $_SESSION["userid"] = $row["userID"];
                $_SESSION["firstname"] = $row["firstName"];
                $_SESSION["middlename"] = $row["middleName"];
                $_SESSION["lastname"] = $row["lastName"];
                $_SESSION["department"] = $row["department"];
                $_SESSION["userType"] = $row["userType"];
                $_SESSION["password"] = $row["password"];
                $_SESSION["email"] = $row["email"];
                $userType = $_SESSION["userType"];

            }
            
            if ($userType == '1' || $userType == '4' || $userType == '0') {
                $query2 = "INSERT INTO loginlogtable (loginTime, logoutTime, userID) VALUES ('$today', NULL, '$username')";
                
                if (!mysqli_query($result[0], $query2)) {
                    $error = "Check query: " . mysqli_error($result[0]);
                } else {

                    $_SESSION["loginLogID"] = mysqli_insert_id($result[0]);
                    
                    if($userType == '1' || $userType == '0'){
                        $location = "dashboard.php";
                    }
                    else if ($userType == '4'){
                        $location = "dpDashboard.php";
                    }
                    header("Location: http://localhost/Thesis/$location");
                    exit();
                }
            } else {
                $error = "Login Details not Applicable for Web!";
            }
        } else {
            $error = "Username or Password is incorrect";
        }
    }

    if (!empty($error)) {
        echo '<script>alert("' . $error . '")</script>'; 
        echo '<script>window.location.href = "http://localhost/Thesis/index.php";</script>';
        exit();
    }
?>  
