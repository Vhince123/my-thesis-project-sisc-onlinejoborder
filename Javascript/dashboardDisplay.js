
    const sidebarToggle = document.querySelector("#sidebar-toggle");
    sidebarToggle.addEventListener("click",function(){
        document.querySelector("#sidebar").classList.toggle("collapsed");
    });

    document.getElementById("sidebar-toggle").addEventListener("click", function() {
        let mainWidth = document.getElementsByClassName("main")[0].style.width;

        if (mainWidth !== "100%") {
            document.getElementsByClassName("main")[0].style.width = "100%";
            document.getElementsByClassName("for-logout")[0].style.paddingRight = "20px";
        }
        else {
            document.getElementsByClassName("main")[0].style.width = "calc(100% - 264px)";
            document.getElementsByClassName("for-logout")[0].style.paddingRight = "280px";
        }
    });

    // FOR PROFILE VIEWING

    var openProfileModal = document.getElementsByClassName("open-profilemodal")[0];
    var profilemodal = document.getElementById("profilemodal");
    var closeProfileModal = document.getElementsByClassName("close-profilemodal");

    
    openProfileModal.addEventListener("click", function() {
        profilemodal.style.display = "block";
    });

    for (let x = 0; x < closeProfileModal.length; x++) {
        closeProfileModal[x].addEventListener("click", function(){
            profilemodal.style.display = "none";
        });
    }

    document.getElementById("updateprofile").addEventListener("click", function() {
        var xml1 = new XMLHttpRequest();
        var origpass = document.getElementById("origpass");
        var oldpass = document.getElementById("oldpass");
        var newpass = document.getElementById("newpass");
        var confirmpass = document.getElementById("confirmpass");

        if(origpass.value !== oldpass.value){
            alert("Old Password is Incorrect!");
        }
        else if(newpass.value !== confirmpass.value){
            alert("New password and confirmation password are not the same!");
        }
        else if(newpass.value == origpass.value){
            alert("New password is the same as old password!");
        }
        else if(newpass.value == "" || confirm.value =="" || oldpass == ""){
            alert("Please enter the necessary details for updating password!");
        }
        else{
            xml1.addEventListener("readystatechange", function() {
                if (xml1.readyState == 4 && xml1.status == 200) {
                    var objJSON = JSON.parse(xml1.responseText);

                    if (objJSON.status == 1) {
                        alert("Profile update success!");
                        window.location.href = window.location.pathname + "?updatesuccess";
                    }
                    else if (objJSON.status == 2) {
                        alert("Query Error");
                    } 
                }
            });

            xml1.open("GET", "PHP/updateProfile.php?newpass=" + encodeURIComponent(newpass.value));
            xml1.send();
        }
    });
