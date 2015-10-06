<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="container">

<?php

// AUTHENTICATION
// We first gather all the data
if (isset($_POST['flightplanId']) AND isset($_POST['email']) AND isset($_POST['privateKey']) AND isset($_POST['departureAirport']) AND isset($_POST['departureTime']) AND isset($_POST['date']) AND isset($_POST['arrivalAirport']) AND isset($_POST['arrivalTime']))
{
    if ($_POST['flightplanId'] != NULL AND $_POST['email'] != NULL AND $_POST['privateKey'] != NULL AND $_POST['departureAirport'] != NULL AND $_POST['departureTime'] != NULL AND $_POST['date'] != NULL AND $_POST['arrivalAirport'] != NULL AND $_POST['arrivalTime'] != NULL)
    {
        global $db;
        
        // We select the flightplan
        $Flightplan = new Flightplan();
        $flightplanId = $_POST['flightplanId'];
        $Flightplan->selectById($flightplanId);
        
        $Flightplan->departureAirport = $_POST['departureAirport'];
        $Flightplan->departureTime = $_POST['departureTime'];
        $Flightplan->date = $_POST['date'];
        $Flightplan->cruiseAltitude = $_POST['cruiseAltitude'];
        $Flightplan->waypoints = $_POST['waypoints'];
        $Flightplan->arrivalAirport = $_POST['arrivalAirport'];
        $Flightplan->arrivalTime = $_POST['arrivalTime'];
        $Flightplan->pilotName = $_POST['pilotName'];
        $Flightplan->airline = $_POST['airline'];
        $Flightplan->flightNumber = $_POST['flightNumber'];
        $Flightplan->category = $_POST['category'];
        $Flightplan->aircraftType = $_POST['aircraftType'];
        $Flightplan->alternateDestination = $_POST['alternateDestination'];
        $Flightplan->trueSpeed = $_POST['trueSpeed'];
        $Flightplan->soulsOnBoard = $_POST['soulsOnBoard'];
        $Flightplan->fuelTime = $_POST['fuelTime'];
        $Flightplan->comments = $_POST['comments'];
        
        // We pick the email and key
        $entered_email = $_POST['email'];
        $entered_privateKey = $_POST['privateKey'];
        
        // We check if those info correspond to those inside the flightplan
        if ($entered_email == $Flightplan->email AND $entered_privateKey == $Flightplan->privateKey)
        {
            $Flightplan->editFlightplan();
            
            if (sizeof($Flightplan->error) == 0)
            {
                echo "<div class='alert alert-info'>Your flightplan has been successfully edited !</div>";
            }
            else
            {
                foreach($Flightplan->error as $error)
                {
                    echo "<div class='alert alert-danger'>WARNING : " . $error . "</div>";
                }
            }
        }
        else
        {
            echo "<div class='alert alert-danger'>WARNING : incorrect email or/and key.</div>";
        }
    }
}

// Initial behaviour of the page : a user sets a flightplanId and gets the form below.
// the person will be asked to give an e-mail and key
if (isset($_GET['flightplanId']) AND $_GET['flightplanId'] != NULL)
{
    // We select the flightplan
    $Flightplan = new Flightplan();
    $flightplanId = $_GET['flightplanId'];
    $Flightplan->selectById($flightplanId);
    
    ?>

    <div class="alert alert-info">
        You are about to edit your flightplan number <?php echo $Flightplan->id; ?>.
        <br/>
        Check the key that has been sent when you created your flightplan.
    </div>
    
    
          
        <form role="form" method="post" class="" action="./index.php5" <?php if (isset($_GET['form_newSession'])) echo "style='display:none;'";?>>
            
            <h4>Login information</h4>
            <div class="col-md-6 form-group">
                <label class="control-label" for="file_flightplan-email">E-mail</label>
                <div class="">
                    <input type="text" class="form-control" id="file_flightplan-email" name="email" placeholder="E-mail" required>
                </div>
            </div>
            <div class="col-md-6 form-group">
                <label class="control-label" for="file_flightplan-key">Key</label>
                <div class="">
                    <input type="text" class="form-control" id="file_flightplan-key" name="privateKey" placeholder="Key" required>
                </div>
            </div>
            
            <h4>Pilot information</h4>
            <div class="col-md-12 form-group">
                <label class="control-label" for="file_flightplan-callsign">Callsign</label>
                <div class="">
                    <input type="text" class="form-control" id="file_flightplan-callsign" name="callsign" placeholder="Callsign" value="<?php echo $Flightplan->callsign;?>" required>
                </div>
            </div>
            
            <h4>Flight information</h4>
            <div class="col-md-12 form-group">
                <label class="control-label" for="file_flightplan-date">Date</label>
                <div class="">
                    <input type="text" class="form-control" name="date" id="file_flightplan-date" placeholder="Departure date" value="<?php echo $Flightplan->dateDeparture;?>" required>
                </div>
            </div>
            <div class="col-xs-6 form-group">
                <label class="control-label" for="file_flightplan-departureAirport">Departure airport</label>
                <div class="">
                    <input type="text" class="form-control" name="departureAirport" id="file_flightplan-departureAirport" placeholder="Departure airport" value="<?php echo $Flightplan->departureAirport;?>" required>
                </div>
            </div>
            <div class="col-xs-6 form-group" for="file_flightplan-arrivalAirport">
                <label class="control-label">Arrival airport</label>
                <div class="">
                    <input type="text" class="form-control" name="arrivalAirport" id="file_flightplan-arrivalAirport" placeholder="Arrival airport" value="<?php echo $Flightplan->arrivalAirport;?>" required>
                </div>
            </div>
            <div class="col-xs-6 form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label" for="file_flightplan-departureTime">Departure time</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <input type="text" name="departureTime" class="form-control" id="file_flightplan-departureTime" value="<?php echo $Flightplan->departureTime;?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-6 form-group"><div class="row">
                    <div class="col-md-12">
                        <label class="control-label">Arrival time</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <input type="text" name="arrivalTime" class="form-control" id="file_flightplan-arrivalTime" value="<?php echo $Flightplan->arrivalTime;?>">
                    </div>
                </div>
            </div>
            
            <h4>Additional information</h4>
            <div class="col-sm-4 col-xs-6 form-group">
                <label class="control-label">Cruise altitude</label>
                <div class="">
                    <input type="text" class="form-control" id="file_flightplan-cruiseAltitude" name="cruiseAltitude" placeholder="Cruise altitude" value="<?php echo $Flightplan->cruiseAltitude;?>">
                </div>
            </div>
            <div class="col-sm-4 col-xs-6 form-group">
                <label class="control-label">Route</label>
                <div class="">
                    <input type="text" class="form-control" id="file_flightplan-waypoints" name="waypoints" placeholder="Waypoints" value="<?php echo $Flightplan->waypoints;?>">
                </div>
            </div>
            <div class="col-sm-4 col-xs-6 form-group">
                <label class="control-label">Pilot name</label>
                <div class="">
                    <input type="text" class="form-control" name="pilotName" placeholder="Pilot" value="<?php echo $Flightplan->pilotName;?>">
                </div>
            </div>
            <div class="col-sm-4 col-xs-6 form-group">
                <label class="control-label">Airline</label>
                <div class="">
                    <input type="text" class="form-control" name="airline" placeholder="Cruise altitude" value="<?php echo $Flightplan->airline;?>">
                </div>
            </div>
            <div class="col-sm-4 col-xs-6 form-group">
                <label class="control-label">Flight number</label>
                <div class="">
                    <input type="text" class="form-control" name="flightNumber" placeholder="Cruise altitude" value="<?php echo $Flightplan->flightNumber;?>">
                </div>
            </div>
            <div class="col-sm-4 col-xs-6 form-group">
                <label class="control-label">Category</label>
                <div class="">
                    <select name="category" class="form-control" id="file_flightplan-category">
                        <option value="ifr" <?php if ($Flightplan->category == "ifr") echo "selected";?>>Instrument (IFR)</option>
                        <option value="vfr" <?php if ($Flightplan->category == "vfr") echo "selected";?>>Visual (VFR)</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4 col-xs-6 form-group">
                <label class="control-label">Aircraft</label>
                <div class="">
                    <input type="text" class="form-control" name="aircraft" placeholder="Aircraft" value="<?php echo $Flightplan->aircraftType;?>">
                </div>
            </div>
            <div class="col-xs-12 form-group">
                <button type="submit" value="Edit" class="btn btn-success">Edit</button>
            </div>
        </form>

<?php

}

?>
</div>
    
<br/>
<br/>
<?php include('./include/footer.php5'); ?>
