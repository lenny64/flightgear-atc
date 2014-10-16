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
    $db = new PDO("mysql:host=".SQL_SERVER.";dbname=".SQL_DB, SQL_LOGIN, SQL_PWD);
    
    $query = $db->query("SELECT $wantedInfo FROM $table WHERE $col = '$value'");
    
    foreach($query as $info)
    {
        $result = $info[0];
    }
    
    // In case there is no result from the query
    if (!isset($result) OR $result == NULL)
    {
        return false;
    }
    // If everything is fine, we return the result
    else
    {
        return $result;
    }
}

function returnEvents($date = NULL)
{
    $db = new PDO("mysql:host=".SQL_SERVER.";dbname=".SQL_DB, SQL_LOGIN, SQL_PWD);
    
    $events = array();
    if (isset($date) AND $date != NULL) $events_list = $db->query("SELECT * FROM events WHERE date = '$date' ORDER BY beginTime");   
    else $events_list = $db->query("SELECT * FROM events WHERE date >= ".date('Y-m-d')." ORDER BY date,beginTime"); 
    $i = 0;
    
    foreach ($events_list as $row)
    {
        if ($row["airportICAO"] != NULL)
        {
            $events[$i]["Id"] = $row['eventId'];
            $events[$i]["ICAO"] = $row['airportICAO'];
            $events[$i]["ATCId"] = $row['userId'];
            $events[$i]["Name"] = getInfo('name','airports','ICAO',$row['airportICAO']);
            $events[$i]["date"] = $row['date'];
            $events[$i]["beginTime"] = $row['beginTime'];
            $events[$i]["endTime"] = $row['endTime'];
            $events[$i]["fgcom"] = $row['fgcom'];
            $events[$i]["teamspeak"] = $row['teamspeak'];
            $events[$i]["docsLink"] = $row['docsLink'];
            $events[$i]["remarks"] = $row['remarks'];
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
	$XMLflightplan->addChild('airline',$Flightplan->airline);
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
	if (isset($Flightplan->comments) AND $Flightplan->comments != NULL)
	{
		foreach ($Flightplan->comments as $comments)
		{
			$XMLcomment = $XMLflightplanComments->addChild('comment');
			$XMLcomment->addChild('user',$comments['pseudo']);
			$XMLcomment->addChild('message',$comments['comment']);
		}
	}
	$XMLflightplan->addChild('status',$Flightplan->status);
	
	$XMLflightplanInfo = $XMLflightplan->addChild('additionalInformation');
	
	if (isset($Flightplan->history) AND $Flightplan->history != NULL)
	{
		foreach($Flightplan->history as $variable => $info)
		{
			$XMLflightplanInfo->addChild($variable,$info['value']);
		}
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
		$XMLflightplan->addChild('airline',$Flightplan->airline);
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
