<?php

if (isset($_POST['date']) AND isset($_POST['callsign']) AND isset($_POST['departureAirport']) AND isset($_POST['arrivalAirport']))
{
    $NewFlightplan = new Flightplan();
    $NewFlightplan->create($_POST['date'],$_POST['departureAirport'], $_POST['arrivalAirport'], $_POST['cruiseAltitude'], $_POST['callsign'], $_POST['category'], $_POST['aircraftType'], $_POST['departureTime'], $_POST['arrivalTime'], $_POST['waypoints'], $_POST['comments']);
}

?>

<form class="file_flightplan" method="post" action="./index.php5#scheduled_flights">

    <h3>File a flightplan</h3>
    
    <h4>Category</h4>
    <select name="category">
        <option value="IFR">Instrumental</option>
        <option value="VFR">Visual</option>
    </select>
    
    <h4>Date</h4>
    <input type="text" id="file_flightplan2-date" name="date" value="<?php echo date('Y').'-'.date('m').'-'.date('d');?>" size="8" style="background: #f6f6f6 url('./img/scheme_date.png') no-repeat 5px 5px;"/>
    
    <h4>Departure info</h4>
    <input type="text" id="file_flightplan2-departureAirport" name="departureAirport" value="Airport" size="6" style="background: #f6f6f6 url('./img/scheme_airport.png') no-repeat 5px 5px;" onclick="this.value='';"/>
    <br/>
    <input type="text" id="file_flightplan2-departureTime" name="departureTime" value="TI:ME*" size="6" style="background: #f6f6f6 url('./img/scheme_time.png') no-repeat 5px 5px;" onclick="this.value='';"/>
    <br/>
    * Time is UTC
    
    <h4>Arrival info</h4>
    <input type="text" id="file_flightplan2-arrivalAirport" name="arrivalAirport" value="Airport" size="6" style="background: #f6f6f6 url('./img/scheme_airport.png') no-repeat 5px 5px;" onclick="this.value='';"/>
    <br/>
    <input type="text" id="file_flightplan2-arrivalTime" name="arrivalTime" value="TI:ME*" size="6" style="background: #f6f6f6 url('./img/scheme_time.png') no-repeat 5px 5px;" onclick="this.value='';"/>
    <br/>
    * Time is UTC
    
    <h4>Callsign</h4>
    <input type="text" name="callsign" size="6" style="background: #f6f6f6 url('./img/scheme_plane.png') no-repeat 5px 5px;"/>
    
    <br/>
    <br/>
    <a href="#" onclick="document.getElementById('file_flightplan2-additionalInfo').style.display = 'block'; return false;">+++ Additional info</a>
    
    <div id="file_flightplan2-additionalInfo" style="display: none;">
        <h5>Cruise level</h5>
        <input type="text" name="cruiseAltitude" value="FL" size="6" style="background: #f6f6f6 url('./img/scheme_flightLevel.png') no-repeat 5px 5px;"/>
        <h5>Aircraft type</h5>
        <input type="text" name="aircraftType" value="" size="9" style="background: #f6f6f6 url('./img/scheme_plane.png') no-repeat 5px 5px;"/>
        <h5>Waypoints (comma separated)</h5>
        <textarea name="waypoints" cols="6" style="background: #f6f6f6 url('./img/scheme_waypoints.png') no-repeat 5px 5px; padding: 30px 5px 5px 30px;"/></textarea>
        <h5>Comments</h5>
        <textarea name="comments" cols="10" style="background: #f6f6f6; padding: 0;"/></textarea>
    </div>
<br/>
<br/>
<input type="submit" value="File this flightplan"/>
</form>
