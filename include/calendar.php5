
<!-- FILTER FORM - NOT VERY USED ACTUALLY !
<form class="calendar_filter" method="get" action="./index.php5">
    Show only :
    <input type="text" name="filterICAO" placeholder="ICAO" size="5"/> <input type="submit" value="Filter"/>
</form>
-->



<div class="panel list-group" id="menucollapse">
    
    <?php
    
    $today = date('Y-m-d');
    
    for ($calendarDay = 0 ; $calendarDay < 30 ; $calendarDay++)
    {
        $dayCounter = date('Y-m-d', strtotime($today." +".$calendarDay." days"));
        if ($calendarDay == 0) $dayLine = "Today";
        else if ($calendarDay == 1) $dayLine = "Tomorrow";
        else if ($calendarDay > 1 AND $calendarDay < 6) $dayLine = "On ".date('l', strtotime($dayCounter));
        else $dayLine = date('D j M', strtotime($dayCounter));
        
        if (isset($events))
        {
            $filteredEvents = filterEvents('date', $dayCounter, $events);
        }
        ?>
    
    <!-- BELOW THE MAIN LINE IN THE TABLE -->
    <div class="list-group-item" data-toggle="collapse" data-target="#<?php echo $dayCounter; ?>" data-parent="#menucollapse">
        <div class="row">
            <div class="col-xs-4">
                <strong><?php echo $dayLine;?></strong>
            </div>
            <div class="col-xs-4">
                <?php
                if (sizeof($filteredEvents) > 0)
                {
                    $airportLabel = 'label-primary';
                }
                else
                {
                    $airportLabel = 'label-default';
                }
                ?>
                <span class="label <?php echo $airportLabel;?>"><?php echo sizeof($filteredEvents);?> airports</span>
            </div>
            <div class="col-xs-4">
                <span class="label label-success">0 flightplans</span>
            </div>
        </div>
    </div>
    
    <!-- BELOW THE ADDITIONAL INFORMATION ABOUT AIRPORTS -->
    <div class="sublinks collapse" id="<?php echo $dayCounter; ?>">
        <div class="row">
            <div class="col-sm-8 col-xs-6">
                <div class="col-xs-12 createEvent">
                    <a href="./new_event.php5?date=<?php echo $dayCounter; ?>" class="btn btn-primary btn-sm">+ New event</a>
                </div>
            </div>
            
            <div class="col-sm-4 col-xs-6">
                <div class="col-xs-12 createFlightplan">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" onclick="document.getElementById('file_flightplan-date').value='<?php echo $dayCounter;?>';">+ New flightplan</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
            <?php

            foreach ($filteredEvents as $event)
            {
                $Event = new Event();
                $Event->selectById($event);
                
                $atcName = getInfo('userName', 'users_names', 'userId', $Event->userId);
                
                ?>
                
                <div class="event">
                    <div class="row">
                        <!-- ICAO AND AIRPORT LOCATION -->
                        <div class="col-sm-6">
                            <span class="label label-primary"><?php echo $Event->airportICAO;?></span>
                            <span class="event-location"><?php echo getInfo('globalAirportName', 'airports_global', 'globalAirportICAO', $Event->airportICAO)." (".getInfo('globalAirportCity', 'airports_global', 'globalAirportICAO', $Event->airportICAO).")"; ?></span>
                        </div>
                        <!-- TIMES -->
                        <div class="col-sm-6">
                            <span class="event-times"><?php echo date('H:i', strtotime($Event->beginTime)); ?> UTC &rarr; <?php echo date('H:i', strtotime($Event->endTime)); ?> UTC</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="event-communication"><strong>FGCOM</strong> <?php echo $Event->fgcom; ?></span>
                            <span class="event-communication"><strong>Mumble</strong> <?php echo $Event->teamspeak; ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="event-documentation"><a href="<?php echo $Event->docsLink; ?>" target="_blank">Airport documentation</a></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="event-atc">Hosted by <strong><?php echo $atcName; ?></strong></span>
                        </div>
                    </div>
                </div>
                
                <?php
            }

            ?>
            </div>
        </div>
    </div>
    
        <?php
    }
    
    ?>
    
</div>


<?php

/*

// We open the calendar DIV, master
echo "<a name='calendar_anchor'> </a>";

for ($monthNumber = 0; $monthNumber < 2; $monthNumber++)
{
    $today = date('Y-m-d');
    $dayCursor = date('Y-m-d',strtotime(date('Y-m-01').'+ '.$monthNumber.' month'));
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
        
        // We check if the date is selected. We will apply a different style.
        $isSelected = '';
        if (isset($_GET['date']) AND $_GET['date'] == $dayCounter)
        {
            $isSelected = 'isSelected';
        }
        
        // CELL DAY GENERATION
        // Is this date in the past ?
        if ($dayCounter < $today) { echo "<td class='pastDate'>"; }
        // Is this date today ?
        elseif ($dayCounter == $today) { echo "<td class='today'><a href='index.php5?viewEvents&date=".$dayCounter."'>"; }
        
        // Otherwise it's in the future
        else { echo "<td class='".$isSelected."'><a href='index.php5?viewEvents&date=".$dayCounter."'>"; }
        
        // CELL CONTENT GENERATION
        // Inside the cell we print the day number
        echo date('d',strtotime($dayCounter));
        
        // We get all the events for this particular day
        if (isset($_GET['filterICAO']) AND $_GET['filterICAO'] != NULL)
        {
            // In the case there is an ICAO filter, i gather all events relative to this particular ICAO
            $events = array();
            $events_list = $db->query("SELECT eventId,airportICAO FROM events WHERE date = '$dayCounter' AND airportICAO = '".$_GET['filterICAO']."' ORDER BY beginTime");
            $i = 0;
            foreach ($events_list as $row)
            {
                $events[$i]['Id'] = $row['eventId'];
                $events[$i]['airportICAO'] = $row['airportICAO'];
                $i++;
            }
            $events[$dayCounter] = $events;
        }
        else
        {
            if (isset($events))
            {
                $filteredEvents = filterEvents('date', $dayCounter, $events);
            }
        }
        // If there are no events for this day we don't show anything
        if (isset($filteredEvents) AND sizeof($filteredEvents) < 1) { echo "<br/>&nbsp;"; }
        // Otherwise we show the number of events occuring this day
        else if (isset($filteredEvents) AND sizeof($filteredEvents) >= 1)
        {
            // Number of events
            echo "<br/><span class='number_events'>".sizeof($filteredEvents)." ";
            // Several events ?
            if (sizeof($filteredEvents) > 1) { echo "events"; }
            // Single event ?
            else { echo "event"; }
            echo "</span>";
            
        }
        
        // Was there a link because it is today or a future date ?
        if ($dayCounter >= $today)
        {
            echo "</a>";
            // If an ATC is connected, we show a little "+" button to let him plan an event at this date
            if (isset($_SESSION['id']) AND $_SESSION['id'] != NULL AND $_SESSION['mode'] == 'connected')
            {
                echo "<br/><a href='./new_event.php5?date=".$dayCounter."'>+</a>";
            }
        }
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

*/

?>
