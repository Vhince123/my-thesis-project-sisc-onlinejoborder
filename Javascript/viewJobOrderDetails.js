var opencsModal = document.getElementsByClassName("open-jomodal");
var csmodal = document.getElementById("jomodal");
var closecsModal = document.getElementsByClassName("close-jomodal");
var jobdes = document.getElementById("jobdes");
var jolocation = document.getElementById("jolocation");
var jouserid = document.getElementById("jouserid");
var imageSrc = document.getElementById("imageSrc");

for (let x = 0; x < opencsModal.length; x++){
    opencsModal[x].addEventListener("click", function() {
        var xmlhttp = new XMLHttpRequest();
        var joid = this.getAttribute("data-joid");

        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var joDetails = JSON.parse(xmlhttp.responseText);
                
                jobdes.value = joDetails["jobOrderDescription"];
                jolocation.value = joDetails["location"];
                jouserid.value = joDetails["firstName"] + " " + joDetails[ "lastName"];

                if (!joDetails["photo"]){
                    imageSrc.innerHTML= "<span>No Images Found</span>";

                }
                else{
                    var images = joDetails["photo"].split(',');
                    var imageHTML = "";
                    for(var i = 0; i < images.length; i++) {
                        imageHTML += "<a target='_blank' href='" + images[i] + "'>" + images[i].replace('JOImages/','') + "</a><br>";
                    }
                    imageSrc.innerHTML = imageHTML;
                }

            }
        });

        xmlhttp.open("GET","PHP/getJODetails.php?jobOrderID=" + joid, true);
        xmlhttp.send();

        csmodal.style.display = "block";
    });
}

for (let x = 0; x < closecsModal.length; x++) {
    closecsModal[x].addEventListener("click", function(){
        csmodal.style.display = "none";
    });
}
