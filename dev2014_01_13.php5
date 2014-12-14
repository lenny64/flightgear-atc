<?php

/*
 * C O N F I G U R A T I O N
 */

// In need of error reporting
//error_reporting(E_ALL); ini_set("display_errors", 1);

// Time UTC
date_default_timezone_set('UTC');

// Configuration file
require_once './include/config.php5';

// Let's open the DB
$db = new PDO("mysql:host=".SQL_SERVER.";dbname=".SQL_DB, SQL_LOGIN, SQL_PWD);

// Version definition
define("DEV_VERSION","20141213");
// Error codes definitions
define("WRONG_IDENT", 'The ident you are using is not correct');
define("ERR_VAR1", 'A variable is missing or is NULL');
define("ERR_VAR2", 'Some variables are missing or are NULL');
define("INVALID_FLIGHTPLAN", 'This flight plan does not exist');

// A little tracker
$db->query("INSERT INTO queries VALUES('','".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."','".$_SERVER['REQUEST_URI']."');");

include('./include/classes.php5');
include('./include/functions.php5');

/*
 * F U N C T I O N  D E F I N I T I O N S
 */

function generateError($errno,$message)
{
    $XMLerror = new SimpleXMLElement("<error></error>");
    
    $XMLerror->addAttribute('version', DEV_VERSION);
    
    $XMLerror->addChild("code",$errno);
    $XMLerror->addChild("message",$message);
    
    header('Content-type: application/xml');
    echo $XMLerror->asXML();
}

/*
 * A P I 
 */

// IS AIRPORT CONTROLLED ?
if (isset($_GET['isAirportControlled']) AND isset($_GET['date']) AND isset($_GET['time']))
{
    if ($_GET['isAirportControlled'] != NULL AND $_GET['date'] != NULL AND $_GET['time'] != NULL)
    {
        $ICAO = $_GET['isAirportControlled'];
        $date = $_GET['date'];
        $time = $_GET['time'];
        
        if (isAirportControlled($ICAO, $date, $time) == true) $controlled = 1;
        else if (isAirportControlled($ICAO, $date, $time) == false) $controlled = 0;
        
        $XMLairport = new SimpleXMLElement("<airport></airport>");
        $XMLairport->addAttribute('version',DEV_VERSION);
        
        $XMLairport->addChild('icao',$ICAO);
        $XMLairport->addChild('date',$date);
        $XMLairport->addChild('time',$time);
        $XMLairport->addChild('isControlled',$controlled);
        header('Content-type: application/xml');
        echo $XMLairport->asXML();
    }
    else
    {
        generateError('ERR_VAR2',ERR_VAR2);
    }
}
else if (isset($_GET['isAirportControlled']))
{
    generateError('ERR_VAR','isAirportControlled requires date and time');
}

// GET FLIGHTPLANS
else if (isset($_GET['getFlightplans']))
{
    // Callsign
    if (isset($_GET['callsign']) AND $_GET['callsign'] != NULL)
    {
        $callsign = $_GET['callsign'];
        $queryCallsign = "FP.callsign = '$callsign'";
    }
    else
    {
        $queryCallsign = "FP.callsign LIKE '%'";
    }
    // Date
    if (isset($_GET['date']) AND $_GET['date'] != NULL)
    {
        $date = $_GET['date'];
        $queryDate = "FP.dateDeparture = '$date'";
    }
    else
    {
        $queryDate = "FP.dateDeparture LIKE '%'";
    }
    // Airport
    if (isset($_GET['airport']) AND $_GET['airport'] != NULL)
    {
        $ICAO = $_GET['airport'];
        $queryICAO = "(FP.airportICAOFrom = '$ICAO' OR FP.airportICAOTo = '$ICAO')";
    }
    else
    {
        $queryICAO = "(FP.airportICAOFrom LIKE '%' OR FP.airportICAOTo LIKE '%')";
    }
    // Status
    if (isset($_GET['status']) AND $_GET['status'] != NULL)
    {
        $queryStatus = "HAVING FPStatus.status = '".$_GET['status']."'";
    }
    else
    {
        $queryStatus = "";
    }
    
    $query = "SELECT FP.flightplanId,FPStatus.*
                FROM (
                    SELECT * FROM flightplan_status
                    ORDER BY flightplan_status.dateTime DESC) AS FPStatus
                        JOIN (
                        SELECT * FROM flightplans20140113) AS FP
                            ON FP.flightplanId = FPStatus.flightplanId
                WHERE $queryCallsign AND $queryDate AND $queryICAO
                GROUP BY FP.flightplanId
                $queryStatus;";
    
    $queryPrepare = $db->prepare($query);
    $queryPrepare->execute();
    $flightplans = $queryPrepare->fetchAll();
    
    flightplansToXML($flightplans);
    
}

// GET FLIGHTPLAN DETAILS
else if (isset($_GET['getFlightplanDetails']) AND isset($_GET['flightplanId']))
{
    if ($_GET['flightplanId'] != NULL)
    {
        $id = $_GET['flightplanId'];
        // I just select the FP
        $Flightplan = new Flightplan();
        $Flightplan->selectById($id);
        
        // If the flight plan is correct
        // This means has a date and callsign -> should be managed by classes.php in the future !
        if ($Flightplan->dateDeparture != NULL AND $Flightplan->callsign != NULL)
        {
            // And output it as XML
            flightplanToXML($Flightplan);
        }
        // If the flight plan is not correct
        else
        {
            generateError('INVALID_FLIGHTPLAN',INVALID_FLIGHTPLAN);
        }
    }
    // If the flightplanId has not been entered
    else
    {
        generateError('ERR_VAR1',ERR_VAR1);
    }
}
else if (isset($_GET['getFlightplanDetails']))
{
    generateError('ERR_VAR','getFlightplanDetails requires flightplanId');
}

// GET ATC SESSIONS
else if (isset($_GET['getATCSessions']) AND isset($_GET['limitDate']))
{
    if ($_GET['limitDate'] != NULL)
    {
        // We get the date
        $date = $_GET['limitDate'];
        $today = date('Y-m-d');
        
        $events = $db->query("SELECT * FROM events WHERE date >= '$today' AND date <= '$date'");
        
        $XMLEvents = new SimpleXMLElement("<events></events>");
        $XMLEvents->addAttribute('version',DEV_VERSION);
        
        foreach ($events as $event)
        {
            $Event = new Event();
            $Event->selectById($event['eventId']);
            
            $XMLEvent = $XMLEvents->addChild('event');
            //$XMLEvent->addChild('eventId',$Event->id);
            $XMLEvent->eventId = $Event->id;
            //$XMLEvent->addChild('airportICAO',$Event->airportICAO);
            $XMLEvent->airportICAO = $Event->airportICAO;
            //$XMLEvent->addChild('date',$Event->date);
            $XMLEvent->date = $Event->date;
            //$XMLEvent->addChild('beginTime',$Event->beginTime);
            $XMLEvent->beginTime = $Event->beginTime;
            //$XMLEvent->addChild('endTime',$Event->endTime);
            $XMLEvent->endTime = $Event->endTime;
            //$XMLEvent->addChild('fgcom',$Event->fgcom);
            $XMLEvent->fgcom = $Event->fgcom;
            //$XMLEvent->addChild('teamspeak',$Event->teamspeak);
            $XMLEvent->teamspeak = $Event->teamspeak;
            //$XMLEvent->addChild('transitionLevel',$Event->transitionLevel);
            $XMLEvent->transitionLevel = $Event->transitionLevel;
            //$XMLEvent->addChild('runways',$Event->runways);
            $XMLEvent->runways = $Event->runways;
            //$XMLEvent->addChild('ILS',$Event->ils);
            $XMLEvent->ILS = $Event->ils;
            //$XMLEvent->addChild('docsLink',  htmlspecialchars($Event->docsLink));
            $XMLEvent->docsLink = htmlspecialchars($Event->docsLink);
            //$XMLEvent->addChild('remarks',$Event->remarks);
            $XMLEvent->remarks = $Event->remarks;
        }
        
        header('Content-type: application/xml');
        echo $XMLEvents->asXML();
    }
    else
    {
        generateError('ERR_VAR1',ERR_VAR1);
    }
}
else if (isset($_GET['getATCSessions']))
{
    generateError('ERR_VAR','getATCSessions requires limitDate');
}

// REQUESTING AUTHORIZATION TO FILE FLIGHTPLANS
else if (isset($_GET['request_auth']) AND isset($_POST['mail']))
{
    // We gather the mail
    $mail = $_POST['mail'];
    
    // We check if there is an information
    $userInfo = "";
    if (!empty($_POST['userInfo']))
    {
        foreach ($_POST['userInfo'] as $info)
        {
            $userInfo .= $info;
        }
    }
    
    $md5_mail = substr(md5($mail.$userInfo),0,8);
    
    $statement = $db->prepare("INSERT INTO request_users VALUES('',:mail,:md5_mail,:userInfo);");
    $statement->execute(array(":mail"=>$mail,":md5_mail"=>$md5_mail,":userInfo"=>$userInfo));
    
    echo '<h3>Congratulations, your mail is <b>'.$mail.'</b>, your <b>IDENT</b> : '.$md5_mail.'</h3>
        <br/>
        <b>VERY IMPORTANT</b> You can already use this IDENT to file flightplans with the following ident :
        <h2>'.$md5_mail.'</h2>
        <br/><br/>
        The syntax is :
        <br/>
        http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?fileFlightplan&ident=<b>'.$md5_mail.'</b>&callsign=&date=&departureTime=&departureAirport=&arrivalTime=&arrivalAirport=&cruiseAltitude=&aircraft=&category=&waypoints=
        <br/><br/>
        <a href="./dev2014_01_13.php5">Go back to the dev page</a>';
    mail('lenny64@free.fr','Your ident to file flightplans','Hello, you can now file flightplans using this URL : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?fileFlightplan&ident='.$md5_mail.'&callsign=&date=&departureTime=&departureAirport=&arrivalTime=&arrivalAirport=&cruiseAltitude=&aircraft=&category=&waypoints=');
    mail($mail,'Your ident to file flightplans','Hello, you can now file flightplans using this URL : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?fileFlightplan&ident='.$md5_mail.'&callsign=&date=&departureTime=&departureAirport=&arrivalTime=&arrivalAirport=&cruiseAltitude=&aircraft=&category=&waypoints=');
    
}
else if (isset($_GET['request_auth']))
{
    generateError('ERR_VAR','request_auth requires mail');
}

// NEW LIVE ATC SESSION FROM OPENRADAR
else if (isset($_GET['newAtcSession']) AND isset($_GET['ident']) AND isset($_GET['date']) AND isset($_GET['time']) AND isset($_GET['airportICAO']))
{
    if ($_GET['ident'] != NULL AND $_GET['date'] != NULL AND $_GET['time'] != NULL AND $_GET['airportICAO'] != NULL)
    {
        $inject_ident = $_GET['ident'];
        $inject_date = $_GET['date'];
            $inject_year = date("Y",strtotime($inject_date));
            $inject_month = date("m",strtotime($inject_date));
            $inject_day = date("d",strtotime($inject_date));
        $inject_time = $_GET['time'];
            $inject_hour = date("H",strtotime($inject_time));
            $inject_minute = date("i",strtotime($inject_time));
        $inject_airportICAO = $_GET['airportICAO'];

        // We check if ident is fine
        if (checkIdent($inject_ident) != true)
        {
            $Event = new Event();
            $Event->create($inject_year,$inject_month,$inject_day,$inject_hour,$inject_minute,'23','59',$inject_airportICAO,'','','','openradar');

            $XMLEvents = new SimpleXMLElement("<events></events>");
            $XMLEvents->addAttribute('version',DEV_VERSION);

            $XMLEvent = $XMLEvents->addChild('event');
            $XMLEvent->addChild('eventId',$Event->id);
            $XMLEvent->addChild('airportICAO',$Event->airportICAO);
            $XMLEvent->addChild('date',$Event->date);
            $XMLEvent->addChild('beginTime',$Event->beginTime);
            $XMLEvent->addChild('endTime',$Event->endTime);
            $XMLEvent->addChild('fgcom',$Event->fgcom);
            $XMLEvent->addChild('teamspeak',$Event->teamspeak);
            $XMLEvent->addChild('transitionLevel',$Event->transitionLevel);
            $XMLEvent->addChild('runways',$Event->runways);
            $XMLEvent->addChild('ILS',$Event->ils);
            $XMLEvent->addChild('docsLink',  htmlspecialchars($Event->docsLink));
            $XMLEvent->addChild('remarks',$Event->remarks);

            header('Content-type: application/xml');
            echo $XMLEvents->asXML();
        }
        // If the ident is not fine we generate an error
        else
        {
            generateError('WRONG_IDENT',WRONG_IDENT);
        }

    }
    else
    {
        generateError('ERR_VAR2',ERR_VAR2);
    }
}
else if (isset($_GET['newAtcSession']))
{
    generateError('ERR_VAR','newAtcSession requires ident, date, time, and airportICAO');
}

// FILE A FLIGHTPLAN
else if (isset($_GET['fileFlightplan']) AND isset($_GET['ident']) AND isset($_GET['callsign']) AND isset($_GET['dateDeparture']) AND isset($_GET['departureAirport']) AND isset($_GET['departureTime']) AND isset($_GET['arrivalAirport']) AND isset($_GET['arrivalTime']))
{
    if ($_GET['ident'] != NULL AND $_GET['callsign'] != NULL AND $_GET['dateDeparture'] != NULL AND $_GET['departureAirport'] != NULL AND $_GET['departureTime'] != NULL AND $_GET['arrivalAirport'] != NULL AND $_GET['arrivalTime'] != NULL)
    {
        $inject_ident = $_GET['ident'];
        $inject_callsign = $_GET['callsign'];
        $inject_airline = $_GET['airline'];
        $inject_flightNumber = $_GET['flightNumber'];
        $inject_departureAirport = $_GET['departureAirport'];
        $inject_arrivalAirport = $_GET['arrivalAirport'];
        $inject_alternateDestination = $_GET['alternateDestination'];
        $inject_cruiseAltitude = $_GET['cruiseAltitude'];
        $inject_trueAirspeed = $_GET['trueAirspeed'];
        $inject_dateDeparture = $_GET['dateDeparture'];
        $inject_departureTime = $_GET['departureTime'];
        $inject_arrivalTime = $_GET['arrivalTime'];
        $inject_aircraftType = $_GET['aircraft'];
        $inject_soulsOnBoard = $_GET['soulsOnBoard'];
        $inject_fuelTime = $_GET['fuelTime'];
        $inject_pilotName = $_GET['pilotName'];
        $inject_waypoints = $_GET['waypoints'];
        $inject_category = $_GET['category'];
        $inject_comments = $_GET['comments'];
        
        // If the ident is fine
        if (checkIdent($inject_ident) != true)
        {
            $injectFlightplan = new Flightplan();
            $injectFlightplan->create($inject_dateDeparture, $inject_departureAirport, $inject_arrivalAirport, $inject_alternateDestination, $inject_cruiseAltitude, $inject_trueAirspeed, $inject_callsign, $inject_pilotName, $inject_airline, $inject_flightNumber, $inject_category, $inject_aircraftType, $inject_departureTime, $inject_arrivalTime, $inject_waypoints, $inject_soulsOnBoard, $inject_fuelTime, $inject_comments);
            
            flightplanToXML($injectFlightplan);
        }
        // If the ident is not fine we generate an error
        else
        {
            generateError('WRONG_IDENT',WRONG_IDENT);
        }
    }
    else
    {
        generateError('ERR_VAR2',ERR_VAR2);
    }
}
else if (isset($_GET['fileFlightplan']))
{
    generateError('ERR_VAR','newAtcSession requires ident, callsign, dateDeparture, departureAirport, departureTime, arrivalAirport and arrivalTime');
}

// EDIT A FLIGHTPLAN
else if (isset($_GET['editFlightplan']) AND isset($_GET['ident']) AND isset($_GET['flightplanId']))
{
    if ($_GET['ident'] != NULL AND $_GET['flightplanId'] != NULL)
    {
        $inject_ident = $_GET['ident'];
        $flightplanId = $_GET['flightplanId'];
        
        // If the ident is fine
        if (checkIdent($inject_ident) != true)
        {
            $Flightplan = new Flightplan();
            $Flightplan->selectById($flightplanId);
            
            // If the flight plan is correct
            // This means has a date and callsign -> should be managed by classes.php in the future !
            if ($Flightplan->dateDeparture != NULL AND $Flightplan->callsign != NULL)
            {
                // We list every GET variable given
                foreach ($_GET as $k => $v)
                {
                    // Some little exceptions (differences between variables appearing in FP and in DB)
                    if ($k == "airportFrom") { $k = "departureAirport"; }
                    if ($k == "airportTo") { $k = "arrivalAirport"; }
                    if ($k == "aircraft") { $k = "aircraftType"; }
                    // We check if the FP contains this variable and has a correct value
                    if (isset($Flightplan->$k) AND isset($v) AND $v != NULL)
                    {
                        $Flightplan->$k = $v;
                    }
                    // If the FP does not contain this variable
                    else if (!isset($Flightplan->$k))
                    {
                        // We have exceptions : variables inherent to the command
                        if ($k != 'editFlightplan' AND $k != 'ident' AND $k != 'flightplanId')
                        {
                            $fpUnknownVar[] = $k;
                        }
                    }
                    // If the FP contains this variable but the value is not set
                    else
                    {
                        $fpWrongValue[] = $k;
                    }
                }
                
                // If there was unknown vars we generate an error
                if (isset($fpUnknownVar) AND $fpUnknownVar != NULL)
                {
                    $errors = implode(' ',$fpUnknownVar);
                    generateError('WRONG_VAR', 'You tried to edit following values that does not exist : '.$errors);
                }
                
                // If a value was null
                else if (isset($fpWrongValue) AND $fpWrongValue != NULL)
                {
                    $errors = implode(' ',$fpWrongValue);
                    generateError('WRONG_VAL', 'Those variables should not be null : '.$errors);
                }
                
                // No errors ? We continue : we edit flightplan and show it
                else
                {
                    $Flightplan->editFlightplan();
                    // And output it as XML
                    flightplanToXML($Flightplan);
                }
            }
            // In case the FP is not correct
            else
            {
                generateError('INVALID_FLIGHTPLAN', INVALID_FLIGHTPLAN);
            }
        }
        // If the ident is not fine we generate an error
        else
        {
            generateError('WRONG_IDENT',WRONG_IDENT);
        }
    }
    // Given variables are NULL ?
    else
    {
        generateError('ERR_VAR2',ERR_VAR2);
    }
}
else if (isset($_GET['editFlightplan']))
{
    generateError('ERR_VAR','editFlightplan requires ident and flightplanId');
}

// MODIFY THE STATUS OF A FLIGHTPLAN
else if ((isset($_GET['openFlightplan']) OR isset($_GET['closeFlightplan'])) AND isset($_GET['ident']) AND isset($_GET['flightplanId']))
{
    if ($_GET['ident'] != NULL AND $_GET['flightplanId'] != NULL)
    {
        $inject_ident = $_GET['ident'];
        $inject_flightplanId = $_GET['flightplanId'];
        
        // We check if the user wants to open or close flightplan
        if (isset($_GET['openFlightplan'])) $operation = 'open';
        else if (isset($_GET['closeFlightplan'])) $operation = 'close';
        // If the ident is fine
        if (checkIdent($inject_ident) != true)
        {
            $FlightplanToOpen = new Flightplan();
            $FlightplanToOpen->selectById($_GET['flightplanId']);
            
            // If the flight plan is correct
            // This means has a date and callsign -> should be managed by classes.php in the future !
            if ($FlightplanToOpen->dateDeparture != NULL AND $FlightplanToOpen->callsign != NULL)
            {
                $FlightplanToOpen->changeFlightplanStatus($inject_ident, $inject_flightplanId, $operation);

                flightplanToXML($FlightplanToOpen);
            }
            // In case the FP is not correct
            else
            {
                generateError('INVALID_FLIGHTPLAN', INVALID_FLIGHTPLAN);
            }
        }
        // If the ident is not fine we generate an error
        else
        {
            generateError('WRONG_IDENT',WRONG_IDENT);
        }
    }
    else
    {
        generateError('ERR_VAR2',ERR_VAR2);
    }
}
else if (isset($_GET['openFlightplan']) OR isset($_GET['closeFlightplan']))
{
    generateError('ERR_VAR','openFlightplan or closeFlightplan requires ident and flightplanId');
}

// MODIFY THE VARIABLE/VALUE OF A FLIGHTPLAN
else if (isset($_GET['setVar']) AND isset($_GET['ident']) AND isset($_GET['flightplanId']) AND isset($_GET['variable']) AND isset($_GET['value']))
{
    if ($_GET['ident'] != NULL AND $_GET['flightplanId'] != NULL AND $_GET['variable'] != NULL AND $_GET['value'] != NULL)
    {
        $inject_ident = $_GET['ident'];
        $inject_flightplanId = $_GET['flightplanId'];
        $variable = urldecode($_GET['variable']);
        $value = urldecode($_GET['value']);
        
        // We check if the ident is fine
        if (checkIdent($inject_ident) != true)
        {
            $FlightplanToOpen = new Flightplan();
            $FlightplanToOpen->selectById($_GET['flightplanId']);
            
            // If the flight plan is correct
            // This means has a date and callsign -> should be managed by classes.php in the future !
            if ($FlightplanToOpen->dateDeparture != NULL AND $FlightplanToOpen->callsign != NULL)
            {
                $FlightplanToOpen->changeFlightplanInfo($inject_ident, $inject_flightplanId, $variable, $value);

                flightplanToXML($FlightplanToOpen);
            }
            // In case the FP is not correct
            else
            {
                generateError('INVALID_FLIGHTPLAN', INVALID_FLIGHTPLAN);
            }
        }
        // If the ident is not fine we generate an error
        else
        {
            generateError('WRONG_IDENT',WRONG_IDENT);
        }
    }
    else
    {
        generateError('ERR_VAR2',ERR_VAR2);
    }
}
else if (isset($_GET['setVar']))
{
    generateError('ERR_VAR','setVar requires ident, flightplanId, variable and value');
}


// IF THERE IS AN UNKNOWN COMMAND
else if (isset($_GET) AND $_GET != NULL)
{
    $arguments = array();
    foreach ($_GET as $key => $value)
    {
        if ($key != NULL)
        {
            $arguments[] = $key;
        }
    }
    generateError('WRONG_COMMAND', 'The command '.$arguments[0].' is not valid');
}

/*
 * A P I  H O M E  P A G E
 */

else
{ ?>
    
    <form action="./dev2014_01_13.php5" method="get">
        <h5>Is airport controlled ?</h5>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?isAirportControlled=<input type="text" name="isAirportControlled" value="LFML" size="4"/>&date=<input type="text" name="date" value="<?php echo date("Y-m-d");?>" size="7"/>&time=<input type="text" name="time" value="20:00:00" size="5"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>
            
    <form action="./dev2014_01_13.php5" method="get">
        <h5>Get flightplans at a given date</h5>
        <input type="hidden" name="getFlightplans"/>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?getFlightplans&airport=<input type="text" name="airport" value="EDDF" size="4"/>&date=<input type="text" name="date" value="<?php echo date("Y-m-d");?>" size="7"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>
    
    <form action="./dev2014_01_13.php5" method="get">
        <h5>Get flightplans of a given callsign</h5>
        <input type="hidden" name="getFlightplans"/>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?getFlightplans&callsign=<input type="text" name="callsign" value="F-LENNY" size="8"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>
    
    <form action="./dev2014_01_13.php5" method="get">
        <h5>Get flightplans of a given callsign at a given airport and date</h5>
        <input type="hidden" name="getFlightplans"/>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?getFlightplans&callsign=<input type="text" name="callsign" value="F-LENNY" size="8"/>&airport=<input type="text" name="airport" value="EDDF" size="4"/>&date=<input type="text" name="date" value="<?php echo date("Y-m-d");?>" size="7"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>

    <form action="./dev2014_01_13.php5" method="get">
        <h5>Get details about a particular flightplan</h5>
        <input type="hidden" name="getFlightplanDetails"/>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?getFlightplanDetails&flightplanId=<input type="text" name="flightplanId" value="" size="3"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>

    <form action="./dev2014_01_13.php5" method="get">
        <h5>Edit a flightplan</h5>
        <input type="hidden" name="editFlightplan"/>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?editFlightplan&ident=<input type="text" name="ident" value="MY IDENT" size="8" disabled/>&flightplanId=<input type="text" name="flightplanId" value="" size="3"/>
        <br/>&callsign=<input type="text" name="callsign" value="" size="8" disabled/>
        <br/>&airline=<input type="text" name="airline" value="" size="8" disabled/>
        <br/>&flightNumber=<input type="text" name="flightNumber" value="" size="6" disabled/>
        <br/>&departureAirport=<input type="text" name="departureAirport" disabled/>
        <br/>&arrivalAirport=<input type="text" name="arrivalAirport" disabled/>
        <br/>&alternateDestination=<input type="text" name="alternateDestination" disabled/>
        <br/>&cruiseAltitude=<input type="text" name="cruiseAltitude" disabled/>
        <br/>&trueAirspeed=<input type="text" name="trueAirspeed" disabled/>
        <br/>&dateDeparture=<input type="text" name="dateDeparture" disabled/>
        <br/>&departureTime=<input type="text" name="departureTime" disabled/>
        <br/>&arrivalTime=<input type="text" name="arrivalTime" disabled/>
        <br/>&aircraft=<input type="text" name="aicraft" disabled/>
        <br/>&soulsOnBoard=<input type="text" name="soulsOnBoard" size="3" disabled/>
        <br/>&fuelTime=<input type="text" name="fuelTime" size="3" disabled/>
        <br/>&pilotName=<input type="text" name="pilotName" size="8" disabled/>
        <br/>&waypoints=<input type="text" name="waypoints" size="30" disabled/>
        <br/>&category=<input type="text" name="category" size="3" disabled/>
        <br/>&comments=<input type="text" name="comments" size="30" disabled/>
        <br/>
        <input type="submit" value="OK"/>
    </form>

    <form action="./dev2014_01_13.php5" method="get">
        <h5>Get ATC sessions until a given date</h5>
        <input type="hidden" name="getATCSessions"/>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?getATCSessions&limitDate=<input type="text" name="limitDate" value="<?php echo date("Y-m-d");?>" size="7"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>
    
    <form action="./dev2014_01_13.php5" method="get">
        <h5>Change a flightplan's custom variable</h5>
        <input type="hidden" name="setVar"/>
        Syntax : http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?setVar&ident=<input type="text" name="ident" value="MY IDENT" size="8" disabled/>&flightplanId=<input type="text" name="flightplanId" size="3"/>&variable=<input type="text" name="variable" size="5"/>&value=<input type="text" name="value" size="3"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>

<form action="./dev2014_01_13.php5?request_auth" method="post" style="border: solid 2px #ccc; padding: 10px;">
    <h4>Do you want to file flightplans automatically ?</h4>
    I want an ident :
    <br/>
    Mail : <input type="text" name="mail" size="35"/>
    <br/>
    You are : 
    <br/>
    <input type="checkbox" name="userInfo[]" value="developer">A developer
    <br/>
    <input type="checkbox" name="userInfo[]" value="pilot">A pilot
    <br/>
    <input type="checkbox" name="userInfo[]" value="virtualAirline">A virtual airline owner
    <br/>
    <input type="checkbox" name="userInfo[]" value="atc">An Air Traffic Controller
    <br/>
    <br/>
    <input type="submit" value="Create my ident"/>
    <br/>
    <br/>
    <h4>The syntax is :</h4>
    http://flightgear-atc.alwaysdata.net/dev2014_01_13.php5?fileFlightplan
    <br/>&ident=<input type="text" name="ident" value="MY IDENT" disabled/>
    <br/>&callsign=<input type="text" name="callsign" value="" size="8" disabled/>
    <br/>&airline=<input type="text" name="airline" value="" size="8" disabled/>
    <br/>&flightNumber=<input type="text" name="flightNumber" value="" size="6" disabled/>
    <br/>&departureAirport=<input type="text" name="departureAirport" disabled/>
    <br/>&arrivalAirport=<input type="text" name="arrivalAirport" disabled/>
    <br/>&alternateDestination=<input type="text" name="alternateDestination" disabled/>
    <br/>&cruiseAltitude=<input type="text" name="cruiseAltitude" disabled/>
    <br/>&trueAirspeed=<input type="text" name="trueAirspeed" disabled/>
    <br/>&dateDeparture=<input type="text" name="dateDeparture" disabled/>
    <br/>&departureTime=<input type="text" name="departureTime" disabled/>
    <br/>&arrivalTime=<input type="text" name="arrivalTime" disabled/>
    <br/>&aircraft=<input type="text" name="aicraft" disabled/>
    <br/>&soulsOnBoard=<input type="text" name="soulsOnBoard" size="3" disabled/>
    <br/>&fuelTime=<input type="text" name="fuelTime" size="3" disabled/>
    <br/>&pilotName=<input type="text" name="pilotName" size="8" disabled/>
    <br/>&waypoints=<input type="text" name="waypoints" size="30" disabled/>
    <br/>&category=<input type="text" name="category" size="3" disabled/>
    <br/>&comments=<input type="text" name="comments" size="30" disabled/>    
</form>
<?php }
// We close the session
$db = null;
?>