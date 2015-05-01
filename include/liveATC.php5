
<?php

// Known ATC models !
$atc_models = array("atc", "atc2", "atc-ml", "atc-fs", "openradar", "atc-tower2","atc-pie");

// Raw content from mpserver12
$rawContent = file_get_contents('http://mpserver12.flightgear.org/external/airspace_json.php');

// We decode the data
$allContacts = json_decode($rawContent, TRUE);
// List of ATCS
$atcs = Array();

// If there are contacts
if ($allContacts != NULL)
{
	// For each "pilot" we see
	foreach ($allContacts['pilots'] as $pilot)
	{
		// We gather all info relevant to this pilot
		foreach($pilot as $information => $value)
		{
			// We focus on the pilot's aircraft
			if ($information == "aircraft" AND (array_search(strtolower($value), $atc_models) !== FALSE))
			{
				$atcs[$pilot['callsign']]['callsign'] = strtoupper($pilot['callsign']);
				$atcs[$pilot['callsign']]['latitude'] = $pilot['latitude'];
				$atcs[$pilot['callsign']]['longitude'] = $pilot['longitude'];
			}
		}
	}
}

function getAirportByCoordinates($lon,$lat)
{
    global $db;
    
    $precision = 20;
    
    $airportInformation = Array();
    if (isset($lon) AND isset($lat))
    {
        $longitude = floor($lon*$precision);
        $latitude = floor($lat*$precision);
        
        $airports = $db->query("SELECT globalAirportICAO,globalAirportCity,globalAirportCountry FROM airports_global WHERE FLOOR(globalAirportLat*$precision) = '$latitude' AND FLOOR(globalAirportLon*$precision) = '$longitude'");
        if ($airports != NULL)
        {
            foreach($airports as $airport)
            {
                $airportInformation['ICAO'] = $airport['globalAirportICAO'];
                $airportInformation['city'] = $airport['globalAirportCity'];
                $airportInformation['country'] = $airport['globalAirportCountry'];
            }
        }
    }
    
    return $airportInformation;
}

?>

<div class="liveATC">
    <h1>Live Flightgear ATC</h1>
    <div class="liveEvents">
        <ul>
            <?php
            if ($atcs != NULL)
            {
                foreach($atcs as $atc)
                {
                    $airportInformation = getAirportByCoordinates($atc['longitude'], $atc['latitude']);
                    echo "<li>".$atc['callsign'];
                    if ($airportInformation != NULL)
                    {
                        echo " (".$airportInformation['city'].",".$airportInformation['country'].")";
                    }
                    echo "</li>";
                }
            }
            else
            {
                echo "There are no ATC online.";
            }
            
            ?>
        </ul>
        <ul>
            <li>Data from <a href="http://mpserver12.flightgear.org">mpserver12.flightgear.org</a></li>
        </ul>
    </div>
</div>
