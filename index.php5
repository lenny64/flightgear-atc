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

for ($monthNumber = 0; $monthNumber < 2; $monthNumber++)
{
    $today = date('Y-m-d');
    $dayCursor = date('Y-m-d',strtotime('+'.$monthNumber.' month'));
    $firstDayCurrentMonth = date('Y-m-01',strtotime($dayCursor));
    $lastDayCurrentMonth = date('Y-m-t',strtotime($dayCursor));
    $totalDaysCurrentMonth = date('t',strtotime($dayCursor));

    ?>

    <table class='calendar'>
        <tr class="calendarHeader">
            <td colspan="7"><?php echo date('F',strtotime($dayCursor)); ?></td>
        </tr>
        <tr class="calendarHeader">
            <td>Mon</td>
            <td>Tue</td>
            <td>Wed</td>
            <td>Thu</td>
            <td>Fri</td>
            <td>Sat</td>
            <td>Sun</td>
        </tr>

    <?php

    // We initialize the day index at 0
    $dayCounter = $firstDayCurrentMonth;
    $firstWeek = TRUE;

    // We go through all the days in the month
    while ($dayCounter <= $lastDayCurrentMonth)
    {
        // We open the week on monday ?
        if (date('w',strtotime($dayCounter)) == 1) echo "<tr>";
        // If it's the first week
        if ($firstWeek == TRUE)
        {
            // We create as much TD as required
            for ($i = 1; $i < date('N',strtotime($dayCounter)); $i++)
            {
                echo "<td></td>";
            }
            // And of course we are not in the first week anymore
            $firstWeek = FALSE;
        }
        
        // CELL DAY GENERATION
        // Is this date in the past ?
        if ($dayCounter < $today) { echo "<td class='pastDate'>"; }
        // Is this date today ?
        elseif ($dayCounter == $today) { echo "<td class='today'>"; }
        // Otherwise it's in the future
        else { echo "<td>"; }
        
        // CELL CONTENT GENERATION
        // Inside the cell we print the day number
        echo date('d',strtotime($dayCounter));
        // We get all the events for this particular day
        $events[$dayCounter] = returnEvents($dayCounter);
        // If there are no events for this day we don't show anything
        if (sizeof($events[$dayCounter]) < 1) { echo "<br/>&nbsp;"; }
        // Otherwise we show the number of events occuring this day
        else { echo "<br/><span class='number_events'>".sizeof($events[$dayCounter])." events</span>"; }
        
        // WE CLOSE THE CELL DAY
        echo "</td>";

        // We close the week on sunday
        if (date('w',strtotime($dayCounter)) == 0) echo "</tr>";
        
        // We increment the day counter by one day
        $dayCounter = date('Y-m-d',strtotime($dayCounter." +1 day"));
    }

// We close the calendar display
    echo "</table>";
}

?>

<br style="clear:both;"/>
<br/>
<br/>
<?php include('./include/footer.php5'); ?>
