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
    global $db;

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

function checkIdent($ident)
{
    global $db;

    // We check if it is a registered user
    $ident_infos_list = $db->query("SELECT * FROM request_users WHERE ident = '$ident' LIMIT 1");
    $ident_infos = $ident_infos_list->fetch(PDO::FETCH_ASSOC);
    $wrong_ident = false;
    if ($ident_infos['ident'] != $ident AND $ident_infos['ident'] == 0)
    {
        $wrong_ident = true;
    }

    return $wrong_ident;
}

function returnEvents($date = NULL)
{
    global $db;

    $events = array();
    if (isset($date) AND $date != NULL) $events_list = $db->query("SELECT eventId,airportICAO,date FROM events WHERE date = '$date' ORDER BY beginTime");
    else $events_list = $db->query("SELECT eventId,airportICAO,date FROM events WHERE date >= '".date('Y-m-d')."' ORDER BY date,beginTime");
    $i = 0;

    foreach ($events_list as $row)
    {
        $events[$i]['Id'] = $row['eventId'];
        $events[$i]['airportICAO'] = $row['airportICAO'];
        $events[$i]['date'] = $row['date'];
        $i++;
    }

    return $events;
}

// Function to filter events with a particular field
function filterEvents($filter,$filterValue,$events)
{
    $outputEvents = Array();
    foreach ($events as $event)
    {
        if ($event[$filter] == $filterValue)
        {
            $outputEvents[] = $event['Id'];
        }
    }

    return $outputEvents;
}

function isAirportControlled($ICAO,$date,$time)
{
    global $db;

    // The value to return at the end of the function
    $airportControlled = false;

    $requestAirports = $db->query("SELECT * FROM events WHERE airportICAO = '$ICAO' AND date = '$date'");

    foreach ($requestAirports as $requestAirport)
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
	$XMLflightplan = new SimpleXMLElement("<flightplan></flightplan>");
	$XMLflightplan->addAttribute('version',DEV_VERSION);

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
            if (is_array($Flightplan->comments))
            {
		foreach ($Flightplan->comments as $comments)
		{
			$XMLcomment = $XMLflightplanComments->addChild('comment');
			$XMLcomment->addChild('user',$comments['pseudo']);
			$XMLcomment->addChild('message',$comments['comment']);
		}
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
	echo $XMLflightplan->asXML();
}

function flightplansToXML($flightplans_list)
{
	$XMLflightplans = new SimpleXMLElement("<flightplans></flightplans>");
	$XMLflightplans->addAttribute('version',DEV_VERSION);
    $XMLflightplans->addAttribute('count',count($flightplans_list));

	foreach ($flightplans_list as $flightplan)
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
            if ($info['value'] != NULL)
            {
    			$XMLflightplanInfo->addChild($variable,$info['value']);
            }
		}
		$XMLflightplan->addChild('lastUpdated',$Flightplan->lastUpdated);
	}

	header('Content-type: application/xml');
	echo $XMLflightplans->asXML();

}

// This function will treat input values against code injection
function purgeInputs($inputValue)
{
    if (is_array($inputValue))
    {
        foreach ($inputValue as $arrayKey => $arrayValue)
        {
            if (is_array($arrayValue))
            {
                foreach ($arrayValue as $valueKey => $valueValue)
                {
                    $arrayValue[$valueKey] = htmlspecialchars($valueValue);
                }
                $value = $arrayValue;
            }
            else
            {
                $inputValue[$arrayKey] = htmlspecialchars($arrayValue);
                $value = $inputValue;
            }
        }
    }
    else
    {
        $value = htmlspecialchars($inputValue);
    }
    return $value;
}

?>
