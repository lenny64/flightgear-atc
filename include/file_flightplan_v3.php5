<?php
if (isset($_POST['date']) AND isset($_POST['callsign']) AND isset($_POST['departureAirport']) AND isset($_POST['arrivalAirport']))
{
    $NewFlightplan = new Flightplan();
    
    $callsign = $_POST['callsign'];
    $email = $_POST['email'];
    $departureAirport = $_POST['departureAirport'];
    $departureTime = $_POST['departureTimeHours'].":".$_POST['departureTimeMinutes'].":00";
    $date = $_POST['date'];
    $cruiseAltitude = $_POST['cruiseAltitude'];
    $waypoints = $_POST['waypoints'];
    $arrivalAirport = $_POST['arrivalAirport'];
    $arrivalTime = $_POST['arrivalTimeHours'].":".$_POST['arrivalTimeMinutes'].":00";
    $pilotName = $_POST['pilotName'];
    $airline = $_POST['airline'];
    $flightNumber = $_POST['flightNumber'];
    $category = $_POST['category'];
    $aircraftType = $_POST['aircraftType'];
    
    // We create the Flightplan
    $NewFlightplan->create($date, $departureAirport, $arrivalAirport, $alternateDestination, $cruiseAltitude, $trueAirspeed, $callsign, $pilotName, $airline, $flightNumber, $category, $aircraftType, $departureTime, $arrivalTime, $waypoints, $soulsOnBoard, $fuelTime, $comments);
    
    // If there has not been an ID due to data missing and/or wrong data, we display errors
    if ($NewFlightplan->id == FALSE)
    {
        echo "<div class='warning'>
            Please note that ";
        // We display each error separately
        foreach($NewFlightplan->error as $error)
        {
            echo $error;
        }
        echo "</div>";
    }
    // If there are no errors, we then create an email address.
    else
    {
        $NewFlightplan->createEmail($email);
        echo "<div class='information'>Your flightplan has been accepted. A key has been sent to $email .</div>";
    }
}

?>

<!--
<div class="flightplan_banner">
    <img src="./img/flightplanBanner.png" alt="flightgear ATC events"/>
</div>
-->

<a name="flightplan_filling"></a>
<div class="flightplan_list_area">
    <?php
    // We first show x days (default : 5)
    for ($flightplanDay = 0; $flightplanDay < 5; $flightplanDay++)
    {
        $currentDate = date('Y-m-d',strtotime(date('Y-m-d')." +".$flightplanDay." days"));
        // We gather all FP for this date
        $flightplans_query = $db->query("SELECT * FROM flightplans20140113 WHERE dateDeparture='$currentDate' ORDER BY dateDeparture, departureTime");
        ?>
        
    <div class="flightplan_day">
        <span class="flightplan_date"><?php echo date('D j M',strtotime($currentDate));?></span>
        <div class="flightplan_add_button" onclick="document.getElementById('file_flightplan-form').style.display='block'; document.getElementById('file_flightplan-date').value='<?php echo $currentDate;?>';">+ NEW FLIGHTPLAN</div>
        <?php
        
        $flightplanEvents[$currentDate] = filterEvents('date', $currentDate, $events);
        
        // Anyway we also show ATC events
        echo '<div class="flightplan_atcevents">';
        echo 'Controlled airports<br/>';
        if ($flightplanEvents[$currentDate] != NULL)
        {
            foreach ($flightplanEvents[$currentDate] as $event)
            {
                $Event = new Event();
                $Event->selectById($event);
                echo "<b>".$Event->airportICAO."</b>";
                echo " ";
            }
        }
        else
        {
            echo "None";
        }
        echo '</div>';
        
        // FP counter
        $nbFlightplans = 0;
        // We list all FP for this date
        foreach ($flightplans_query as $flightplan)
        {
            $nbFlightplans++;
            $Flightplan = new Flightplan();
            $Flightplan->selectById($flightplan['flightplanId']);
            
            // CAUTION VERY IMPORTANT !
            // The _TEST callsign is relative to OR and ATCs tests
            // Those flight plans are available through the API and remain into the DB
            // but they do not appear on the website graphical interface.
            if ($Flightplan->callsign != '_TEST')
            {
            ?>
        
            <div class="flightplan <?php if ($nbFlightplans >= 4) { echo "flightplanHidden"; } ?>">
                <table>
                    <tr>
                        <td class="align-left"><span class="callsign"><?php echo $Flightplan->callsign; ?></span></td>
                        <td class="align-right"><span class="aircraft"><?php echo $Flightplan->aircraftType; ?></span></td>
                    </tr>
                    <tr>
                        <td class="align-left"><span class="airport"><?php echo $Flightplan->departureAirport; ?></span></td>
                        <td class="align-right"><span class="time"><?php echo $Flightplan->departureTime; ?></span></td>
                    </tr>
                    <tr>
                        <td class="align-left"><span class="airport"><?php echo $Flightplan->arrivalAirport; ?></span></td>
                        <td class="align-right"><span class="time"><?php echo $Flightplan->arrivalTime; ?></span></td>
                    </tr>
                    <tr>
                        <td class="align-left"><span class="status <?php echo $Flightplan->status;?>"><img src="./img/flightplan_indicator_<?php echo $Flightplan->status;?>.png"/> Flightplan <?php echo $Flightplan->status; ?></span></td>
                        <td class="align-right">
                            <form action="./edit_flightplan.php5" method="get">
                                <input type="hidden" name="flightplanId" value="<?php echo $Flightplan->id;?>"/>
                                <input type="submit" class="action" value="Edit"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        
            <?php
            }
        }
        // Are there no FP this day ?
        if ($nbFlightplans == 0)
        {
            ?>
            <div class="flightplan empty">
                No flightplan yet
            </div>
            <?php
        }
        // More than 4 FP to show ? We can handle that by showing a button
        if ($nbFlightplans >= 4)
        {
            ?>
            <div class="flightplan empty showall">
                See all (<?php echo $nbFlightplans; ?>)
            </div>
            <?php
        }
        ?>
    </div>
    
    <?php
    }
    
    ?>
    <br style="clear: both;"/>
    <br/>
</div>

<form class="file_flightplan" id="file_flightplan-form" method="post" action="./index.php5" <?php if (isset($_GET['form_newSession'])) echo "style='display:none;'";?>>

    <div class="category" id="file_flightplan-pilotInformation">
        <span class="title" id="file_flightplan-pilotInformationTitle">Pilot information</span>
        <div class="category_content" id="file_flightplan-pilotInformationContent">
            <label>Callsign</label><input type="text" name="callsign" id="file_flightplan-callsign" class="callsign" size="6" required/>
            <br/>
            <label>E-mail address*</label><br/><input type="text" name="email" id="file_flightplan-email" class="email" size="16" required/>
            <br/>*A code is sent to edit the flightplan (this feature is fresh ! please <a href="./contact.php5">report bugs here</a>)
        </div>
    </div>
    <div class="category" id="file_flightplan-flightInformation">
        <span class="title" id="file_flightplan-flightInformationTitle">Flight information</span>
        <div class="category_content" id="file_flightplan-flightInformationContent">
            <span class="subtitle">Departure</span>
            <label>Airport</label><input type="text" name="departureAirport" id="file_flightplan-departureAirport" class="airport" size="6" placeholder="ICAO" required/>
            <br/>
            <label>Time</label>
            <select name="departureTimeHours" id="file_flightplan-departureTimeHours" class="time" required>
            <?php
            for ($h = 0; $h < 24; $h++)
            {
                if ($h == date('H'))
                {
                    echo "<option value='".sprintf("%02d",$h)."' selected='selected'>".sprintf("%02d",$h)."</option>";
                }
                else
                {
                    echo "<option value='".sprintf("%02d",$h)."'>".sprintf("%02d",$h)."</option>";
                }
            }
            ?>
            </select>
            :
            <select name="departureTimeMinutes" id="file_flightplan-departureTimeMinutes" class="time" required>
            <?php
            for ($m = 0; $m < 60; $m+=5)
            {
                // Calculation of the nearest 5 minutes
                $currentM = date('i');
                $roundM = (round($currentM)%5 === 0) ? round($currentM) : round(($currentM+5/2)/5)*5;
                
                if ($roundM == $m)
                {
                    echo "<option value='".sprintf("%02d",$m)."' selected='selected'>".sprintf("%02d",$m)."</option>";
                }
                else
                {
                    echo "<option value='".sprintf("%02d",$m)."'>".sprintf("%02d",$m)."</option>";
                }
            }
            ?>
            </select> UTC
            <!--
            <input type="text" placeholder="hh" name="departureTimeHours" id="file_flightplan-departureTimeHours" class="time" size="2" required/>:
            <input type="text" placeholder="mm" name="departureTimeMinutes" id="file_flightplan-departureTimeMinutes" class="time" size="2" required/>UTC
            -->
            <br/>
            <label>Date</label><input type="text" value="" name="date" id="file_flightplan-date" class="date" size="8" required/>
            <span class="subtitle">En-route</span>
            <label>Cruise altitude</label><input type="text" value="" name="cruiseAltitude" id="file_flightplan-cruiseAltitude" class="altitude" size="4"/>
            <br/>
            <label>Route/Waypoints</label>
            <br/>
            <textarea name="waypoints" cols="15" rows="4"></textarea>
            <span class="subtitle">Arrival</span>
            <label>Airport</label><input type="text" name="arrivalAirport" id="file_flightplan-arrivalAirport" class="airport" size="6" placeholder="ICAO" required/>
            <br/>
            <label>Time</label>
            <select name="arrivalTimeHours" id="file_flightplan-arrivalTimeHours" class="time" required>
            <?php
            for ($h = 0; $h < 24; $h++)
            {
                if ($h == date('H'))
                {
                    echo "<option value='".sprintf("%02d",$h)."' selected='selected'>".sprintf("%02d",$h)."</option>";
                }
                else
                {
                    echo "<option value='".sprintf("%02d",$h)."'>".sprintf("%02d",$h)."</option>";
                }
            }
            ?>
            </select>
            :
            <select name="arrivalTimeMinutes" id="file_flightplan-arrivalTimeMinutes" class="time" required>
            <?php
            for ($m = 0; $m < 60; $m+=5)
            {
                // Calculation of the nearest 5 minutes
                $currentM = date('i');
                $roundM = (round($currentM)%5 === 0) ? round($currentM) : round(($currentM+5/2)/5)*5;
                
                if ($roundM == $m)
                {
                    echo "<option value='".sprintf("%02d",$m)."' selected='selected'>".sprintf("%02d",$m)."</option>";
                }
                else
                {
                    echo "<option value='".sprintf("%02d",$m)."'>".sprintf("%02d",$m)."</option>";
                }
            }
            ?>
            </select> UTC
            <!--
            <input type="text" placeholder="hh" name="arrivalTimeHours" id="file_flightplan-arrivalTimeHours" class="time" size="2" required/>:
            <input type="text" placeholder="mm" name="arrivalTimeMinutes" id="file_flightplan-arrivalTimeMinutes" class="time" size="2" required/>UTC
            -->
        </div>
    </div>
    <div class="category" id="file_flightplan-optionalInformation">
        <span class="title" id="file_flightplan-optionalInformationTitle">Optional information</span>
        <div class="category_content" id="file_flightplan-optionalInformationContent">
            <label>Pilot name</label><input type="text" name="pilotName" id="file_flightplan-pilotName" size="10"/>
            <br/>
            <label>Airline</label><input type="text" name="airline" class="airline" id="file_flightplan-airline" size="10"/>
            <label>Flight number</label><input type="text" name="flightNumber" class="airline" id="file_flightplan-flightNumber" size="10"/>
            <br/>
            <label>Category</label>
            <select name="category" id="file_flightplan-category">
                <option value="IFR">Instrumental (IFR)</option>
                <option value="VFR">Visual (VFR)</option>
            </select>
            <br/>
            <label>Aircraft</label><input type="text" name="aircraftType" class="aircraft" id="file_flightplan-aircraft" size="10"/>
        </div>
    </div>
    <input type="submit" value="Create" class="create_flightplan_button"/>
</form>

<script type="text/javascript" language="javascript">
    $(document).ready(function(){
       $(".showall").click(function(){
           $(".flightplanHidden").removeClass("flightplanHidden");
           $(".showall").addClass("flightplanHidden");
       });
    });
</script>