<?php

$today = date('Y-m-d');

if (isset($events)) {
    foreach ($events as $event) {
        $Event = new Event();
        $Event->selectById($event['Id']);
        //echo date('H:i:s',strtotime($Event->beginTime))." ";
        if ($User->id == $Event->userId && time() >= strtotime($Event->beginTime) && time() <= strtotime($Event->endTime)) {
            $selectedEvent = $Event;
            $Flightplan = new Flightplan();
            $Flightplan->departureAirport = $selectedEvent->airportICAO;
            $Flightplan->dateDeparture = $selectedEvent->date;
            $Flightplan->dateArrival = $selectedEvent->date;
            $Flightplans = $Flightplan->getFlightplans();
        }
    }
}

if (isset($selectedEvent)) {
?>

<div class="card card-primary my-2">
    <div class="card-header">
        Your event at <?php echo $selectedEvent->airportICAO;?> today from <?php echo $selectedEvent->beginTime." to ".$selectedEvent->endTime;?>
    </div>
    <div class="card-body">
        <?php
        if (isset($Flightplans) AND sizeof($Flightplans) > 0) {
            foreach ($Flightplans as $flightplan) {
                $Flightplan = new Flightplan();
                $Flightplan->selectById($flightplan['flightplanId']);
                // For departures
                if ($Flightplan->departureAirport == $selectedEvent->airportICAO) {
                    $departureFlightplans[] = $Flightplan->id;
                }
                // For arrivals
                if ($Flightplan->arrivalAirport == $selectedEvent->airportICAO) {
                    $arrivalFlightplans[] = $Flightplan->id;
                }
            }
        }
        ?>
        <div class="col-sm-6">
            <h5>DEPARTURES</h5>
            <?php
            if (isset($departureFlightplans) AND sizeof($departureFlightplans) > 0) {
                foreach ($departureFlightplans as $departureFlightplan) {
                    $departureFP = new Flightplan();
                    $departureFP->selectById($departureFlightplan);
                    echo $departureFP->callsign;
                }
            }
            ?>
        </div>
        <div class="col-sm-6">
            <h5>ARRIVALS</h5>
            <?php
            if (isset($arrivalFlightplans) AND sizeof($arrivalFlightplans) > 0) {
                foreach ($arrivalFlightplans as $arrivalFlightplan) {
                    $arrivalFP = new Flightplan();
                    $arrivalFP->selectById($arrivalFlightplan);
                    ?>
                    <div class="row panel panel-default">
                        <div class="col-xs-3">
                            <span class="label label-success"><?php echo $arrivalFP->callsign;?></span>
                            <br/>
                            <?php echo $arrivalFP->aircraftType; ?>
                        </div>
                        <div class="col-xs-3">
                            <span class="label label-default"><?php echo $arrivalFP->departureTime;?></span>
                            <br/>
                            <?php echo $arrivalFP->departureAirport;?>
                        </div>
                        <div class="col-xs-3">
                            <span class="label label-primary"><?php echo $arrivalFP->arrivalTime;?></span>
                            <br/>
                            <?php echo $arrivalFP->arrivalAirport;?>
                        </div>
                    </div>
                    <?php
                }
                ?>
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
