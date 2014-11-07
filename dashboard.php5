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
        $changeFlightplan = $_GET['changeFlightplan'];
        $status = $_GET['status'];
        
        $FlightplanToChange = new Flightplan();
        $FlightplanToChange->selectById($changeFlightplan);
        $FlightplanToChange->changeFlightplanStatus($User->id, $FlightplanToChange->id, $status);
    }
}

// If the notifications or parameters are changing
if (isset($_POST['change_settings']))
{	
    /* Notification part */
    if (isset($_POST['flightplan_notification']) AND $_POST['flightplan_notification'] == "1") { $User->changeNotification(true); }
    else { $User->changeNotification(false); }
    
    /* Name part /!\ Requires the "users_names" table */
    if (isset($_POST['atcName']))
    {
        $atcName = $_POST['atcName'];
        $User->changeName($atcName);
    }
    
    /* Other parameters part */
    // FPForm visibility on home screen
    if (isset($_POST['FPForm_visibility']) AND $_POST['FPForm_visibility'] == "1") { $FPForm_visibility = 'visible'; }
    else { $FPForm_visibility = 'hidden'; }
    // We list every parameter into an array that will be inserted into DB
    $userParameters = ['FPForm_visibility' => $FPForm_visibility];
    $User->changeParameters($userParameters);
    
    // Anyway we show this information
    echo "<div class='information'>";
    echo "Your settings have been saved at ".date('H:i:s');
    echo "</div>";
}

?>

<div class="new">
    <form action="./dashboard.php5" method="post">
        <input type="hidden" name="change_settings"/>
        <input type="checkbox" name="flightplan_notification" value="1" <?php if ($User->notifications == true) echo "checked";?>/> I want to be notified once a flightplan is filed (<?php echo $User->mail;?>)
        <br/>
        <input type="checkbox" name="FPForm_visibility" value="1" <?php if ($User->parameters['FPForm_visibility'] == 'visible') echo "checked";?>/> I want to see the flightplan filling form on the home page
        <br/>
        My pseudo : <input type="text" name="atcName" size="8" value="<?php if(isset($User->name) AND $User->name != NULL) echo $User->name; ?>" /> (blank = your pseudo won't appear in your ATC events)
        <br/>
        <input type="submit" value="Change settings"/>
    </form>
</div>

<?php
// We gather every sessions this user made
$events = $db->query("SELECT eventId FROM events WHERE userId = $User->id ORDER BY `date` DESC LIMIT 0,10");

foreach ($events as $event)
{ 
    // We pick the event
    $Event = new Event();
    $Event->selectById($event['eventId']);
    
    // We pick the airport ID to ident it
    $airportId = getInfo('airportId', 'airports', 'ICAO', $Event->airportICAO);
    // We list the airport which is concerned
    $airports = $db->query("SELECT * FROM airports WHERE airportId = $airportId");
    // We gather the information
    $airport = $airports->fetch(PDO::FETCH_ASSOC);
    
    ?>
<div class="dashboard_atcSession">
	<span class="event_location"><?php echo $Event->date;?> at <?php echo $airport['ICAO'];?></span> 
	<a href="./edit_event.php5?eventId=<?php echo $Event->id;?>">Edit event</a>
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
		$flightplans = $db->query("SELECT flightplanId FROM flightplans20140113 WHERE airportICAOFrom = '$Event->airportICAO' OR airportICAOTo = '$Event->airportICAO' ORDER BY departureTime");

		foreach ($flightplans as $flightplan)
		{
			$Flightplan = new Flightplan();
			$Flightplan->selectById($flightplan['flightplanId']);

			if ((isAirportControlled($Event->airportICAO, $Flightplan->dateDeparture, $Flightplan->departureTime) == true 
					OR isAirportControlled($Event->airportICAO, $Flightplan->dateArrival, $Flightplan->arrivalTime) == true)
					AND ($Flightplan->departureAirport == $Event->airportICAO OR $Flightplan->arrivalAirport == $Event->airportICAO) 
					AND ($Flightplan->dateDeparture == $Event->date OR $Flightplan->dateArrival == $Event->date))
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
echo "</div>";

    /*
     *  // FLIGHTPLANS
     */

}
?>
    
<br/>
<br/>
<?php include('./include/footer.php5'); ?>
