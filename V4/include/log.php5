<?php //* ?>
<div class="information">
	<div class="close_button">x</div>
	This version is pretty sexy, <a href="./contact.php5">isn't it ?</a> ! But you can <a href="../index.php5">go back to the previous version</a> if you want
	
</div>

<script type="text/javascript">
$(document).ready(function()
{
	$(".close_button").click(function()
	{
		$(".information").fadeOut();
	});
});
</script>

<?php //*/ ?>
