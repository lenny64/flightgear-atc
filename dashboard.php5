<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>
<?php include('./include/restriction.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="container">

<?php

// If the flightplan status will change
if (isset($_GET['changeFlightplan']) AND isset($_GET['status']))
{
    if ($_GET['changeFlightplan'] != NULL AND $_GET['status'] != NULL)
    {
        $changeFlightplan = $_GET['changeFlightplan'];
        $status = $_GET['status'];
        
        $FlightplanToChange = new Flightplan();
        $FlightplanToChange->selectById($changeFlightplan);
        $FlightplanToChange->changeFlightplanStatus($User->id, $FlightplanToChange->id, $status);
    }
}

// SPECIAL EVENTS
if (isset($_POST['specialEventId']) AND isset($_POST['eventId']))
{
    if ($_POST['specialEventId'] != NULL AND $_POST['eventId'] != NULL)
    {
        $specialEventId = $_POST['specialEventId'];
        $eventId = $_POST['eventId'];
        $userId = $User->id;
        
        $SpecialEvent = new SpecialEvent();
        $SpecialEvent->selectById($specialEventId);
        
        if ($_POST['operation'] == 'add')
        {
            $SpecialEvent->addEventToSpecialEvent($eventId, $userId);
            echo "<div class='alert alert-info'>Your event has successfully been added to ".$SpecialEvent->title.".</div>";
        }
        
        else if ($_POST['operation'] == 'remove')
        {
            $SpecialEvent->removeEventFromSpecialEvent($eventId);
            echo "<div class='alert alert-info'>Your event has successfully been removed from ".$SpecialEvent->title.".</div>";
        }
        
    }
}

// If the notifications or parameters are changing
if (isset($_POST['change_settings']))
{	
    /* Notification part */
    if (isset($_POST['flightplan_notification']) AND $_POST['flightplan_notification'] == "1") { $User->changeNotification(true); }
    else { $User->changeNotification(false); }
    
    /* Name part /!\ Requires the "users_names" table */
    if (isset($_POST['atcName']))
    {
        $atcName = $_POST['atcName'];
        $User->changeName($atcName);
    }
    
    /* Other parameters part */
    // FPForm visibility on home screen
    if (isset($_POST['FPForm_visibility']) AND $_POST['FPForm_visibility'] == "1") { $FPForm_visibility = 'visible'; }
    else { $FPForm_visibility = 'hidden'; }
    // We list every parameter into an array that will be inserted into DB
    $userParameters = ['FPForm_visibility' => $FPForm_visibility];
    $User->changeParameters($userParameters);
    
    // Anyway we show this information
    echo "<div class='alert alert-info'>";
    echo "Your settings have been saved at ".date('H:i:s');
    echo "</div>";
}

?>

    <h3><span class="glyphicon glyphicon glyphicon-wrench"> Settings</h3>
    <form role="form" action="./dashboard.php5" method="post">
        <input type="hidden" name="change_settings"/>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="flightplan_notification" value="1" <?php if ($User->notifications == true) echo "checked";?>/> I want to be notified once a flightplan is filed (<?php echo $User->mail;?>)
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="FPForm_visibility" value="1" <?php if ($User->parameters['FPForm_visibility'] == 'visible') echo "checked";?>/> I want to see the flightplan filling form on the home page
            </label>
        </div>
        <div class="form-group">
            <label for="atcName">My pseudo</label>
            <input type="text" class="form-control" id="atcName" name="atcName" value="<?php if(isset($User->name) AND $User->name != NULL) echo $User->name; ?>" />
            <p class="help-block">(blank = your pseudo won't appear in your ATC events)</p>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-default" value="Change settings">Change settings</button>
        </div>
    </form>

    
<?php

// We gather every sessions this user made
$events = $db->query("SELECT eventId FROM events WHERE userId = $User->id ORDER BY `date` DESC LIMIT 0,10");

foreach ($events as $event)
{ 
    // We pick the event
    $Event = new Event();
    $Event->selectById($event['eventId']);
    
    // We pick the airport ID to ident it
    $airportId = getInfo('airportId', 'airports', 'ICAO', $Event->airportICAO);
    
    if ($airportId != NULL)
    {
        // We list the airport which is concerned
        $airports = $db->query("SELECT * FROM airports WHERE airportId = $airportId");
        // We gather the information
        $airport = $airports->fetch(PDO::FETCH_ASSOC);
      
    
    ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><span class="label label-primary"><?php echo $airport['ICAO'];?></span></h4> 
        <a class="btn btn-default btn-sm" href="./edit_event.php5?eventId=<?php echo $Event->id;?>"><span class="glyphicon glyphicon glyphicon-pencil"></span> Edit event</a>
    </div>
    <div class="panel-body">
        <h5>Date</h5>
        <?php echo $Event->date; ?>

        <h5>Time</h5>
        From <?php echo $Event->beginTime;?> to <?php echo $Event->endTime;?>

        <h5>Other information</h5>
        FGCom : <?php echo $Event->fgcom;?> // Teamspeak : <?php echo $Event->teamspeak; ?>
        <br/>
        Documents to download : <a href="<?php echo $Event->docsLink;?>"><?php echo $Event->docsLink;?></a>
        <br/>
        Remarks :
        <br/>
        <?php echo $Event->remarks; ?>
    </div>
</div>
<?php
    }
}
?>
    </div>
    
<br/>
<br/>

</div>

<?php include('./include/footer.php5'); ?>
