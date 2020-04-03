
<?php

$number_days_displayed = 4;

if (isset($_GET['dateBegin']) && $_GET['dateBegin'] != NULL) {
    $today = $_GET['dateBegin'];
} else {
    $today = date('Y-m-d');
}
$today_plus_x_days = date('Y-m-d', strtotime($today." +".$number_days_displayed." days"));
$today_minus_x_days = date('Y-m-d', strtotime($today." -".$number_days_displayed." days"));
$show_previous_events = true;
if ($today_minus_x_days < date('Y-m-d')) {
    $show_previous_events = false;
}
?>

<h1 class="display-4 my-4" id="next_atc_events">
    Next upcoming ATC Events
    <?php if ($show_previous_events == true) { ?><a href="./index.php?dateBegin=<?= $today_minus_x_days;?>#next_atc_events" class="btn btn-info btn-sm">&larr; previous events</a><?php } ?>
    <a href="./index.php?dateBegin=<?= $today_plus_x_days;?>#next_atc_events" class="btn btn-info btn-sm">next events &rarr;</a>
</h1>

<div class="row mt-3">

<?php
for ($calendarDay = 0 ; $calendarDay < $number_days_displayed ; $calendarDay++)
{
    $additional_card_class = "";
    $dayCounter = date('Y-m-d', strtotime($today." +".$calendarDay." days"));
    if ($calendarDay == 0 AND $dayCounter == date('Y-m-d')) {
        $dayLine = "Today";
        $additional_card_class = "border-info";
    }
    else if ($calendarDay == 1 AND $dayCounter == date('Y-m-d', strtotime(date('Y-m-d')." +1 day"))) $dayLine = "Tomorrow";
    else if ($calendarDay > 1 AND $calendarDay < 6) $dayLine = "On ".date('l', strtotime($dayCounter));
    else $dayLine = date('D j M', strtotime($dayCounter));

    if (isset($events))
    {
        $filteredEvents = filterEvents('date', $dayCounter, $events);
    }
    ?>

<div class="col-md-3 col-sm-6">
    <center><h5><?=$dayLine;?> <a href="./new_event.php?date=<?=$dayCounter;?>" class="btn btn-primary btn-sm">+</a></h5></center>
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
        $showFlightplans = false;
        $flightplans = $Event->getFlightplans();
        if (sizeof($flightplans) > 0) {
            $showFlightplans = true;
        }

        ?>
        <div class="card mb-1 <?= $additional_card_class;?>">
            <div class="card-header event-title" data-eventid="<?= $Event->id; ?>">
                <a href="#"><?= $Event->airportICAO; ?> <?= $Event->airportName; ?></a>
                <br/>
                <?= $Event->airportCity; ?>
                <br/>
                <span class="badge badge-success"><?= $Event->beginTime; ?></span> &rarr; <span class="badge badge-success"><?= $Event->endTime; ?></span>
                <?php if ($showFlightplans === true) { ?><span class="badge badge-info"><?= sizeof($flightplans);?> flightplans</span><?php } ?>
            </div>
            <div class="card-body event-details" id="event_details_<?= $Event->id; ?>">
                <p class="card-text">
                    <?php if ($Event->fgcom != "N/A") { ?>
                        <small><strong>FGCOM</strong> <?=$Event->fgcom; ?></small>
                        <br/>
                    <?php } if ($Event->teamspeak != "N/A") { ?>
                        <small><strong>Mumble</strong></small>
                        <br/>
                        <small><?= $Event->teamspeak; ?></small>
                        <br/>
                    <?php } ?>
                    <small><a href="<?php echo $Event->docsLink; ?>" target="_blank">Airport documentation</a></small>
                    <br/>
                    <?php if ($verified == "true") { ?>
                        <span class="label label-success">Hosted by <strong><?php echo $atcName; ?></strong> <span class='glyphicon glyphicon-ok' aria-hidden='true'></span></span>
                    <?php } else { ?>
                        <span class="event-atc">Hosted by <strong><?php echo $atcName; ?></strong></span>
                    <?php } ?>
                </p>
            </div>
        </div>

        <?php
    }
    ?>
</div>

    <?php
} ?>
</div>
<?php
/*
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
            echo "<div class='well well-calendar'><h4><span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> This week</h4></div>";
        }
        else if ($calendarDay > 0 AND $calendarDay < 8 AND date('l', strtotime($dayCounter)) == "Monday")
        {
            echo "<div class='well well-calendar'><h4><span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> Next week</h4></div>";
        }
        else if ($calendarDay >= 8 AND date('l', strtotime($dayCounter)) == "Monday")
        {
            echo "<div class='well well-calendar'><h4><span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> Week ".date('W', strtotime($dayCounter))."</h4></div>";
        }
        ?>

<div class="panel list-group" id="menucollapse">
    <!-- BELOW THE MAIN LINE IN THE TABLE -->
    <div class="list-group-item list-group-item-info list-clickable" data-toggle="collapse" data-target="#<?php echo $dayCounter; ?>" data-parent="#menucollapse">
        <div class="row">
            <div class="col-xs-4">
                <strong><span class="glyphicon glyphicon-time" aria-hidden="true"></span> <?php echo $dayLine;?></strong>
            </div>
            <!-- Event counter indicator -->
            <div class="col-xs-4">
                <?php
                if (isset($filteredEvents) AND sizeof($filteredEvents) > 0)
                {
                    $airportLabel = 'label-primary';
                    (sizeof($filteredEvents) > 1) ? $airportText = 'airports' : $airportText = 'airport';
                }
                else
                {
                    $airportLabel = 'label-default';
                    $airportText = 'airports';
                }
                ?>
                <span class="label <?php echo $airportLabel;?>"><?php echo sizeof($filteredEvents);?> <?php echo $airportText;?></span>
                <br/>
                <?php
                for ($bulletEvent = 0 ; $bulletEvent < sizeof($filteredEvents) ; $bulletEvent++)
                {
                    echo "&bull;";
                }
                ?>&nbsp;
            </div>
            <!-- Flightplan counter indicator -->
            <div class="col-xs-4">
                <?php
                $Flightplan = new Flightplan();
                $Flightplan->dateDeparture = $dayCounter;
                $Flightplan->dateArrival = $dayCounter;
                $flightplans = $Flightplan->getFlightplans();

                if (isset($flightplans) AND $flightplans != NULL AND sizeof($flightplans) > 0)
                {
                    $flightplanLabel = 'label-success';
                    (sizeof($flightplans) > 1) ? $flightplanText = 'flightplans' : $flightplanText = 'flightplan';
                }
                else
                {
                    $flightplanLabel = 'label-default';
                    $flightplanText = 'flightplans';
                }
                ?>
                <span class="label <?php echo $flightplanLabel;?>"><?php echo sizeof($flightplans);?> <?php echo $flightplanText;?></span>
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
    <div class="sublinks <?php echo $collapseBehaviour;?> dayContent" id="<?php echo $dayCounter; ?>">
        <div class="row">
            <!-- Button to create new event -->
            <div class="col-sm-8 col-xs-6" style="padding-right: 0;">
                <div class="col-xs-12 createEvent">
                    <a href="./new_event.php?date=<?php echo $dayCounter; ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus-sign"></span> New ATC event</a>
                </div>
            </div>
            <!-- Button to create new flightplan -->
            <div class="col-sm-4 col-xs-6" style="padding-left: 0;">
                <div class="col-xs-12 createFlightplan">
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
                            <div class="col-xs-12">
                                <span class="event-communication"><strong>FGCOM</strong> <?php echo $Event->fgcom; ?></span>
                                <span class="event-communication"><strong>Mumble</strong> <?php echo $Event->teamspeak; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <!-- DOCUMENTATION LINK -->
                            <div class="col-xs-12">
                                <span class="event-documentation"><a href="<?php echo $Event->docsLink; ?>" target="_blank">Airport documentation</a></span>
                            </div>
                        </div>
                        <div class="row">
                            <!-- ATC NAME -->
                            <div class="col-xs-12">
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
                        <!-- COMMENTS -->
                        <?php echo nl2br($comments); ?>
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
                        <div class="col-xs-6">
                            <span class="label label-success"><strong><span class="glyphicon glyphicon-star" aria-hidden="true"></span> <?php echo $Flightplan->callsign;?></strong></span>
                        </div>
                        <!-- AIRPLANE -->
                        <div class="col-xs-6">
                            <span class="flightplan-plane"><span class="glyphicon glyphicon-plane"></span> <?php echo $Flightplan->aircraftType;?></span>
                        </div>
                        <!-- INFORMATION ABOUT DEPARTURE -->
                        <div class="col-xs-6">
                            <span class="flightplan-info">Departure</span>
                        </div>
                        <!-- INFORMATION ABOUT ARRIVAL -->
                        <div class="col-xs-6">
                            <span class="flightplan-info">Arrival</span>
                        </div>
                        <!-- DEPARTURE AIRPORT -->
                        <div class="col-xs-6">
                            <span class="flightplan-time"><?php echo $Flightplan->departureAirport;?></span>
                        </div>
                        <!-- ARRIVAL AIRPORT -->
                        <div class="col-xs-6">
                            <span class="flightplan-time"><?php echo $Flightplan->arrivalAirport;?></span>
                        </div>
                        <!-- DEPARTURE TIME -->
                        <div class="col-xs-6">
                            <span class="label label-primary"><?php echo $printDepartureTime;?></span> <span class="label label-primary"><?php echo $printDateDeparture;?></span>
                        </div>
                        <!-- ARRIVAL TIME -->
                        <div class="col-xs-6">
                            <span class="label label-primary"><?php echo $printArrivalTime;?></span> <span class="label label-primary"><?php echo $printDateArrival;?></span>
                        </div>
                        <!-- STATUS -->
                        <div class="col-xs-6">
                            <span class="label label-default">flightplan <?php echo $Flightplan->status;?></span>
                        </div>
                        <!-- EDIT BUTTON -->
                        <div class="col-xs-6">
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
*/
    ?>

    <script type="text/javascript" language="javascript" src="./include/calendar.js"></script>
