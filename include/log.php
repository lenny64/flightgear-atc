<?php //*
if (isset($User->id)) {
    if (!$User->checkPasswordSecured()) { ?>

<div class="alert alert-danger alert-dismissable" role="alert" id="log">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    You may encounter problems using in-game ATC clients.<br/>
    Please <a href="./dashboard.php?disconnect">log out</a> and log in again.
</div>

<script type="text/javascript">
$(document).ready(function()
{
    // Automatically disconnect the user
    // TO REMOVE ///////////////////////////////////////
    window.location.replace("./index.php?disconnect");
    ///////////////////////////////////////////////////
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
