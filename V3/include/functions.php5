<?php


/**
 * Returns the wanted information from table
 * @param string $wantedInfo    Name of column wanted
 * @param string $table         Name of table
 * @param string $col           Column for filter
 * @param string $value         Value for filter
 * @return string
 */
function getInfo($wantedInfo,$table,$col,$value)
{
    $result = mysql_query("SELECT $wantedInfo FROM $table WHERE $col = '$value'") or die(mysql_error());
    
    $info = mysql_fetch_row($result);
    return $info[0];
}

function returnEvents($date = NULL)
{
    $events = array();
    if (isset($date) AND $date != NULL) $events_list = mysql_query("SELECT * FROM events WHERE date = '$date' ORDER BY beginTime");   
    else $events_list = mysql_query("SELECT * FROM events WHERE date >= ".date('Y-m-d')." ORDER BY date,beginTime"); 
    $i = 0;
    
    while ($event = mysql_fetch_array($events_list))
    {
        if ($event["airportICAO"] != NULL)
        {
            $events[$i]["Id"] = $event['eventId'];
            $events[$i]["ICAO"] = $event['airportICAO'];
            $events[$i]["ATCId"] = $event['userId'];
            $events[$i]["Name"] = getInfo('name','airports','ICAO',$event['airportICAO']);
            $events[$i]["date"] = $event['date'];
            $events[$i]["beginTime"] = $event['beginTime'];
            $events[$i]["endTime"] = $event['endTime'];
            $events[$i]["fgcom"] = $event['fgcom'];
            $events[$i]["teamspeak"] = $event['teamspeak'];
            $events[$i]["docsLink"] = $event['docsLink'];
            $events[$i]["remarks"] = $event['remarks'];
        }
        $i++;
    }

    return $events;
}

function isAirportControlled($ICAO,$date,$time)
{
    
    // The value to return at the end of the function
    $airportControlled = false;
    
    $requestAirports = mysql_query("SELECT * FROM events WHERE airportICAO = '$ICAO' AND date = '$date'") or die(mysql_error());
    
    while ($requestAirport = mysql_fetch_array($requestAirports))
    {
        $beginTime = $requestAirport['beginTime'];
        $endTime = $requestAirport['endTime'];
        
        if ($time >= $beginTime AND $time <= $endTime)
        {
            $airportControlled = true;
            return $airportControlled;
        }
        else
        {
            $airportControlled = false;
        }
    }
}

function date_difference ($date1timestamp, $date2timestamp)
{
	$all = round(($date1timestamp - $date2timestamp) / 60);
	$d = floor ($all / 1440);
	$h = floor (($all - $d * 1440) / 60);
	$m = $all - ($d * 1440) - ($h * 60);
	
	return array('days'=>$d, 'hours'=>$h, 'mins'=>$m);
}


function flightplanToXML($Flightplan)
{
	$XMLflightplans = new SimpleXMLElement("<flightplans></flightplans>");
	$XMLflightplans->addAttribute('version',DEV_VERSION);
	
	$XMLflightplan = $XMLflightplans->addChild('flightplan');
		
	$XMLflightplan->addChild('flightplanId',$Flightplan->id);
	$XMLflightplan->addChild('callsign',$Flightplan->callsign);
	$XMLflightplan->addChild('flightNumber',$Flightplan->flightNumber);
	$XMLflightplan->addChild('airportFrom',$Flightplan->departureAirport);
	$XMLflightplan->addChild('airportTo',$Flightplan->arrivalAirport);
	$XMLflightplan->addChild('alternateDestination',$Flightplan->alternateDestination);
	$XMLflightplan->addChild('cruiseAltitude',$Flightplan->cruiseAltitude);
	$XMLflightplan->addChild('trueAirspeed',$Flightplan->trueAirspeed);
	$XMLflightplan->addChild('dateDeparture',$Flightplan->dateDeparture);
	$XMLflightplan->addChild('dateArrival',$Flightplan->dateArrival);
	$XMLflightplan->addChild('departureTime',$Flightplan->departureTime);
	$XMLflightplan->addChild('arrivalTime',$Flightplan->arrivalTime);
	$XMLflightplan->addChild('aircraft',$Flightplan->aircraftType);
	$XMLflightplan->addChild('soulsOnBoard',$Flightplan->soulsOnBoard);
	$XMLflightplan->addChild('fuelTime',$Flightplan->fuelTime);
	$XMLflightplan->addChild('pilotName',$Flightplan->pilotName);
	$XMLflightplan->addChild('waypoints',$Flightplan->waypoints);
	$XMLflightplan->addChild('category',$Flightplan->category);
	
	$XMLflightplanComments = $XMLflightplan->addChild('comments');
	foreach ($Flightplan->comments as $comments)
	{
		$XMLcomment = $XMLflightplanComments->addChild('comment');
		$XMLcomment->addChild('user',$comments['pseudo']);
		$XMLcomment->addChild('message',$comments['comment']);
	}
	$XMLflightplan->addChild('status',$Flightplan->status);
	
	$XMLflightplanInfo = $XMLflightplan->addChild('additionalInformation');
	
	foreach($Flightplan->history as $variable => $info)
	{
		$XMLflightplanInfo->addChild($variable,$info['value']);
	}
	$XMLflightplan->addChild('lastUpdated',$Flightplan->lastUpdated);
	
	header('Content-type: application/xml');
	echo $XMLflightplans->asXML();
}

function flightplansToXML($flightplans_list)
{
	$XMLflightplans = new SimpleXMLElement("<flightplans></flightplans>");
	$XMLflightplans->addAttribute('version',DEV_VERSION);
	
	while ($flightplan = mysql_fetch_array($flightplans_list))
	{
		
		$Flightplan = new Flightplan();
		$Flightplan->selectById($flightplan['flightplanId']);
		
		$XMLflightplan = $XMLflightplans->addChild('flightplan');
		
		$XMLflightplan->addChild('flightplanId',$Flightplan->id);
		$XMLflightplan->addChild('callsign',$Flightplan->callsign);
		$XMLflightplan->addChild('flightNumber',$Flightplan->flightNumber);
		$XMLflightplan->addChild('airportFrom',$Flightplan->departureAirport);
		$XMLflightplan->addChild('airportTo',$Flightplan->arrivalAirport);
		$XMLflightplan->addChild('alternateDestination',$Flightplan->alternateDestination);
		$XMLflightplan->addChild('cruiseAltitude',$Flightplan->cruiseAltitude);
		$XMLflightplan->addChild('trueAirspeed',$Flightplan->trueAirspeed);
		$XMLflightplan->addChild('dateDeparture',$Flightplan->dateDeparture);
		$XMLflightplan->addChild('dateArrival',$Flightplan->dateArrival);
		$XMLflightplan->addChild('departureTime',$Flightplan->departureTime);
		$XMLflightplan->addChild('arrivalTime',$Flightplan->arrivalTime);
		$XMLflightplan->addChild('aircraft',$Flightplan->aircraftType);
		$XMLflightplan->addChild('soulsOnBoard',$Flightplan->soulsOnBoard);
		$XMLflightplan->addChild('fuelTime',$Flightplan->fuelTime);
		$XMLflightplan->addChild('pilotName',$Flightplan->pilotName);
		$XMLflightplan->addChild('waypoints',$Flightplan->waypoints);
		$XMLflightplan->addChild('category',$Flightplan->category);
		
		$XMLflightplanComments = $XMLflightplan->addChild('comments');
		foreach ($Flightplan->comments as $comments)
		{
			$XMLcomment = $XMLflightplanComments->addChild('comment');
			$XMLcomment->addChild('user',$comments['pseudo']);
			$XMLcomment->addChild('message',$comments['comment']);
		}
		$XMLflightplan->addChild('status',$Flightplan->status);
		
		$XMLflightplanInfo = $XMLflightplan->addChild('additionalInformation');
	
		foreach($Flightplan->history as $variable => $info)
		{
			$XMLflightplanInfo->addChild($variable,$info['value']);
		}
		$XMLflightplan->addChild('lastUpdated',$Flightplan->lastUpdated);
	}
	
	header('Content-type: application/xml');
	echo $XMLflightplans->asXML();
		
}

?>
