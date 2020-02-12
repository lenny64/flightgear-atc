
// Map initialization
var mymap = L.map('mapid', {
    zoom: 3,
    center: [51.505, -0.09],
    layers: []
});
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
        '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    id: 'mapbox/streets-v11'
}).addTo(mymap);

var myIcon = L.icon({
    iconUrl: './img/flightplan_indicator_open.png',
    iconSize: [10,10],
    iconAnchor: [5,10],
    popupAnchor: [0, -12]
});

// Initialisation de la date
function formatDate (date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;
    return [year, month, day].join('-');
}

var date_7days = new Date();
date_7days.setDate(date_7days.getDate() + 7);
var readable_date_7days = formatDate(date_7days);

// I look for airports controlled
$.get("./dev2017_04_28.php?getATCSessions&limitDate="+readable_date_7days+"&format=json", function(data) {
    var my_layers = [];
    var my_markers = [];
    var overlayMaps = {};
    $.each(data, function(i,airport) {
        if ($.inArray(airport.date,my_layers)) my_layers.push(airport.date);
        var marker = L.marker([airport.lat, airport.lon], {icon: myIcon}).bindPopup(airport.airportICAO+" "+airport.date+"<br/>"+airport.beginTime+" "+airport.endTime);
        my_markers.push(marker);
        if (airport.date in overlayMaps) {
            overlayMaps[airport.date].push(marker);
        }
        else {
            overlayMaps[airport.date] = Array(marker);
        }
    });
    // console.log(my_markers);
    var airports = L.layerGroup(my_markers);
    // var overlayMaps = {
    //   "2020-02-12": airports,
    //   "2020-02-15": airports
    // };
    console.log(overlayMaps);

    L.control.layers(overlayMaps).addTo(mymap);
    // console.log(my_layers);
});
