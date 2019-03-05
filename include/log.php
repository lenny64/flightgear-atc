<?php //* ?>
<div class="alert alert-success alert-dismissable" role="alert" id="log">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <span class="glyphicon glyphicon glyphicon-cog" aria-hidden="true"></span><b> WARNING </b> Some malicious users wanted to harm Jomo's sessions at EDDF on wednesdays, saturdays and sundays.
    <br/>
    When the real Jomo actually plans a session it will be marked with a <span class="label label-success">green label</span>.
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

<?php //*/ ?>
