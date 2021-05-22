<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<?php

$query_airport = $db->query("SELECT * FROM controlled_area ORDER BY airport_icao ASC");

?>

<!-- LE CODE COMMENCE ICI -->

<div class="jumbotron">
    <div class="container">
        <h1 class="display-3">Regularly controlled area</h1>
        <p class="lead">
            European airports are one of the most controlled places, and provide a great regular Air Traffic Control service.
            <br/>
            Here is a quick list of airports being controlled. The list will get longer, and don't hesitate to <a href="./contact.php">contact me</a> if you want to add a new regular Air Traffic Control event.
        </p>
        <p class="lead text-warning">
            Caution: this list is to take with care because some data may not be up to date. If you want to have a better overview of airports that may be controlled please refer to the "<b>Based on stats</b>" events below the <a href="index.php">regular events on the home page</a>.
            <br/>
            <a href="faq.php#basedonstatsevents">Learn more about based on stats events</a>
        </p>
    </div>
</div>

<div class="container">

    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th>ICAO</th>
                    <th>Airport name</th>
                    <th>Days of control</th>
                    <th>Times</th>
                    <th>Additional information</th>
                </tr>
            </thead>
            <tbody>
<?php

            foreach ($query_airport as $airport) {
                echo "<tr>";
                    echo "<td>".$airport['airport_icao']."</td>";
                    echo "<td>".$airport['airport_name']."</td>";
                    echo "<td>".$airport['days_control']."</td>";
                    echo "<td>".$airport['time_start']." to ".$airport['time_end']." UTC</td>";
                    echo "<td>";
                        if ($airport['link'] != NULL) echo '<a href="'.$airport['link'].'" target="_blank">';
                        echo $airport['additional_info'];
                        if ($airport['link'] != NULL) echo '</a>';
                    echo "</td>";
                echo "</tr>";
            }
?>

            </tbody>
        </table>

    </div>

</div>

<?php include('./include/footer.php'); ?>
