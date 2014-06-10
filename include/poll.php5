
<?php

// If there is a new poll submission
if (isset($_POST['poll_id']) AND $_POST['poll_id'] != NULL AND isset($_POST['poll_answer']) AND $_POST['poll_answer'] != NULL)
{
	// I retreive some information
	$answer = $_POST['poll_answer'];
	$pollId = $_POST['poll_id'];
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$Poll = new Poll();
	$Poll->selectById($pollId);
	$Poll->answer($answer,$ip);
}

// We get all the polls
$polls_list = mysql_query("SELECT * FROM polls_submits");

while ($poll = mysql_fetch_array($polls_list))
{
	// We get all the information regarding the poll
	$Poll = new Poll();
	$Poll->selectById($poll['pollId']);
	
	$Poll->checkAnswer();
	
	// I only show current poll
	if ($Poll->okToVote !== FALSE AND $Poll->dateBegin <= date('Y-m-d') AND $Poll->dateEnd >= date('Y-m-d'))
	{
		// If there are not at least 2 choices
		if (sizeof($Poll->choices) >= 2)
		{
?>

<div class="poll" id="poll_div" <?php //if (isset($_COOKIE['lenny64_poll']) AND $_COOKIE['lenny64_poll'] == $Poll->id) echo 'style="display: none;"';?>>
	<span class="poll_close" onclick="document.getElementById('poll_div').style.display='none';">x Close</span>
	<span class="poll_title"><?php echo $Poll->title; ?></span>
	<span class="poll_content"><?php echo $Poll->content; ?></span>
	<form class="poll_form" method="post" action="./index.php5">
		<input type="hidden" name="cookie_create"/>
		<input type="hidden" name="cookie_name" value="poll"/>
		<input type="hidden" name="cookie_value" value="<?php echo $Poll->id;?>"/>
		<input type="hidden" name="poll_id" value="<?php echo $Poll->id; ?>"/>
		<?php
		$choice_iteration = 0;
		
		while ($choice_iteration < sizeof($Poll->choices))
		{
			$choice = $Poll->choices[$choice_iteration]; ?>
			<input type="radio" name="poll_answer" value="<?php echo $choice; ?>" class="poll_choice" id="choice_<?php echo $choice_iteration; ?>"/><label for="choice_<?php echo $choice_iteration; ?>"><?php echo $choice; ?></label>
		<?php
		$choice_iteration++;
		}
		?>
		<br/><input type="submit" value="Submit !" class="poll_submit_button"/>
	</form>
</div>

<?php
		}

	}
}
?>
