<?php

// In case a date is selected
if (isset($_GET['viewEvents']) AND isset($_GET['date']))
{
    // I test the date format
    if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$_GET['date']) == TRUE)
    {
        // I pick the date
        $viewSelectedDate = $_GET['date'];
    }
    // Wrong format ?
    else
    {
        $viewSelectedDate = date('Y-m-d');
    }
}
// No date selected ?
else
{
    $viewSelectedDate = date('Y-m-d');
}
        
// I load every event occuring on this date
$eventsSelectedDate = filterEvents('date', $viewSelectedDate, $events);
        ?>

<div class="viewEvents">
    <!-- We show the date -->
    <h4>
        <img src="./img/menu_events.png" alt=""/> Flightgear ATC Events
        <span class="eventDate">
            <?php
            // If today
            if ($viewSelectedDate == date('Y-m-d')) { echo "Today"; }
            // If tomorrow
            else if ($viewSelectedDate == date('Y-m-d', strtotime(date('Y-m-d')." +1 day"))) { echo "Tomorrow"; }
            // If within this week
            else if ($viewSelectedDate <= date('Y-m-d', strtotime(date('Y-m-d')." +6 days"))) { echo "On ".date('l', strtotime($viewSelectedDate)); }
            else { echo date('F \> l jS', strtotime($viewSelectedDate)); }
            ?>
        </span>
    </h4>
    
    <!-- Div to create a new event -->
    <div class="newEvent">
        <a href="./new_event.php5?date=<?php echo $viewSelectedDate;?>">+ new event</a>
    </div>
    <div class="eventsList">
        <?php
        // For each event
        foreach ($eventsSelectedDate as $event)
        {
            // We select the event
            $Event = new Event();
            $Event->selectById($event);
            ?>
        
        <div class="event">
            <!-- Dividing information into multiple divs -->
            <div class="firstInformation">
                <span class="eventICAO"><?php echo $Event->airportICAO; ?></span>
                <span class="eventName"><?php echo getInfo('globalAirportName', 'airports_global', 'globalAirportICAO', $Event->airportICAO)." (".getInfo('globalAirportCity', 'airports_global', 'globalAirportICAO', $Event->airportICAO).")"; ?></span>
                <br/>
                <img src="./img/scheme_time.png" height="11" alt=""/>
                <span class="eventBeginTime"><?php echo date('H:i', strtotime($Event->beginTime)); ?> UTC</span>
                <span class="eventEndTime">&rarr; <?php echo date('H:i', strtotime($Event->endTime)); ?> UTC</span>
                <?php
                $atcName = getInfo('userName', 'users_names', 'userId', $Event->userId);
                if ($atcName != NULL)
                {
                    echo '<br/><span class="eventATC">Hosted by <b>'.$atcName.'</b></span>';
                }?>
            </div>
            <!-- Dividing information into multiple divs -->
            <div class="secondInformation">
                <img src="./img/scheme_airport.png" height="10"/>
                <a href="<?php echo $Event->docsLink; ?>" target="_blank" class="eventDocsLink">Airport documentation</a>
                <br/>
                <span class="eventFgcom"><img src="./img/menu_phraseology.png" height="14"/> FGCOM : <b><?php echo $Event->fgcom; ?></b></span>
                <br/>
                <span class="eventTeamspeak"><img src="./img/menu_phraseology.png" height="14"/> Mumble : <b><?php echo $Event->teamspeak; ?></b></span>
            </div>
            <!-- Removing the "float: left" -->
            <br style="clear: both; display: none;"/>
            <br/>
            <span class="quickFlightPlanToggle" onclick="document.getElementById('quickFlightPlanForm<?php echo $Event->id; ?>').style.display = 'block';">+ Flight Plan</span>
            <form class="quickFlightPlanForm" method="post" action="./index.php5" id="quickFlightPlanForm<?php echo $Event->id; ?>">
                Every field is required
                <br/>
                <br/>
                <input type="hidden" name="date" value="<?php echo $Event->date; ?>"/>
                <img src="./img/scheme_pilot.png"/>
                <input type="text" name="callsign" placeholder="Callsign" size="6" required="required"/>
                <input type="text" name="email" placeholder="Email address" size="25"/>
                <br/>
                Departure
                <br/>
                <img src="./img/scheme_airport.png"/>
                <input type="text" name="departureAirport" placeholder="ICAO" size="4" required="required"/> at
                <input type="text" name="departureTimeHours" placeholder="HH" size="2" required="required"/>:<input type="text" name="departureTimeMinutes" placeholder="MM" size="2" required="required"/> UTC<br/>
                Arrival
                <br/>
                <img src="./img/scheme_airport.png"/>
                <input type="text" name="arrivalAirport" placeholder="ICAO" size="4" required="required"/> at
                <input type="text" name="arrivalTimeHours" placeholder="HH" size="2" required="required"/>:<input type="text" name="arrivalTimeMinutes" placeholder="MM" size="2" required="required"/> UTC<br/>
                <input type="submit" class="fpSubmitButton" value="Quick submit!"/>
                Please make sure to be at <b><?php echo $Event->airportICAO; ?></b> between <b><?php echo $Event->beginTime." and ".$Event->endTime; ?> UTC</b>
            </form>
        </div>
        
            <?php
        }
        ?>
    </div>
</div>
