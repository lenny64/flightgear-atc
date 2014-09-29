<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<!-- EXPERTIMENTAL -->
<div class="submenu">
    <div class="menu_entry"><a href="index.php5#flightplan_filling">File a flightplan</a></div>
    <div class="menu_entry"><a href="index.php5#calendar_anchor">ATC calendar</a></div>
    <?php if ($_SESSION['mode'] == 'connected' AND isset($_SESSION['id']))
    {
        ?>
            <div class="menu_entry">
                    <form action="./index.php5#newSession" method="get" class="submenu_quick_atc">
                            <input type="hidden" name="form_newSession"/>
                            <input type="text" id="dateTimePicker" name="date" size="8" value="<?php echo date('Y-m-d');?>"/>
                            <input type="submit" id="submenu_dateTimePicker_submit" value=" » Create ATC event"/>
                    </form>
            </div>
            <?php
    }
    ?>
</div>

<?php include('./include/log.php5'); ?>


<!-- ///////////// -->
<?php if ($_SESSION['mode'] == 'connected' AND isset($_SESSION['id']))
{
	if (isset($_POST['FPForm_visibility']) AND $_POST['FPForm_visibility'] != NULL)
	{
		$FPForm_visibility = mysql_real_escape_string(htmlspecialchars($_POST['FPForm_visibility']));
		$parameters = ['FPForm_visibility' => $FPForm_visibility];
		$User->changeParameters($parameters);
		?>
		<div class="information">
			Thanks ! You can change your settings in your dashboard
		</div>
		<?php
	}
	else if (!isset($User->parameters['FPForm_visibility']))
	{
	?>
	<div class="information">
		As an ATC, do you care about the Flightplan filling area ?
		<br/>
		<form action="./index.php5" method="post">
			<input type="radio" name="FPForm_visibility" value="visible"/> Yes
			<br/>
			<input type="radio" name="FPForm_visibility" value="hidden"/> No
			<br/>
			<input type="submit" value="Apply"/>
		</form>
	</div>
<?php
	}
}



// If we receive how many days one wants to see
if (!isset($_GET['daysToShow']) OR $_GET['daysToShow'] < 1 OR $_GET['daysToShow'] > 200) $daysToShow = 37;
else $daysToShow = mysql_real_escape_string(htmlspecialchars($_GET['daysToShow']));

// We include the form to create an event. It will be hidden at the top of the page
include('./include/form_newEvent.php5');

// I am first looking for every event
$events = returnEvents();

// New specialEvents feature
include('./include/specialEvents.php5');

// If the user is not connected or wants to see the FP area
if (!isset($User->parameters['FPForm_visibility']) OR (isset($User->parameters['FPForm_visibility']) AND $User->parameters['FPForm_visibility'] == "visible"))
{
	include('./include/file_flightplan_v3.php5');
}
// If the user is connected and does not want to see the FP area, we include the date picker that is inside file_flightplan_v3.php5 in normal conditions
// *Warning : this is a workaround... Needs to be improved.
elseif (isset($User->parameters['FPForm_visibility']) AND $User->parameters['FPForm_visibility'] == "hidden")
{
	echo "<script type='text/javascript' language='javascript'>";
	echo "$(document).ready(function()";
	echo "{";
	echo "$('#dateTimePicker').datepicker({ dateFormat:'yy-mm-dd', showOn: 'button', buttonImage: './img/scheme_date.png', buttonImageOnly: true });";
	echo "})";
	echo "</script>";
}

// We open the calendar DIV, master
echo "<a name='calendar_anchor'> </a>";
echo "<div class='calendar'>";

// We initialize the day index at 0
$day = 0;
while ($day < $daysToShow)
{
    $humanReadableDate = date('M d - l', mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));  // HUMAN READABLE date
		$humanReadableMonth = date('M',mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));		// MONTH
		$humanReadableDay = date('d',mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));		// DAY
		$humanReadableDayInWeek = date('l',mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));	// DAY IN WEEK
    $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));                 // FUNCTION PURPOSES date
    
    $daysInWeek = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
    
    if ($humanReadableDay == "01" OR $day == 0)
    {
		echo "<div class='calendar_header'><div class='calendar_month'>".date('F',strtotime($date))."</div>";
		for ($dayInWeekCounter = 0; $dayInWeekCounter < 7; $dayInWeekCounter++)
		{
			$dayIW = str_split($daysInWeek[$dayInWeekCounter],1);
			echo "<span class='calendar_dayInWeek'>".$dayIW[0]."</span>";
		}
		echo "</div>";
		
		$dayInWeekCounter = 0;
		while ($humanReadableDayInWeek != $daysInWeek[$dayInWeekCounter])
		{
			echo "<div class='calendar_day_empty'></div>";
			$dayInWeekCounter++;
		}
	}
	
	if ($humanReadableDayInWeek == "Monday")
	{
		echo "<div class='calendar_header'>";
		for ($dayInWeekCounter = 0; $dayInWeekCounter < 7; $dayInWeekCounter++)
		{
			$dayIW = str_split($daysInWeek[$dayInWeekCounter],1);
			echo "<span class='calendar_dayInWeek'>".$dayIW[0]."</span>";
		}
		echo "</div>";
	}
	
    ?>
    <a name="calendar_day<?php echo $date;?>"></a>
    <div class="calendar_day">
		<span class="calendar_dayNumber"><?php echo $humanReadableDay; ?></span><a href="./index.php5?form_newSession&date=<?php echo $date;?>#newSession" class="calendar_button_new_session">+ Event</a>
		
		<?php
		$eventCounter = 0;
		
		while ($eventCounter < sizeof($events))
		{
			if ($events[$eventCounter]["date"] == $date) include('./include/event.php5');
			
			$eventCounter++;
		}
		
		if ($eventCounter == 0) echo "";
	
		?>

    </div>

	<?php
    
	$previousDay = $date;
    $day++;
}

// We close the calendar display
echo "</div>";

?>


<br/>
<br/>
<?php include('./include/footer.php5'); ?>
