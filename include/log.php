<?php //*
if (!isset($_COOKIE['hasSeenBasedOnStatsEvents'])) {
    if ($_COOKIE['hasSeenBasedOnStatsEvents'] != "yes") {
?>
<div class="alert alert-warning alert-dismissable mb-1" role="alert" id="log">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    There is now smarter info in the calendar: "Based on stats" events.
    <a href="./faq.php#basedonstatsevents" target="_blank">Learn more</a>
</div>

<script type="text/javascript">

if (cookieConsentAnswer == "1") {
    setCookie('hasSeenNewInfo','calendar',365);
    setCookie('hasSeen', 'new_mumble', 365);
    setCookie('hasSeenBasedOnStatsEvents', 'yes', 365);
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
