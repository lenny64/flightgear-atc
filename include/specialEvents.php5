
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
        echo "<div class='information'>";
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

            <div class="specialEvent mini">
                <div class="specialEventContent">
                    <h4>Special Flightgear ATC Event - <?php echo $SpecialEvent->title." on ".date('D M jS', strtotime($SpecialEvent->dateEvent)); ?></h4>
                    <span class="participants">You and <?php echo $nbParticipants[$SpecialEvent->id]; ?> other people are going</span>
                </div>
            </div>
            <br style="clear: both;"/>

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
        <div class="specialEvent">
            <img src="./img/SpecialEventImage.png" alt="flightgear ATC events" class="specialEventImg"/>
            <div class="specialEventContent">
                <h1>Special flightgear ATC event</h1>
                <h4><?php echo $SpecialEvent->title; ?></h4>
                <p>
                    <?php echo $SpecialEvent->description; ?>
                    <br/>
                    For more info please refer to <a href="<?php echo $SpecialEvent->url; ?>" target="_blank">this link</a>.
                </p>
                <div class="specialEventDate">
                    <h5>Date</h5>
                    <?php foreach($SpecialEvent->eventsList as $specialEventId)
                    {
                        $Event = new Event();
                        $Event->selectById($specialEventId);
                        echo $Event->date."<br/>";
                    } ?>
                </div>
                <div class="specialEventAirport">
                    <h5>Airport</h5>
                    <?php foreach($SpecialEvent->eventsList as $specialEventId)
                    {
                        $Event = new Event();
                        $Event->selectById($specialEventId);
                        echo $Event->airportICAO."<br/>";
                    } ?>
                </div>
                <div class="specialEventTime">
                    <h5>Time</h5>
                    <?php foreach($SpecialEvent->eventsList as $specialEventId)
                    {
                        $Event = new Event();
                        $Event->selectById($specialEventId);
                        echo "From ".$Event->beginTime." to ".$Event->endTime."<br/>";
                    } ?>
                </div>
                <br style="clear: both;"/>
                <h5>Will you participate ? (<?php echo $nbParticipants[$SpecialEvent->id] ;?> people will !)</h5>
                <form action="index.php5" method="post" name="specialEventParticipate">
                    <!-- Cookie part (in header) -->
                    <input type="hidden" name="createCookie" value="<?php echo md5('specialEvent'.$SpecialEvent->id);?>"/>
                    <input type="hidden" name="cookieValue" id="cookieValue<?php echo $SpecialEvent->id ;?>" value="no"/>
                    <!-- Normal form part (see above) -->
                    <input type="hidden" name="specialEventId" value="<?php echo $SpecialEvent->id;?>"/>
                    <label class="buttonYes" onclick="document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').style.display = 'inline'; document.getElementById('cookieValue<?php echo $SpecialEvent->id ;?>').value='yes'; document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').required='required';"><input type="radio" name="specialEventParticipate" value="yes" class="specialEventButton" required/>Yes</label>
                    <label class="buttonMaybe" onclick="document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').style.display = 'inline'; document.getElementById('cookieValue<?php echo $SpecialEvent->id ;?>').value='maybe'; document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').required='required';"><input type="radio" name="specialEventParticipate" value="maybe" class="specialEventButton"/>Maybe</label>
                    <label class="buttonNo" onclick="document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').style.display = 'none'; document.getElementById('cookieValue<?php echo $SpecialEvent->id ;?>').value='no'; document.getElementById('participationCallsign<?php echo $SpecialEvent->id ;?>').removeAttribute('required');"><input type="radio" name="specialEventParticipate" value="no" class="specialEventButton"/>No</label>
                    <br/>
                    <input type="text" name="participationCallsign" class="participationCallsign" id="participationCallsign<?php echo $SpecialEvent->id ;?>" size="7" placeholder="Callsign"/>
                    <br/>
                    <input type="submit" value="OK" class="buttonValidate"/>
                </form>
                <p>
                    <a href="./faq.php5"/>What's this ?</a>
                </p>
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