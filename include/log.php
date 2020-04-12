<?php //*
if (!isset($_COOKIE['hasSeenNewInfo'])) {
    if ($_COOKIE['hasSeenNewInfo'] != "calendar") {
?>
<div class="alert alert-success alert-dismissable mb-1" role="alert" id="log">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Wow... a fresh new look is out there! You may need to clear your cache. The changes include:
    <ul>
        <li><strong>a new calendar presentation</strong></li>
        <li>possibility to <strong>show/hide the map</strong></li>
        <li>possibility to collapse/expand event details</li>
        <li>... <a href="./information.php" class="btn btn-success">All changes are summarized here</a></li>
    </ul>
</div>

<script type="text/javascript">

if (cookieConsentAnswer == "1") {
    setCookie('hasSeenNewInfo','calendar',365);
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
