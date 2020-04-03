<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<!-- LE CODE COMMENCE ICI -->


<?php

// If the event is correct
if (isset($_GET['eventId']) AND $_GET['eventId'] != NULL)
{
    // We get the eventId
    $eventId = $_GET['eventId'];
    $Event = new Event();
    // We pick the event we want
    $Event->selectById($eventId);
    $flightplans = $Event->getFlightplans();

}
?>

<!-- AIRPORT -->
<div class="jumbotron">
    <h1 class="display-2"><?= $Event->airportName; ?> - <?= $Event->airportICAO; ?></h1>
    <p class="lead">
        Details about the ATC event occurring in <?= $Event->airportCity; ?> on <?= $Event->date; ?> between <?= $Event->beginTime; ?> and <?= $Event->endTime; ?>
    </p>
</div>


<div class="container">
    <a href="./index.php" class="btn btn-primary btn-lg">« Go back</a>
    <div class="row">
        <div class="col-md-8">
            <div class="card my-3">
                <div class="card-header">
                    <h4>ATC Event information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Airport</h4>
                            Airport name: <?= $Event->airportName; ?>
                            <br/>
                            Airport ICAO: <span class="badge badge-success"><?= $Event->airportICAO; ?></span>
                        </div>
                        <div class="col-md-4">
                            <h4>Event</h4>
                            Date: <?= $Event->date; ?>
                            <br/>
                            Begin time: <span class="badge badge-info"><?= $Event->beginTime;?></span>
                            <br/>
                            End time: <span class="badge badge-info"><?= $Event->endTime;?></span>
                        </div>
                        <div class="col-md-4">
                            <h4>Communication</h4>
                            FGCom: <?= $Event->FGCOM; ?>
                            <br/>
                            Mumble/Teamspeak: <?= $Event->teamspeak; ?>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-4">
                            <h4>Other</h4>
                            Transition level: <?= $Event->transitionLevel;?>
                            <br/>
                            ILS: <?= $Event->ils;?>
                            <br/>
                            Runways: <?= $Event->runways;?>
                        </div>
                        <div class="col-md-8">
                            <h4>Documentation</h4>
                            Documents to download: <?= $Event->docsLink; ?>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-12">
                            Remarks: <?= $Event->remarks; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card my-3">
                <div class="card-header">
                    <h4>Flight plans</h4>
                </div>
                <div class="card-body">
<?php
foreach ($flightplans as $flightplan_id) {
    $FP = new Flightplan();
    $FP->selectById($flightplan_id);
?>
                    <h5><?= $FP->callsign;?> <small class="text-muted"><?= $FP->aircraftType;?></small></h5>
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
                    <p>
                        Flightplan <?= $FP->status;?> <a href="./edit_flightplan.php?flightplanId=<?php echo $FP->id;?>" class="btn btn-primary btn-sm">Edit</a>
                    </p>
                    <hr/>
<?php
}
?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./include/footer.php'); ?>
