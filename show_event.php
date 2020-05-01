<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<!-- LE CODE COMMENCE ICI -->

<?php include('./include/file_flightplan_v3.php'); ?>

<?php

// If the event is correct
if (isset($_GET['eventId']) AND $_GET['eventId'] != NULL)
{
    $realEvent = true;
    // We get the eventId
    $eventId = $_GET['eventId'];
    $Event = new Event();
    // We pick the event we want
    $Event->selectById($eventId);
    $Event->getATCInfo();
    $atc_verified_style = "";
    $atc_verified_text = "No";
    if ($Event->atcVerified == true) {
        $atc_verified_style = "badge-success";
        $atc_verified_text = "Yes";
    }
    $flightplans = $Event->getFlightplans();

    $Airport = new Airport();
    $Airport->selectByICAO($Event->airportICAO);

    $Event->image = "";
    if (file_exists("./img/airport_".$Event->airportICAO.".jpg")) {
        $Event->image = "http://flightgear-atc.alwaysdata.net/img/airport_".$Event->airportICAO.".jpg";
    }

}
// If the event is not correct
else {
    $realEvent = false;
    $Event = new Event();
    $Event->airportName = "Showing an ATC event";
    $Event->airportICAO = "for an airport";
    $Event->airportCity = "a city";
    $Event->date = "a date";
    $Event->beginTime = "begin";
    $Event->endTime = "end time";
    $Event->image = "";
    $Event->FGCOM = "";
    $Event->teamspeak = "";
    $Event->transitionLevel = "";
    $Event->ils = "";
    $Event->runways = "";
    $Event->docsLink = "";
    $Event->remarks = "";
    $atc_verified_text = "no";
    $atc_verified_style = "";
    $Event->atcId = "";
    $flightplans = Array();
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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./index.php">Flightgear ATC events</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$Event->airportICAO;?> event at <?=$Event->airportCity;?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <img src="<?=$Event->image;?>" class="img-fluid">
        </div>
    </div>

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
                            Airport ICAO: <span class="badge badge-success" id="airportICAO"><?= $Event->airportICAO; ?></span>
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
                            Documents to download: <a href="<?= $Event->docsLink; ?>" target="_blank"><?= $Event->docsLink; ?></a>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-12">
                            <h4>Remarks</h4>
                            <?= nl2br(htmlspecialchars_decode($Event->remarks)); ?>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-12">
                            <h4>Air Traffic Controller information</h4>
                            Name: <span class="badge <?=$atc_verified_style;?>"><?= $Event->atcName;?></span>
                            <br/>
                            Verified: <?= $atc_verified_text; ?>
                            <br/>
                            Controller id: <span class="badge badge-info" id="atcId"><?= $Event->atcId;?></span>
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
if (sizeof($flightplans) == 0) {
                    echo "There is no flight plan yet";
}
foreach ($flightplans as $flightplan_id) {
    $FP = new Flightplan();
    $FP->selectById($flightplan_id);
?>
                    <h5><?= $FP->callsign;?> <small class="text-muted"><?= $FP->aircraftType;?> (<?=$FP->airline;?>)</small></h5>
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
                            Altitude: <span class="badge badge-secondary"><?= $FP->cruiseAltitude;?></span> / Airspeed: <span class="badge badge-secondary"><?= $FP->trueAirspeed;?></span>
                            <br/>
                            Flight nr: <span class="badge badge-secondary"><?= $FP->flightNumber;?></span> / Souls on board: <span class="badge badge-secondary"><?= $FP->soulsOnBoard;?></span>
                            <br/>
                            Fuel time: <?=$FP->fuelTime;?>
                            <br/>
                            Waypoints:<br/> <?= $FP->waypoints;?>
                        </div>
                    </div>
                    <p>
                        Flightplan <?= $FP->status;?> <a href="./edit_flightplan.php?flightplanId=<?php echo $FP->id;?>" class="btn btn-primary btn-sm">Edit</a>
                    </p>
                    <hr/>
<?php
}
?>
                    <a href="#" class="btn btn-sm btn-info" data-toggle="modal" data-target="#myModal" onclick="document.getElementById('file_flightplan-date').value='<?php echo $dayCounter;?>';"><span class="oi oi-plus" aria-hidden="true"></span> flight plan</a>
                </div>
            </div>
        </div>
    </div>
<?php if ($realEvent == true) { ?>
    <div class="row my-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Occurences <?=$Event->airportICAO;?> is controlled per days of week</h4>
                </div>
                <div class="card-body">
                    <canvas id="weeklyControlled" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Occurences <?=$Event->airportICAO;?> is controlled per month</h4>
                </div>
                <div class="card-body">
                    <canvas id="monthlyControlled" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Number of ATC Events controlled by the user <?=$Event->atcName;?></h4>
                </div>
                <div class="card-body">
                    <canvas id="atcControlled" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div>

<script type="text/javascript" src="./include/controller_showEvent.js"></script>
<?php if ($realEvent == true) { ?>
<script type="application/ld+json">
    {
        "@context":"http://schema.org",
        "@type":"Event",
        "name":"ATC Event at <?= $Event->airportCity; ?> on <?= $Event->date; ?> between <?= $Event->beginTime; ?> and <?= $Event->endTime; ?>",
        "location":{
            "@type":"VirtualLocation",
            "name":"<?= $Event->airportICAO;?> airport <?=$Event->airportCity;?>",
            "url":"http://flightgear-atc.alwaysdata.net/show_event?eventId=<?=$Event->id;?>"
        },
        "startDate":"<?=$Event->date;?>T<?=$Event->beginTime;?>-00:00",
        "endDate":"<?=$Event->date;?>T<?=$Event->endTime;?>-00:00",
        "eventStatus": "https://schema.org/EventScheduled",
        "url":"http://flightgear-atc.alwaysdata.net/show_event?eventId=<?=$Event->id;?>",
        "description": "Flightgear Air Traffic Control event at <?= $Event->airportCity; ?> on <?= $Event->date; ?> between <?= $Event->beginTime; ?> and <?= $Event->endTime; ?>",
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
<?php } ?>
<?php include('./include/footer.php'); ?>
