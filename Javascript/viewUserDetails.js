var openModal = document.getElementsByClassName("open-modal");
var modal = document.getElementById("modal");
var closeModal = document.getElementsByClassName("close-modal");
var fName = document.getElementById("fName");
var mName = document.getElementById("mName");
var lName = document.getElementById("lName");
var dept = document.getElementById("dept");
var uEmail = document.getElementById("uEmail");
var uName = document.getElementById("uName");
var pword = document.getElementById("pword");
var dateC = document.getElementById("dateC");
var uType = document.getElementById("uType");
var checkbox = document.getElementById("showPasswordCheckbox");

for (let x = 0; x < openModal.length; x++) {
    openModal[x].addEventListener("click", function() {
        document.getElementById("email-error").textContent = ""; 
        var xmlhttp = new XMLHttpRequest();
        var userID = this.getAttribute("data-userid");
        
        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var userDetails = JSON.parse(xmlhttp.responseText);
                
                fName.value = userDetails["firstName"] ? userDetails["firstName"] : "";
                fName.placeholder = userDetails["firstName"] ? "" : "---";

                mName.value = userDetails["middleName"] ? userDetails["middleName"] : "";
                mName.placeholder = userDetails["middleName"] ? "" : "---";

                lName.value = userDetails["lastName"] ? userDetails["lastName"] : "";
                lName.placeholder = userDetails["lastName"] ? "" : "---";

                dept.value = userDetails["department"] ? userDetails["department"] : "";
                dept.placeholder = userDetails["department"] ? "" : "---";

                uEmail.value = userDetails["email"] ? userDetails["email"] : "";
                uEmail.placeholder = userDetails["email"] ? "" : "---";
                
                uName.value = userDetails["userID"];
                pword.value = userDetails["password"];
                dateC.value = userDetails["dateCreated"];
                uType.value = userDetails["userType"];

                if(uType.value == "1"){
                    uType.value  = "Admin Personnel";
                }
                else if(uType.value == "2"){
                    uType.value = "Requisitioner";
                }
                else if(uType.value == "3"){
                    uType.value = "Maintenance Staff";
                }
                else if(uType.value == "0"){
                    uType.value = "Administrator";
                }
            }
        });

        xmlhttp.open("GET", "PHP/getUserDetails.php?userID=" + userID, true);
        xmlhttp.send();

        modal.style.display = "block";
    });
}

for (let x = 0; x < closeModal.length; x++) {
    closeModal[x].addEventListener("click", function(){
        modal.style.display = "none";
        checkbox.checked = false;
        pword.type = "password";
        document.getElementById("email-error").textContent = ""; 
    });
}