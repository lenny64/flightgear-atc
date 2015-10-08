<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>


<!-- LE CODE COMMENCE ICI -->

<div class="container">

    
    <?php
    // DEPECHE AFP PART (including abstract picture)
    // NEW : now includes liveATC
    include('./include/depeche.php5');

    // FOR SIGNIFICATIVE CHANGES
    include('./include/log.php5');

    // I am first looking for every eventId
    $events = returnEvents();

    include('./include/file_flightplan_v3.php5');


    // New specialEvents feature
    include('./include/specialEvents.php5');

    // C A L E N D A R
    include('./include/calendar.php5');
    echo '<br style="clear:both;"/>';

    ?>

    <br/>
    <br/>
</div>
<?php include('./include/footer.php5'); ?>
