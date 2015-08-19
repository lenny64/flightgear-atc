
<?php

if (isset($_GET['eventId']) AND $_GET['eventId'] != NULL)
{
    if (isset($_POST['airportICAO']) AND isset($_POST['date']) AND isset($_POST['beginTime']) AND isset($_POST['endTime']))
    {
        if ($_POST['airportICAO'] != NULL AND $_POST['date'] != NULL AND $_POST['beginTime'] != NULL AND $_POST['endTime'] != NULL)
        {
            $Event->id = $_GET['eventId'];
            $Event->airportICAO = $_POST['airportICAO'];
            $Event->userId = $_POST['userId'];
            $Event->date = $_POST['date'];
            $Event->beginTime = $_POST['beginTime'];
            $Event->endTime = $_POST['endTime'];
            $Event->fgcom = $_POST['fgcom'];
            $Event->teamspeak = $_POST['teamspeak'];
            $Event->transitionLevel = $_POST['transitionLevel'];
            $Event->runways = $_POST['runways'];
            $Event->ils = $_POST['ils'];
            $Event->docsLink = $_POST['docsLink'];
            $Event->remarks = $_POST['remarks'];
            if ($Event->updateEvent() === true)
            {
                echo "The event has been successfully edited ! <a href='./edit_event.php5?eventId=$Event->id'>Refresh the page</a>";
            }
            else
            {
                echo "Sorry there was an error, please try again.";
            }
        }
    }
}

?>

<form action="./edit_event.php5?eventId=<?php echo $Event->id;?>" method="post" class="edit_event">
    <input type="hidden" name="userId" value="<?php echo $User->id;?>"/>
    <input type="hidden" name="eventId" value="<?php echo $Event->id;?>"/>
    <!-- AIRPORT -->
    <h3>Airport</h3>
    <label>Name<br/>
    <input type="text" value="<?php echo getInfo('name','airports','ICAO',$Event->airportICAO);?>"/></label>
    <label>ICAO<br/>
    <input type="text" name="airportICAO" value="<?php echo $Event->airportICAO;?>" size="4"/></label>
    <br/>
    
    <!-- TIME -->
    <h3>Time</h3>
    <label>Date<br/>
    <input type="text" name="date" value="<?php echo $Event->date;?>"/></label>
    <label>Begin time<br/>
    <input type="text" name="beginTime" value="<?php echo $Event->beginTime;?>"/></label>
    <label>End time<br/>
    <input type="text" name="endTime" value="<?php echo $Event->endTime;?>"/></label>
    
    <!-- COMMUNICATION -->
    <h3>Communication</h3>
    <label>FGcom<br/>
    <input type="text" name="fgcom" value="<?php echo $Event->fgcom;?>"/></label>
    <label>Teamspeak<br/>
    <input type="text" name="teamspeak" value="<?php echo $Event->teamspeak;?>"/></label>
    
    <!-- FLYING INFORMATION -->
    <h3>Useful information</h3>
    <label>Transition level<br/>
    <input type="text" name="transitionLevel" value="<?php echo $Event->transitionLevel;?>"/></label>
    <label>Runways<br/>
    <input type="text" name="runways" value="<?php echo $Event->runways;?>"/></label>
    <label>ILS<br/>
    <input type="text" name="ils" value="<?php echo $Event->ils;?>"/></label>
    <label>Documents to download<br/>
    <input type="text" name="docsLink" value="<?php echo $Event->docsLink;?>"/></label>
    <br/>
    <label>Remarks<br/>
    <textarea name="remarks" rows="5" cols="25"><?php echo $Event->remarks;?></textarea></label>
    <input type="submit" value="Edit event !"/>
    
</form>
