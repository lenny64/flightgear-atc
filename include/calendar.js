
var displayEvent = function() {
    $.each($('.event-details'), function(i, el) {
        $(el).show();
    });
}

var hideEvent = function() {
    $.each($('.event-details'), function(i, el) {
        $(el).hide();
    });
}

var collapseEvents = function() {
    $.each($('.event-details'), function(i, el) {
        $(el).toggle();
    });
}

$(document).ready(function(){
    $('.btn-show-flightplans').click(function() {
        console.log('ok');
        $('.flightplans-day-list').toggle();
    });
    var cookie_collapse_events = getCookie('collapseEvents');
    if (cookie_collapse_events == "") {
        var collapse_events = "0";
        displayEvent();
    }
    else {
        var collapse_events = cookie_collapse_events;
        if (cookie_collapse_events == "1") {
            hideEvent();
        }
        else {
            displayEvent();
        }
    }
    $('#collapse_events').click(function() {
        collapseEvents();
        if (collapse_events == "0") collapse_events = "1";
        else collapse_events = "0";
        createCookieWithPermission('collapseEvents',collapse_events,365);
        return false;
    });
    $( "#datepicker" ).datepicker();
});
