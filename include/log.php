<?php //*
if (!isset($_COOKIE['hasSeen'])) {
    if ($_COOKIE['hasSeen'] != "new_mumble") {
?>
<div class="alert alert-warning alert-dismissable mb-1" role="alert" id="log">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Mumble information: please use <strong>radio-mumble.flightgear.fr</strong> instead of flightgear-radio.autosoft.fr.
    <a href="https://forum.flightgear.org/viewtopic.php?f=10&t=33344&p=370076#p370042" target="_blank">More info</a>
</div>

<script type="text/javascript">

if (cookieConsentAnswer == "1") {
    setCookie('hasSeenNewInfo','calendar',365);
    setCookie('hasSeen', 'new_mumble', 365);
}
$(".close_button").click(function()
{
	$("#log").fadeOut();
});
</script>
<?php
    }
}
?>


<?php //*/ ?>
