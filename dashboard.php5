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

// SPECIAL EVENTS
if (isset($_POST['specialEventId']) AND isset($_POST['eventId']))
{
    if ($_POST['specialEventId'] != NULL AND $_POST['eventId'] != NULL)
    {
        $specialEventId = $_POST['specialEventId'];
        $eventId = $_POST['eventId'];
        $userId = $User->id;
        
        $SpecialEvent = new SpecialEvent();
        $SpecialEvent->selectById($specialEventId);
        
        if ($_POST['operation'] == 'add')
        {
            $SpecialEvent->addEventToSpecialEvent($eventId, $userId);
            echo "<div class='information'>Your event has successfully been added to ".$SpecialEvent->title.".</div>";
        }
        
        else if ($_POST['operation'] == 'remove')
        {
            $SpecialEvent->removeEventFromSpecialEvent($eventId);
            echo "<div class='information'>Your event has successfully been removed from ".$SpecialEvent->title.".</div>";
        }
        
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
    <h1>Settings</h1>
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
	<span class="event_location"><?php echo $airport['ICAO'];?></span> 
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
        <br/>
        <div class="event_specialEvents">
            <h3>Special Events</h3>
            <?php
            // PART RELATIVE TO SPECIAL EVENTS

            // We limit the airports associated to a special event to 2 days
            $limitDateEvent = date('Y-m-d', strtotime($Event->date." +3 days"));
            // In any case we select all the special events
            $specialEvents_list = $db->query("SELECT specialEventsId FROM specialEvents_events WHERE dateBegin <= '$Event->date' AND dateEnd >= '$Event->date'");
            // We go through them
            if ($specialEvents_list->rowCount() > 0)
            {
                foreach ($specialEvents_list as $specialEvents)
                {
                    echo "<div class='event_specialEvent'>";
                    // We select the specialEvent
                    $SpecialEvent = new SpecialEvent();
                    $SpecialEvent->selectById($specialEvents['specialEventsId']);
                    
                    echo "<h4>".$SpecialEvent->title."</h4>";

                    echo "A special event occurs on ".date('l F d', strtotime($SpecialEvent->dateBegin))." with the title '".$SpecialEvent->title."'.<br/>";

                    // We check if the event is already listed
                    if (array_search($Event->id, $SpecialEvent->eventsList) === false)
                    { ?>
                    Would you like to add your event to this gig?
                    <form method="post" action="./dashboard.php5">
                        <input type="hidden" name="specialEventId" value="<?php echo $SpecialEvent->id;?>"/>
                        <input type="hidden" name="eventId" value="<?php echo $Event->id;?>"/>
                        <input type="hidden" name="operation" value="add"/>
                        <input type="submit" value="+ Add to the <?php echo $SpecialEvent->title; ?> Special Event"/>
                    </form>
                    <?php
                    }
                    else
                    { ?>
                    Would you like to remove your event from this gig?
                    <form method="post" action="./dashboard.php5">
                        <input type="hidden" name="specialEventId" value="<?php echo $SpecialEvent->id;?>"/>
                        <input type="hidden" name="eventId" value="<?php echo $Event->id;?>"/>
                        <input type="hidden" name="operation" value="remove"/>
                        <input type="submit" value="- Remove from the Special Event : <?php echo $SpecialEvent->title; ?>" class="remove"/>
                    </form>
                    <?php
                    }
                    echo "</div>";
                }
            }
            else
            {
                echo "<div class='event_specialEvent'>";
                echo "Sorry, there are no Special Events occurring at this date. <a href='./contact.php5'>Contact us</a> to propose a new Special Event.";
                echo "</div>";
            }
            ?>
            <a href="./faq.php5"/>What's this ?</a>
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
