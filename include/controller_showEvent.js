
var weeklyControlled = $('#weeklyControlled');
var weeklyControlled = new Chart(weeklyControlled, {
    type: 'bar',
    data: {
        labels: ['Mon', 'Tue', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'occurences per day since jan. 2018',
            data: [],
            backgroundColor: []
        }]
    },
    options: {
        responsive:true,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                },
                gridLines: {
                    color: "rgba(0,0,0,0)"
                }
            }],
            xAxes: [{
                gridLines: {
                    color: "rgba(0,0,0,0)"
                }
            }]
        }
    }
});

var monthlyControlled = $('#monthlyControlled');
var monthlyControlled = new Chart(monthlyControlled, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'occurences per month since jan. 2018',
            data: [],
            backgroundColor: []
        }]
    },
    options: {
        responsive:true,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                },
                gridLines: {
                    color: "rgba(0,0,0,0)"
                }
            }],
            xAxes: [{
                gridLines: {
                    color: "rgba(0,0,0,0)"
                }
            }]
        }
    }
});

var atcControlled = $('#atcControlled');
var atcControlled = new Chart(atcControlled, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'occurences per month since jan. 2018',
            data: [],
            backgroundColor: []
        }]
    },
    options: {
        responsive:true,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                },
                gridLines: {
                    color: "rgba(0,0,0,0)"
                }
            }],
            xAxes: [{
                gridLines: {
                    color: "rgba(0,0,0,0)"
                }
            }]
        }
    }
});

var airportICAO = $('#airportICAO').html();
var atcId = $('#atcId').html();

$.get("./dev2017_04_28.php?getStatsOccurencesControlledPerDayInWeek&airport="+airportICAO, function(data) {
    var maxOccurences = -1;
    // We go through the array to look for the highest value
    $.each(data, function(day,occ) {
        if (occ > maxOccurences) {
            maxOccurences = occ;
        }
        weeklyControlled.data.datasets[0].data.push(occ);
    });
    $.each(data, function(day, occ) {
        var alpha = (100-(maxOccurences-occ))/100;
        weeklyControlled.data.datasets[0].backgroundColor.push('rgba(99, 255, 132, '+alpha+')');
    });
    weeklyControlled.update();
});

$.get("./dev2017_04_28.php?getStatsOccurencesControlledPerMonth&airport="+airportICAO, function(data) {
    // We go through the array to look for the highest value
    $.each(data, function(day,occ) {
        monthlyControlled.data.labels.push(day);
        monthlyControlled.data.datasets[0].data.push(occ);
        monthlyControlled.data.datasets[0].backgroundColor.push('rgba(99, 132, 255, 1.0)');
    });
    monthlyControlled.update();
});

$.get("./dev2017_04_28.php?getStatsOccurencesAtcPerMonth&atcId="+atcId, function(data) {
    // We go through the array to look for the highest value
    $.each(data, function(day,occ) {
        atcControlled.data.labels.push(day);
        atcControlled.data.datasets[0].data.push(occ);
        atcControlled.data.datasets[0].backgroundColor.push('rgba(99, 132, 255, 1.0)');
    });
    atcControlled.update();
});
