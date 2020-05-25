
var displayEvent = function() {
    $.each($('.event-details'), function(i, el) {
        $(el).show();
    });
}

var displayImage = function() {
    $.each($('.card-img-top'), function(i, el) {
        $(el).show();
    });
}

var hideEvent = function() {
    $.each($('.event-details'), function(i, el) {
        $(el).hide();
    });
}

var hideImage = function() {
    $.each($('.card-img-top'), function(i, el) {
        $(el).hide();
    });
}

var collapseEvents = function(state) {
    if (state == "0") {
        displayEvent();
        displayImage();
    }
    else {
        hideEvent();
        hideImage();
    }
}

let offsetContent = $('#jumbotron_mainPage').offset().top;

$(document).scroll(function() {
  let y = $(this).scrollTop();
  var calendar_percent_top = $('.calendar').position().top/$(window).height() *100;
  if (y > offsetContent || calendar_percent_top < 40) {
    $('.btn-day').fadeIn();
  } else {
    $('.btn-day').fadeOut();
  }
});

$(document).ready(function(){
    $('.btn-show-flightplans').click(function() {
        console.log('ok');
        $('.flightplans-day-list').toggle();
    });
    var cookie_collapse_events = getCookie('collapseEvents');
    if (cookie_collapse_events == "") {
        var collapse_events = "0";
        displayEvent();
        displayImage();
    }
    else {
        var collapse_events = cookie_collapse_events;
        if (cookie_collapse_events == "1") {
            hideEvent();
            hideImage();
        }
        else {
            displayEvent();
            displayImage();
        }
    }
    $('#collapse_events').click(function() {
        if (collapse_events == "0") collapse_events = "1";
        else collapse_events = "0";
        collapseEvents(collapse_events);
        createCookieWithPermission('collapseEvents',collapse_events,365);
        return false;
    });
    $( "#datepicker" ).datepicker();
});
