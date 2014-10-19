<?php

global $db;

// THIS PAGE IS CALLED BY EVENT.PHP5

// We gather the flightplans
$flightplans = $db->query("SELECT * FROM flightplans20140113 ORDER BY departureTime");
//TO ADD AGAIIIN !!! WHERE eventId=$Event->id 

// This index will tell us if there is a flightplan for the event
$flightplan_num = 0;
// We list the flightplans
foreach ($flightplans as $flightplan)
{
    // We select the flightplan
    $Flightplan = new Flightplan();
    $Flightplan->selectById($flightplan['flightplanId']);
    
    // Variable to show or not the flightplan
    $OkToShowDeparture = false;
    $OkToShowArrival = false;
    
    // First check if the dates match
    if ($Flightplan->dateDeparture == $Event->date OR $Flightplan->dateArrival == $Event->date)
    {    
        $OkToShowDeparture = isAirportControlled($Flightplan->departureAirport, $Flightplan->dateDeparture, $Flightplan->departureTime);
        $OkToShowArrival = isAirportControlled($Flightplan->arrivalAirport, $Flightplan->dateArrival, $Flightplan->arrivalTime);
    }
    
    // Every flightplans coming from this event's airport
    if ($Flightplan->departureAirport == $Event->airportICAO AND $OkToShowDeparture === true)
    {
        if ($Flightplan->status == "open") echo '<div class="calendar_event_flightplan_summary" style="background-color: #33ee33;"
					onclick="if(document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display == \'block\')
                        {
                            document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display = \'none\';
                        }
                        else document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display=\'block\';"> Opened flightplan<br/><br/>';
        else echo '<div class="calendar_event_flightplan_summary"
					onclick="if(document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display == \'block\')
                        {
                            document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display = \'none\';
                        }
                        else document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display=\'block\';">';
            ?>
            <img src="./img/aircraft_takeoff.png"/> 
            <span class="calendar_event_flightplan_callsign">
				<?php echo $Flightplan->callsign;?>
			</span>
        </div>
        <div id="flightplan<?php echo $Flightplan->id.$Event->id;?>" class="calendar_event_flightplan_infos">
			<!-- Blue top left arrow -->
			<em class="calendar_blue_arrow"></em>
            <span class="calendar_event_flightplan_infos_category">Callsign</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->callsign; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Aircraft</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->aircraftType; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Category of flight</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->category; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Departure time</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->departureTime; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Destination</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->arrivalAirport; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Arrival time</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->arrivalTime; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Cruise level</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->cruiseAltitude; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">True Airspeed</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->trueAirspeed; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Souls on board</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->soulsOnBoard; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Fuel time</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->fuelTime; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Waypoints</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->waypoints; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Comments</span>
            <br/>
            <?php
            
			foreach ($Flightplan->comments as $comments)
			{
				$pseudo = $comments['pseudo'];
				$comment = $comments['comment'];
				echo "<b>$pseudo</b> $comment<br/>";
			}
            ?>
            <br/>
            <a href="./edit_flightplan.php5?idFlightplan=<?php echo $Flightplan->id; ?>" class="calendar_event_flightplan_add_comment">+ Add a comment</a>
        </div>
    <?php
    // We increment the flightplan index
    $flightplan_num++; }
    // Every flightplans going to this event's airport
    if ($Flightplan->arrivalAirport == $Event->airportICAO AND $OkToShowArrival === true)
    {
        if ($Flightplan->status == "open") echo '<div class="calendar_event_flightplan_summary" style="background-color: #33ee33;"
					onclick="if(document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display == \'block\')
                        {
                            document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display = \'none\';
                        }
                        else document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display=\'block\';"> Opened flightplan<br/><br/>';
        else echo '<div class="calendar_event_flightplan_summary"
					onclick="if(document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display == \'block\')
                        {
                            document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display = \'none\';
                        }
                        else document.getElementById(\'flightplan'.$Flightplan->id.$Event->id.'\').style.display=\'block\';">';
        ?>
            <img src='./img/aircraft_landing.png'/>
            <span class="calendar_event_flightplan_callsign">
                      <?php echo $Flightplan->callsign;?>
            </span>
        </div>
        <div id="flightplan<?php echo $Flightplan->id.$Event->id;?>" class="calendar_event_flightplan_infos">
			<!-- Blue top left arrow -->
			<em class="calendar_blue_arrow"></em>
            Flightplan <?php echo $Flightplan->status; ?> at <?php echo getInfo('dateTime','flightplan_status','flightplanId',$Flightplan->id);?><br/>
			<span class="calendar_event_flightplan_infos_category">Callsign</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->callsign; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Aircraft</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->aircraftType; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Category of flight</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->category; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Departure time</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->departureTime; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">From</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->departureAirport; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Arrival time</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->arrivalTime; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">At</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->arrivalAirport; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Cruise level</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->cruiseAltitude; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">True Airspeed</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->trueAirspeed; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Souls on board</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->soulsOnBoard; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Fuel time</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->fuelTime; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Waypoints</span> <span class="calendar_event_flightplan_infos_value"><?php echo $Flightplan->waypoints; ?></span><br/>
            <span class="calendar_event_flightplan_infos_category">Comments</span>
            <br/>
            <?php
			foreach ($Flightplan->comments as $comments)
			{
				$pseudo = $comments['pseudo'];
				$comment = $comments['comment'];
				echo "<b>$pseudo</b> $comment<br/>";
			}
            ?>
            <br/>
            <a href="./edit_flightplan.php5?idFlightplan=<?php echo $Flightplan->id; ?>" class="calendar_event_flightplan_add_comment">+ Add a comment</a>
        </div>
    <?php
    // We increment the flightplan index
    $flightplan_num++; }
}
?>
<!--
<div class="calendar_event_flightplan" onclick="
                        if(document.getElementById('file_flightplan<?php echo $Event->id;?>').style.display == 'block')
                        {
                            document.getElementById('file_flightplan<?php echo $Event->id;?>').style.display = 'none';
                        }
                        else document.getElementById('file_flightplan<?php echo $Event->id;?>').style.display='block';">
    <img src="./img/aircraft_takeoff.png" class="calendar_airportChoice" onclick="document.getElementById('flightplan_filling_form').style.display='block';document.getElementById('file_flightplan2-departureAirport').value = '<?php echo $Event->airportICAO;?>'; document.getElementById('file_flightplan2-date').value = '<?php echo $Event->date;?>'; document.getElementById('file_flightplan2-date').style.backgroundColor = '#33ee33'; document.getElementById('file_flightplan2-departureAirport').style.backgroundColor='#33ee33'; document.getElementById('file_flightplan2-departureTime').value = '<?php echo $Event->beginTime;?>'; document.getElementById('file_flightplan2-departureTime').style.backgroundColor='#33ee33'; document.location+='#flightplan_filling';return false;" /> 
    <img src="./img/aircraft_landing.png" class="calendar_airportChoice" onclick="document.getElementById('flightplan_filling_form').style.display='block';document.getElementById('file_flightplan2-arrivalAirport').value = '<?php echo $Event->airportICAO;?>'; document.getElementById('file_flightplan2-date').value = '<?php echo $Event->date;?>'; document.getElementById('file_flightplan2-date').style.backgroundColor = '#33ee33'; document.getElementById('file_flightplan2-arrivalAirport').style.backgroundColor='#33ee33'; document.getElementById('file_flightplan2-arrivalTime').value = '<?php echo $Event->endTime;?>'; document.getElementById('file_flightplan2-arrivalTime').style.backgroundColor='#33ee33'; document.location+='#flightplan_filling';return false;" /> <?php echo $Event->airportICAO;?>
</div>
-->
