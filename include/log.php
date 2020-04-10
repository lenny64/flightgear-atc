<?php //*
if (!isset($_COOKIE['hasSeenNewInfo'])) {
    if ($_COOKIE['hasSeenNewInfo'] != "calendar") {
?>
<div class="alert alert-danger alert-dismissable mb-1" role="alert" id="log">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Wow... a fresh new look is out there!
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
