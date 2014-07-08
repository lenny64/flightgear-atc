<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<?php include('./include/log.php5'); ?>
<?php include('./include/poll.php5'); ?>
<?php //include('./include/file_flightplan_v2.php5'); ?>

<?php


// If we receive how many days one wants to see
if (!isset($_GET['daysToShow']) OR $_GET['daysToShow'] < 1 OR $_GET['daysToShow'] > 40) $daysToShow = 35;
else $daysToShow = mysql_real_escape_string(htmlspecialchars($_GET['daysToShow']));

// We include the form to create an event. It will be hidden at the top of the page
include('./include/form_newEvent.php5');

// I am first looking for every event
$events = returnEvents();

echo "<h4 style='padding: 1%; background-color: #71c837; border: solid 1px #71c837; display: inline-block; margin-left: 40px; '><a href='../index.php5' style='color: #fff;'>Get back to the future</a></h4>";

include('./include/file_flightplan_v3.php5');

// We open the calendar DIV, master
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

<a name="scheduled_flights"></a>
<h2>Next scheduled flights</h2>

<div class="flightplans_list">
<?php

$today = date('Y-m-d');

// We gather the flightplans
$flightplans = mysql_query("SELECT * FROM flightplans20140113 WHERE dateDeparture>='$today' ORDER BY dateDeparture, departureTime") or die(mysql_error());

// We list the flightplans
while ($flightplan = mysql_fetch_array($flightplans))
{
	$Flightplan = new Flightplan();
	$Flightplan->selectById($flightplan['flightplanId']);
?>

	<div class="flightplans_list_flightplan">
		<span class="flightplans_list_flightplanCallsign"><?php echo $Flightplan->callsign; ?> [flightplan <?php echo $Flightplan->status; ?>]</span>
		<span class="flightplans_list_flightplanDate"><?php echo $Flightplan->dateDeparture ;?></span>
		<span class="flightplans_list_flightplanDeparture_airport"><?php echo $Flightplan->departureAirport; ?></span>
		<span class="flightplans_list_flightplanArrival_airport"><?php echo $Flightplan->arrivalAirport; ?></span>
		<br/>
		<span class="flightplans_list_flightplanDeparture_time"><?php echo $Flightplan->departureTime; ?></span>
		<span class="flightplans_list_flightplanArrival_time"><?php echo $Flightplan->arrivalTime; ?></span>
		<br/>
		<?php
		if ($Flightplan->lastUpdated != 0)
		{
			?>
			<span class="flightplans_list_flightplanInfo">Flightplan updated on <?php echo $Flightplan->lastUpdated;?>
			<br/>
			<?php
			foreach($Flightplan->history as $variable => $info)
			{
				echo $variable." = ".$info['value']."<br/>";
			}
		}
		?>
		</span>
	</div>

<?php
}

?>

</div>

<br/>
<br/>
<?php include('./include/footer.php5'); ?>
