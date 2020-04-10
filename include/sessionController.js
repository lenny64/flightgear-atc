var cookies = document.cookie;

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function createCookieWithPermission(cname, cvalue, exdays) {
    cookieConsentAnswer = getCookie('cookieConsentAnswer');
    if (cookieConsentAnswer == "1") {
        setCookie(cname, cvalue, exdays);
        return true;
    }
    return false;
}

$(document).ready(function() {
    var cookieConsentAnswer = getCookie('cookieConsentAnswer');
    var html = "";
    if (cookieConsentAnswer != "1") {
        html += "This website uses functional cookies to work properly. Please accept them.";
        html += "<br/>";
        html += "<a href='#' id='acceptCookies' class='btn btn-success'>I accept cookies</a>";
        $('#sessionAlert').html(html);
        $('#sessionAlert').show();
    }

    $('#acceptCookies').click(function() {
        $('#sessionAlert').hide();
        setCookie('cookieConsentAnswer', '1', 365);
        return false;
    });
});
