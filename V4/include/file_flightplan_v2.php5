<?php

if (isset($_POST['date']) AND isset($_POST['callsign']) AND isset($_POST['departureAirport']) AND isset($_POST['arrivalAirport']))
{
    $NewFlightplan = new Flightplan();
    $NewFlightplan->create($_POST['date'],$_POST['departureAirport'], $_POST['arrivalAirport'], $_POST['alternateDestination'], $_POST['cruiseAltitude'], $_POST['trueAirspeed'], $_POST['callsign'], $_POST['pilotName'], $_POST['flightNumber'], $_POST['category'], $_POST['aircraftType'], $_POST['departureTime'], $_POST['arrivalTime'], $_POST['waypoints'], $_POST['soulsOnBoard'], $_POST['fuelTime'], $_POST['comments']);

    if ($NewFlightplan->dataMissing != false)
    {
		echo "<div class='warning'>
                    Warning : some information is missing.
                    <br/>Your flightplan requires a minima these info :
                    <ul>
                        <li>Callsign</li>
                        <li>Departure and arrival airport</li>
                        <li>Departure and arrival time</li>
                    </ul>
                    Please make sure you filled these fields. Otherwise <a href='./contact.php5' style='color: #ccc;'>contact the admin</a>.
                    </div>";
    }
    
    if ($NewFlightplan->dataMissing == false AND $NewFlightplan->departureATCpresence == false)
    {
		echo "<div class='warning'>
                    Your flightplan has been accepted, but departure airport is not controlled at this time
                    </div>";
	}
	if ($NewFlightplan->dataMissing == false AND $NewFlightplan->arrivalATCpresence == false)
	{
		echo "<div class='warning'>
                    Your flightplan has been accepted, but arrival airport is not controlled at this time
                    </div>";
	}
}

?>
<a name="flightplan_filling"></a>
<form class="file_flightplan2" method="post" action="./index.php5#scheduled_flights" <?php if (isset($_GET['form_newSession'])) echo "style='display:none;'";?>>

    <h3>Quick flightplan filling form <span id="show_hide_flightplan_filling" onclick="if(document.getElementById('flightplan_filling_form').style.display == 'none') document.getElementById('flightplan_filling_form').style.display='inline'; else document.getElementById('flightplan_filling_form').style.display='none';">show/hide</span></h3>
    
    <div id="flightplan_filling_form" style="display: none;">
		
		
		
		<br/>
		<span class="new_flightplan_entry">Date</span>
		<input type="text" id="file_flightplan2-date" name="date" value="<?php echo date('Y').'-'.date('m').'-'.date('d');?>" size="15" style="background: #f6f6f6 url('./img/scheme_date.png') no-repeat 5px 5px;"/> <span class="flightplan_filling_day_button" onclick="document.getElementById('file_flightplan2-date').value='<?php echo date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));?>';">Today</span> <span class="flightplan_filling_day_button" onclick="document.getElementById('file_flightplan2-date').value='<?php echo date('Y-m-d',mktime(0,0,0,date('m'),date('d')+1,date('Y')));?>';">Tomorrow</span> <span class="flightplan_filling_day_button" onclick="document.getElementById('file_flightplan2-date').value='<?php echo date('Y-m-d',mktime(0,0,0,date('m'),date('d')+2,date('Y')));?>';">On <?php echo date('l',mktime(0, 0, 0, date('m'), date('d')+2, date('Y')));?></span>
		<br/>
		
		<span class="new_flightplan_entry">Departure</span>
		<input type="text" id="file_flightplan2-departureAirport" name="departureAirport" value="Airport" size="6" style="background: #f6f6f6 url('./img/scheme_airport.png') no-repeat 5px 5px;" onclick="this.value='';"/>
		<input type="text" id="file_flightplan2-departureTime" name="departureTime" value="TI:ME*" size="6" style="background: #f6f6f6 url('./img/scheme_time.png') no-repeat 5px 5px;" onclick="this.value='';"/>
		* Time is UTC
		<br/>
		
		<span class="new_flightplan_entry">Arrival</span>
		<input type="text" id="file_flightplan2-arrivalAirport" name="arrivalAirport" value="Airport" size="6" style="background: #f6f6f6 url('./img/scheme_airport.png') no-repeat 5px 5px;" onclick="this.value='';"/>
		<input type="text" id="file_flightplan2-arrivalTime" name="arrivalTime" value="TI:ME*" size="6" style="background: #f6f6f6 url('./img/scheme_time.png') no-repeat 5px 5px;" onclick="this.value='';"/>
		* Time is UTC
		<br/>
		<br/>
		
		<a href="#" onclick="document.getElementById('file_flightplan2-additionalInfo').style.display = 'block'; return false;">+ Additional info</a>
		
		<div id="file_flightplan2-additionalInfo" style="display: none;">
			<span class="new_flightplan_entry">Category</span>
			<select name="category"><option value="IFR">Instrumental</option><option value="VFR">Visual</option></select>
			<br/>
			<span class="new_flightplan_entry">Cruise level</span>
			<input type="text" name="cruiseAltitude" value="FL" size="6" style="background: #f6f6f6 url('./img/scheme_flightLevel.png') no-repeat 5px 5px;"/>
			<br/>
			<span class="new_flightplan_entry">Aircraft type</span>
			<input type="text" name="aircraftType" value="" size="9" style="background: #f6f6f6 url('./img/scheme_plane.png') no-repeat 5px 5px;"/>
			<br/>
			<span class="new_flightplan_entry">Pilot name</span>
			<input type="text" name="pilotName" value="" size="9" style="background: #f6f6f6 url('./img/scheme_people.png') no-repeat 5px 5px;"/>
			<br/>
			<span class="new_flightplan_entry">Flight number</span>
			<input type="text" name="flightNumber" value="" size="6" style="background: #f6f6f6 url('./img/scheme_flight.png') no-repeat 5px 5px;"/>
			<br/>
			<span class="new_flightplan_entry">Waypoints</span>
			<textarea name="waypoints" cols="15" rows="4" style="background: #f6f6f6 url('./img/scheme_waypoints.png') no-repeat 5px 5px; padding: 30px 5px 5px 30px;"/></textarea>
			<br/>
			<span class="new_flightplan_entry">Comments</span>
			<textarea name="comments" cols="30" rows="6" style="background: #f6f6f6; padding: 0;"/></textarea>
			
			<br/>
			<br/>
			<a href="#" onclick="document.getElementById('file_flightplan2-additionalInfo2').style.display = 'block'; return false;">+ Additional info</a>
		
			<div id="file_flightplan2-additionalInfo2" style="display: none;">
				<span class="new_flightplan_entry">Alternate destination</span>
				<input type="text" name="alternateDestination" value="" size="5" style="background: #f6f6f6 url('./img/scheme_airport.png') no-repeat 5px 5px;"/>
				<br/>
				<span class="new_flightplan_entry">True Air Speed</span>
				<input type="text" name="trueAirspeed" value="TAS" size="4" style="background: #f6f6f6 url('./img/scheme_plane.png') no-repeat 5px 5px;"/>
				<br/>
				<span class="new_flightplan_entry">Souls on board</span>
				<input type="text" name="soulsOnBoard" value="" size="3" style="background: #f6f6f6 url('./img/scheme_people.png') no-repeat 5px 5px;"/>
				<br/>
				<span class="new_flightplan_entry">Fuel time</span>
				<input type="text" name="fuelTime" value="" size="6" style="background: #f6f6f6 url('./img/scheme_fuel.png') no-repeat 5px 5px;"/>
			</div>
			
			<br/><br/>
			
		</div>
		
		
		
		<input type="button" value="File this flightplan" onclick="document.getElementById('file_flightplan2-callsignForm').style.display='block'; this.style.display='none';"/>
		<div id="file_flightplan2-callsignForm" style="display: none;">
			<span class="new_flightplan_entry">Callsign</span>
			<input type="text" name="callsign" size="7" style="background: #f6f6f6 url('./img/scheme_plane.png') no-repeat 5px 5px;" value="Callsign" onclick="this.value='';"/>
			<br/><br/>
			<input type="submit" value="File this flightplan"/>
		</div>
	</div>
</form>
