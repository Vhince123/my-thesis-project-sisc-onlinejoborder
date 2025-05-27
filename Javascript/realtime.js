var datetime = new Date();
var monthArr = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

var month = monthArr[datetime.getMonth()];
var day = datetime.getDate();
var year = datetime.getFullYear();
document.getElementById("dateRequested").innerHTML = month + " " + day + ", " + year;


