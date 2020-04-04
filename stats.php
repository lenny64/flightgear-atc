<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<?php

$airports = Array();
$count = Array();

$list_of_airports = $db->query("SELECT COUNT(*) as count, airportICAO, date FROM `events`
                                GROUP BY airportICAO, date
                                ORDER BY `airportICAO`");

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

?>

<!-- LE CODE COMMENCE ICI -->

<div class="container">
    <div class="row">

<?php
foreach ($count as $airport => $number)
{ ?>
        <div class="col-md-2 my-2">
            <div class="card">
                <div class="card-header">
                    <?= $airport; ?>
                </div>
                <div class="card-body">
                    <?= $number;?>
                </div>
            </div>
        </div>
<?php
}
?>

    </div>
</div>


<br/>
<br/>
<?php include('./include/footer.php'); ?>
