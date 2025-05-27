var openViewRatedModal = document.getElementsByClassName("close-viewratedmodal");
var viewratedmodal = document.getElementById("viewratedmodal");
var closeViewRatedModal = document.getElementsByClassName("close-viewratedmodal");
var jobno = document.getElementById("jobno");
var jobdes = document.getElementById("jobdes");
var jolocation = document.getElementById("jolocation");
var accountable = document.getElementById("accountable");
var datereq = document.getElementById("datereq");
var dateneed = document.getElementById("dateneed");
var dateser = document.getElementById( "dateser" );
var datefin = document.getElementById( "datefin" ); 
var jouserid =  document.getElementById("jouserid");
var imageSrc = document.getElementById("imageSrc");


for (let x = 0; x < closeViewRatedModal.length; x++) {
    closeViewRatedModal[x].addEventListener("click", function(){
        viewratedmodal.style.display = "none";
    });
}

function openModal(id) {
    var xmlhttp = new XMLHttpRequest();

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
            dateser.value = joDetails["dateServed"];
            datefin.value = joDetails["dateFinished"];

            if (!joDetails["donePhoto"]){
                imageSrc.innerHTML= "<span>No Images Found</span>";
            }
            else{
                var images = joDetails["donePhoto"].split(',');
                var imageHTML = "";
                for(var i = 0; i < images.length; i++) {
                    imageHTML += "<a target='_blank' href='" + images[i] + "'>" + images[i].replace('JOImages/','') + "</a><br>";
                }
                imageSrc.innerHTML = imageHTML;
            }
        }
    });

    xmlhttp.open("GET","PHP/getRatedJODetails.php?jobOrderID=" + id, true);
    xmlhttp.send();

    viewratedmodal.style.display = "block";
}