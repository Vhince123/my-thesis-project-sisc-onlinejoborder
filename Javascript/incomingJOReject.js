var openJOReject = document.getElementsByClassName("open-joreject");
var joreject = document.getElementById("joreject");
var closeJOReject = document.getElementsByClassName("close-joreject");
var rejectjoid = document.getElementById("rejectjoid");

for (let x = 0; x < openJOReject.length; x++){
    openJOReject[x].addEventListener("click", function() {
        var xmlhttp = new XMLHttpRequest();
        var joid = openJOReject[x].getAttribute("data-joid");

        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var joDetails = JSON.parse(xmlhttp.responseText);
                
                rejectjoid.value = joDetails["jobOrderID"];
            }
        });

        xmlhttp.open("GET","PHP/getJODetails.php?jobOrderID=" + joid, true);
        xmlhttp.send();

        joreject.style.display = "block";
    });
}

for (let x = 0; x < closeJOReject.length; x++) {
    closeJOReject[x].addEventListener("click", function(){
        joreject.style.display = "none";
    });
}