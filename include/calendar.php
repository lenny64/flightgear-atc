
<?php

include_once('./include/calendarController.php');
if (!isset($_GET['revertToV1'])) {
?>
<div class="my-2" id="next_atc_events">

    <div class="collapse mb-2" id="collapseMenuDetails">
        <form action="./" class="form form-inline" method="get">
            <a id="collapse_events" href="#" class="mr-2 btn btn-info btn-sm"><span class="oi oi-collapse-down" title="collapse" aria-hidden="true"></span> Collapse/expand events</a>
            <input type="text" id="datepicker" name="dateBegin" class="mx-2 form-control form-control-sm" placeholder="Select a date"/><input type="submit" class="mx-2 btn btn-outline-primary btn-sm" value="Go"/>
            <a href="./index.php?revertToV1">Revert to old version</a>
        </form>
    </div>
    <a href="#collapseMenuDetails" class="mr-2 btn btn-sm btn-outline-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseMenuDetails"><span class="oi oi-cog" title="expand menu" aria-hidden="true"></span></a>

</div>

<div class="row mt-3 calendar">
    <a href="./index.php?dateBegin=<?= $today_minus_x_days;?>#next_atc_events" class="btn btn-lg btn-day btn-previous-days <?=$style_previous_events;?>"><span class="oi oi-chevron-left" title="previous days" aria-hidden="true"></span></a>
    <a href="./index.php?dateBegin=<?= $real_today;?>#next_atc_events" class="btn btn-lg btn-day btn-today <?=$style_previous_events;?>"><span class="oi oi-home" title="today" aria-hidden="true"></span></a>
    <a href="./index.php?dateBegin=<?= $today_plus_x_days;?>#next_atc_events" class="btn btn-secondary btn-lg btn-day btn-next-days"><span class="oi oi-chevron-right" title="next days" aria-hidden="true"></span></a>

<?php
for ($calendarDay = 0 ; $calendarDay < $number_days_displayed ; $calendarDay++)
{
    $Day = new Day($calendarDay);
    $Day->getDayCounter($today);
    $Day->getDayDisplayInfo();
    $Day->getEventsList($events);
    // Based on the controlled area
    // $controlled_area_events = new Event();
    // $list_controlled_area_events = $controlled_area_events->getControlledAreaEvents($Day->day_counter);
    // foreach ($list_controlled_area_events as $event) {
    //     $Airport = new Airport();
    //     // I collect lat and lon
    //     $Airport->selectByICAO($event['airport_icao']);
    //     $event['was_really_controlled_last_week'] = $Airport->wasReallyControlledLastWeek($Day->day_counter);
    // }
    $airport_observations = new Airport();
    $airport_observations_summary = $airport_observations->getAirportObservationSummary($Day->day_counter);

    $events_badge_text = $Day->getEventsBadgeText();
    $Flightplan = new Flightplan();
    $Flightplan->dateDeparture = $Day->day_counter;
    $Flightplan->dateArrival = $Day->day_counter;
    $total_flightplans = $Flightplan->getFlightplans();
    $total_flightplans_badge_text = $Day->getTotalFlightplansBadgeText($total_flightplans);
    ?>

<div class="col-md-3 col-sm-6">
    <div class="text-center mb-1">
        <h5 class="mb-0"><?=$Day->day_line;?></h5>
        <?= $total_flightplans_badge_text ;?>
        <span class="badge badge-primary"><?= $events_badge_text;?></span> <a href="./new_event.php?date=<?=$Day->day_counter;?>" class="badge badge-primary"><span class="oi oi-plus" title="Add an event" aria-hidden="true"></span></a>
    </div>
    <?php
    // FLIGHTPLANS LOOP
    foreach ($total_flightplans as $fp) {
        $FP = new Flightplan();
        $FP->selectById($fp['flightplanId']);
        ?>
        <div class="card border-secondary mb-2 flightplans-day-list" style="display: none;">
            <div class="card-header">
                <?= $FP->callsign;?> <small><?= $FP->aircraftType;?></small>
            </div>
            <div class="card-body text-secondary">
                <div class="row">
                    <div class="col mb-2">
                        From
                        <br/>
                        <span class="badge badge-success"><?= $FP->departureAirport;?></span>
                        <span class="badge badge-info"><?= $FP->departureTime;?></span>
                    </div>
                    <div class="col mb-2">
                        To
                        <br/>
                        <span class="badge badge-success"><?= $FP->arrivalAirport;?></span>
                        <span class="badge badge-info"><?= $FP->arrivalTime;?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-2">
                        Pilot: <?= $FP->pilotName;?>
                        <br/>
                        Flight rules: <span class="badge badge-secondary"><?= $FP->category;?></span>
                        <br/>
                        Altitude: <span class="badge badge-secondary"><?= $FP->cruiseAltitude;?></span>
                        <br/>
                        Flight nr: <span class="badge badge-secondary"><?= $FP->flightNumber;?></span>
                        <br/>
                        Waypoints:<br/> <?= $FP->waypoints;?>
                    </div>
                </div>
                <p>
                    Flightplan <?= $FP->status;?> <a href="./edit_flightplan.php?flightplanId=<?php echo $FP->id;?>" class="btn btn-outline-secondary btn-sm"><span class="oi oi-pencil" title="Edit flightplan" aria-hidden="true"></span></a>
                </p>
            </div>
        </div>
        <?php
    }
    ?>
    <?php // WHEN THERE ARE NO EVENTS ?>
    <?= $Day->no_events_message; ?>
    <?php
    // EVENTS LOOP
    foreach ($Day->events_list as $event)
    {
        $Event = new Event();
        $Event->selectById($event);
        $Event->getATCInfo();

        // We indicate if there are flightplans to show
        $showFlightplans = false;
        $flightplans = $Event->getFlightplans();
        if (sizeof($flightplans) > 0) {
            $showFlightplans = true;
        }

        ?>
        <div class="card mb-2 <?= $additional_card_class;?>">
            <?php if (file_exists("./img/airport_".$Event->airportICAO.".jpg")) { ?>
            <img class="card-img-top img-fluid" src="./img/airport_<?= $Event->airportICAO;?>.jpg" alt="<?=$Event->airportICAO;?> ATC event image">
            <?php } ?>
            <div class="card-header event-title" data-eventid="<?= $Event->id; ?>">
                <h6><img src="./img/menu_controlled.png"/> <a href="./show_event.php?eventId=<?=$Event->id;?>"><?= $Event->airportICAO; ?> <?= $Event->airportName; ?></a></h6>
                <?= $Event->airportCity; ?>
                <br/>
                <span class="badge badge-success"><?= $Event->beginTime; ?></span> &rarr; <span class="badge badge-success"><?= $Event->endTime; ?></span>
                <?php if ($showFlightplans === true) { ?><span class="badge badge-info"><?= sizeof($flightplans);?> flightplans</span><?php } ?>
            </div>
            <div class="card-body py-2 event-details" id="event_details_<?= $Event->id; ?>">
                <?php if ($Event->fgcom != "N/A") { ?>
                    <small><span class="oi oi-comment-square" aria-hidden="true"></span> <strong>FGCOM</strong> <?=$Event->fgcom; ?></small>
                    <br/>
                <?php } if ($Event->teamspeak != "N/A") { ?>
                    <small><span class="oi oi-comment-square" aria-hidden="true"></span> <strong>Mumble</strong> <?= $Event->teamspeak; ?></small>
                    <br/>
                <?php } ?>
                <?php if ($Event->docsLink != "http://") { ?>
                <small><a href="<?php echo $Event->docsLink; ?>" target="_blank"><span class="oi oi-document" aria-hidden="true"></span> Airport documentation</a></small>
                <br/>
                <?php } ?>
                <?php if ($Event->atcVerified == "true" && $Event->atcName != "") { ?>
                    Hosted by <strong><span class="badge badge-success"><span class='oi oi-check' aria-hidden='true'></span> <?php echo $Event->atcName; ?></strong></span>
                <?php } else if ($Event->atcName != "") { ?>
                    Hosted by <strong><?php echo $Event->atcName; ?></strong>
                <?php } ?>
                <hr/>
                <div class="mt-2">
                    <a href="#" class="btn btn-sm btn-info" data-toggle="modal" data-target="#myModal" onclick="document.getElementById('file_flightplan-date').value='<?php echo $Day->day_counter;?>';"><span class="oi oi-plus" aria-hidden="true"></span> flight plan</a>
<?php
                foreach ($flightplans as $flightplan) {
                    $FP = new Flightplan();
                    $FP->selectById($flightplan);
                    $printDateDeparture = date('d M', strtotime($FP->dateDeparture));
                    $printDepartureTime = date('H:i', strtotime($FP->departureTime));
                    $printDateArrival = date('d M', strtotime($FP->dateArrival));
                    $printArrivalTime = date('H:i', strtotime($FP->arrivalTime));
?>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <b><?= $FP->callsign; ?></b> <small><span class="text-muted"><?=$FP->aircraftType;?></span></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="badge badge-info"><?= $FP->departureAirport; ?></span>
                            <br/>
                            <span class="badge badge-success"><?= $printDepartureTime; ?></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="badge badge-info"><?= $FP->arrivalAirport; ?></span>
                            <br/>
                            <span class="badge badge-success"><?= $printArrivalTime; ?></span>
                        </div>
                    </div>
                        <a class="" href="./edit_flightplan.php?flightplanId=<?=$FP->id;?>" target="_blank"><span class="oi oi-pencil" aria-hidden="true"></span> Edit flightplan</a>

<?php
                }
?>
                </div>
            </div>
        </div>

        <?php
    } ?>
    <?php
    // ARTIFICIAL EVENTS LOOP
    if (sizeof($airport_observations_summary) > 0) { ?>
    <hr/>
    <h6 class="text-muted py-2 text-center"><span class="oi oi-pulse"></span> Based on stats <span class="small"><a href="faq.php#basedonstatsevents" target="_blank">learn more</a></span></h6>
    <?php
        $text_multiple_airports = (sizeof($airport_observations_summary) > 1) ? 'these airports' : 'this airport';
        foreach ($airport_observations_summary as $icao => $information) {
            $nb_weeks = $information['nb_weeks'];
            $style = "info";
            if ($information['accuracy'] >= 75) {
                $style = "success";
            }
            else if ($information['accuracy'] <= 50) {
                $style = "dark";
            }

            if ($information['nb_hours'] > 1) {
                $information['nb_hours'] = $information['nb_hours'] . " hours";
            }
            else {
                $information['nb_hours'] = $information['nb_hours'] . " hour";
            }
            ?>
            <div class="card mb-1 border-<?=$style;?>">
                <div class="card-body py-2">
                    <h6 class="text-<?= $style; ?>"><img src="./img/menu_controlled.png"/> <?= $icao; ?> * <span class="badge badge-success"><?= $information['begin']; ?></span> &rarr; <span class="badge badge-success"><?= $information['end']; ?></span> <span class="small">UTC</span></h6>
                    <p class="text-<?=$style;?> my-1">
                        <span class="badge badge-<?=$style;?>"><?= $information['accuracy']; ?> %</span> of chances that this airport will be controlled ~ <?=$information['nb_hours'];?>.
                        <br/>
                        <span class="small text-muted">(controlled <b><?= $information['nb_days_recorded']; ?> <?= $Day->day_in_week; ?>s</b> / <?= $nb_weeks; ?>)</span>
                    </p>
                    <?php if ($information['user_name'] != NULL && $information['user_name'] != '') { ?>
                    <p class="text-success my-1">
                        <b>Regularly controlled by </b><span class="badge badge-success"><?= $information['user_name']; ?></span>
                    </p>
                    <?php } ?>
                    <?php if ($information['user_name'] != NULL && $information['user_name'] != '' && $information['accuracy'] >= 75) { ?>
                    <div class="alert alert-info p-1 text-success small font-weight-bold">
                        <?php if ($information['accuracy'] >= 90) { ?>
                            Incredibly regular control at <?= $icao; ?>! Get ready to fly!
                        <?php } else if ($information['accuracy'] >= 80) { ?>
                            <?= $icao; ?> is a good spot! Let's fly!
                        <?php } else if ($information['accuracy'] >= 75) { ?>
                            You should give <?= $icao; ?> a try ;)
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <?php // Warning message
                    if (isset($information['warning']) && $information['warning'] != NULL) { ?>
                        <p class="text-warning small my-1">
                            <span class="oi oi-warning"></span> <?= $information['warning']; ?>
                        </p>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
        ?>
        <p class="mb-2 text-center">
            <span class="text-muted small">* Based on control activity of <?= $text_multiple_airports; ?> during last <?= $nb_weeks; ?> weeks. Please take this information carefully.
            <br/>
            <a href="faq.php#basedonstatsevents" target="_blank">learn more</a></span>
        </p>
    <?php
    }
    ?>
</div>

    <?php
} ?>
</div>
<?php
}
else {
    ?>
    <div class='alert alert-warning'>
        You are using an old version of this website. It may not work properly.<br/>
        <a href="./index.php" class="btn btn-success">Lead me to the new version</a>
    </div>
<?php
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

        // Printing a visual help
        if ($calendarDay == 0 AND date('l', strtotime($dayCounter)) != "Monday")
        {
            echo "<div class='card card-header'><h4><span class='oi oi-calendar' aria-hidden='true'></span> This week</h4></div>";
        }
        else if ($calendarDay > 0 AND $calendarDay < 8 AND date('l', strtotime($dayCounter)) == "Monday")
        {
            echo "<div class='card card-header'><h4><span class='oi oi-calendar' aria-hidden='true'></span> Next week</h4></div>";
        }
        else if ($calendarDay >= 8 AND date('l', strtotime($dayCounter)) == "Monday")
        {
            echo "<div class='card card-header'><h4><span class='oi oi-calendar' aria-hidden='true'></span> Week ".date('W', strtotime($dayCounter))."</h4></div>";
        }
        ?>

<div class="card bg-light my-3">
    <!-- BELOW THE MAIN LINE IN THE TABLE -->
    <div class="card-header">
        <div class="row">
            <div class="col-sm-4">
                <strong><span class="oi oi-clock" aria-hidden="true"></span> <?php echo $dayLine;?></strong>
            </div>
            <!-- Event counter indicator -->
            <div class="col-sm-4">
                <?php
                if (isset($filteredEvents) AND sizeof($filteredEvents) > 0)
                {
                    $airportLabel = 'badge-primary';
                    (sizeof($filteredEvents) > 1) ? $airportText = 'airports' : $airportText = 'airport';
                }
                else
                {
                    $airportLabel = 'badge-default';
                    $airportText = 'airports';
                }
                ?>
                <span class="badge <?php echo $airportLabel;?>"><?php echo sizeof($filteredEvents);?> <?php echo $airportText;?></span>
                <br/>
                <?php
                for ($bulletEvent = 0 ; $bulletEvent < sizeof($filteredEvents) ; $bulletEvent++)
                {
                    echo "&bull;";
                }
                ?>&nbsp;
            </div>
            <!-- Flightplan counter indicator -->
            <div class="col-sm-4">
                <?php
                $Flightplan = new Flightplan();
                $Flightplan->dateDeparture = $dayCounter;
                $Flightplan->dateArrival = $dayCounter;
                $flightplans = $Flightplan->getFlightplans();

                if (isset($flightplans) AND $flightplans != NULL AND sizeof($flightplans) > 0)
                {
                    $flightplanLabel = 'badge-success';
                    (sizeof($flightplans) > 1) ? $flightplanText = 'flightplans' : $flightplanText = 'flightplan';
                }
                else
                {
                    $flightplanLabel = 'badge-default';
                    $flightplanText = 'flightplans';
                }
                ?>
                <span class="badge <?php echo $flightplanLabel;?>"><?php echo sizeof($flightplans);?> <?php echo $flightplanText;?></span>
                <br/>
                <span class="flightplan-bullet-counter">
                <?php
                for ($bulletFlightplans = 0 ; $bulletFlightplans < sizeof($flightplans) ; $bulletFlightplans++)
                {
                    echo "&bull;";
                }
                ?>
                </span>
            </div>
        </div>
    </div>

    <!-- BELOW THE ADDITIONAL INFORMATION ABOUT AIRPORTS -->
    <?php
    // if the date is today or tomorrow, we don't collapse
    if ($calendarDay <= 0) {
        $collapseBehaviour = "";
    }
    else {
        $collapseBehaviour = "collapse";
    }
    ?>
    <div class="sublinks dayContent" id="<?php echo $dayCounter; ?>">
        <div class="row">
            <!-- Button to create new event -->
            <div class="col-sm-8 col-xs-6" style="padding-right: 0;">
                <div class="col-sm-12 createEvent">
                    <a href="./new_event.php?date=<?php echo $dayCounter; ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus-sign"></span> New ATC event</a>
                </div>
            </div>
            <!-- Button to create new flightplan -->
            <div class="col-sm-4 col-xs-6" style="padding-left: 0;">
                <div class="col-sm-12 createFlightplan">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" onclick="document.getElementById('file_flightplan-date').value='<?php echo $dayCounter;?>';"><span class="glyphicon glyphicon-plus-sign"></span> New flightplan</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8 col-xs-6">
            <?php

            foreach ($filteredEvents as $event)
            {
                $Event = new Event();
                $Event->selectById($event);

                $atcName = getInfo('userName', 'users_names', 'userId', $Event->userId);
                $atcParams = json_decode(getInfo('userParameters', 'users', 'userId', $Event->userId));
                $verified = $atcParams->{'verified'};


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

                <?php
                // For later
                // <div class="event" style="background: url('./img/banniere7.jpg') no-repeat center center;">
                //
                ?>
                <div class="event">
                    <div>
                        <div class="row">
                            <!-- ICAO AND AIRPORT LOCATION -->
                            <div class="col-sm-6">
                                <span class="label label-primary"><?php echo $Event->airportICAO;?></span>
                                <span class="event-location">
                                  <?php
                                  echo $Event->airportName;
                                  if (isset($Event->airportCity) AND $Event->airportCity != NULL)
                                  {
                                    echo "(".$Event->airportCity.")";
                                  }
                                  ?>
                                </span>
                            </div>
                            <!-- TIMES -->
                            <div class="col-sm-6">
                                <span class="event-times"><?php echo date('H:i', strtotime($Event->beginTime)); ?> UTC &rarr; <?php echo date('H:i', strtotime($Event->endTime)); ?> UTC</span>
                            </div>
                        </div>
                        <div class="row">
                            <!-- COMMUNICATION -->
                            <div class="col-sm-12">
                                <span class="event-communication"><strong>FGCOM</strong> <?php echo $Event->fgcom; ?></span>
                                <span class="event-communication"><strong>Mumble</strong> <?php echo $Event->teamspeak; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <!-- DOCUMENTATION LINK -->
                            <div class="col-sm-12">
                                <span class="event-documentation"><a href="<?php echo $Event->docsLink; ?>" target="_blank">Airport documentation</a></span>
                            </div>
                        </div>
                        <div class="row">
                            <!-- ATC NAME -->
                            <div class="col-sm-12">
                                <?php if ($verified == "true")
                                {   ?>
                                    <span class="label label-success">Hosted by <strong><?php echo $atcName; ?></strong> <span class='glyphicon glyphicon-ok' aria-hidden='true'></span></span>
                                    <?php
                                }
                                else
                                {   ?>
                                    <span class="event-atc">Hosted by <strong><?php echo $atcName; ?></strong></span>
                                    <?php
                                } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- COMMENTS -->
                                <?php echo nl2br($comments); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            }

            ?>
            </div>
            <div class="col-sm-4 col-xs-6">



            <?php
            if (isset($flightplans))
            {
                foreach ($flightplans as $flightplan)
                {
                    $Flightplan->selectById($flightplan['flightplanId']);

                    // I'm making dates and times more readable
                    $printDateDeparture = date('d M', strtotime($Flightplan->dateDeparture));
                    $printDepartureTime = date('H:i', strtotime($Flightplan->departureTime));
                    $printDateArrival = date('d M', strtotime($Flightplan->dateArrival));;
                    $printArrivalTime = date('H:i', strtotime($Flightplan->arrivalTime));

                    ?>
                <div class="flightplan">
                    <div class="row">
                        <!-- CALLSIGN -->
                        <div class="col-sm-6">
                            <span class="badge badge-success"><strong><span class="oi oi-star" aria-hidden="true"></span> <?php echo $Flightplan->callsign;?></strong></span>
                        </div>
                        <!-- AIRPLANE -->
                        <div class="col-sm-6">
                            <span class="flightplan-plane"><span class="oi oi-plane"></span> <?php echo $Flightplan->aircraftType;?></span>
                        </div>
                        <!-- INFORMATION ABOUT DEPARTURE -->
                        <div class="col-sm-6">
                            <span class="flightplan-info">Departure</span>
                        </div>
                        <!-- INFORMATION ABOUT ARRIVAL -->
                        <div class="col-sm-6">
                            <span class="flightplan-info">Arrival</span>
                        </div>
                        <!-- DEPARTURE AIRPORT -->
                        <div class="col-sm-6">
                            <span class="flightplan-time"><?php echo $Flightplan->departureAirport;?></span>
                        </div>
                        <!-- ARRIVAL AIRPORT -->
                        <div class="col-sm-6">
                            <span class="flightplan-time"><?php echo $Flightplan->arrivalAirport;?></span>
                        </div>
                        <!-- DEPARTURE TIME -->
                        <div class="col-sm-6">
                            <span class="label label-primary"><?php echo $printDepartureTime;?></span> <span class="label label-primary"><?php echo $printDateDeparture;?></span>
                        </div>
                        <!-- ARRIVAL TIME -->
                        <div class="col-sm-6">
                            <span class="label label-primary"><?php echo $printArrivalTime;?></span> <span class="label label-primary"><?php echo $printDateArrival;?></span>
                        </div>
                        <!-- STATUS -->
                        <div class="col-sm-6">
                            <span class="label label-default">flightplan <?php echo $Flightplan->status;?></span>
                        </div>
                        <!-- EDIT BUTTON -->
                        <div class="col-sm-6">
                            <a href="./edit_flightplan.php?flightplanId=<?php echo $Flightplan->id;?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit</a>
                        </div>

                    </div>
                </div>
                <?php
                }
            }
            ?>
            </div>
        </div>
    </div>
</div>

        <?php
    }
}
    ?>




    <script type="text/javascript" language="javascript" src="./include/calendar.js"></script>



    <?php
for ($calendarDay = 0 ; $calendarDay < $number_days_displayed ; $calendarDay++)
{
    $Day = new Day($calendarDay);
    $Day->getDayCounter($today);
    $Day->getDayDisplayInfo();
    $Day->getEventsList($events);

    foreach ($Day->events_list as $event) {
        $Event = new Event();
        $Event->selectById($event);
        $Event->getATCInfo();
        $Event->image = "http://flightgear-atc.alwaysdata.net/img/airport_EHAM.jpg";
        if (file_exists("./img/airport_".$Event->airportICAO.".jpg")) {
            $Event->image = "http://flightgear-atc.alwaysdata.net/img/airport_".$Event->airportICAO.".jpg";
        }
        if ($Event->atcName == "" OR $Event->atcName != TRUE) {
            $Event->atcName = "Unknown ATC";
        }
    ?>
    <script type="application/ld+json">
        {
            "@context":"http://schema.org",
            "@type":"Event",
            "name":"ATC Event at <?= $Event->airportCity; ?> between <?= $Event->beginTime; ?> and <?= $Event->endTime; ?>",
            "location":{
                "@type":"VirtualLocation",
                "name":"<?= $Event->airportICAO;?> airport <?=$Event->airportCity;?>",
                "url":"http://flightgear-atc.alwaysdata.net/show_event?eventId=<?=$Event->id;?>"
            },
            "startDate":"<?=$Event->date;?>T<?=$Event->beginTime;?>-00:00",
            "endDate":"<?=$Event->date;?>T<?=$Event->endTime;?>-00:00",
            "eventStatus": "https://schema.org/EventScheduled",
            "url":"http://flightgear-atc.alwaysdata.net/show_event?eventId=<?=$Event->id;?>",
            "description": "Flightgear multiplayer Air Traffic Control event at <?= $Event->airportCity; ?> on <?= $Event->date; ?> between <?= $Event->beginTime; ?> and <?= $Event->endTime; ?>",
            "eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",
            "offers": {
                "@type": "Offer",
                "url": "http://flightgear-atc.alwaysdata.net/show_event?eventId=<?=$Event->id;?>",
                "price": "0",
                "priceCurrency": "EUR",
                "availability": "https://schema.org/InStock",
                "validFrom": "<?=$Event->date;?>"
            },
            "isAccessibleForFree": 1,
            "organizer": {
                "@type": "Organization",
                "url": "http://flightgear-atc.alwaysdata.net",
                "name": "Flightgear ATC",
                "description": "Flightgear Air Traffic Controller events"
            },
            "performer": {
                "@type": "Person",
                "name": "<?=$Event->atcName;?>"
            },
            "image": "<?=$Event->image;?>"
        }
    </script>
    <?php
    }
}
     ?>
