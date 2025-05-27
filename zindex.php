<!DOCTYPE html>
<html lang="en" dir="ltr">
<link rel="stylesheet" href="CSS/login.css" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome to Online Job Order</title>    
</head>
<body>  

<div style="width: 100%; background-color: #512173; color: white; height: 70px; padding-left: 30px; display:flex; align-items: center;">
    <h2>Online Job Order System</h2>
</div>



    <section>
        <img src="Images/sisc-01.jpg" class="bg">
        <div class="login">
            <form name="f1" action = "PHP/login.php" onsubmit = "return validation()" method = "POST">
                <div class="inputBox">
                    <input type = "text" id ="user" name  = "user"  placeholder="Username" autocomplete="off"/> 
                </div>
                <div class="inputBox">
                    <input type = "password" id ="pass" name  = "pass" placeholder="Password" autocomplete="off" />  
                </div>
                <div class="inputBox">
                    <input type="submit" value="Login" id="btn">
                </div>
            </form>
        </div>
    </section>
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
        
    </script>
</body>    

