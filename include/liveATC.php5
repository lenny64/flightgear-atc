
<?php

function getSSLPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_SSLVERSION,3); 
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// Known ATC models !
$atc_models = array("atc", "atc2", "atc-ml", "atc-fs", "openradar", "atc-tower", "atc-tower2", "atc-pie");

// Raw content from mpserver12
//$rawContent = file_get_contents('https://mpserver12.org/external/airspace_json.php');
$rawContent = getSSLPage('https://mpserver12.org/external/airspace_json.php');
echo $rawContent;
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

<ul class="list-group" style="font-size: 0.9em;">
    <li class='list-group-item list-group-item-info'>
        Live ATCs availables
    </li>
    <?php
    if ($atcs != NULL)
    {
        foreach($atcs as $atc)
        {
            $airportInformation = getAirportByCoordinates($atc['longitude'], $atc['latitude']);
            echo "<li class='list-group-item'>".$atc['callsign'];
            if ($airportInformation != NULL)
            {
                echo " (".$airportInformation['city'].",".$airportInformation['country'].")";
            }
            echo "</li>";
        }
    }
    else
    {
        echo "<li class='list-group-item small'>There are no ATC online.</li>";
    }
    ?>
    <li class="list-group-item list-group-item-info">
        <small>data from <a href="https://mpserver12.org">mpserver12.org</a></small>
    </li>
</ul>
