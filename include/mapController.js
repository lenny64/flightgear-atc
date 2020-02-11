
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
//
// var overlayMaps = {
//     "Cities": 0
// };

// I look for airports controlled
$.get("./dev2017_04_28.php?getATCSessions&limitDate=2020-02-30&format=json", function(data) {
    var my_layers = [];
    $.each(data, function(i,airport) {
        if ($.inArray(airport.date,my_layers)) my_layers.push(airport.date);
        var marker = L.marker([airport.lat, airport.lon], {icon: myIcon}).addTo(mymap);
        marker.bindPopup(airport.airportICAO+" "+airport.date+"<br/>"+airport.beginTime+" "+airport.endTime);
    });
    // L.control.layers(overlayMaps).addTo(mymap);
    console.log(my_layers);
});
