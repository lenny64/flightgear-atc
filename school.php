<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<link rel="stylesheet" href="./style/school.css" type="text/css">

<!-- LE CODE COMMENCE ICI -->
<div class="jumbotron">
        <h1 class="display-3">Flight plan Suggestions</h1>
        <p class="lead">
            Flight plan Suggestions aim to increase pilots' skills and knowledge. Find below short documents focusing on simple courses.
        </p>
</div>

<div class="container">

    <div class="col-md-12 flightplanSuggestionList">
        <?php
        // We create the database instance
        global $db;
        // We list all the flight plans suggestions
        $flightplanSuggestionList = $db->query("SELECT * FROM flightplan_suggestion");

        foreach ($flightplanSuggestionList as $flightplanSuggestion)
        {
            // We check if the availability date is OK
            // -> we are able to add new flight plan suggestions ahead of time without them being visible!
            if (date('Y-m-d') >= $flightplanSuggestion['availabilityDate'])
            {
                ?>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">
                                    <h3><?php echo $flightplanSuggestion['title'];?></h3>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <a href="./download.php?file=<?php echo $flightplanSuggestion['docsLink'];?>" target="_blank" class="btn btn-info btn-sm"><img src="./img/logo_pdf.png" width="25"/> Flight plan Suggestion document</a>
                                    </p>
                                    <p>
                                        <a href="<?php echo $flightplanSuggestion['skyVector'];?>" target="_blank" class="btn btn-sm"><img src="./img/scheme_waypoints.png" width="20"/> SkyVector route</a>
                                    </p>
                                    <p>
                                        <b><?php echo $flightplanSuggestion['description'];?></b>
                                    </p>
                                    <p>
                                        In this Flight plan Suggestion you will fly from <b><?php echo $flightplanSuggestion['departureAirport']." to ".$flightplanSuggestion['arrivalAirport'];?></b> at this recommended cruise level (<b><?php echo $flightplanSuggestion['cruiseAltitude'];?></b>).
                                        <br/>
                                        Those airports are mostly controlled on <b><?php echo $flightplanSuggestion['mostControlled'];?></b> so please make sure they are both controlled <a href="./index.php" target="_blank">there</a> at the time you plan to fly.
                                    </p>
                                    <p>
                                        This Flight plan Suggestion mainly focuses on <b><?php echo $flightplanSuggestion['courses'];?></b>.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <form role="form" class="" action="./index.php" method="post">
                                <script type="text/javascript" language="javascript">

                                    function calculateNewArrivalTime(flightplanSuggestionId)
                                    {
                                        var speed = document.getElementById('inputSpeed'+flightplanSuggestionId).value;
                                        var departureTime = document.getElementById('file_flightplan-departureTimeHours'+flightplanSuggestionId).value+':'+document.getElementById('file_flightplan-departureTimeMinutes'+flightplanSuggestionId).value;
                                        var departureTimeArray = departureTime.split(':');
                                        var floatDuration = <?php echo ($flightplanSuggestion['distance'] * 60); ?> / speed;
                                        var hours = Math.floor(floatDuration/60);
                                        var minutes = Math.round(floatDuration % 60);

                                        var DateNow = new Date();
                                        var DepartureTime = new Date(DateNow.getFullYear(),DateNow.getMonth(),DateNow.getDay(),departureTimeArray[0],departureTimeArray[1]);
                                        var ArrivalTime = new Date(DateNow.getFullYear(),DateNow.getMonth(),DateNow.getDay(),parseInt(departureTimeArray[0])+hours,parseInt(departureTimeArray[1])+minutes);

                                        if(ArrivalTime.getHours().toString().length < 2)
                                        {
                                            var arrivalHours = "0"+ArrivalTime.getHours();
                                        }
                                        else var arrivalHours = ArrivalTime.getHours();
                                        if (ArrivalTime.getMinutes().toString().length < 2)
                                        {
                                            var arrivalMinutes = "0"+ArrivalTime.getMinutes();
                                        }
                                        else var arrivalMinutes = ArrivalTime.getMinutes();

                                        document.getElementById('inputArrivalHours'+flightplanSuggestionId).value = arrivalHours;
                                        document.getElementById('inputArrivalMinutes'+flightplanSuggestionId).value = arrivalMinutes;
                                    }

                                    calculateNewArrivalTime();

                                </script>
                                <?php
                                    // Arrival time calculation
                                    $floatDuration = (($flightplanSuggestion['distance'] * 60) / $flightplanSuggestion['speed']);
                                    $hours = floor($floatDuration / 60);
                                    $minutes = $floatDuration % 60;
                                    $flightplanSuggestion['duration'] = $hours.":".$minutes;
                                    $flightplanSuggestion['arrivalTime'] = date('H:i', strtotime($flightplanSuggestion['departureTime']." +".$minutes." minutes"));
                                ?>
                                <div class="form-group">
                                    <label for="departureDate">Departure date</label>
                                    <input type="text" name="date" id="departureDate" value="<?php echo date('Y-m-d');?>" class="form-control "/>
                                </div>
                                <div class="form-group">
                                    <label for="departureAirport">Departure airport : <?php echo $flightplanSuggestion['departureAirport'];?></label>
                                    <input type="hidden" name="departureAirport" value="<?php echo $flightplanSuggestion['departureAirport'];?>"/>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col">
                                            <label for="departureTime">Departure time</label>
                                        </div>
                                        <div class="col">
                                            <select name="departureTimeHours" class="form-control input-sm" id="file_flightplan-departureTimeHours<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" class="time" onchange="calculateNewArrivalTime(<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>);">
                                            <?php
                                            for ($h = 0; $h < 24; $h++)
                                            {
                                                if ($h == date('H'))
                                                {
                                                    echo "<option value='".$h."' selected='selected'>".sprintf("%02d",$h)."</option>";
                                                }
                                                else
                                                {
                                                    echo "<option value='".$h."'>".sprintf("%02d",$h)."</option>";
                                                }
                                            }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="departureTimeMinutes" class="form-control input-sm" id="file_flightplan-departureTimeMinutes<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" class="time" onchange="calculateNewArrivalTime(<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>);">
                                            <?php
                                            for ($m = 0; $m < 60; $m+=5)
                                            {
                                                // Calculation of the nearest 5 minutes
                                                $currentM = date('i');
                                                $roundM = (round($currentM)%5 === 0) ? round($currentM) : round(($currentM+5/2)/5)*5;

                                                if ($roundM == $m)
                                                {
                                                    echo "<option value='".sprintf("%02d",$m)."' selected='selected'>".sprintf("%02d",$m)." UTC</option>";
                                                }
                                                else
                                                {
                                                    echo "<option value='".sprintf("%02d",$m)."'>".sprintf("%02d",$m)." UTC</option>";
                                                }
                                            }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="arrivalAirport">Arrival airport: <?php echo $flightplanSuggestion['arrivalAirport'];?></label>
                                    <input type="hidden" name="arrivalAirport" id="arrivalAirport" value="<?php echo $flightplanSuggestion['arrivalAirport'];?>"/>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col">
                                            <label for="arrivalTime">Arrival time</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control input-sm" name="arrivalTimeHours" id="inputArrivalHours<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" value="?" readonly="readonly"/>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control input-sm" name="arrivalTimeMinutes" id="inputArrivalMinutes<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" value="?" readonly="readonly"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="airspeed">Airspeed (kts)</label>
                                    <input type="text" class="form-control" name="trueSpeed" id="inputSpeed<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" value="<?php echo $flightplanSuggestion['speed'];?>" onKeyUp="calculateNewArrivalTime(<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>);"/>
                                </div>
                                <div class="form-group">
                                    <label for="cruiseLevel">Cruise level</label>
                                    <input type="hidden" class="form-control" name="cruiseAltitude" value="<?php echo $flightplanSuggestion['cruiseAltitude'];?>"/>
                                    <p class="form-control-static">
                                        <?php echo $flightplanSuggestion['cruiseAltitude'];?>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="route">Route</label>
                                    <p class="form-control-static"><?php echo $flightplanSuggestion['route'];?></p>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="callsign" placeholder="Callsign" required/>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="email" placeholder="E-mail address" required/>
                                </div>
                                <input type="submit" class="btn btn-success btn-block" value="Create new flight plan"/>
                            </form>
                        </div>
                    </div>
                    <br style="clear:both;"/>
                    <hr class="mb-2"/>
                <?php
            }
        }
        ?>
    </div>
</div>

<?php include('./include/footer.php'); ?>
