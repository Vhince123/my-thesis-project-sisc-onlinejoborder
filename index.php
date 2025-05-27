<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="forlogin/fonts/icomoon/style.css">

    <link rel="stylesheet" href="forlogin/css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="forlogin/css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="forlogin/css/style.css">

    <title>Login</title>
    <link rel="icon" href= "Images/sisc.png" type="image/x-icon"/>
  </head>
  <body>
  

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('Images/sisc-01.jpg');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container" style="background-color: none; ">
        <div class="row align-items-center justify-content-center">
          <div class="">
            <h3>Welcome to <strong>Online Job Order</strong></h3>
            <form name="f1" action = "PHP/login.php" onsubmit = "return validation()" method = "POST">
              <div class="form-group first">
                <label for="username">Username</label>
                <input type="text" class="form-control" id ="user" name  = "user"  placeholder="Username" autocomplete="off" style="background-color: transparent;">
              </div>
              <div class="form-group last mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" id ="pass" name  = "pass" placeholder="Password" autocomplete="off" style="background-color: transparent;">
              </div>
              <div class="d-flex mb-5 align-items-center">
                <label class="control control--checkbox mb-0"><span class="caption">Show Password</span>
                  <input type="checkbox" id="showPasswordCheckbox"/>
                  <div class="control__indicator"></div>
                </label>
              </div>

              <input type="submit" value="Log In" class="btn btn-block btn-primary" style="background-color: purple; border: none;">

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>  
        function validation()  
        {  
            var id=document.f1.user.value;  
            var ps=document.f1.pass.value;  
            if(id.length=="" && ps.length=="") {  
                alert("Username and Password fields are empty");  
                return false;  
            }  
            else  
            {  
                if(id.length=="") {  
                    alert("User Name is empty");  
                    return false;  
                }   
                if (ps.length=="") {  
                alert("Password field is empty");  
                return false;  
                }  
            }                             
        }  
    </script> 
    <script>
    var passwordInput = document.getElementById("pass");
    var showPasswordCheckbox = document.getElementById("showPasswordCheckbox");

    showPasswordCheckbox.addEventListener("change", function() {
        passwordInput.type = this.checked ? "text" : "password";
    });
</script>
  </body>
</html>