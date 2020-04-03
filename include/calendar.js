
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

$(document).ready(function(){
});
