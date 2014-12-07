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
                            <a href="<?php echo $flightplanSuggestion['docsLink'];?>" target="_blank"><img src="./img/logo_pdf.png" width="25"/> Flight plan Suggestion document</a>
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
                <form class="flightplanSuggestionForm">
                    <h4>File your flight plan now!</h4>
                    <script type="text/javascript" language="javascript">
                        
                        function calculateNewArrivalTime()
                        {
                            var speed = document.getElementById('inputSpeed').value;
                            var departureTime = document.getElementById('inputDepartureTime').value;
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
                            
                            document.getElementById('inputArrivalTime').value = arrivalHours+':'+arrivalMinutes;
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
                            <td><input type="text" size="8" value="<?php echo date('Y-m-d');?>"/></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Departure airport</td>
                            <td><b><?php echo $flightplanSuggestion['departureAirport'];?></b></td>
                            <td>Arrival airport</td>
                            <td><b><?php echo $flightplanSuggestion['arrivalAirport'];?></b></td>
                        </tr>
                        <tr>
                            <td>Departure time</td>
                            <td><input type="text" size="4" id="inputDepartureTime" value="<?php echo $flightplanSuggestion['departureTime'];?>" onKeyUp="calculateNewArrivalTime();"/></td>
                            <td>Arrival time</td>
                            <td><input type="text" size="4" id="inputArrivalTime" value="<?php echo $flightplanSuggestion['arrivalTime'];?>" disabled="disabled"/></td>
                        </tr>
                        <tr>
                            <td>Airspeed</td>
                            <td><input type="text" size="3" id="inputSpeed" value="<?php echo $flightplanSuggestion['speed'];?>" onKeyUp="calculateNewArrivalTime();"/> kts</td>
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
