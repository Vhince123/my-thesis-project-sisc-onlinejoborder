var opencsModal = document.getElementsByClassName("open-csmodal");
var csmodal = document.getElementById("csmodal");
var closecsModal = document.getElementsByClassName("close-csmodal");
var joiddone = document.getElementById("joid-done");

for (let x = 0; x < opencsModal.length; x++){
    opencsModal[x].addEventListener("click", function() {
        var xmlhttp = new XMLHttpRequest();
        var joid = opencsModal[x].getAttribute("data-joview");

        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var joDetails = JSON.parse(xmlhttp.responseText);
                console.log(joDetails["jobOrderID"]);
                joiddone.value = joDetails["jobOrderID"];
                
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
