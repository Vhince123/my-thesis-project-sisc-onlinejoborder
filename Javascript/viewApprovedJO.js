var openViewModal = document.getElementsByClassName("open-viewjomodal");
var viewjomodal = document.getElementById("viewjomodal");
var closeViewModal = document.getElementsByClassName("close-viewjomodal");
var jobno = document.getElementById("jobno");
var jobdes = document.getElementById("jobdes");
var jolocation = document.getElementById("jolocation");
var accountable = document.getElementById("accountable");
var datereq = document.getElementById("datereq");
var dateneed = document.getElementById("dateneed");
var jouserid =  document.getElementById("jouserid");
var imageSrc = document.getElementById("imageSrc");

for (let x = 0; x < openViewModal.length; x++){
    openViewModal[x].addEventListener("click", function() {
        var xmlhttp = new XMLHttpRequest();
        var viewjoid = openViewModal[x].getAttribute("data-joview");

        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var joDetails = JSON.parse(xmlhttp.responseText);

                jobno.value = joDetails["jobOrderNumber"];
                jobdes.value = joDetails["jobOrderDescription"];
                jolocation.value = joDetails["location"];
                accountable.value = joDetails["accountable"];
                datereq.value = joDetails["dateRequested"];
                dateneed.value = joDetails["dateNeeded"];
                jouserid.value = joDetails["firstName"] + " " + joDetails["lastName"];

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
                    // imageSrc.innerHTML =  "<a target='_blank' href='" + joDetails["photo"] + "'>" + joDetails['photo'].replace('JOImages/','') + "</a>";
                }
            }
        });

        xmlhttp.open("GET","PHP/getJODetails.php?jobOrderID=" + viewjoid, true);
        xmlhttp.send();

        viewjomodal.style.display = "block";
    });
}

for (let x = 0; x < closeViewModal.length; x++) {
    closeViewModal[x].addEventListener("click", function(){
        viewjomodal.style.display = "none";
    });
}