<?php //*
if (isset($User->id)) {
    if (!$User->checkPasswordSecured()) { ?>

<div class="alert alert-danger alert-dismissable" role="alert" id="log">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <span class="glyphicon glyphicon-lock" aria-hidden="true"></span> We think your password is not secured enough.
    <br/>Please <a href='./dashboard.php?settings' target="_blank">update your password</a> for improved security.
    <br/>
    <a href="./information.php" target="_blank">more info here</a>
</div>

<script type="text/javascript">
$(document).ready(function()
{
	$(".close_button").click(function()
	{
		$("#log").fadeOut();
	});
});
</script>

<?php
    }
}
?>


<?php //*/ ?>
