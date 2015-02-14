
<?php

// Known ATC models !
$atc_models = array("atc", "atc2", "atc-ml", "atc-fs", "openradar", "atc-tower2","atc-pie");

// Raw content from mpserver12
$rawContent = file_get_contents('http://mpserver12.flightgear.org/external/airspace_json.php');

// We decode the data
$allContacts = json_decode($rawContent, TRUE);
// List of ATCS
$atcs = Array();

// For each "pilot" we see
foreach ($allContacts['pilots'] as $pilot)
{
    // We gather all info relevant to this pilot
    foreach($pilot as $information => $value)
    {
        // We focus on the pilot's aircraft
        if ($information == "aircraft")
        {
            // We compare the lowercase value to our atc models
            if (array_search(strtolower($value), $atc_models) !== FALSE)
            {
                $atcs[] = strtoupper($pilot['callsign']);
            }
        }
    }
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
                    echo "<li>".$atc."</li>";
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