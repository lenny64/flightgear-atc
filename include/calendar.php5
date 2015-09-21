
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
                    <a href="./new_event.php5?date=<?php echo $dayCounter; ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus-sign"></span> New event</a>
                </div>
            </div>
            
            <div class="col-sm-4 col-xs-6">
                <div class="col-xs-12 createFlightplan">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" onclick="document.getElementById('file_flightplan-date').value='<?php echo $dayCounter;?>';"><span class="glyphicon glyphicon-plus-sign"></span> New flightplan</button>
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
                
                $comments = '';
                if (isset($Event->remarks) AND $Event->remarks != NULL AND $Event->remarks != "N/A")
                {
                    $comments = '<div class="row">';
                    $comments .= '<div class="col-xs-12">';
                    $comments .= '<p class="event-comments">';
                    $comments .= htmlspecialchars_decode($Event->remarks);
                    $comments .= '</p>';
                    $comments .= '</div>';
                    $comments .= '</div>';
                }
                
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
                    <?php echo $comments; ?>
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