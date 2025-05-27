var openwfmModal = document.getElementsByClassName("open-wfmmodal");
var wfmmodal = document.getElementById("wfmmodal");
var closewfmModal = document.getElementsByClassName("close-wfmmodal");
var wfmjoid = document.getElementById("wfmjoid");

for (let x = 0; x < openwfmModal.length; x++){
    openwfmModal[x].addEventListener("click", function() {
        var xmlhttp = new XMLHttpRequest();
        var joid = openwfmModal[x].getAttribute("data-joview");

        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var joDetails = JSON.parse(xmlhttp.responseText);
                
                wfmjoid.value = joDetails["jobOrderID"];
                
            }
        });

        xmlhttp.open("GET","PHP/getJODetails.php?jobOrderID=" + joid, true);
        xmlhttp.send();

        wfmmodal.style.display = "block";
    });
}

for (let x = 0; x < closewfmModal.length; x++) {
    closewfmModal[x].addEventListener("click", function(){
        wfmmodal.style.display = "none";
    });
}
