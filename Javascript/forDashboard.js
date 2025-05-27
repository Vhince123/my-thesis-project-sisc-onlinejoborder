var totaljo = document.getElementById("total-jo");
var filterMonth = document.getElementById("filter-month");
var filterYear = document.getElementById("filter-year");
const myChart = document.querySelector(".myChart").getContext("2d");
const ul = document.querySelector(".pie-chart .details ul");
var displaychart;
var chartData; // Declare chartData globally

function updateTotalJOCount() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.addEventListener("readystatechange", function(e) {
        if (this.readyState === 4 && this.status == 200) {
            var responseObj = JSON.parse(this.responseText);
            totaljo.innerHTML = "TOTAL JOB ORDER: " + responseObj.sum_of_counts;
        }
    });

    xmlhttp.open("GET", "PHP/getJOCount.php?month=" + filterMonth.value, true);
    xmlhttp.send();
}

document.addEventListener("DOMContentLoaded", function() {
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1;
    var options = filterMonth.options;
    for (var i = 0; i < options.length; i++) {
        if (options[i].value === String(currentMonth).padStart(2, '0')) {
            options[i].setAttribute("selected", "selected");
            break;
        }
    }

    updateTotalJOCount();
    updateLeadTime(); 

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.addEventListener("readystatechange", function(e) {
        if (this.readyState === 4 && this.status == 200) {
            const JOCountObj = JSON.parse(this.responseText);

            chartData = { 
                labels: ["Ongoing", "Waiting For Materials", "Outsource", "Done", "Rejected"],
                data: [JOCountObj.ongoing_JO, JOCountObj.waiting_JO, JOCountObj.outsource_JO, JOCountObj.done_JO, JOCountObj.rejected_JO],
                backgroundColor: ['#45FF86', '#2BCEFE', '#9AFC44', '#FF9D35', '#FFF838']
            };

            const config = {
                type: "doughnut",
                data: {
                    labels: chartData.labels,
                    datasets: [{ label: "No. of JOs", data: chartData.data, backgroundColor: chartData.backgroundColor }]
                },
                options: {
                    aspectRatio: 1.5,
                    borderRadius: 2,
                    borderWidth: 1,
                    borderColor: 'rgb(120, 120, 120)',
                    borderAlign: 'center',
                    hoverBorderWidth: 0,
                    plugins: {
                        legend: { display: true }
                    }
                },
            };

            displaychart = new Chart(myChart, config);
            populateUl(chartData);
        }
    });

    xmlhttp.open("GET", "PHP/getJOCount.php?month=" + filterMonth.value, true);
    xmlhttp.send();
});

filterMonth.addEventListener("change", function(event) {
    updateTotalJOCount();
    updateLeadTime();

    ul.innerHTML = "";
    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.addEventListener("readystatechange", function(e) {
        if (this.readyState === 4 && this.status == 200) {
            const JOCountObj = JSON.parse(this.responseText);

            displaychart.data.datasets[0].data = [JOCountObj.ongoing_JO, JOCountObj.waiting_JO, JOCountObj.outsource_JO, JOCountObj.done_JO, JOCountObj.rejected_JO];

            displaychart.update();

            populateUl({
                labels: ["Ongoing", "Waiting For Materials", "Outsource", "Done", "Rejected"],
                data: [JOCountObj.ongoing_JO, JOCountObj.waiting_JO, JOCountObj.outsource_JO, JOCountObj.done_JO, JOCountObj.rejected_JO]
            });
        }
    });

    xmlhttp.open("GET", "PHP/getJOCount.php?month=" + filterMonth.value, true);
    xmlhttp.send();
});

function populateUl(data) {
    ul.innerHTML = ""; 
    data.labels.forEach((l, i) => {
        let li = document.createElement("li");
        li.innerHTML = `${l}: <span class='joreport'>${data.data[i]}</span>`;
        ul.appendChild(li);
    });
}

