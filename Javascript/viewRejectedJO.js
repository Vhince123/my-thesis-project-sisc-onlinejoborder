var openViewModal = document.getElementsByClassName("open-joreject1");
var viewjomodal = document.getElementById("joreject1");
var closeViewModal = document.getElementsByClassName("close-joreject1");
var jobdes = document.getElementById("jobdes");
var jolocation = document.getElementById("jolocation");
var jocomment = document.getElementById("jocomment");
var jouserid =  document.getElementById("jouserid");
var imageSrc = document.getElementById("imageSrc");

for (let x = 0; x < closeViewModal.length; x++) {
    closeViewModal[x].addEventListener("click", function(){
        viewjomodal.style.display = "none";
    });
}

function openModal(id) {
    var xmlhttp = new XMLHttpRequest();
        
        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var rejectJO = JSON.parse(xmlhttp.responseText);

                jobdes.value = rejectJO["jobOrderDescription"];
                jolocation.value = rejectJO["location"];
                jouserid.value = rejectJO["fullname"];
                jocomment.value = rejectJO["comments"];

                if (!rejectJO["photo"]){
                    imageSrc.innerHTML= "<span>No Images Found</span>";

                }
                else{
                    var images = rejectJO["photo"].split(',');
                    var imageHTML = "";
                    for(var i = 0; i < images.length; i++) {
                        imageHTML += "<a target='_blank' href='" + images[i] + "'>" + images[i].replace('JOImages/','') + "</a><br>";
                    }
                    imageSrc.innerHTML = imageHTML;
                }
            }
        });

        xmlhttp.open("GET","PHP/getCancelledDetails.php?jobOrderID=" + id, true);
        xmlhttp.send();

        viewjomodal.style.display = "block";
}