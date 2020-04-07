
var displayEvent = function(el) {
    var target = $(el.target);
    console.log(target);
    var id_event = target.data('eventid');
    $('#event_details_'+id_event).show();
}

var hideEvent = function(el) {
    var target = $(el.target);
    console.log(target);
    var id_event = target.data('eventid');
    $('#event_details_'+id_event).hide();
}

var collapseEvents = function() {
    $.each($('.event-details'), function(i, el) {
        $(el).toggle();
    });
    return false;
}

$(document).ready(function(){
    $('#collapse_events').click(collapseEvents);
    $( "#datepicker" ).datepicker();
});
