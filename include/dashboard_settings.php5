
<?php

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
    
    <h3><span class="glyphicon glyphicon glyphicon-wrench"></span> Settings</h3>
    <form role="form" action="./dashboard.php5?settings" method="post">
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