<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<?php

$query_airport = $db->query("SELECT * FROM controlled_area ORDER BY airport_icao ASC");

?>

<!-- LE CODE COMMENCE ICI -->

<div class="jumbotron">
    <div class="container">
        <h2>Regularly controlled area</h2>

        <p>
            European airports are one of the most controlled places, and provide a great regular ATC service.
        </p>
        <p>
            Here is a quick list of airports being controlled. The list will get longer, and don't hesitate to <a href="./contact.php">contact me</a> if you want to add a new regular ATC session.
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
