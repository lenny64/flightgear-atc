<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<link rel="stylesheet" href="./style/school.css" type="text/css">

<!-- LE CODE COMMENCE ICI -->

<h3>Flight School</h3>

<h4>Introducing Flight plan Suggestions</h4>
<p class="normal_content">
    Flight plan Suggestions aim to help pilots increasing their skills and knowledge. They are short documents trying focusing on simple courses while guiding pilots flying from an airport to an other.
    <br/>
    Each Flight plan Suggestion will handle a particular point of what every pilot should know.
</p>


<div class="flightplanSuggestionList">
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
            <div class="flightplanSuggestion">
                <div class="flightplanSuggestionContent">
                    <span class="flightplanSuggestionTitle"><?php echo $flightplanSuggestion['title'];?></span>
                    <div class="flightplanSuggestionDescription">
                        <div class="flightplanSuggestionDocs">
                            <a href="./download.php5?file=<?php echo $flightplanSuggestion['docsLink'];?>" target="_blank"><img src="./img/logo_pdf.png" width="25"/> Flight plan Suggestion document</a>
                            <br/>
                            <a href="<?php echo $flightplanSuggestion['skyVector'];?>" target="_blnak"><img src="./img/scheme_waypoints.png" width="25"/> SkyVector route</a>
                        </div>
                        <p>
                            <b><?php echo $flightplanSuggestion['description'];?></b>
                        </p>
                        <p>
                            In this Flight plan Suggestion you will fly from <b><?php echo $flightplanSuggestion['departureAirport']." to ".$flightplanSuggestion['arrivalAirport'];?></b> at this recommended cruise level (<b><?php echo $flightplanSuggestion['cruiseAltitude'];?></b>).
                            <br/>
                            Those airports are mostly controlled on <b><?php echo $flightplanSuggestion['mostControlled'];?></b> so please make sure they are both controlled <a href="./index.php5" target="_blank">there</a> at the time you plan to fly.
                        </p>
                        <p>
                            This Flight plan Suggestion mainly focuses on <b><?php echo $flightplanSuggestion['courses'];?></b>.
                        </p>
                    </div>
                </div>
                <form class="flightplanSuggestionForm" action="./index.php5" method="post">
                    <h4>Download the <a href="./download.php5?file=<?php echo $flightplanSuggestion['docsLink'];?>" target="_blank">Flight plan Suggestion</a><br/>and File your flight plan now!</h4>
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
                    <table>
                        <tr>
                            <td>Departure date</td>
                            <td><input type="text" name="date" size="8" value="<?php echo date('Y-m-d');?>"/></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Departure airport</td>
                            <input type="hidden" name="departureAirport" value="<?php echo $flightplanSuggestion['departureAirport'];?>"/>
                            <td><b><?php echo $flightplanSuggestion['departureAirport'];?></b></td>
                            <td>Arrival airport</td>
                            <input type="hidden" name="arrivalAirport" value="<?php echo $flightplanSuggestion['arrivalAirport'];?>"/>
                            <td><b><?php echo $flightplanSuggestion['arrivalAirport'];?></b></td>
                        </tr>
                        <tr>
                            <td>Departure time</td>
                            <td>
                                <select name="departureTimeHours" id="file_flightplan-departureTimeHours<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" class="time" onchange="calculateNewArrivalTime(<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>);">
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
                                :
                                <select name="departureTimeMinutes" id="file_flightplan-departureTimeMinutes<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" class="time" onchange="calculateNewArrivalTime(<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>);">
                                <?php
                                for ($m = 0; $m < 60; $m+=5)
                                {
                                    // Calculation of the nearest 5 minutes
                                    $currentM = date('i');
                                    $roundM = (round($currentM)%5 === 0) ? round($currentM) : round(($currentM+5/2)/5)*5;

                                    if ($roundM == $m)
                                    {
                                        echo "<option value='".sprintf("%02d",$m)."' selected='selected'>".sprintf("%02d",$m)."</option>";
                                    }
                                    else
                                    {
                                        echo "<option value='".sprintf("%02d",$m)."'>".sprintf("%02d",$m)."</option>";
                                    }
                                }
                                ?>
                                </select> UTC
                            </td>
                            <td>Arrival time</td>
                            <td>
                                <input type="text" size="2" name="arrivalTimeHours" id="inputArrivalHours<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" value="?" readonly="readonly"/>:
                                <input type="text" size="2" name="arrivalTimeMinutes" id="inputArrivalMinutes<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" value="?" readonly="readonly"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Airspeed</td>
                            <td><input type="text" size="3" id="inputSpeed<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>" value="<?php echo $flightplanSuggestion['speed'];?>" onKeyUp="calculateNewArrivalTime(<?php echo $flightplanSuggestion['flightplanSuggestionId'];?>);"/> kts</td>
                            <td>Cruise level</td>
                            <td><input type="text" size="4" value="<?php echo $flightplanSuggestion['cruiseAltitude'];?>" disabled="disabled"/></td>
                        </tr>
                    </table>
                    Route :
                    <br/>
                    <?php echo $flightplanSuggestion['route'];?>
                    <br/>
                    <input type="text" name="callsign" placeholder="Callsign" required/>
                    <br/>
                    <input type="text" name="email" placeholder="E-mail address" required/>
                    <br/>
                    E-mail address will allow you to edit the flight plan
                    <br/>
                    <br/>
                    <input type="submit" value="Submit this flight plan" class="flightplanSuggestionSubmit"/>
                </form>
                <br style="clear:both;"/>
            </div>
            <?php
        }
    }
    ?>
</div>

<?php include('./include/footer.php5'); ?>
