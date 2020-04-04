
// Map initialization
var mymap = L.map('mapid', {
    zoom: 3,
    center: [46.505, -0.09],
    scrollWheelZoom: false,
    layers: []
});
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mymap);

var myIcon = L.icon({
    iconUrl: './img/favicon.png',
    iconSize: [13,13],
    iconAnchor: [5,10],
    popupAnchor: [0, -12]
});
var iconLiveATC = L.icon({
    iconUrl: './img/menu_controlled.png',
    iconSize: [16,16],
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


var overlay = {'Live ATC': Array()};
var overlayMaps = {};
var addControlLayers = false;
var map_recenter = false;

$.get("http://crossfeed.freeflightsim.org/flights.json", function(data) {
    var data = JSON.parse(data);
    var atc_models = ["atc", "atc2", "atc-ml", "atc-fs", "openradar", "atc-tower", "atc-tower2", "atc-pie", "atc-pie"];
    if (data != null) {
        $.each(data.flights, function(i, airport) {
            if (atc_models.indexOf(airport.model.toLowerCase()) != -1) {
                var marker = L.marker([airport.lat, airport.lon], {icon: iconLiveATC}).bindPopup(airport.callsign);
                marker.on('mouseover',function(ev) {
                  ev.target.openPopup();
                });
                marker.on('mouseout',function(ev) {
                  ev.target.closePopup();
                });
                overlay['Live ATC'].push(marker);
                // I put the center of the map on first marker
                if (i == 0) {
                    mymap.panTo(new L.LatLng(airport.lat, airport.lon));
                    map_recenter = true;
                }
            }
        });
        $.each(overlay, function(i, layer) {
            overlayMaps[i] = L.layerGroup(layer);
        });
        // if (!addControlLayers) {
        //     L.control.layers(overlayMaps).addTo(mymap);
        //     addControlLayers = true;
        // }
        mymap.addLayer(overlayMaps['Live ATC']);
        var boutons_map = $('.boutons_map').html();
        $('.boutons_map').html(boutons_map+'<a class="btn btn-sm btn-success bouton-map" id="live_atc" onclick="showLayer_new(this,\'Live ATC\')"><span class="oi oi-bolt" title="now" aria-hidden="true"></span> Live ATC</a>');
    }
    // I look for airports controlled
    $.get("./dev2017_04_28.php?getATCSessions&limitDate="+readable_date_7days+"&format=json", function(data) {
        if (data != null) {
            $.each(data, function(i,airport) {
                var marker = L.marker([airport.lat, airport.lon], {icon: myIcon}).bindPopup(airport.airportICAO+" "+airport.date+"<br/>"+airport.beginTime+" "+airport.endTime);
                marker.on('mouseover',function(ev) {
                  ev.target.openPopup();
                });
                marker.on('mouseout',function(ev) {
                  ev.target.closePopup();
                });
                // I put the center of the map on first marker
                if (i == 0 && map_recenter == false) {
                    mymap.panTo(new L.LatLng(airport.lat, airport.lon));
                }

                // If there are several airports on same day
                if (airport.date in overlay) {
                    overlay[airport.date].push(marker);
                } // For the first airport of the day we initialize the array and add a button
                else {
                    overlay[airport.date] = Array(marker);
                    addButton(airport.date);
                }
            });
            // For each date we create a layer
            $.each(overlay, function(i, layer) {
                overlayMaps[i] = L.layerGroup(layer);
            });
            // // We add a control layer
            // if (!addControlLayers) {
            //     L.control.layers(overlayMaps).addTo(mymap);
            //     addControlLayers = true;
            // }

            // I add the first layer by default
            mymap.addLayer(overlayMaps[Object.keys(overlayMaps)[0]]);
            // With the proper button active
            $('.bouton-map').first().addClass("btn-primary");
            $('.bouton-map').first().removeClass("btn-default");
        }
    });
});

var addButton = function(airport_date) {
    // Current html
    var boutons_map = $('.boutons_map').html();
    // Dates
    var date_today = new Date();
    var date_evt = new Date(airport_date);
    var delta_days = date_evt.getDate() - date_today.getDate();
    var days_of_week = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
    // Computed text for better readability
    if (delta_days == 0) var text = "Today";
    else if (delta_days == 1) var text = "Tomorrow";
    else var text = "On "+days_of_week[date_evt.getDay()];
    $('.boutons_map').html(boutons_map+'<a class="bouton-map btn btn-sm btn-light" onmouseover="showLayer(this,\''+airport_date+'\')">'+text+'</a>');
}

var showLayer = function(el,layer) {
    event.preventDefault();
    // I remove all layers
    $.each(overlayMaps, function (i, l) {
        if (i != 'Live ATC') {
          mymap.removeLayer(l);
        }
    });
    // I turn off all layer buttons
    $('.bouton-map').each(function(i, obj) {
        if ($(obj).attr('id') != 'live_atc') {
            $(obj).removeClass('btn-primary');
            $(obj).addClass('btn-default');
        }
    });
    // We add the layer
    mymap.addLayer(overlayMaps[layer]);
    // We turn on the active layer button
    $(el).addClass('btn-primary');
    $(el).removeClass('btn-default');
};

var showLayer_new = function(el,layer) {
    event.preventDefault();
    if(mymap.hasLayer(overlayMaps[layer])) {
        $(el).removeClass('btn-primary');
        $(el).addClass('btn-default');
        mymap.removeLayer(overlayMaps[layer]);
    } else {
        $(el).removeClass('btn-default');
        $(el).addClass('btn-primary');
        mymap.addLayer(overlayMaps[layer]);
   }
}

var mapToggle = function() {
    $('#mapid').toggle();
}

$('#mapToggle').click(mapToggle);
