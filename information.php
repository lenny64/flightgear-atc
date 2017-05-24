<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<!-- LE CODE COMMENCE ICI -->
<div class="jumbotron">
    <div class="container">
        <h2>Information</h2>
        <p>
            Important information about the flightgear-atc network and website
        </p>
    </div>
</div>

<div class="container">

    <div class="panel panel-default">
        <div class="panel-heading">2017-04-28 - <strong>Maintenance operation</strong></div>
        <div class="panel-body">
            <p>
                Due to an internal migration on alwaysdata host services, a maintenance operation is planned on Thursday may 25th from 19:00 to 21:00 UTC. The main change will occur on webpages extensions. Indeed, the regular ".php5" pages will be renamed in ".php" according to the new PHP5 version hosted by Alwaysdata.
                <br/>
                This operation won't affect ATCs and pilots regular operations (planning an ATC session, filling a flight plan, etc...).
            </p>
            <p>
                WARNING!
                <br/>
                This operation will affect API users.
                <br/>
                We kindly ask our API users to call the new version (dev2017_04_28) :
                <br/>
                <a href="http://flightgear-atc.alwaysdata.net/dev2017_04_28.php" target="_blank">http://flightgear-atc.alwaysdata.net/dev2017_04_28.php</a>
            </p>
        </div>
    </div>

</div>

<?php include('./include/footer.php'); ?>
