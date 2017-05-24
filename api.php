<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<?php

// Function to get the result of a query to the API
function execute_query($path){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$path);
    curl_setopt($ch, CURLOPT_FAILONERROR,1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $retValue = curl_exec($ch);
    curl_close($ch);
    return $retValue;
}

function get_api_version() {
    $reponse = execute_query(API_URL.'?isAirportControlled');
    $oXML = new SimpleXMLElement($reponse);
    $version = $oXML->attributes()->version;
    return $version;
}

///////////////////////////////////////////////////
/////// GENERAL INFORMATION ABOUT THE API /////////
///////////////////////////////////////////////////
define('API_URL','http://flightgear-atc.alwaysdata.net/dev2017_04_28.php');
$api_version = get_api_version();


?>

<!-- LE CODE COMMENCE ICI -->
<div class="jumbotron">
    <div class="container">
        <h2 class="page-header" id="topPage">API <span class="badge">VERSION <?php echo $api_version; ?></span></h2>
        <p>
            This page is dedicated to developers wishing to interact with flightear-atc
        </p>
    </div>
</div>

<div class="container">

    <h3><a href="#apioverview">API Overview</a></h3>
    <h3><a href="#authentication">Authentication</a></h3>
    <h3><a href="#apiqueries">API Queries</a></h3>
    <h4><a href="#get1">GET - Check if an airport is controlled</a></h4>
    <h4><a href="#get2">GET - Retrieve flight plans</a></h4>
    <h4><a href="#get3">GET - Flight plan details</a></h4>
    <h4><a href="#get4">GET - ATC Sessions</a></h4>
    <h4><a href="#get5">GET - Test authentication</a></h4>
    <h4><a href="#post1">POST - Create an ATC session</a></h4>
    <h4><a href="#post2">POST - File a new flight plan</a></h4>
    <h4><a href="#post3">POST - Edit a flight plan</a></h4>
    <h4><a href="#post4">POST - Open a flight plan</a></h4>
    <h4><a href="#post5">POST - Close a flight plan</a></h4>
    <h4><a href="#post6">POST - Insert/Modify a custom value</a></h4>

    <h1 class="page-header" id="apioverview"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> API Overview </h1>
    <h3><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> Description</h3>
    <p>
        The API queries are made directly through the URL. Every query has an answer, in an XML format (not JSON).
        <br/>
        Current API version is <?php echo $api_version;?>. The URL to access the API is :
        <pre><?php echo API_URL;?></pre>
        The name of the query and the parameters are mentioned after the ".php".
        <br/>
        Example : "<?php echo API_URL; ?>?isAirportControlled&icao=LFML&date=2017-04-28&time=20:00:00"
    </p>
    <p>
        There are two kinds of queries : GET and POST.
    </p>
    <h3><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> Errors</h3>
    <p>
        If any query is not correct, an error message will appear. Example of error message :
<pre><xmp><error version="<?php echo $api_version;?>">
    <code>ERR_VAR</code>
    <message>getATCSessions requires limitDate</message>
</error></xmp></pre>
    </p>
    <p>
        Error types are :
        <ul>
            <li><code>ERR_VAR</code> an error occured on variables (missing variables are shown).</li>
            <li><code>ERR_VAR1</code> a mandatory variable is missing.</li>
            <li><code>ERR_VAR2</code> several mandatories variables are missing.</li>
            <li><code>WRONG_IDENT</code> the email/password couple is not correct.</li>
            <li><code>INVALID_FLIGHTPLAN</code> the information given to file/edit a flight plan are not correct. Check that dates (YYYY-MM-DD) and times (HH:MM:SS) are in the correct format.
        </ul>
    </p>

    <h1 class="page-header" id="authentication"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> Authentication</h1>
    <p>
        For POST queries you will need to sign in.
        <br/>
        For this purpose please <a href="./subscribe.php" target="_blank">sign in here</a>. Enter your email address and choose a password.
    </p>
    <p>
        The <code>email</code> and <code>password</code> will be asked for any POST query.
    </p>

    <h1 class="page-header" id="apiqueries"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> API queries</h1>
    <h3 id="get1"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> GET - Check if an airport is controlled</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?isAirportControlled&airport=...&date=YYYY-MM-DD&time=HH:MM:SS</pre></div>
        <div class="panel-body">
            <p>
                Check if an airport is controlled at a given <code>date</code> and <code>time</code>.
            </p>
            <p>
                <a href="./dev2017_04_28.php?isAirportControlled&airport=EDDF&date=2017-04-28&time=20:30:00" class="btn btn-default btn-sm" target="_blank">TEST with isAirportControlled&airport=EDDF&date=2017-04-28&time=20:30:00</a>
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>airport : airport ICAO code (example : LFML, KSFO, EGLL, ...)</pre>
                <pre>date : in YYYY-MM-DD format</pre>
                <pre>time : in HH:MM:SS format</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>-</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<airport version="<?php echo $api_version;?>">
    <icao>EDDF</icao>
    <date>2017-04-28</date>
    <time>20:30:00</time>
    <isControlled>1</isControlled>
</airport></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="get2"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> GET - Retrieve flight plans</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?getFlightplans[&callsign=...[&date=YYYY-MM-DD[&airport=...[&status=...[&limit=...]]]]]</pre></div>
        <div class="panel-body">
            <p>
                Retrieve flight plans for a given pilot <code>callsign</code> AND/OR a given <code>date</code> AND/OR a given <code>airport</code> AND/OR a given <code>status</code> limited by <code>limit</code>.
            </p>
            <p>
                <a href="./dev2017_04_28.php?getFlightplans&date=2017-04-28&airport=EDDF&limit=1" class="btn btn-default btn-sm" target="_blank">TEST with getFlightplans&date=2017-04-28&airport=EDDF&limit=1</a>
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>getFlightplans</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>callsign : pilot callsign</pre>
                <pre>date : flightplan's departure date</pre>
                <pre>airport : departure or arrival airport ICAO code</pre>
                <pre>status : flightplan status ("filed" / "open" / "close")</pre>
                <pre>limit : maximum number of flightplans to retrieve (default : 30 ; <b>maximum : 80</b>)</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<flightplans version="<?php echo $api_version;?>" count="1">
    <flightplan>
        <flightplanId>3160</flightplanId>
        <callsign>ABC123</callsign>
        <airline>DLH</airline>
        <flightNumber>DLH141</flightNumber>
        <airportFrom>EDDS</airportFrom>
        <airportTo>EDDF</airportTo>
        <alternateDestination/>
        <cruiseAltitude>FL200</cruiseAltitude>
        <trueAirspeed/>
        <dateDeparture>2017-04-28</dateDeparture>
        <dateArrival>2017-04-28</dateArrival>
        <departureTime>18:20:00</departureTime>
        <arrivalTime>18:50:00</arrivalTime>
        <aircraft>A320</aircraft>
        <soulsOnBoard/>
        <fuelTime/>
        <pilotName/>
        <waypoints/>
        <category>IFR</category>
        <comments>
        <comment>
        <user>ABC123</user>
        <message/>
        </comment>
        </comments>
        <status>filed</status>
        <additionalInformation/>
        <lastUpdated>0</lastUpdated>
    </flightplan>
</flightplans></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="get3"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> GET - Flight plan details</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?getFlightplanDetails&flightplanId=...</pre></div>
        <div class="panel-body">
            <p>
                Get all flightplan information for a given <code>flightplanId</code>.
            </p>
            <p>
                <a href="./dev2017_04_28.php?getFlightplanDetails&flightplanId=3160" class="btn btn-default btn-sm" target="_blank">TEST with getFlightplanDetails&flightplanId=3160</a>
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>getFlightplanDetails</pre>
                <pre>flightplanId : flightplan ID</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>-</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<flightplan version="<?php echo $api_version;?>">
    <flightplanId>3160</flightplanId>
    <callsign>ABC123</callsign>
    <airline>DLH</airline>
    <flightNumber>DLH141</flightNumber>
    <airportFrom>EDDS</airportFrom>
    <airportTo>EDDF</airportTo>
    <alternateDestination/>
    <cruiseAltitude>FL200</cruiseAltitude>
    <trueAirspeed/>
    <dateDeparture>2017-04-28</dateDeparture>
    <dateArrival>2017-04-28</dateArrival>
    <departureTime>18:20:00</departureTime>
    <arrivalTime>18:50:00</arrivalTime>
    <aircraft>A320</aircraft>
    <soulsOnBoard/>
    <fuelTime/>
    <pilotName/>
    <waypoints/>
    <category>IFR</category>
    <comments>
    <comment>
    <user>ABC123</user>
    <message/>
    </comment>
    </comments>
    <status>filed</status>
    <additionalInformation/>
    <lastUpdated>0</lastUpdated>
</flightplan></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="get4"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> GET - ATC sessions</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?getATCSessions&limitDate=YYYY-MM-DD</pre></div>
        <div class="panel-body">
            <p>
                Get all ATC sessions before <code>limitDate</code>.
            </p>
            <p>
                <a href="./dev2017_04_28.php?getATCSessions&limitDate=<?php echo date('Y-m-d');?>" class="btn btn-default btn-sm" target="_blank">TEST with getATCSessions&limitDate=<?php echo date('Y-m-d');?></a>
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>getATCSessions</pre>
                <pre>limitDate : limit date in YYYY-MM-DD format</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>-</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<events version="<?php echo $api_version;?>">
    <event>
        <eventId>2877</eventId>
        <airportICAO>SBEG</airportICAO>
        <date>2017-05-01</date>
        <beginTime>15:00:00</beginTime>
        <endTime>21:00:00</endTime>
        <fgcom>118.30</fgcom>
        <teamspeak>Portugues</teamspeak>
        <transitionLevel>4000</transitionLevel>
        <runways>10</runways>
        <ILS>110.30 105</ILS>
        <docsLink>http://www.aisweb.aer.mil.br/</docsLink>
        <remarks>
        http://inutiles.byethost16.com/flightgear/modules/brazukaairlines/Evento/
        </remarks>
    </event>
</events></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="get5"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> GET - Test authentication</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?testAuthentication&email=...&password=...</pre></div>
        <div class="panel-body">
            <p>
                Check if your <code>email</code> and <code>password</code> work fine.
            </p>
            <p>
                <a href="./dev2017_04_28.php?testAuthentication&email=abc&password=abc" class="btn btn-default btn-sm" target="_blank">TEST with ?testAuthentication&email=abc&password=abc</a>
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>email : your email</pre>
                <pre>password : your password</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>-</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<airport version="<?php echo $api_version;?>">
    <icao>EDDF</icao>
    <date>2017-04-28</date>
    <time>20:30:00</time>
    <isControlled>1</isControlled>
</airport></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="post1"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> POST - Create an ATC session</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?newAtcSession&email=...&password=...&date=YYYY-MM-DD&beginTime=HH:MM:SS&endTime=HH:MM:SS&airportICAO=...
[&fgCom=...[&teamSpeak=...[&docsLink[&remarks=...]]]]</pre></div>
        <div class="panel-body">
            <p>
                Creates a new ATC session at a given <code>date</code> according specified <code>airportICAO</code>, <code>beginTime</code> and <code>endTime</code>.
                <br/>
                Warning: as all POST queries, you'll need to create an account <a href="./subscribe.php" target="_blank">here</a> and use your login information into the query.
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>newAtcSession</pre>
                <pre>email : your email</pre>
                <pre>password : your password</pre>
                <pre>date : date in YYYY-MM-DD format</pre>
                <pre>beginTime : HH:MM:SS of the begin of the ATC session</pre>
                <pre>endTime : HH:MM:SS of the end of the ATC session</pre>
                <pre>airportICAO : ICAO code of the airport</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>fgCom : FGCom frequency</pre>
                <pre>teamspeak : Information regarding team speak client</pre>
                <pre>docsLink : link for additional documentation</pre>
                <pre>remarks : any useful information</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<event version="<?php echo $api_version;?>">
    <eventId>2894</eventId>
    <airportICAO>TEST</airportICAO>
    <date>2017-05-10</date>
    <beginTime>20:00:00</beginTime>
    <endTime>21:00:00</endTime>
    <fgcom>N/A</fgcom>
    <teamspeak>N/A</teamspeak>
    <transitionLevel/>
    <runways/>
    <ILS/>
    <docsLink>N/A</docsLink>
    <remarks>N/A</remarks>
</event></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="post2"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> POST - File a new flight plan</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?fileFlightplan&email=...&password=...&callsign=...&dateDeparture=YYYY-MM-DD&departureAirport=...&departureTime=HH:MM:SS&arrivalAirport=...&arrivalTime=HH:MM:SS
[&airline=...[&flightNumber=...[&alternateDestination=...[&cruiseAltitude=...[&trueAirspeed=...[&aircraft=...[&soulsOnBoard=...[&fuelTime=...[&pilotName=...[&waypoints=...[&category=IFR/VFR[&comments=...]]]]]]]]]]]]</pre></div>
        <div class="panel-body">
            <p>
                Creates a new flight plan for given information.
                <br/>
                Warning: as all POST queries, you'll need to create an account <a href="./subscribe.php" target="_blank">here</a> and use your login information into the query.
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>fileFlightplan</pre>
                <pre>email : your email</pre>
                <pre>password : your password</pre>
                <pre>callsign : pilot callsign</pre>
                <pre>dateDeparture : date in YYYY-MM-DD format</pre>
                <pre>departureAirport : ICAO code of the departure airport</pre>
                <pre>departureTime : HH:MM:SS of flight departure</pre>
                <pre>arrivalAirport : ICAO code of the arrival airport</pre>
                <pre>arrivalTime : HH:MM:SS of the flight arrival</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>airline : name of the airline</pre>
                <pre>flightNumber : flight number</pre>
                <pre>alternateDestination : alternate airport ICAO code (for divert)</pre>
                <pre>cruiseAltitude : cruise altitude or flight level</pre>
                <pre>trueAirspeed : speed of the aircraft</pre>
                <pre>aircraft : aircraft type/model</pre>
                <pre>soulsOnBoard : number of people</pre>
                <pre>fuelTime : autonomy of the aircraft</pre>
                <pre>pilotName : name of the pilot</pre>
                <pre>waypoints : waypoints during the flight</pre>
                <pre>category : either VFR of IFR (Visual Flight Rules or Instrument Flight Rules)</pre>
                <pre>comments : any useful information regarding the flight</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<flightplan version="<?php echo $api_version;?>">
    <flightplanId>3171</flightplanId>
    <callsign>_TEST</callsign>
    <airline/>
    <flightNumber/>
    <airportFrom>LFML</airportFrom>
    <airportTo>LFOK</airportTo>
    <alternateDestination/>
    <cruiseAltitude/>
    <trueAirspeed/>
    <dateDeparture>2017-05-05</dateDeparture>
    <dateArrival>2017-05-05</dateArrival>
    <departureTime>20:00:00</departureTime>
    <arrivalTime>22:00:00</arrivalTime>
    <aircraft/>
    <soulsOnBoard/>
    <fuelTime/>
    <pilotName/>
    <waypoints/>
    <category/>
    <comments/>
    <status>filed</status>
    <additionalInformation/>
    <lastUpdated/>
</flightplan></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="post3"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> POST - Edit a flight plan</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?editFlightplan&email=...&password=...&flightplanId=...
[&callsign=...[&dateDeparture=YYYY-MM-DD[&departureAirport=...[&departureTime=HH:MM:SS[&arrivalAirport=...[&arrivalTime=HH:MM:SS[&airline=...[&flightNumber=...[&alternateDestination=...[&cruiseAltitude=...[&trueAirspeed=...[&aircraft=...[&soulsOnBoard=...[&fuelTime=...[&pilotName=...[&waypoints=...[&category=IFR/VFR[&comments=...]]]]]]]]]]]]</pre></div>
        <div class="panel-body">
            <p>
                Edits the <code>flightplanId</code> with the optional information.
                <br/>
                Warning: as all POST queries, you'll need to create an account <a href="./subscribe.php" target="_blank">here</a> and use your login information into the query.
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>editFlightplan</pre>
                <pre>email : your email</pre>
                <pre>password : your password</pre>
                <pre>flightplanId : flightplan ID</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>callsign : pilot callsign</pre>
                <pre>dateDeparture : date in YYYY-MM-DD format</pre>
                <pre>departureAirport : ICAO code of the departure airport</pre>
                <pre>departureTime : HH:MM:SS of flight departure</pre>
                <pre>arrivalAirport : ICAO code of the arrival airport</pre>
                <pre>arrivalTime : HH:MM:SS of the flight arrival</pre>
                <pre>airline : name of the airline</pre>
                <pre>flightNumber : flight number</pre>
                <pre>alternateDestination : alternate airport ICAO code (for divert)</pre>
                <pre>cruiseAltitude : cruise altitude or flight level</pre>
                <pre>trueAirspeed : speed of the aircraft</pre>
                <pre>aircraft : aircraft type/model</pre>
                <pre>soulsOnBoard : number of people</pre>
                <pre>fuelTime : autonomy of the aircraft</pre>
                <pre>pilotName : name of the pilot</pre>
                <pre>waypoints : waypoints during the flight</pre>
                <pre>category : either VFR of IFR (Visual Flight Rules or Instrument Flight Rules)</pre>
                <pre>comments : any useful information regarding the flight</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<flightplan version="<?php echo $api_version;?>">
    <flightplanId>3171</flightplanId>
    <callsign>_TEST</callsign>
    <airline/>
    <flightNumber/>
    <airportFrom>LFML</airportFrom>
    <airportTo>LFOK</airportTo>
    <alternateDestination/>
    <cruiseAltitude/>
    <trueAirspeed/>
    <dateDeparture>2017-05-05</dateDeparture>
    <dateArrival>2017-05-05</dateArrival>
    <departureTime>20:00:00</departureTime>
    <arrivalTime>22:00:00</arrivalTime>
    <aircraft/>
    <soulsOnBoard/>
    <fuelTime/>
    <pilotName/>
    <waypoints/>
    <category/>
    <comments/>
    <status>filed</status>
    <additionalInformation/>
    <lastUpdated>2017-04-29 10:00:00</lastUpdated>
</flightplan></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="post4"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> POST - Open a flight plan</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?openFlightplan&email=...&password=...&flightplanId=...</pre></div>
        <div class="panel-body">
            <p>
                Opens <code>flightplanId</code>.
                <br/>
                Warning: as all POST queries, you'll need to create an account <a href="./subscribe.php" target="_blank">here</a> and use your login information into the query.
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>openFlightplan</pre>
                <pre>email : your email</pre>
                <pre>password : your password</pre>
                <pre>flightplanId : flightplan ID</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>-</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<flightplan version="<?php echo $api_version;?>">
    <flightplanId>3171</flightplanId>
    <callsign>_TEST</callsign>
    <airline/>
    <flightNumber/>
    <airportFrom>LFML</airportFrom>
    <airportTo>LFOK</airportTo>
    <alternateDestination/>
    <cruiseAltitude/>
    <trueAirspeed/>
    <dateDeparture>2017-05-05</dateDeparture>
    <dateArrival>2017-05-05</dateArrival>
    <departureTime>20:00:00</departureTime>
    <arrivalTime>22:00:00</arrivalTime>
    <aircraft/>
    <soulsOnBoard/>
    <fuelTime/>
    <pilotName/>
    <waypoints/>
    <category/>
    <comments/>
    <status>open</status>
    <additionalInformation/>
    <lastUpdated>2017-04-29 10:00:00</lastUpdated>
</flightplan></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="post5"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> POST - Close a flight plan</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?closeFlightplan&email=...&password=...&flightplanId=...</pre></div>
        <div class="panel-body">
            <p>
                Closes <code>flightplanId</code>.
                <br/>
                Warning: as all POST queries, you'll need to create an account <a href="./subscribe.php" target="_blank">here</a> and use your login information into the query.
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>closeFlightplan</pre>
                <pre>email : your email</pre>
                <pre>password : your password</pre>
                <pre>flightplanId : flightplan ID</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>-</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<flightplan version="<?php echo $api_version;?>">
    <flightplanId>3171</flightplanId>
    <callsign>_TEST</callsign>
    <airline/>
    <flightNumber/>
    <airportFrom>LFML</airportFrom>
    <airportTo>LFOK</airportTo>
    <alternateDestination/>
    <cruiseAltitude/>
    <trueAirspeed/>
    <dateDeparture>2017-05-05</dateDeparture>
    <dateArrival>2017-05-05</dateArrival>
    <departureTime>20:00:00</departureTime>
    <arrivalTime>22:00:00</arrivalTime>
    <aircraft/>
    <soulsOnBoard/>
    <fuelTime/>
    <pilotName/>
    <waypoints/>
    <category/>
    <comments/>
    <status>close</status>
    <additionalInformation/>
    <lastUpdated>2017-04-29 10:00:00</lastUpdated>
</flightplan></xmp></pre>
            </p>
        </div>
    </div>
    <h3 id="post6"><a href="#topPage"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a> POST - Insert/Modify a custom value</h3>
    <div class="panel panel-default">
        <div class="panel-heading"><pre>?setVar&email=...&password=...&flightplanId=...&variable=...&value=[...]</pre></div>
        <div class="panel-body">
            <p>
                This query allows one to add some custom information on a flight plan. Any variable and value can be created/modified.
                <br/>
                Caution: if the variable already exists, this command will overwrite it.
                <br/>
                Notice: leaving the value empty will unset the variable.
                <br/>
                Warning: as all POST queries, you'll need to create an account <a href="./subscribe.php" target="_blank">here</a> and use your login information into the query.
            </p>
            <p>
                <b>Mandatory parameters</b>
                <pre>editFlightplan</pre>
                <pre>email : your email</pre>
                <pre>password : your password</pre>
                <pre>flightplanId : flightplan ID</pre>
                <pre>variable : custom variable to set/overwrite</pre>
                <pre>value : custom value to set/overwrite<br/>(if empty, the variable is unset)</pre>
            </p>
            <p>
                <b>Optional parameters</b>
                <pre>-</pre>
            </p>
            <p>
                <b>Answer example</b>
                <pre><xmp>
<flightplan version="<?php echo $api_version;?>">
    <flightplanId>3171</flightplanId>
    <callsign>_TEST</callsign>
    <airline/>
    <flightNumber/>
    <airportFrom>LFML</airportFrom>
    <airportTo>LFOK</airportTo>
    <alternateDestination/>
    <cruiseAltitude/>
    <trueAirspeed/>
    <dateDeparture>2017-05-05</dateDeparture>
    <dateArrival>2017-05-05</dateArrival>
    <departureTime>20:00:00</departureTime>
    <arrivalTime>22:00:00</arrivalTime>
    <aircraft/>
    <soulsOnBoard/>
    <fuelTime/>
    <pilotName/>
    <waypoints/>
    <category/>
    <comments/>
    <status>close</status>
    <additionalInformation>
        <custom>2</custom>
    </additionalInformation>
    <lastUpdated>2017-04-29 10:00:00</lastUpdated>
</flightplan></xmp></pre>
            </p>
        </div>
    </div>

</div>

<?php include('./include/footer.php'); ?>
