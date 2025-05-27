var openDeleteModal = document.getElementsByClassName("open-deletemodal");
var deletemodal = document.getElementById("deletemodal");
var closeDeleteModal = document.getElementsByClassName("close-deletemodal");
var deleteuser = document.getElementById("deleteuserid");

for (let x = 0; x < openDeleteModal.length; x++) {
    openDeleteModal[x].addEventListener("click", function() {
        var xmlhttp = new XMLHttpRequest();
        var userID = this.getAttribute("data-userid");

        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var userDetails = JSON.parse(xmlhttp.responseText);
                
                deleteuser.value = userDetails["userID"];

            }
        });

        xmlhttp.open("GET", "PHP/getUserDetails.php?userID=" + userID, true);
        xmlhttp.send();

        deletemodal.style.display = "block";
        });
    }

    for (let x = 0; x < closeDeleteModal.length; x++) {
        closeDeleteModal[x].addEventListener("click", function(){
            deletemodal.style.display = "none";
        });
    }