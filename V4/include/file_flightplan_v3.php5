
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

<a name="flightplan_filling"></a>
<form class="file_flightplan2" method="post" action="./index.php5#scheduled_flights" <?php if (isset($_GET['form_newSession'])) echo "style='display:none;'";?>>

	
    <div style="width:512px; height:256px" id="map"></div>
     <script defer="defer" type="text/javascript">
		
		// *******************************************
		// ************** MAP PART *******************
		// *******************************************
		
		// We initialize projections
		var fromProjection = new OpenLayers.Projection("EPSG:4326"); // transform from WGS 1984
		var toProjection = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
		var extent = new OpenLayers.Bounds(-180,-90,180,90).transform(fromProjection,toProjection);
		
		// We set some options
		var options = { scales: [50000000, 30000000, 10000000, 5000000],
                    resolutions: [1.40625,0.703125,0.3515625,0.17578125,0.087890625,0.0439453125],
                    minScale: 50000000,
                    maxResolution: "auto",
                    maxExtent: new OpenLayers.Bounds(-180, -90, 180, 90),
                    maxResolution: 0.17578125,
                    maxScale: 10000000,
                    minResolution: "auto",
                    minExtent: new OpenLayers.Bounds(-1, -1, 1, 1),
                    minResolution: 0.0439453125,
                    numZoomLevels: 5,
                    units: "degrees"
                  };
         
		// Map creation
		var map = new OpenLayers.Map('map',options);
		// First layer creation with tiles
		var wms = new OpenLayers.Layer.OSM( "OpenLayers WMS",
            "http://tiles.connectedserver.com/tiles/${z}/${x}/${y}.png",{maxZoomLevel: 12, numZoomLevels: 12},
        {layers: 'basic'}, {
            "tileOptions": {
                "crossOriginKeyword": null
            }
        } );
        
        
        
        // ***********************************
        // ******* LAYERS PARTS **************
        // ***********************************
        
        <?php
        
        // Color picker
        $layerColors = Array('#196dff','#ffab19','#9e9e9e','#444546','#C11515','#097054','#6599FF','#FF9900','#37c88e','#8E37C8','#C83771','#700925','#705409');
        
        $layerColori = 0;
        
        while ($layerColori < sizeof($layerColors))
        {
			?>
				var style<?php echo $layerColori; ?> = new OpenLayers.Style({
					pointRadius: 5, fillColor: "<?php echo $layerColors[$layerColori];?>", fillOpacity: 0.8, stroke: 0});
					
				var layer<?php echo $layerColori; ?> = new OpenLayers.Layer.Vector("My Layer", { styleMap: style<?php echo $layerColori; ?> });
			<?php
			$layerColori++;
		}
		?>

<?php
		/*
		 *  We will list every ATC sessions coming in the next days
		 */
		// We first initialize the number of next ATC sessions
		$nbNextSession = 0;
		// Offset ?
		$previousDate = 0;
		// For color chooser
		$layerColori = 0;
		// We go through the ATC sessions from the array $events (created on index.php5)
		while ($nbNextSession < sizeof($events))
		{
			// We verify if there is an ICAO and if the date will occur in the future
			if ($events[$nbNextSession]["ICAO"] != NULL AND $events[$nbNextSession]["date"] >= date('Y-m-d') AND $events[$nbNextSession]["date"] <= date('Y-m-d',strtotime(date('Y-m-d').' +2 days')))
			{
				$resultCoordinates = mysql_query("SELECT globalAirportLat,globalAirportLon FROM airports_global WHERE globalAirportICAO = '".$events[$nbNextSession]["ICAO"]."';");
				$airportCoordinates = mysql_fetch_array($resultCoordinates);
				$lat = $airportCoordinates['globalAirportLat'];
				$lon = $airportCoordinates['globalAirportLon'];
				
				?>
				
				var point<?php echo $nbNextSession; ?>lonlat = new OpenLayers.LonLat(<?php echo $lon;?>,<?php echo $lat; ?>).transform(fromProjection, toProjection);
				
				point<?php echo $nbNextSession; ?> = new OpenLayers.Feature.Vector(
					new OpenLayers.Geometry.Point(point<?php echo $nbNextSession; ?>lonlat.lon, point<?php echo $nbNextSession; ?>lonlat.lat));
				
				layer<?php echo $layerColori; ?>.addFeatures([point<?php echo $nbNextSession; ?>]);
				
				<?php
				$layerColori++;
			}
			// We increment the number of next ATC sessions
			$nbNextSession++;
		}
?>

	

		map.addLayers([wms,layer12,layer11,layer10,layer9,layer8,layer7,layer6,layer5,layer4,layer3,layer2,layer1,layer0]);
		map.setCenter(new OpenLayers.LonLat(8.5706,50.0333).transform(fromProjection, toProjection),2);
      </script>
    
    <div id="palmares">
		<h4>Most frequent pilots</h4>
		
		<ol>
		<?php
		
		$result = mysql_query('SELECT callsign, count(callsign) AS count FROM `flightplans20140113` GROUP BY callsign ORDER BY count DESC LIMIT 0,5');
		
		while ($flightplan = mysql_fetch_array($result))
		{
			
			echo "<li><span class='palmaresPilot'>".$flightplan['callsign']."</span> : ".$flightplan['count']." flights</li>";
			
		}
		?>
		</ol>
		
	</div>
    
    <h3 id="flightplanFillingTitle"> </h3>
    
    <div class='flightplanSortable no-border'>
		<img src="./img/scheme_pilot.png"/>
    </div>
    
    <div class='flightplanSortable static'>
		<input type="text" value="MY CALLSIGN" id="file_flightplan2-callsign" name="callsign" size="14" onfocus="if(this.value=='MY CALLSIGN') this.value='';"/>	
    </div>
    
    <div class="flightplanSortable flightplanSortableValue">
		<input type="text" value="FROM" name="departureAirport" id="file_flightplan3-departureAirport" size="6"/>
    </div>
    
    <div class="flightplanSortable flightplanSortableValue">
		<input type="text" value="TO" name="arrivalAirport" id="file_flightplan3-arrivalAirport" size="6"/>
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
    
    <ul id="nextATCSessions">
		<?php
		/*
		 *  We will list every ATC sessions coming in the next days
		 */
		// We first initialize the number of next ATC sessions
		$nbNextSession = 0;
		// Offset ?
		$previousDate = 0;
		// For color chooser
		$layerColori = 0;
		// We go through the ATC sessions from the array $events (created on index.php5)
		while ($nbNextSession < sizeof($events))
		{
			// We verify if there is an ICAO and if the date will occur in the future
			if ($events[$nbNextSession]["ICAO"] != NULL AND $events[$nbNextSession]["date"] >= date('Y-m-d') AND $events[$nbNextSession]["date"] <= date('Y-m-d',strtotime(date('Y-m-d').' +2 days')))
			{
				// New day
				if ($previousDate != $events[$nbNextSession]["date"])
				{
					if ($events[$nbNextSession]["date"] == date('Y-m-d')) echo "<div class='nextATCSessionsDay'>Today</div>";
					elseif ($events[$nbNextSession]["date"] == date('Y-m-d',strtotime(date('Y-m-d').' +1 day'))) echo "<div class='nextATCSessionsDay'>Tomorrow</div>";
					else echo "<div class='nextATCSessionsDay'>On ".date('l',strtotime($events[$nbNextSession]["date"]))."</div>";
				}
			?>
				<li class="nextATCSessionsSession" style='background-color: <?php echo $layerColors[$layerColori]; ?>;' id="session<?php echo $nbNextSession;?>" onclick="document.getElementById('file_flightplan2-callsign').style.backgroundColor='#33ee33';document.getElementById('file_flightplan2-departureAirport').value = '<?php echo $events[$nbNextSession]["ICAO"];?>'; document.getElementById('file_flightplan2-date').value = '<?php echo $events[$nbNextSession]["date"];?>'; document.getElementById('file_flightplan2-date').style.backgroundColor = '#33ee33'; document.getElementById('file_flightplan2-departureAirport').style.backgroundColor='#33ee33'; document.getElementById('file_flightplan2-arrivalAirport').value = '<?php echo $events[$nbNextSession]["ICAO"];?>'; document.getElementById('file_flightplan2-arrivalAirport').style.backgroundColor='#33ee33';">
					<img src="./img/eventHandle.png" class="eventHandle"/><span class="dragAirportName"><b><?php echo $events[$nbNextSession]["ICAO"]." - ".$events[$nbNextSession]["Name"]; ?></b></span>
					<?php // We format times displayed in hh:mm instead of hh:mm:ss ?>
					<br/><span class="dragTime">From <b><?php echo date('H:i',strtotime($events[$nbNextSession]["beginTime"])); ?></b> to <b><?php echo date('H:i',strtotime($events[$nbNextSession]["endTime"])); ?></b></span>
					<div id="session<?php echo $nbNextSession;?>additional" class="dragHiddenInformation additionalInformation" style="display: none;">
						<?php if ($events[$nbNextSession]["fgcom"] != NULL) { ?> Frequency <b><?php echo $events[$nbNextSession]["fgcom"]; } ?></b>
						<br/>
						<?php if ($events[$nbNextSession]["teamspeak"] != NULL) { ?>Mumble/TeamSpeak <b><?php echo $events[$nbNextSession]["teamspeak"]; } ?></b>
						<span class="dragHiddenInformation dragDate"><?php echo $events[$nbNextSession]["date"]; ?></span>
						<span class="dragHiddenInformation dragBeginTime"><?php echo $events[$nbNextSession]["beginTime"]; ?></span>
						<span class="dragHiddenInformation dragEndTime"><?php echo $events[$nbNextSession]["endTime"]; ?></span>
					</div>
				</li>
			<?php
				$previousDate = $events[$nbNextSession]["date"];
				$layerColori++;
			}
			// We increment the number of next ATC sessions
			$nbNextSession++;
		}
		?>
	</ul>
	
    
</form>

<script type="text/javascript">

$(document).ready(function()
{
	
	$('ul#nextATCSessions li').draggable({
      appendTo: "body",
      helper: "clone",
      start: function(e, ui)
		{
		$(ui.helper).addClass("ui-draggable-helper");
		}
    });
	$('.flightplanSortableValue').droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		accept: ":not(.ui-sortable-helper)",
		drop: function( event, ui ) {
			$( this ).find( ".placeholder" ).remove();
			var dataAirportName = ui.draggable.find('.dragAirportName').text();
			var arrAirportName = dataAirportName.split(' ');
			var dataDate = ui.draggable.find('.dragDate').text();
			var dataTime = ui.draggable.find('.dragTime').text();
			$( this ).find('input').val(arrAirportName[0]);
			$('#file_flightplan2-date').val(dataDate);
			$('#file_flightplan2-date').addClass('correct');
			$( this ).css("background-color", ui.draggable.css('background-color'));
			$( this ).addClass('flightplanSelected');
		}
	});
	
	
	$('#dateTimePicker').datepicker({ dateFormat:'yy-mm-dd', showOn: 'button', buttonImage: './img/scheme_date.png', buttonImageOnly: true });

	
	$('.nextATCSessionsSession').hover(function()
	{
		$('#' + this.id + 'additional').slideDown("fast");
	}, function()
	{
		$('#' + this.id + 'additional').slideUp("fast");
	});
	
	$('.nextATCSessionsSession').click(function()
	{
		$('#flightplan_filling_form').fadeIn();
		$(document).scrollTop( $('#flightplanFillingTitle').offset().top);
	});
	
	$('#file_flightplan2-callsign').click(function()
	{
		$('#flightplan_filling_form').fadeIn();
		$(document).scrollTop( $('#flightplanFillingTitle').offset().top);
	});
	
	$(".file_flightplan2").submit(function()
	{
		var error = 0;
		if ($('#file_flightplan2-callsign').val() == '' || $('#file_flightplan2-callsign').val() == 'MY CALLSIGN')
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
			error = 1;
		}
		else { error = 0; $('#file_flightplan2-dateError').hide(); }
			
		if ($('#file_flightplan3-departureAirport').val() == 'FROM' || $('#file_flightplan2-departureTime').val() == '' || $('#file_flightplan2-departureTime').val() == 'TI:ME*')
		{
			$('#file_flightplan2-departureError').html("Please indicate a valid airport and departure time");
			$('#file_flightplan2-departureError').show();
			error = 1;
		}
		else { error = 0; $('#file_flightplan2-departureError').hide(); }
			
		if ($('#file_flightplan3-arrivalAirport').val() == 'TO' || $('#file_flightplan2-arrivalTime').val() == '' || $('#file_flightplan2-arrivalTime').val() == 'TI:ME*')
		{
			$('#file_flightplan2-arrivalError').html("Please indicate a valid airport and arrival time");
			$('#file_flightplan2-arrivalError').show();
			error = 1;
		}
		else { error = 0; $('#file_flightplan2-arrivalError').hide(); }
		
		if (error == 1)
		{
			return false;
		}
	});
	
});

</script>

