<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>
<?php include('./include/restriction.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<?php

// If the flightplan status will change
if (isset($_GET['changeFlightplan']) AND isset($_GET['status']))
{
    if ($_GET['changeFlightplan'] != NULL AND $_GET['status'] != NULL)
    {
        $changeFlightplan = mysql_real_escape_string(htmlspecialchars($_GET['changeFlightplan']));
        $status = mysql_real_escape_string(htmlspecialchars($_GET['status']));
        
        $FlightplanToChange = new Flightplan();
        $FlightplanToChange->selectById($changeFlightplan);
        $FlightplanToChange->changeFlightplanStatus($User->id, $FlightplanToChange->id, $status);
    }
}

// If the notifications are changing
if (isset($_POST['change_notification']))
{
    if (isset($_POST['flightplan_notification']) AND $_POST['flightplan_notification'] == "1") { $User->changeNotification(true); }
    else { $User->changeNotification(false); }
}

?>

<div class="new">
    
    <h3>*NEW : Be notified once a flightplan is filled !</h3>
    
    <form action="./dashboard.php5" method="post">
        <input type="hidden" name="change_notification"/>
        <input type="checkbox" name="flightplan_notification" value="1" <?php if ($User->nofitications == true) echo "checked";?>/> I want to be notified once a flightplan is filed (<?php echo $User->mail;?>)
        <br/>
        <input type="submit" value="Change my settings"/>
    </form>
    
</div>

<h2>My dashboard</h2>

<br/>

<?php //include('./include/form_newEvent.php5'); ?>

<!-- Button to plan a new session 
<b><a href="./dashboard.php5?form_newSession&date=<?php echo $date; ?>#newSession" class="button_new_session">+ Plan a new session</a></b>

<br/>
<br/>
<br/>-->
<?php

// We pick the user ID
$User = new User();
$User->selectById($_SESSION['id']);

// We gather every sessions this user made
$events = mysql_query("SELECT * FROM events WHERE userId = $User->id ORDER BY `date` DESC LIMIT 0,10") or die(mysql_error());

while ($event = mysql_fetch_array($events))
{ 
    // We pick the event
    $Event = new Event();
    $Event->selectById($event['eventId']);
    
    // We pick the airport ID to ident it
    $airportId = getInfo('airportId', 'airports', 'ICAO', $event['airportICAO']);
    // We list the airport which is concerned
    $airports = mysql_query("SELECT * FROM airports WHERE airportId = $airportId");
    // We gather the information
    $airport = mysql_fetch_assoc($airports);
    
    ?>
<a class="event_location" 
name="event<?php echo $Event->id;?>" 
href="#event<?php echo $Event->id;?>" 
<?php //echo 'onclick="document.getElementById(\'file_flightplan'.$event['id'].'\').style.display=\'block\';"';?> >
<?php echo $airport['name'];?> (<?php echo $airport['ICAO'];?>) <?php echo $Event->date;?>
</a> <a href="./edit_event.php5?eventId=<?php echo $Event->id;?>">Edit event</a>
<div class='event_flightplan' style='display:block;'>
    <h5>Date</h5>
    <?php echo $Event->date; ?>

    <h5>Time</h5>
    From <?php echo $Event->beginTime;?> to <?php echo $Event->endTime;?>

    <h5>Other information</h5>
    FGCom : <?php echo $Event->fgcom;?> // Teamspeak : <?php echo $Event->teamspeak; ?>
    <br/>
    Documents to download : <a href="<?php echo $Event->docsLink;?>"><?php echo $Event->docsLink;?></a>
    <br/>
    Remarks :
    <br/>
    <?php echo $Event->remarks; ?>
</div>

<ul>
    
    <?php 
    
    /*
     * FLIGHTPLANS
     */
    
    // We select all the flightplans relevant to this event
    $flightplans = mysql_query("SELECT * FROM flightplans20140113 ORDER BY departureTime");

    while ($flightplan = mysql_fetch_array($flightplans))
    {
        $Flightplan = new Flightplan();
        $Flightplan->selectById($flightplan['flightplanId']);

        if ((isAirportControlled($Event->airportICAO, $Flightplan->dateDeparture, $Flightplan->departureTime) == true 
                OR isAirportControlled($Event->airportICAO, $Flightplan->dateArrival, $Flightplan->arrivalTime) == true)
                AND ($Flightplan->departureAirport == $Event->airportICAO OR $Flightplan->arrivalAirport == $Event->airportICAO) 
                AND ($Flightplan->dateDeparture == $Event->date OR $Flightplan->dateArrival == $Even->date))
        {
            // If the flightplan is open
            if ($Flightplan->status == 'open') echo "<li class='dashboard_flightplan' style='color: #33ee33;'> [OPENED] ";
            else echo "<li class='dashboard_flightplan'>";
            echo $Flightplan->callsign . " [" . $Flightplan->departureTime . "] " . $Flightplan->departureAirport . " -> [" . $Flightplan->arrivalTime . "] " . $Flightplan->arrivalAirport . " @" . $Flightplan->cruiseAltitude . " " . $Flightplan->aircraftType . " " . $Flightplan->category;
            echo "<br/><a href='edit_flightplan.php5?idFlightplan=$Flightplan->id'>Comment</a> | <a href='./dashboard.php5?changeFlightplan=$Flightplan->id&status=open'>Open</a> | <a href='./dashboard.php5?changeFlightplan=$Flightplan->id&status=close'>Close</a>";
            echo "</li>";
        }
    }

echo "</ul>";
echo "<br/>";
echo "<br/>";

    /*
     *  // FLIGHTPLANS
     */

}
?>
    
<br/>
<br/>
<?php include('./include/footer.php5'); ?>
