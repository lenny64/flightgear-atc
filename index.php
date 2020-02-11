<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>


<!-- LE CODE COMMENCE ICI -->

<div class="container">


    <?php
    // DEPECHE AFP PART (including abstract picture)
    // NEW : now includes liveATC
    include('./include/depeche.php');

    // FOR SIGNIFICATIVE CHANGES
    include('./include/log.php');

    // I am first looking for every eventId
    $events = returnEvents();

    include('./include/file_flightplan_v3.php');


    // New specialEvents feature
    include('./include/specialEvents.php');

    // C U S T O M   A T C   C A L E N D A R
    if ($_SESSION['mode'] == "connected") {
        include('./include/atc_homescreen.php');
    }

    // M A P
    include('./include/map.php');

    // C A L E N D A R
    include('./include/calendar.php');
    echo '<br style="clear:both;"/>';

    ?>

    <br/>
    <br/>
</div>
<?php include('./include/footer.php'); ?>
