<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<?php

// AUTHENTICATION
// We first gather all the data
if (isset($_POST['flightplanId']) AND isset($_POST['email']) AND isset($_POST['privateKey']) AND isset($_POST['departureAirport']) AND isset($_POST['departureTime']) AND isset($_POST['date']) AND isset($_POST['arrivalAirport']) AND isset($_POST['arrivalTime']))
{
    if ($_POST['flightplanId'] != NULL AND $_POST['email'] != NULL AND $_POST['privateKey'] != NULL AND $_POST['departureAirport'] != NULL AND $_POST['departureTime'] != NULL AND $_POST['date'] != NULL AND $_POST['arrivalAirport'] != NULL AND $_POST['arrivalTime'] != NULL)
    {
        // We select the flightplan
        $Flightplan = new Flightplan();
        $flightplanId = mysql_real_escape_string(htmlspecialchars($_POST['flightplanId']));
        $Flightplan->selectById($flightplanId);
        
        $departureAirport = mysql_real_escape_string(htmlspecialchars($_POST['departureAirport']));
        $departureTime = mysql_real_escape_string(htmlspecialchars($_POST['departureTime']));
        $date = mysql_real_escape_string(htmlspecialchars($_POST['date']));
        $cruiseAltitude = mysql_real_escape_string(htmlspecialchars($_POST['cruiseAltitude']));
        $waypoints = mysql_real_escape_string(htmlspecialchars($_POST['waypoints']));
        $arrivalAirport = mysql_real_escape_string(htmlspecialchars($_POST['arrivalAirport']));
        $arrivalTime = mysql_real_escape_string(htmlspecialchars($_POST['arrivalTime']));
        $pilotName = mysql_real_escape_string(htmlspecialchars($_POST['pilotName']));
        $airline = mysql_real_escape_string(htmlspecialchars($_POST['airline']));
        $flightNumber = mysql_real_escape_string(htmlspecialchars($_POST['flightNumber']));
        $category = mysql_real_escape_string(htmlspecialchars($_POST['category']));
        $aircraftType = mysql_real_escape_string(htmlspecialchars($_POST['aircraftType']));
        $alternateDestination = mysql_real_escape_string(htmlspecialchars($_POST['alternateDestination']));
        $trueSpeed = mysql_real_escape_string(htmlspecialchars($_POST['trueSpeed']));
        $soulsOnBoard = mysql_real_escape_string(htmlspecialchars($_POST['soulsOnBoard']));
        $fuelTime = mysql_real_escape_string(htmlspecialchars($_POST['fuelTime']));
        $comments = mysql_real_escape_string(htmlspecialchars($_POST['comments']));
        
        // We pick the email and key
        $entered_email = mysql_real_escape_string(htmlspecialchars($_POST['email']));
        $entered_privateKey = mysql_real_escape_string(htmlspecialchars($_POST['privateKey']));
        
        // We check if those info correspond to those inside the flightplan
        if ($entered_email == $Flightplan->email AND $entered_privateKey == $Flightplan->privateKey)
        {
            // If it's ok then we update the table
            mysql_query("UPDATE flightplans20140113 SET
                airline = '$airline',
                flightnumber = '$flightNumber',
                airportICAOFrom = '$departureAirport',
                airportICAOTo = '$arrivalAirport',
                alternateDestination = '$alternateDestination',
                cruiseAltitude = '$cruiseAltitude',
                trueAirspeed = '$trueSpeed',
                dateDeparture = '$date',
                departureTime = '$departureTime',
                arrivalTime = '$arrivalTime',
                aircraft = '$aircraftType',
                soulsOnBoard = '$soulsOnBoard',
                fuelTime = '$fuelTime',
                pilotName = '$pilotName',
                waypoints = '$waypoints',
                category = '$category',
                comments = '$comments'
                WHERE flightplanId = $Flightplan->id;") or die(mysql_error());
            
            echo "<div class='information'>Your flightplan has been successfully edited !</div>";
        }
    }
}

// Initial behaviour of the page : a user sets a flightplanId and gets the form below.
// the person will be asked to give an e-mail and key
if (isset($_GET['flightplanId']) AND $_GET['flightplanId'] != NULL)
{
    // We select the flightplan
    $Flightplan = new Flightplan();
    $flightplanId = mysql_real_escape_string(htmlspecialchars($_GET['flightplanId']));
    $Flightplan->selectById($flightplanId);
    
    ?>


<form class="file_flightplan" id="file_flightplan-form" method="post" action="./edit_flightplan.php5?flightplanId=<?php echo $Flightplan->id; ?>" style="display: block; position: relative; overflow-y: auto; max-height: none; width: 60%;">
    <div class="category">
        <br/>
        You are about to edit your flightplan number <?php echo $Flightplan->id; ?>.
        <br/>
        Check the key that has been sent when you created your flightplan.
    </div>
    <div class="category">
        <span class="title" id="file_flightplan-pilotInformationTitle">Log in</span>
        <div class="category_content" id="file_flightplan-pilotInformationContent">
            <input type="text" name="email" id="file_flightplan-email" class="email" size="24" placeholder="Email" required <?php if($entered_email) echo "value='$entered_email'";?>/>
            <br/>
            <input type="text" name="privateKey" class="key" size="24" placeholder="Key" required <?php if($entered_privateKey) echo "value='$entered_privateKey'";?>/>
        </div>
    </div>
    <br/><br/><br/>
    <div class="category" id="file_flightplan-pilotInformation">
        <span class="title" id="file_flightplan-pilotInformationTitle">Pilot information</span>
        <div class="category_content" id="file_flightplan-pilotInformationContent">
            <label>Callsign</label><input type="text" name="callsign" id="file_flightplan-callsign" class="callsign" size="6" value="<?php echo $Flightplan->callsign;?>" required readonly="readonly"/>
            <input type="hidden" name="flightplanId" value="<?php echo $Flightplan->id;?>"/>
        </div>
    </div>
    <div class="category" id="file_flightplan-flightInformation">
        <span class="title" id="file_flightplan-flightInformationTitle">Flight information</span>
        <div class="category_content" id="file_flightplan-flightInformationContent">
            <span class="subtitle">Departure</span>
            <label>Airport</label><input type="text" name="departureAirport" id="file_flightplan-departureAirport" class="airport" size="6" value="<?php echo $Flightplan->departureAirport;?>" required/>
            <br/>
            <label>Time</label>
            <input type="text" name="departureTime" id="file_flightplan-departureTime" class="time" size="10" value="<?php echo $Flightplan->departureTime;?>" required/>UTC
            <br/>
            <label>Date</label><input type="text" value="<?php echo $Flightplan->dateDeparture;?>" name="date" id="file_flightplan-date" class="date" size="8" required/>
            <span class="subtitle">En-route</span>
            <label>Cruise altitude</label><input type="text" value="<?php echo $Flightplan->cruiseAltitude;?>" name="cruiseAltitude" id="file_flightplan-cruiseAltitude" class="altitude" size="4"/>
            <br/>
            <label>Route/Waypoints</label>
            <br/>
            <textarea name="waypoints" cols="15" rows="4"><?php echo $Flightplan->waypoints;?></textarea>
            <span class="subtitle">Arrival</span>
            <label>Airport</label><input type="text" name="arrivalAirport" id="file_flightplan-arrivalAirport" class="airport" size="6" value="<?php echo $Flightplan->arrivalAirport;?>" required/>
            <br/>
            <label>Time</label>
            <input type="text" name="arrivalTime" id="file_flightplan-arrivalTime" class="time" size="10" value="<?php echo $Flightplan->arrivalTime;?>" required/>UTC
        </div>
    </div>
    <div class="category" id="file_flightplan-optionalInformation">
        <span class="title" id="file_flightplan-optionalInformationTitle">Optional information</span>
        <div class="category_content" id="file_flightplan-optionalInformationContent">
            <label>Pilot name</label><input type="text" class="callsign" name="pilotName" id="file_flightplan-pilotName" size="10" value="<?php echo $Flightplan->pilotName;?>"/>
            <br/>
            <label>Airline</label><input type="text" class="airline" name="airline" id="file_flightplan-airline" size="10" value="<?php echo $Flightplan->airline;?>"/>
            <label>Flight number</label><input type="text" class="airline" name="flightNumber" id="file_flightplan-flightNumber" size="10" value="<?php echo $Flightplan->flightNumber;?>"/>
            <br/>
            <label>Category</label>
            <select name="category" id="file_flightplan-category">
                <option value="IFR">Instrumental (IFR)</option>
                <option value="VFR" <?php if ($Flightplan->category == 'VFR') { echo "selected"; }?> >Visual (VFR)</option>
            </select>
            <br/>
            <label>Aircraft</label><input type="text" name="aircraft" class="aircraft" id="file_flightplan-aircraft" size="10" value="<?php echo $Flightplan->aircraftType; ?>"/>
            <br/><br/>
            <label>Alternate destination</label><input type="text" class="airport" name="alternateDestination" id="file_flightplan-alternateDestination" size="6" placeholder="ICAO" value="<?php echo $Flightplan->alternateDestination; ?>"/>
            <br/>
            <label>Cruise speed</label><input type="text" class="aircraft" name="trueSpeed" id="file_flightplan-trueSpeed" size="4" value="<?php echo $Flightplan->trueAirspeed; ?>"/>
            <br/>
            <label>Souls on board</label><input type="text" class="souls" name="soulsOnBoard" id="file_flightplan-soulsOnBoard" size="6" value="<?php echo $Flightplan->soulsOnBoard; ?>"/>
            <br/>
            <label>Fuel time</label><input type="text" class="fuel" name="fuelTime" id="file_flightplan-fuelTime" size="6" value="<?php echo $Flightplan->fuelTime; ?>"/>
            <br/>
            <label>Additional information</label>
            <br/>
            <textarea cols="40" rows="4" name="comments"><?php echo $Flightplan->comments[0][0]; ?></textarea>
        </div>
    </div>
    <input type="submit" value="Edit the flightplan" class="create_flightplan_button"/>
</form>

<?php

}

?>

<br/>
<br/>
<?php include('./include/footer.php5'); ?>
