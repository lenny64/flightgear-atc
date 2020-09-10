
<?php

global $db;

// We create an instance of the specialEvent
$SpecialEvent = new SpecialEvent();

// If a new pilot wants to participate
if (isset($_POST['specialEventId']) AND isset($_POST['specialEventParticipate']) AND isset($_POST['participationCallsign']))
{
    if ($_POST['specialEventId'] != NULL AND $_POST['specialEventParticipate'] != NULL AND $_POST['participationCallsign'] != NULL)
    {
        $specialEventId = $_POST['specialEventId'];
        $participation = $_POST['specialEventParticipate'];
        $callsign = $_POST['participationCallsign'];
        $SpecialEvent->selectById($specialEventId);
        $SpecialEvent->addPilot($callsign, $participation);
        echo "<div class='alert alert-info'>";
        echo "Your choice has been taken in account !";
        echo "</div>";
    }
}

// In any case we select all the special events
$specialEvents_list = $db->query("SELECT specialEventsId FROM specialEvents_events");
// We go through them
foreach ($specialEvents_list as $specialEvents)
{
    // We select the specialEvent
    $SpecialEvent->selectById($specialEvents['specialEventsId']);

    // Here we check if the special event is valid. The first reason is when all events are in the past
    if ($SpecialEvent->valid == TRUE)
    {

        // The value of the cookie corresponding to this particular event
        $specialEventCookie = md5('specialEvent'.$SpecialEvent->id);

        // We get the number of participants (to show in the mini specialEvent div)
        $nbParticipants[$SpecialEvent->id] = 0;
        foreach ($SpecialEvent->pilotsList as $participant)
        {
            // Does not really make sense since every pilot in the DB is either "yes" or "maybe"
            if ($participant['participation'] == 'yes' OR $participant['participation'] == 'maybe')
            {
                $nbParticipants[$SpecialEvent->id]++;
            }
        }

        // THE PILOT HAS ALREADY VOTED
        // If there is a cookie, we won't show the initial form     Below is the POST due to the form.
        if (isset($_COOKIE[$specialEventCookie]) OR (isset($specialEventId) AND isset($participation)))
        {
            // THE PILOT PARTICIPATES
            // The cookie OR the POST value must be either "yes" or "maybe"
            if ($_COOKIE[$specialEventCookie] == 'yes' OR $_COOKIE[$specialEventCookie] == 'maybe' OR ($specialEventId == $SpecialEvent->id AND ($participation == "yes" OR $participation == "maybe")))
            {

            ?>

            <div class="alert alert-success mt-2">
                <p class="lead"><span class="oi oi-badge"></span> Special Flightgear ATC Event - <?php echo $SpecialEvent->title." on ".date('D M jS', strtotime($SpecialEvent->dateEvent)); ?></p>
                You and <?php echo $nbParticipants[$SpecialEvent->id]; ?> other people are going
            </div>

            <?php
            }
            // THE PILOT DO NOT PARTICIPATE
            else {}
        }

        // THE PILOT HAS DONE NOTHING
        // And here is the initial form and description
        else
        {
        ?>
        <div class="jumbotron py-4 mb-0">
            <!-- <img src="./img/SpecialEventImage.png" alt="flightgear ATC events" class="specialEventImg"/> -->
            <h1 class="display-5"><span class="oi oi-badge"></span> <?php echo $SpecialEvent->title; ?></h1>
            <div class="row">
                <div class="col-6">
                    <p class="lead">
                        <?php echo $SpecialEvent->description; ?> - <a href="<?php echo $SpecialEvent->url; ?>" target="_blank">more info</a>
                    </p>
                    <ul class="list-group">
                        <?php foreach($SpecialEvent->eventsList as $specialEventId) {
                            $Event = new Event();
                            $Event->selectById($specialEventId); ?>
                            <li class="list-group-item">
                                <span class="badge badge-info"><?= $Event->date ;?></span> -
                                <span class="badge badge-primary"><?= $Event->airportICAO ;?></span>
                                <span class="badge badge-success"><?= $Event->beginTime ;?></span> &rarr; <span class="badge badge-success"><?= $Event->endTime ;?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-6">
                    <?php echo $nbParticipants[$SpecialEvent->id] ;?> people will participate
                    <form action="index.php" method="post" name="specialEventParticipate">
                        <!-- Cookie part (in header) -->
                        <input type="hidden" name="createCookie" value="<?php echo md5('specialEvent'.$SpecialEvent->id);?>"/>
                        <input type="hidden" name="cookieValue" id="cookieValue<?php echo $SpecialEvent->id ;?>" value="no"/>
                        <!-- Normal form part (see above) -->
                        <input type="hidden" name="specialEventId" value="<?php echo $SpecialEvent->id;?>"/>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="specialEventParticipate" value="yes" class="specialEventButton" onchange="getInput('yes', <?=$SpecialEvent->id;?>)" required/>
                            <label class="form-check-label" onclick="document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').style.display = 'inline'; document.getElementById('cookieValue<?php echo $SpecialEvent->id ;?>').value='yes'; document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').required='required';">
                                I will participate
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="specialEventParticipate" value="maybe" class="specialEventButton" onchange="getInput('maybe', <?=$SpecialEvent->id;?>)"/>
                            <label class="form-check-label" onclick="document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').style.display = 'inline'; document.getElementById('cookieValue<?php echo $SpecialEvent->id ;?>').value='maybe'; document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').required='required';">
                                I will probably participate
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="specialEventParticipate" value="no" class="specialEventButton" onchange="getInput('no', <?=$SpecialEvent->id;?>)"/>
                            <label class="form-check-label" onclick="document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').style.display = 'none'; document.getElementById('cookieValue<?php echo $SpecialEvent->id ;?>').value='no'; document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').removeAttribute('required');">
                                I won't participate
                            </label>
                        </div>
                        <input type="text" name="participationCallsign" class="form-control form-control-sm" id="participationCallsign<?php echo $SpecialEvent->id ;?>" placeholder="Callsign"/>
                        <input type="submit" value="Submit" class="btn btn-success btn-sm my-2"/> <a href="./faq.php"/>What's this ?</a>
                    </form>
                </div>
            </div>
        </div>
        <br style="clear: both;"/>
        <?php
        }
    }
    // If the specialEvent is not valid ... For now we do nothing
    else
    {}
}
?>

<script type="text/javascript">
    var getInput = function(status, special_event_id) {
        $('#cookieValue'+special_event_id).val(status);
        if (status == 'no') {
            $('#participationCallsign'+special_event_id).hide();
            $('#participationCallsign'+special_event_id).removeAttr("required");
            // $('#participationCallsign'+special_event_id).removeAttr('required');
        } else {
            $('#participationCallsign'+special_event_id).show();
            $('#participationCallsign'+special_event_id).prop("required", true);
            // $('#participationCallsign'+special_event_id).attr('required') = 'required';
        }
        console.log($('#cookieValue'+special_event_id).val());
    }
</script>
