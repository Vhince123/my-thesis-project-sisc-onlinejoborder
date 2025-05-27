var openJOReject1 = document.getElementsByClassName("open-joapprove");
var joapprove = document.getElementById("joapprove");
var closeJOReject1 = document.getElementsByClassName("close-joapprove");
var approvejoid = document.getElementById("approvejoid");

for (let x = 0; x < openJOReject1.length; x++){
    openJOReject1[x].addEventListener("click", function() {
        var xmlhttp = new XMLHttpRequest();
        var joid = openJOReject1[x].getAttribute("data-joid");

        xmlhttp.addEventListener("readystatechange", function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var joDetails = JSON.parse(xmlhttp.responseText);
                
                approvejoid.value = joDetails["jobOrderID"];
            }
        });

        xmlhttp.open("GET","PHP/getJODetails.php?jobOrderID=" + joid, true);
        xmlhttp.send();

        joapprove.style.display = "block";
    });
}

for (let x = 0; x < closeJOReject1.length; x++) {
    closeJOReject1[x].addEventListener("click", function(){
        joapprove.style.display = "none";
    });
}