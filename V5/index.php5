<?php include('./include/header.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<!-- EXPERTIMENTAL
<div class="submenu">
    <div class="menu_entry"><a href="index.php5#flightplan_filling">File a flightplan</a></div>
    <div class="menu_entry"><a href="index.php5#calendar_anchor">ATC calendar</a></div>
    <div class="menu_entry"><a href="index.php5#scheduled_flights">Flights</a></div>
    <?php
    if ($_SESSION['mode'] == 'connected' AND isset($_SESSION['id']))
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
if (!isset($_GET['daysToShow']) OR $_GET['daysToShow'] < 1 OR $_GET['daysToShow'] > 200) $daysToShow = 14;
else $daysToShow = mysql_real_escape_string(htmlspecialchars($_GET['daysToShow']));

// We include the form to create an event. It will be hidden at the top of the page
include('./include/form_newEvent.php5');

// I am first looking for every event
$events = returnEvents();

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
?>

<table class="nextATCEvents">
    <thead>
        <tr>
            <?php for ($i = 0; $i <= 97; $i++) { echo "<td>&nbsp;</td>"; } ?>
        </tr>
        <tr>
            <td></td>
            <?php for ($i = 0; $i <= 12; $i++) { echo "<td colspan='8'>".($i*2)."</td>"; } ?>
        </tr>
    </thead>
    <?php
    $day = 0;
    while ($day < $daysToShow)
    {
        $humanReadableDate = date('M d - l', mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));  // HUMAN READABLE date
        $humanReadableMonth = date('M',mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));		// MONTH
        $humanReadableDay = date('d',mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));		// DAY
        $humanReadableDayInWeek = str_split(date('l',mktime(0, 0, 0, date('m'), date('d')+$day, date('Y'))),3);	// DAY IN WEEK
        $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+$day, date('Y')));                 // FUNCTION PURPOSES date

        $daysInWeek = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');

        $nbEventsInDay = 0;
        
        for ($iEvents = 0; $iEvents < sizeof($events); $iEvents++)
        {
            if ($events[$iEvents]["date"] == $date)
            {
                $nbEventsInDay++;
                
            }
        }
        ?>
        <tr class="rule">
            <td>&nbsp;</td>
            <?php for ($fillSpace = 0; $fillSpace <= 12; $fillSpace++) { echo "<td colspan='8'>&nbsp;</td>"; } ?>
        </tr>
        <tr class="line">
            <td rowspan="1">
                <span class="nextATCEvents_date dayInWeek"><?php echo $humanReadableDayInWeek[0]; ?></span>
                <span class="nextATCEvents_date month"><?php echo $humanReadableMonth; ?></span>
                <span class="nextATCEvents_date day"><?php echo $humanReadableDay; ?></span>
            </td>
            <?php
            
            $posDuration = Array();
            
            for ($iEvents = 0; $iEvents < sizeof($events); $iEvents++)
            {
                if ($events[$iEvents]["date"] == $date)
                {
                    $Event = new Event();
                    $Event->selectById($events[$iEvents]['Id']);
                    $posBegin = $Event->beginTime*2;
                    $posEnd = $Event->endTime*2;
                    $posDuration[$Event->id] = $posEnd - $posBegin;
                }
            }
            
            
            $i = 0;
            while ($i <= 47)
            {
                if ($i == $posBegin AND $nbEventsInDay != 0)
                {
                        echo "<td colspan='".($posDuration[$Event->id]*2)."'>".$Event->id."</td>";
                        $i = $i+($posDuration[$Event->id])-1;
                }
                else
                {
                    echo "<td class='td_empty' colspan='2'>&nbsp;</td>";
                }
                $i++;
            }
        ?>
        </tr>
        <tr class="line">
            <td>&nbsp;</td>
            <?php for ($fillSpace = 0; $fillSpace <= 12; $fillSpace++) { echo "<td colspan='8'>+</td>"; } ?>
        </tr>
        <tr class="rule">
            <td>&nbsp;</td>
            <?php for ($fillSpace = 0; $fillSpace <= 12; $fillSpace++) { echo "<td colspan='8'>&nbsp;</td>"; } ?>
        </tr>
        <?php
        $day++;
    }
    ?>
</table>

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
