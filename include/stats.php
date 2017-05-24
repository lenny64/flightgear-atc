<?php

global $db;

echo "<div style='color: #666; font-size: 0.8em; margin: 3% 0 3% 5%;'>";
echo "Number of events for each airport : ";

$airports = Array();
$count = Array();

$list_of_airports = $db->query("SELECT * FROM events ORDER BY airportICAO");

foreach ($list_of_airports as $airport)
{
    $airport_icao = $airport['airportICAO'];
    if (isset($airports[$airport_icao]))
    {
        $count[$airport_icao] = $count[$airport_icao] + 1;
    }
    else
    {
        $airports[$airport_icao] = 1;
        $count[$airport_icao] = 1;
    }
}

foreach ($count as $airport => $number)
{
    echo " ".$airport."(".$number.") ";
}

echo "</div>";

?>
