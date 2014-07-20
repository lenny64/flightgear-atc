
<?php

if (isset($_POST['date']) AND isset($_POST['callsign']) AND isset($_POST['departureAirport']) AND isset($_POST['arrivalAirport']))
{
    $NewFlightplan = new Flightplan();
    $NewFlightplan->create($_POST['date'],$_POST['departureAirport'], $_POST['arrivalAirport'], $_POST['alternateDestination'], $_POST['cruiseAltitude'], $_POST['trueAirspeed'], $_POST['callsign'], $_POST['pilotName'], $_POST['airline'], $_POST['flightNumber'], $_POST['category'], $_POST['aircraftType'], $_POST['departureTime'], $_POST['arrivalTime'], $_POST['waypoints'], $_POST['soulsOnBoard'], $_POST['fuelTime'], $_POST['comments']);

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

<script type="text/javascript" language="javascript">
    function changeFlightplan(airportFrom, airportTo, date)
    {
        document.getElementById('flightplan_filling_form').style.display = 'block';
        
        var departureField = document.getElementById('file_flightplan3-departureAirport');
        var arrivalField = document.getElementById('file_flightplan3-arrivalAirport');
        var dateField = document.getElementById('file_flightplan2-date');
        
        if (airportFrom)
        {
            departureField.value = airportFrom;
            departureField.style.color = '#333';
        }
        if (airportTo)
        {
            arrivalField.value = airportTo;
            arrivalField.style.color = '#333';
        }
        if (date)
        {
            dateField.value = date;
            dateField.style.color = '#333';
        }
    }
</script>

<a name="flightplan_filling"></a>
<form class="file_flightplan2" method="post" action="./index.php5#scheduled_flights" <?php if (isset($_GET['form_newSession'])) echo "style='display:none;'";?>>

    
    <h3 id="flightplanFillingTitle"> </h3>
    
    <div class='flightplanSortable no-border'>
        <img src="./img/scheme_pilot.png"/>
    </div>
    
    <div class='flightplanSortable static'>
        <input type="text" value="MY CALLSIGN" id="file_flightplan2-callsign" name="callsign" size="14" onfocus="if(this.value=='MY CALLSIGN') this.value='';"/>	
    </div>
    
    <div class="flightplanSortable flightplanSortableValue">
        <input type="text" value="FROM" name="departureAirport" id="file_flightplan3-departureAirport" size="6" onclick="this.value='';"/>
    </div>
    
    <div class="flightplanSortable flightplanSortableValue">
        <input type="text" value="TO" name="arrivalAirport" id="file_flightplan3-arrivalAirport" size="6" onclick="this.value='';"/>
    </div>
    
    
    <a name="file_flightplan_anchor"> </a>
    <div class="fileFPv3">
        <div id="file_flightplan2-callsignError" class="file_flightplan2-formError"></div>

        <div id="flightplan_filling_form" style="display: none;">

            <br/>
            <span class="new_flightplan_entry">Date</span>
            <input type="text" id="file_flightplan2-date" name="date" value="<?php echo date('Y').'-'.date('m').'-'.date('d');?>" size="15" style="background: #f6f6f6 url('./img/scheme_date.png') no-repeat 5px 5px;"/> <span class="flightplan_filling_day_button" onclick="document.getElementById('file_flightplan2-date').value='<?php echo date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));?>';">Today</span> <span class="flightplan_filling_day_button" onclick="document.getElementById('file_flightplan2-date').value='<?php echo date('Y-m-d',mktime(0,0,0,date('m'),date('d')+1,date('Y')));?>';">Tomorrow</span> <span class="flightplan_filling_day_button" onclick="document.getElementById('file_flightplan2-date').value='<?php echo date('Y-m-d',mktime(0,0,0,date('m'),date('d')+2,date('Y')));?>';">On <?php echo date('l',mktime(0, 0, 0, date('m'), date('d')+2, date('Y')));?></span>
            <div id="file_flightplan2-dateError" class="file_flightplan2-formError"></div>
            <br/>

            <span class="new_flightplan_entry">Departure</span>
            <input type="text" id="file_flightplan2-departureTime" name="departureTime" value="TI:ME*" size="6" style="background: #f6f6f6 url('./img/scheme_time.png') no-repeat 5px 5px;" onfocus="if(this.value=='TI:ME*') this.value='';" onblur="if(this.value=='') {this.value='TI:ME*'; this.style.color='red';}"/>
            * Time is UTC - <b>Current time : <?php echo date('H:i');?> UTC</b>
            <div id="file_flightplan2-departureError" class="file_flightplan2-formError"></div>
            <br/>

            <span class="new_flightplan_entry">Arrival</span>
            <input type="text" id="file_flightplan2-arrivalTime" name="arrivalTime" value="TI:ME*" size="6" style="background: #f6f6f6 url('./img/scheme_time.png') no-repeat 5px 5px;" onfocus="if(this.value=='TI:ME*') this.value='';" onblur="if(this.value=='') {this.value='TI:ME*'; this.style.color='red';}"/>
            * Time is UTC
            <div id="file_flightplan2-arrivalError" class="file_flightplan2-formError"></div>
            <br/>
            <br/>

            <a href="#" onclick="document.getElementById('file_flightplan2-additionalInfo').style.display = 'block'; return false;">+ Additional info</a>

            <div id="file_flightplan2-additionalInfo" style="display: none;">
                <span class="new_flightplan_entry">Category</span>
                <select name="category"><option value="IFR">Instrumental (IFR)</option><option value="VFR">Visual (VFR)</option></select>
                <br/>
                <span class="new_flightplan_entry">Cruise level</span>
                <input type="text" name="cruiseAltitude" value="FL" size="6" style="background: #f6f6f6 url('./img/scheme_flightLevel.png') no-repeat 5px 5px;" onfocus="if(this.value=='FL') this.value='';"/>
                <br/>
                <span class="new_flightplan_entry">Aircraft type</span>
                <input type="text" name="aircraftType" value="" size="9" style="background: #f6f6f6 url('./img/scheme_plane.png') no-repeat 5px 5px;"/>
                <br/>
                <span class="new_flightplan_entry">Pilot name</span>
                <input type="text" name="pilotName" value="" size="9" style="background: #f6f6f6 url('./img/scheme_people.png') no-repeat 5px 5px;"/>
                <br/>
                <span class="new_flightplan_entry">Airline</span>
                <input type="text" name="airline" value="" size="6" style="background: #f6f6f6 url('./img/scheme_flight.png') no-repeat 5px 5px;"/>
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
                    <input type="text" name="trueAirspeed" value="TAS" size="4" style="background: #f6f6f6 url('./img/scheme_plane.png') no-repeat 5px 5px;" onfocus="if(this.value=='TAS') this.value='';"/>
                    <br/>
                    <span class="new_flightplan_entry">Souls on board</span>
                    <input type="text" name="soulsOnBoard" value="" size="3" style="background: #f6f6f6 url('./img/scheme_people.png') no-repeat 5px 5px;"/>
                    <br/>
                    <span class="new_flightplan_entry">Fuel time</span>
                    <input type="text" name="fuelTime" value="" size="6" style="background: #f6f6f6 url('./img/scheme_fuel.png') no-repeat 5px 5px;"/>
                </div>

                <br/><br/>

            </div>

            <input type="submit" value="File this flightplan" class="flightplan2-submit"/>
        </div>
    </div>
    
    <div id="suggested_routes">
        <?php
        /*
         *  We will list every ATC sessions coming in the next days
         */
        // We first initialize the number of next ATC sessions
        $nbNextSession = 0;
        // Array that will contain the next events as $nextEvent[DATE][I] = ARRAY
        $nextEvents = Array();
        
        // We go through the ATC sessions from the array $events (created on index.php5)
        while ($nbNextSession < sizeof($events))
        {
            // We verify if there is an ICAO and if the date will occur in the future
            if ($events[$nbNextSession]["ICAO"] != NULL AND $events[$nbNextSession]["date"] >= date('Y-m-d') AND $events[$nbNextSession]["date"] <= date('Y-m-d',strtotime(date('Y-m-d').' +2 days')))
            {
                // The first array key is now the date. The second is the number of session occuring that day.
                $nextEvents[$events[$nbNextSession]["date"]][] = $events[$nbNextSession];
            }
            // We increment the number of next ATC sessions
            $nbNextSession++;
        }
        
        foreach ($nextEvents as $eventDate => $eventsArray)
        {
            // Variables that will be reinitialized each day :
            // Suggested routes that will contain an associative array of two eventId
            $suggestedRoutes = Array();
            // If there are more than 3 sessions to display, this variable will be set to TRUE
            $activateSeeAll = 0;
            
            // Title of the day
            if ($eventDate == date('Y-m-d')) echo "<div class='suggested_routes_day'><div class='suggested_routes_title'>Today</div>";
            elseif ($eventDate == date('Y-m-d',strtotime(date('Y-m-d').' +1 day'))) echo "<div class='suggested_routes_day'><div class='suggested_routes_title'>Tomorrow</div>";
            else echo "<div class='suggested_routes_day'><div class='suggested_routes_title'>On ".date('l',strtotime($eventDate))."</div>";
            
            // If the event is the single one of the day, we recommend a local flight :)
            if (sizeof($eventsArray) == 1)
            {
            ?>
                <div class='suggested_routes_container'>
                    <div class='suggested_routes_airport'>
                        <span class='airportICAO'><?php echo $eventsArray[0]["ICAO"];?></span>
                        <span class='airportName'><?php echo $eventsArray[0]["Name"];?></span>
                        <span class='beginTime'><?php echo date('H:i',strtotime($eventsArray[0]["beginTime"]));?> ></span>
                        <span class='endTime'><?php echo date('H:i',strtotime($eventsArray[0]["endTime"]));?></span>
                    </div>
                    <div class='buttonFromTo' id='buttonFrom' onclick="changeFlightplan('<?php echo $eventsArray[0]["ICAO"];?>','','<?php echo $eventsArray[0]["date"];?>');">&#9679;&rarr; &nbsp;FROM</div>
                    <div class='buttonFromTo' id='buttonTo' onclick="changeFlightplan('','<?php echo $eventsArray[0]["ICAO"];?>','<?php echo $eventsArray[0]["date"];?>');">TO&nbsp; &rarr;&#9679;</div>
                </div>
            <?php
            // We suggest a local flight
            $suggestedRoutes[] = Array($eventsArray[0], $eventsArray[0]);
            }
            // Otherwise if there are several events during the day
            else
            {
                // For each event we show it
                for ($i = 0; $i < sizeof($eventsArray); $i++)
                {
                ?>
                    <div class='suggested_routes_container<?php if ($i >= 3) { echo " suggested_routes_hidden"; $activateSeeAll = $i-2; } ?>'>
                        <div class='suggested_routes_airport'>
                            <span class='airportICAO'><?php echo $eventsArray[$i]["ICAO"];?></span>
                            <span class='airportName'><?php echo $eventsArray[$i]["Name"];?></span>
                            <span class='beginTime'><?php echo date('H:i',strtotime($eventsArray[$i]["beginTime"]));?> ></span>
                            <span class='endTime'><?php echo date('H:i',strtotime($eventsArray[$i]["endTime"]));?></span>
                        </div>
                        <div class='buttonFromTo' id='buttonFrom' onclick="changeFlightplan('<?php echo $eventsArray[$i]["ICAO"];?>','','<?php echo $eventsArray[0]["date"];?>');">&#9679;&rarr; &nbsp;FROM</div>
                        <div class='buttonFromTo' id='buttonTo' onclick="changeFlightplan('','<?php echo $eventsArray[$i]["ICAO"];?>','<?php echo $eventsArray[0]["date"];?>');">TO&nbsp; &rarr;&#9679;</div>
                    </div>
                    
                <?php
                    // If there is an event after the current one
                    if (array_key_exists($i+1, $eventsArray) AND array_key_exists('beginTime', $eventsArray[$i+1]))
                    {
                        // If it will occur less than 30 min after the end of this event
                        if ($eventsArray[$i+1]["beginTime"] <= date('H:i:s',  strtotime($eventsArray[$i]["endTime"]." +30 minutes")))
                        {
                            // We add those two airports to our suggestedRoutes array
                            $suggestedRoutes[] = Array($eventsArray[$i], $eventsArray[$i+1]);
                        }
                    }
                }                
            }
            
            
            // If there are more than 3 sessions to display, we print this "See all" feature
            if (isset($activateSeeAll) AND $activateSeeAll != 0)
            {
            ?>
                <span class='suggested_route_seeAllButton' onclick='this.style.display="none"; var elements = document.getElementsByClassName("suggested_routes_hidden"); for (var i = 0; i < elements.length; i++) { var item = elements[i]; item.style.display="block"; }'>&downarrow; <?php echo $activateSeeAll; ?> more &downarrow;</span>
            <?php
            }
            
            // SUGGESTED ROUTES
            if (sizeof($suggestedRoutes) != 0)
            {
            ?>
                <div class='suggested_routes'>
                    <div class='route_title'>
                        <span class='route_counter'><?php echo sizeof($suggestedRoutes);?></span> suggested routes
                        <div class='suggested_routes_extended'>
                            <?php
                            foreach ($suggestedRoutes as $routeNumber => $fromToCouple)
                            {
                                if ($fromToCouple[0]["ICAO"] == $fromToCouple[1]["ICAO"])
                                {
                                ?>
                                    <div class="route_suggestion_entry" onclick="changeFlightplan('<?php echo $fromToCouple[0]["ICAO"];?>','<?php echo $fromToCouple[1]["ICAO"];?>','<?php echo $fromToCouple[0]["date"];?>')">
                                        Local flight around <?php echo $fromToCouple[0]["ICAO"];?>
                                        <div class="route_suggestion_entry_extended">
                                            <b>Suggested aircraft :</b>
                                            <br/>
                                            Cessna 172p, SenecaII
                                            <br/>
                                            <b>Fly in <?php echo $fromToCouple[0]["ICAO"];?> area</b>
                                            <br/>
                                            between <?php echo $fromToCouple[0]["beginTime"]; ?> and <?php echo $fromToCouple[0]["endTime"]; ?>
                                            <br/>
                                            <b>Charts</b>
                                            <br/>
                                            <a href="<?php echo $fromToCouple[0]['docsLink'];?>"><?php echo $fromToCouple[0]['ICAO'];?></a>
                                        </div>
                                    </div>
                                <?php
                                }
                                else
                                {
                                ?>
                                    <div class="route_suggestion_entry" onclick="changeFlightplan('<?php echo $fromToCouple[0]["ICAO"];?>','<?php echo $fromToCouple[1]["ICAO"];?>','<?php echo $fromToCouple[0]["date"];?>')">
                                        Fly from <?php echo $fromToCouple[0]["ICAO"];?> to <?php echo $fromToCouple[1]["ICAO"];?>
                                        <div class="route_suggestion_entry_extended">
                                            <b>Leave before</b>
                                            <br/>
                                            <?php echo $fromToCouple[0]["endTime"]; ?>
                                            <br/>
                                            <b>Arrive after</b>
                                            <br/>
                                            <?php echo $fromToCouple[1]["beginTime"]; ?>
                                            <br/>
                                            <b>Charts</b>
                                            <br/>
                                            <a href="<?php echo $fromToCouple[0]['docsLink'];?>"><?php echo $fromToCouple[0]['ICAO'];?></a> &rarr; <a href="<?php echo $fromToCouple[1]['docsLink'];?>"><?php echo $fromToCouple[1]['ICAO'];?></a>
                                        </div>
                                    </div>
                                <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            // The div was open after the "foreach" statement
            echo "</div>";
        }
        ?>
        
    </div>
    
    <div id="palmares">
        <h4><img src="./img/scheme_pilot_star.png"/> Most flightplans filed</h4>

        <ol>
        <?php

        $result = mysql_query('SELECT callsign, count(callsign) AS count FROM `flightplans20140113` GROUP BY callsign ORDER BY count DESC LIMIT 0,5');

        while ($flightplan = mysql_fetch_array($result))
        {
            echo "<li><span class='palmaresPilot'>".$flightplan['callsign']."</span> : ".$flightplan['count']." flightplans</li>";
        }
        ?>
        </ol>
		
    </div>
    
</form>

<br style='clear: both;'/>

<script type="text/javascript">

$(document).ready(function()
{
	$('.buttonFromTo').hover(function(){
            $('#file_flightplan3-departureAirport').css("background-color","#3C71B2");
            $('#file_flightplan3-arrivalAirport').css("background-color","#3C71B2");
        }, function() {
            $('#file_flightplan3-departureAirport').css("background-color","#e0e0e0");
            $('#file_flightplan3-arrivalAirport').css("background-color","#e0e0e0");
        });

	$('#dateTimePicker').datepicker({ dateFormat:'yy-mm-dd', showOn: 'button', buttonImage: './img/scheme_date.png', buttonImageOnly: true });
	
	$('#file_flightplan2-callsign').click(function()
	{
		$('#flightplan_filling_form').fadeIn();
		$(document).scrollTop( $('#flightplanFillingTitle').offset().top);
	});
	
	$(".file_flightplan2").submit(function()
	{
		var error = 0;
		if ($('#file_flightplan2-callsign').val() == '' || $('#file_flightplan2-callsign').val() == "MY CALLSIGN")
		{
			$('#file_flightplan2-callsignError').html("Please indicate your callsign");
			$('#file_flightplan2-callsignError').show();
			error = 1;
		}
		else { error = 0; $('#file_flightplan2-callsignError').hide(); }
			
		if ($('#file_flightplan2-date').val() == '')
		{
			$('#file_flightplan2-dateError').html("Please indicate a date");
			$('#file_flightplan2-dateError').show();
			error = 2;
		}
		else { error = 0; $('#file_flightplan2-dateError').hide(); }
			
		if ($('#file_flightplan3-departureAirport').val() == 'FROM' || $('#file_flightplan2-departureTime').val() == '' || $('#file_flightplan2-departureTime').val() == 'TI:ME*')
		{
			$('#file_flightplan2-departureError').html("Please indicate a valid airport and departure time");
			$('#file_flightplan2-departureError').show();
			error = 3;
		}
		else { error = 0; $('#file_flightplan2-departureError').hide(); }
			
		if ($('#file_flightplan3-arrivalAirport').val() == 'TO' || $('#file_flightplan2-arrivalTime').val() == '' || $('#file_flightplan2-arrivalTime').val() == 'TI:ME*')
		{
			$('#file_flightplan2-arrivalError').html("Please indicate a valid airport and arrival time");
			$('#file_flightplan2-arrivalError').show();
			error = 4;
		}
		else { error = 0; $('#file_flightplan2-arrivalError').hide(); }
		
		if (error !== 0)
		{
			return false;
		}
	});
	
});

</script>

