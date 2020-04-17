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
    $alternateDestination = "";
    $trueAirspeed = "";
    $soulsOnBoard = 0;
    $fuelTime = "";
    $comments = "";

    // We create the Flightplan
    $NewFlightplan->create($date, $date, $departureAirport, $arrivalAirport, $alternateDestination, $cruiseAltitude, $trueAirspeed, $callsign, $pilotName, $airline, $flightNumber, $category, $aircraftType, $departureTime, $arrivalTime, $waypoints, $soulsOnBoard, $fuelTime, $comments);

    // If there has not been an ID due to data missing and/or wrong data, we display errors
    if ($NewFlightplan->id == FALSE)
    {
        echo "<div class='alert alert-danger'>
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
        echo "<div class='alert alert-info'>Your flightplan has been accepted. A key has been sent to $email .</div>";
    }
}

?>


<a name="flightplan_filling"></a>


<div id="myModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New flightplan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form role="form" method="post" class="" action="./index.php" <?php if (isset($_GET['form_newSession'])) echo "style='display:none;'";?>>
            <h4>Personal information</h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="control-label" for="file_flightplan-callsign">Callsign</label>
                        <input type="text" class="form-control" id="file_flightplan-callsign" name="callsign" placeholder="Callsign" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="control-label" for="file_flightplan-email">E-mail</label>
                        <input type="text" class="form-control" id="file_flightplan-email" name="email" placeholder="Email Address" required>
                    </div>
                </div>
            </div>

            <h4>Flight information</h4>
            <div class="form-group">
                <label class="control-label">Date</label>
                <div class="">
                    <input type="text" class="form-control" name="date" id="file_flightplan-date" placeholder="Departure date (YYYY-MM-DD)" value="" required>
                </div>
            </div>
            <div class="row">
                <div class="col form-group">
                    <label class="control-label">Departure airport</label>
                    <input type="text" class="form-control" name="departureAirport" id="file_flightplan-departureAirport" placeholder="Departure airport" value="" required>
                </div>
                <div class="col form-group">
                    <label class="control-label">Arrival airport</label>
                    <input type="text" class="form-control" name="arrivalAirport" id="file_flightplan-arrivalAirport" placeholder="Arrival airport" value="" required>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <label class="control-label">Departure time</label>
                </div>
                <div class="col-sm-6">
                    <select name="departureTimeHours" class="form-control" id="file_flightplan-departureTimeHours<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>">
                    <?php
                    for ($h = 0; $h < 24; $h++)
                    {
                        if ($h == date('H'))
                        {
                            echo "<option value='".$h."' selected='selected'>".sprintf("%02d",$h)." h</option>";
                        }
                        else
                        {
                            echo "<option value='".$h."'>".sprintf("%02d",$h)." h</option>";
                        }
                    }
                    ?>
                    </select>
                </div>
                <div class="col-sm-6">
                    <select name="departureTimeMinutes" class="form-control" id="file_flightplan-departureTimeMinutes<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>">
                    <?php
                    for ($m = 0; $m < 60; $m+=5)
                    {
                        // Calculation of the nearest 5 minutes
                        $currentM = date('i');
                        $roundM = (round($currentM)%5 === 0) ? round($currentM) : round(($currentM+5/2)/5)*5;

                        if ($roundM == $m)
                        {
                            echo "<option value='".sprintf("%02d",$m)."' selected='selected'>".sprintf("%02d",$m)." UTC</option>";
                        }
                        else
                        {
                            echo "<option value='".sprintf("%02d",$m)."'>".sprintf("%02d",$m)." UTC</option>";
                        }
                    }
                    ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <label class="control-label">Arrival time</label>
                </div>
                <div class="col-sm-6">
                    <select name="arrivalTimeHours" class="form-control" id="file_flightplan-arrivalTimeHours<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>">
                    <?php
                    for ($h = 0; $h < 24; $h++)
                    {
                        if ($h == date('H'))
                        {
                            echo "<option value='".$h."' selected='selected'>".sprintf("%02d",$h)." h</option>";
                        }
                        else
                        {
                            echo "<option value='".$h."'>".sprintf("%02d",$h)." h</option>";
                        }
                    }
                    ?>
                    </select>
                </div>
                <div class="col-sm-6">
                    <select name="arrivalTimeMinutes" class="form-control" id="file_flightplan-arrivalTimeMinutes<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>">
                    <?php
                    for ($m = 0; $m < 60; $m+=5)
                    {
                        // Calculation of the nearest 5 minutes
                        $currentM = date('i');
                        $roundM = (round($currentM)%5 === 0) ? round($currentM) : round(($currentM+5/2)/5)*5;

                        if ($roundM == $m)
                        {
                            echo "<option value='".sprintf("%02d",$m)."' selected='selected'>".sprintf("%02d",$m)." UTC</option>";
                        }
                        else
                        {
                            echo "<option value='".sprintf("%02d",$m)."'>".sprintf("%02d",$m)." UTC</option>";
                        }
                    }
                    ?>
                    </select>
                </div>
            </div>
            <br/>
            <h4>Additional information</h4>
            <div class="row">
                <div class="col-sm-12 form-group">
                    <label class="control-label">Cruise altitude</label>
                    <input type="text" class="form-control" id="file_flightplan-cruiseAltitude" name="cruiseAltitude" placeholder="Cruise altitude" value="">
                </div>
                <div class="col-sm-12 form-group">
                    <label class="control-label">Route</label>
                    <input type="text" class="form-control" id="file_flightplan-waypoints" name="waypoints" placeholder="Waypoints" value="">
                </div>
                <div class="col-sm-6 form-group">
                    <label class="control-label">Pilot name</label>
                    <input type="text" class="form-control" name="pilotName" placeholder="Pilot" value="">
                </div>
                <div class="col-sm-6 form-group">
                    <label class="control-label">Airline</label>
                    <input type="text" class="form-control" name="airline" placeholder="Airline" value="">
                </div>
                <div class="col-sm-6 form-group">
                    <label class="control-label">Flight number</label>
                    <input type="text" class="form-control" name="flightNumber" placeholder="Flight number" value="">
                </div>
                <div class="col-sm-6 form-group">
                    <label class="control-label">Category</label>
                    <select name="category" class="form-control" id="file_flightplan-category">
                        <option value="ifr">Instrument (IFR)</option>
                        <option value="vfr">Visual (VFR)</option>
                    </select>
                </div>
                <div class="col-sm-6 form-group">
                    <label class="control-label">Aircraft</label>
                    <input type="text" class="form-control" name="aircraftType" placeholder="Aircraft" value="">
                </div>
                <button type="submit" value="Create" class="btn btn-success btn-block">Create</button>
            </div>

        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<script type="text/javascript" language="javascript">
    $(document).ready(function(){
       $(".showall").click(function(){
           $(".flightplanHidden").removeClass("flightplanHidden");
           $(".showall").addClass("flightplanHidden");
       });
       $("#file_flightplan-date").datepicker({
           dateFormat: 'yy-mm-dd'
       });
    });
</script>
