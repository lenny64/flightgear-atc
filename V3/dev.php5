<?php

// Configuration file
require_once './include/config.php5';

// Let's open the DB
mysql_connect(SQL_SERVER, 'lenny64', 'ree234rp');
mysql_select_db('lenny64');

// A little tracker
mysql_query("INSERT INTO queries VALUES('','".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."','".$_SERVER['REQUEST_URI']."');");

include('./include/classes.php5');
include('./include/functions.php5');

// IS AIRPORT CONTROLLED ?
if (isset($_GET['isAirportControlled']) AND isset($_GET['date']) AND isset($_GET['time']))
{
    if ($_GET['isAirportControlled'] != NULL AND $_GET['date'] != NULL AND $_GET['time'] != NULL)
    {
        
        $ICAO = mysql_real_escape_string(htmlspecialchars($_GET['isAirportControlled']));
        $date = mysql_real_escape_string(htmlspecialchars($_GET['date']));
        $time = mysql_real_escape_string(htmlspecialchars($_GET['time']));
        
        if (isAirportControlled($ICAO, $date, $time) == true) $controlled = 1;
        else if (isAirportControlled($ICAO, $date, $time) == false) $controlled = 0;
        
        $XMLairport = new SimpleXMLElement("<airport></airport>");
        $XMLairport->addChild('icao',$ICAO);
        $XMLairport->addChild('date',$date);
        $XMLairport->addChild('time',$time);
        $XMLairport->addChild('isControlled',$controlled);
        header('Content-type: application/xml');
        echo $XMLairport->asXML();
        
    }
}

// GET FLIGHTPLANS
else if (isset($_GET['getFlightplans']) AND isset($_GET['airport']) AND isset($_GET['date']))
{
    if ($_GET['airport'] != NULL AND $_GET['date'] != NULL)
    {
        
        $ICAO = mysql_real_escape_string(htmlspecialchars($_GET['airport']));
        $date = mysql_real_escape_string(htmlspecialchars($_GET['date']));
        
        $flightplans = mysql_query("SELECT * FROM flightplans20140113 WHERE (airportICAOFrom = '$ICAO' OR airportICAOTo = '$ICAO') AND dateDeparture = '$date'") or die(mysql_error());
        
        $XMLflightplans = new SimpleXMLElement("<flightplans></flightplans>");
        
        while ($flightplan = mysql_fetch_array($flightplans))
        {
            
            $Flightplan = new Flightplan();
            $Flightplan->selectById($flightplan['flightplanId']);
            
            $XMLflightplan = $XMLflightplans->addChild('flightplan');
            $XMLflightplan->addAttribute('id',$Flightplan->id);
            
            $XMLflightplan->addChild('callsign',$Flightplan->callsign);
            $XMLflightplan->addChild('airportFrom',$Flightplan->departureAirport);
            $XMLflightplan->addChild('airportTo',$Flightplan->arrivalAirport);
            $XMLflightplan->addChild('departureTime',$Flightplan->departureTime);
            $XMLflightplan->addChild('arrivalTime',$Flightplan->arrivalTime);
            $XMLflightplan->addChild('cruiseAltitude',$Flightplan->cruiseAltitude);
            $XMLflightplan->addChild('date',$Flightplan->dateDeparture);
            $XMLflightplan->addChild('aircraft',$Flightplan->aircraftType);
            $XMLflightplan->addChild('waypoints',$Flightplan->waypoints);
            $XMLflightplan->addChild('category',$Flightplan->category);
            
        }
        
        header('Content-type: application/xml');
        echo $XMLflightplans->asXML();
    }
}

// GET ATC SESSIONS
else if (isset($_GET['getATCSessions']) AND isset($_GET['limitDate']))
{
    if ($_GET['limitDate'] != NULL)
    {
        // We get the date
        $date = mysql_real_escape_string(htmlspecialchars($_GET['limitDate']));
        $today = date('Y-m-d');
        
        $events = mysql_query("SELECT * FROM events WHERE date >= '$today' AND date <= '$date'");
        
        $XMLEvents = new SimpleXMLElement("<events></events>");
        
        while ($event = mysql_fetch_array($events))
        {
            $Event = new Event();
            $Event->selectById($event['eventId']);
            
            $XMLEvent = $XMLEvents->addChild('event');
            $XMLEvent->addAttribute('id',$Event->id);
            
            $XMLEvent->addChild('ICAO',$Event->airportICAO);
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
        }
        
        header('Content-type: application/xml');
        echo $XMLEvents->asXML();
    }
}

// REQUESTING AUTHORIZATION TO FILE FLIGHTPLANS
else if (isset($_GET['request_auth']) AND isset($_POST['mail']))
{
    // We gather the mail
    $mail = mysql_real_escape_string(htmlspecialchars($_POST['mail']));
    
    // We check if there is an information
    $userInfo = "";
    if (!empty($_POST['userInfo']))
    {
        foreach ($_POST['userInfo'] as $info)
        {
            $info_ = mysql_real_escape_string(htmlspecialchars($info));
            $userInfo .= $info_.",";
        }
    }
    
    $md5_mail = substr(md5($mail.$userInfo),0,8);
    
    mysql_query("INSERT INTO request_users VALUES('','$mail','$md5_mail','$userInfo');");
    
    echo '<h3>Congratulations, your mail is <b>'.$mail.'</b>, your <b>IDENT</b> : '.$md5_mail.'</h3>
        <br/>
        <b>VERY IMPORTANT</b> You can already use this IDENT to file flightplans with the following ident :
        <h2>'.$md5_mail.'</h2>
        <br/><br/>
        The syntax is :
        <br/>
        http://lenny64.free.fr/dev.php5?fileFlightplan&ident=<b>'.$md5_mail.'</b>&callsign=&date=&departureTime=&departureAirport=&arrivalTime=&arrivalAirport=&cruiseAltitude=&aircraft=&category=&waypoints=
        <br/><br/>
        <a href="./dev.php5">Go back to the dev page</a>';
    mail('lenny64@free.fr','Your ident to file flightplans','Hello, you can now file flightplans using this URL : http://lenny64.free.fr/dev.php5?fileFlightplan&ident='.$md5_mail.'&callsign=&date=&departureTime=&departureAirport=&arrivalTime=&arrivalAirport=&cruiseAltitude=&aircraft=&category=&waypoints=');
    mail($mail,'Your ident to file flightplans','Hello, you can now file flightplans using this URL : http://lenny64.free.fr/dev.php5?fileFlightplan&ident='.$md5_mail.'&callsign=&date=&departureTime=&departureAirport=&arrivalTime=&arrivalAirport=&cruiseAltitude=&aircraft=&category=&waypoints=');
    
}

else if (isset($_GET['fileFlightplan']) AND isset($_GET['ident']) AND isset($_GET['callsign']) AND isset($_GET['date']) AND isset($_GET['departureAirport']) AND isset($_GET['departureTime']) AND isset($_GET['arrivalAirport']) AND isset($_GET['arrivalTime']))
{
    if ($_GET['ident'] != NULL AND $_GET['callsign'] != NULL AND $_GET['date'] != NULL AND $_GET['departureAirport'] != NULL AND $_GET['departureTime'] != NULL AND $_GET['arrivalAirport'] != NULL AND $_GET['arrivalTime'] != NULL)
    {
        $inject_ident = mysql_real_escape_string(htmlspecialchars($_GET['ident']));
        $inject_callsign = mysql_real_escape_string(htmlspecialchars($_GET['callsign']));
        $inject_date = mysql_real_escape_string(htmlspecialchars($_GET['date']));
        $inject_departureAirport = mysql_real_escape_string(htmlspecialchars($_GET['departureAirport']));
        $inject_departureTime = mysql_real_escape_string(htmlspecialchars($_GET['departureTime']));
        $inject_arrivalAirport = mysql_real_escape_string(htmlspecialchars($_GET['arrivalAirport']));
        $inject_arrivalTime = mysql_real_escape_string(htmlspecialchars($_GET['arrivalTime']));
        $inject_cruiseAltitude = mysql_real_escape_string(htmlspecialchars($_GET['cruiseAltitude']));
        $inject_aircraftType = mysql_real_escape_string(htmlspecialchars($_GET['aircraft']));
        $inject_category = mysql_real_escape_string(htmlspecialchars($_GET['category']));
        $inject_waypoints = mysql_real_escape_string(htmlspecialchars($_GET['waypoints']));
        
        // We check if it is a registered user
        $ident_infos_list = mysql_query("SELECT * FROM request_users WHERE ident = '$inject_ident' LIMIT 1");
        $ident_infos = mysql_fetch_assoc($ident_infos_list);
        $wrong_ident = false;
        if ($ident_infos['ident'] != $inject_ident AND $ident_infos['ident'] == 0) $wrong_ident = true;
        
        if ($wrong_ident != true)
        {
            $injectFlightplan = new Flightplan();
            $injectFlightplan->create_old($inject_date, $inject_departureAirport, $inject_arrivalAirport, $inject_cruiseAltitude, $inject_callsign, $inject_category, $inject_aircraftType, $inject_departureTime, $inject_arrivalTime, $inject_waypoints, '');
            echo "Flightplan ID number ".$injectFlightplan->id." filed";
            echo "<br/><br/><a href='./edit_flightplan.php5?idFlightplan=$injectFlightplan->id'>Click here to access and comment that flightplan</a>";
        }
        
    }
}

else if ((isset($_GET['openFlightplan']) OR isset($_GET['closeFlightplan'])) AND isset($_GET['ident']) AND isset($_GET['flightplanId']))
{
    if ($_GET['ident'] != NULL AND $_GET['flightplanId'] != NULL)
    {
        $inject_ident = mysql_real_escape_string(htmlspecialchars($_GET['ident']));
        $inject_flightplanId = mysql_real_escape_string(htmlspecialchars($_GET['flightplanId']));
        
        // We check if it is a registered user
        $ident_infos_list = mysql_query("SELECT * FROM request_users WHERE ident = '$inject_ident' LIMIT 1");
        $ident_infos = mysql_fetch_assoc($ident_infos_list);
        $wrong_ident = false;
        if ($ident_infos['ident'] != $inject_ident AND $ident_infos['ident'] == 0) $wrong_ident = true;
        
        // We check if the user wants to open or close flightplan
        if (isset($_GET['openFlightplan'])) $operation = 'open';
        else if (isset($_GET['closeFlightplan'])) $operation = 'close';
        
        if ($wrong_ident != true)
        {
            $FlightplanToOpen = new Flightplan();
            $FlightplanToOpen->selectById($_GET['flightplanId']);
            $FlightplanToOpen->changeFlightplanStatus($inject_ident, $inject_flightplanId, $operation);
            echo "Flightplan ID number ".$FlightplanToOpen->id." ".$operation;
        }
    }
}

else
{ ?>
    
    <form action="./dev.php5" method="get">
        <h5>Is airport controlled ?</h5>
        Syntax : http://lenny64.free.fr/dev.php5?isAirportControlled=<input type="text" name="isAirportControlled" value="LFML" size="4"/>&date=<input type="text" name="date" value="<?php echo date("Y-m-d");?>"/>&time=<input type="text" name="time" value="20:00:00"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>
        
    <form action="./dev.php5" method="get">
        <h5>Get flightplans at a given date</h5>
        <input type="hidden" name="getFlightplans"/>
        Syntax : http://lenny64.free.fr/dev.php5?getFlightplans&airport=<input type="text" name="airport" value="EDDF" size="4"/>&date=<input type="text" name="date" value="<?php echo date("Y-m-d");?>"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>

    <form action="./dev.php5" method="get">
        <h5>Get ATC sessions until a given date</h5>
        <input type="hidden" name="getATCSessions"/>
        Syntax : http://lenny64.free.fr/dev.php5?getATCSessions&limitDate=<input type="text" name="limitDate" value="<?php echo date("Y-m-d");?>"/>
        <br/>
        <input type="submit" value="OK"/>
    </form>

<form action="./dev.php5?request_auth" method="post" style="border: solid 2px #ccc; padding: 10px;">
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
    http://lenny64.free.fr/dev.php5?fileFlightplan
    <br/>&ident=<input type="text" name="ident" value="MY IDENT" disabled/>
    <br/>&callsign=<input type="text" name="callsign" value="" size="6" disabled/>
    <br/>&date=<input type="text" name="date" disabled/>
    <br/>&departureTime=<input type="text" name="departureTime" disabled/>
    <br/>&departureAirport=<input type="text" name="departureAirport" disabled/>
    <br/>&arrivalTime=<input type="text" name="arrivalTime" disabled/>
    <br/>&arrivalAirport=<input type="text" name="arrivalAirport" disabled/>
    <br/>&cruiseAltitude=<input type="text" name="cruiseAltitude" disabled/>
    <br/>&aircraft=<input type="text" name="aicraft" disabled/>
    <br/>&category=<input type="text" name="category" size="3" disabled/>
    <br/>&waypoints=<input type="text" name="waypoints" size="30" disabled/>
    <br/>
    
</form>

<?php }

// We close the session
mysql_close();

?>
