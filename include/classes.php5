<?php

class User
{
    public $ip;
    public $connected;
    public $mail;
    public $password;
    public $id;
    public $nofitications;
    public $hasCookie = false;
    public $userCookieId;
    public $name;
    private $requestNewUser = true;
    
    public function associateCookieToUser()
    {
        global $db;
        
        // We check if the user has a cookie
        if (isset($_COOKIE['lenny64_id']))
        {
            // If the cookie is not corrupted
            if ($_COOKIE['lenny64_id'] != NULL)
            {
                // We copy this value
                $this->userCookieId = $_COOKIE['lenny64_id'];
                $this->hasCookie = true;
            }
            else $this->hasCookie = false;
        }
        else $this->hasCookie = false;

        // If the user has a cookie
        if ($this->hasCookie == true AND isset($this->id) AND $this->id != NULL)
        {
            // We put it inside the "cookie" column
            $preparedQuery = $db->prepare("UPDATE users SET `cookie` = :userCookieId WHERE `userId` = :userId;");
            $preparedQuery->execute(array(":userCookieId" => $this->userCookieId, ":userId" => $this->id));
        }
    }

    public function searchUserCookie()
    {
        global $db;
        
        // We check if the user has a cookie
        if (isset($_COOKIE['lenny64_id']))
        {
            // If the cookie is not corrupted
            if ($_COOKIE['lenny64_id'] != NULL)
            {
                // We copy this value
                $this->userCookieId = $_COOKIE['lenny64_id'];
                $this->hasCookie = true;
            }
            else $this->hasCookie = false;
        }
        else $this->hasCookie = false;

        if($this->hasCookie == true)
        {
            // We gather every cookie from table
            $list_cookies = $db->query("SELECT userId, cookie FROM users");

            foreach ($list_cookies as $cookie)
            {
                if ($cookie['cookie'] == $this->userCookieId)
                {
                    $this->id = $cookie['userId'];
                    $this->selectById($this->id);
                    return true;
                }
            }
            return false;
        }
    }
    
    public function create($Mail,$Password,$IP)
    {
        global $db;
        
        // We get the list of users
        $users_list = $db->query("SELECT userId, mail, password FROM users");
        
        // We see
        foreach ($users_list as $user)
        {
            // if there is an user with that mail
            if ($user['mail'] == $Mail)
            {
                $this->mail = $Mail;
                
                // and if the password is correct
                if ($user['password'] == $Password)
                {
                    // We get the password
                    $this->password = $Password;
                    // And we get the ID
                    $this->id = $user['userId'];
                    
                    // If the IP differs from the DB, we update
                    if ($user['ip'] != $IP AND $IP != NULL)
                    {
                        $this->ip = $IP;
                        $preparedQuery = $db->prepare("UPDATE users SET ip = :ip WHERE userId = :userId;");
                        $preparedQuery->execute(array(":ip" => $IP, ":userId" => $this->id));
                    }
                    
                    $_SESSION['id'] = $this->id;
                    $_SESSION['mode'] = 'connected';
                    $this->connected = true;
                    //$this->associateCookieToUser();
                    
                    // We tell there is no need to create another user
                    $this->requestNewUser = false;
                }
                else
                {
                    echo "<div class='warning'>Password not correct</div>";
                    $_SESSION['mode'] = 'disconnected';
                    $this->connected = false;
                    
                    // We tell there is no need to create another user
                    $this->requestNewUser = false;
                }
            }
        }
        
        // We make a last check : is a password and mail entered ?
        if (!isset($Mail) OR !isset($Password) OR $Mail == NULL OR $Password == NULL) $this->requestNewUser = false;
        
        // If we receive the signal to create a new user (by default)
        if ($this->requestNewUser == true)
        {
            $preparedQuery = $db->prepare("INSERT INTO `users` VALUES('',:Mail,:Password,:IP,'0','','');");
            $preparedQuery->execute(array(":Mail" => $Mail, ":Password" => $Password, ":IP" => $IP));
            $_SESSION['mode'] = 'connected';
            $_SESSION['id'] = getInfo('userId', 'users', 'mail', $Mail);
            $this->id = $_SESSION['id'];
            $this->connected = true;
            //$this->associateCookieToUser();
        }
    }
    
    /**
     * This function selects every information contained into the user ID
     * @param type $id
     */
    public function selectById($id)
    {
        if (isset($id))
        {
            if ($id != NULL)
            {
                global $db;
                
                $users_list = $db->query("SELECT * FROM users WHERE userId = $id");
                $users = $users_list->fetch(PDO::FETCH_ASSOC);
                
                $this->ip = $users['ip'];
                $this->mail = $users['mail'];
                $this->password = $users['password'];
                $this->id = $users['userId'];
                $this->notifications = $users['notifications'];
                $this->parameters = json_decode($users['userParameters'],true);
                
                $users_names_list = $db->query("SELECT * FROM users_names WHERE userId = '$id' ORDER BY userNameId DESC LIMIT 0,1");
                $users_names = $users_names_list->fetch(PDO::FETCH_ASSOC);
                $this->name = $users_names['userName'];
            }
        }
    }
    
    /* Function to handle connection
     */
    public function connect($id)
    {
        $_SESSION['id'] = $id;
        $_SESSION['mode'] = "connected";
        $this->connected = true;
    }
    
    /* Function to handle disconnection
     */
    public function disconnect($id)
    {
        $_SESSION['mode'] = 'guest';
        unset($_SESSION['id']);
    }
    
    /* Function to change the way an user wants to receive or not
     * notifications about flightplans filled on his airport.
     */
    public function changeNotification($notification)
    {
        global $db;
        
        $preparedQuery = $db->prepare("UPDATE users SET notifications=:notification WHERE userId=:userId;");
        $preparedQuery->execute(array(":notification" => $notification, ":userId" => $this->id));
        $this->notifications = $notification;
    }
    
    /* Function to change other parameters
     */
    public function changeParameters($parameters)
    {
        global $db;
        
	$jsonUserParameters = json_encode($parameters);
        $preparedQuery = $db->prepare("UPDATE users SET userParameters=:jsonUserParameters WHERE userId=:userId;");
        $preparedQuery->execute(array(":jsonUserParameters" => $jsonUserParameters, ":userId" => $this->id));
        $this->parameters = $parameters;
    }
    
    /* Function to add/change the name of the ATC
     * it would appear when a session is shown on the main page
     * /!\ Requires the "users_names" table
     */
    public function changeName($name)
    {
        global $db;
        $preparedQuery = $db->prepare("INSERT INTO users_names VALUES ('',:userId,:name)");
        $preparedQuery->execute(array(":userId" => $this->id, ":name" => $name));
        $this->name = $name;
    }
    
}


class Airport
{
    public $name;
    public $icao;
    public $id;
    private $requestNewAirport = true;
    
    public function create($AirportName, $AirportICAO)
    {
        global $db;
        
        $this->name         =   $AirportName;
        $this->icao         =   $AirportICAO;
        
        // We get the list of airports
        $airports_list = $db->query("SELECT * FROM airports");
        
        // We see
        foreach ($airports_list as $airport)
        {
            // if there is an airport with that name
            if ($airport['name'] == $AirportName)
            {
                $this->name = $AirportName;
                
                $this->id = $airport['airportId'];
                
                // We tell there is no need to create another airport
                $this->requestNewAirport = false;
            }
        }
        
        // If we receive the signal to create a new session (by default)
        if ($this->requestNewAirport == true)
        {
            $preparedQuery = $db->prepare("INSERT INTO `airports` VALUES('',:AirportName,:AirportICAO);");
            $preparedQuery->execute(array(":AirportName" => $AirportName, ":AirportICAO" => $AirportICAO));
        }
    }
}


class Event
{
    public $airportICAO;
    public $date;
    public $beginTime;
    public $endTime;
    public $id;
    public $userId;
    public $fgcom;
    public $teamspeak;
    public $transitionLevel;
    public $runways;
    public $ils;
    public $docsLink;
    public $remarks;
    private $requestNewEvent = true;
    public $error = 0;
    public $eventCreated = false;


    public function create($Year, $Month, $Day, $BeginHour, $BeginMinutes, $EndHour, $EndMinutes, $AirportICAO, $FGCOM, $TeamSpeak, $DocsLink, $Remarks)
    {
        global $db;
        
        $this->fgcom        =   ($FGCOM != NULL ? $FGCOM : 'N/A');
        $this->teamspeak    =   ($TeamSpeak != NULL ? $TeamSpeak : 'N/A');
        $this->docsLink     =   ($DocsLink != NULL ? $DocsLink : 'N/A');
        $this->remarks      =   ($Remarks != NULL ? $Remarks : 'N/A');
        
        // We check if there is an airport called
        if (isset($AirportICAO) AND $AirportICAO != NULL) $this->airportICAO = $AirportICAO;
        // Otherwise we do not create an event
        else            $this->requestNewEvent = false;
        
        // We check if there is a date called
        if (isset($Year) AND isset($Month) AND isset($Day) AND $Year != NULL AND $Month != NULL AND $Day != NULL)
        {
            $this->date = date('Y-m-d',strtotime($Year.'-'.$Month.'-'.$Day));

            // We transform each Hour+Minutes into Time format
            $this->beginTime = date('H:i:s',  strtotime($BeginHour.':'.$BeginMinutes));
            $this->endTime = date('H:i:s',  strtotime($EndHour.':'.$EndMinutes));
            
            // Security to avoid "0 hours" events OR if the egin time occurs after the end time
            if ($this->beginTime >= $this->endTime)
            {
                $this->requestNewEvent = false;
                $this->error = "Please check your date and times";
            }
            
        }
        // Otherwise we do not create an event
        else            $this->requestNewEvent = false;
        
        $this->userId       =   $_SESSION['id'];
        
        // If the user is not connected (password not correct)
        if ($_SESSION['mode'] != 'connected')
        {
            // We check if there is a session planned from OpenRadar
            // (it's "remark" will have the value "openradar")
            if (!isset($Remarks) OR $this->remarks != "openradar")
            {
                $this->requestNewEvent = false;
            }
            else
            {
                $this->userId = 1;
            }
        }
        
        // We get the list of events
        $events_list = $db->query("SELECT * FROM events");
        
        
        // We see
        foreach ($events_list as $event)
        {
            /* A V O I D   T W O   A T C S   A T   T H E    S A M E    T I M E */
            // if there is an airport with that name
            if (    $event['airportICAO'] == $AirportICAO AND 
                    $event['date'] == $this->date AND 
                    // If the wished time is between BEGIN and END times, we won't create it
                    (($this->beginTime >= $event['beginTime'] AND $this->beginTime < $event['endTime']) OR ($this->endTime > $event['beginTime'] AND $this->endTime <= $event['endTime']) OR ($this->beginTime <= $event['beginTime'] AND $this->endTime >= $event['endTime']))                    
                    )
            {
                
                $this->error = "Sorry, an other session is planned at this moment.";
                
                // We tell there is no need to create another event
                $this->requestNewEvent = false;
            }
            /* A V O I D   T W O    A I R P O R T S   F O R   T H E    S A M E   A T C */
            elseif ( $event['userId'] == $this->userId AND $event['date'] == $this->date AND 
					(($this->beginTime >= $event['beginTime'] AND $this->beginTime < $event['endTime']) OR ($this->endTime > $event['beginTime'] AND $this->endTime <= $event['endTime']) OR ($this->beginTime <= $event['beginTime'] AND $this->endTime >= $event['endTime']))
					)
			{
				$this->error = "Would you handle two sessions at a time ?<br/>Please specify a time before or after your session at ".$event['airportICAO']." (from ".$event['beginTime']." to ".$event['endTime']." ).";
				
				// We tell there is no need to create another event
                $this->requestNewEvent = false;
			}
        }
        
        
        
        
        // If we receive the signal to create a new session (by default)
        if ($this->requestNewEvent == true)
        {
            $statement = $db->prepare("INSERT INTO `events` (`airportICAO`, `userId`, `date`, `beginTime`, `endTime`, `fgcom`, `teamspeak`, `docsLink`, `remarks`)
                VALUES(
                :airportICAO,
                :userId,
                :date,
                :beginTime,
                :endTime,
                :fgcom,
                :teamspeak,
                :docsLink,
                :remarks);");
            
            $statement->execute(array(
                ':airportICAO'      =>  $this->airportICAO,
                ':userId'           =>  $this->userId,
                ':date'             =>  $this->date,
                ':beginTime'        =>  $this->beginTime,
                ':endTime'          =>  $this->endTime,
                ':fgcom'            =>  $this->fgcom,
                ':teamspeak'        =>  $this->teamspeak,
                ':docsLink'         =>  $this->docsLink,
                ':remarks'          =>  $this->remarks));
            
            $this->id = $db->lastInsertId();
            $this->eventCreated = true;
        }
    }
    
    /**
     * This function selects every information contained into the event ID
     * @param type $id
     */
    public function selectById($id)
    {
        if (isset($id))
        {
            if ($id != NULL)
            {
                global $db;
                
                $events_list = $db->query("SELECT * FROM events WHERE eventId = $id");
                $event = $events_list->fetch(PDO::FETCH_ASSOC);
                
                $this->id = $event['eventId'];
                $this->airportICAO = $event['airportICAO'];
                $this->userId = $event['userId'];
                $this->date = $event['date'];
                $this->beginTime = $event['beginTime'];
                $this->endTime = $event['endTime'];
                $this->fgcom = $event['fgcom'];
                $this->teamspeak = $event['teamspeak'];
                $this->transitionLevel = $event['transitionLevel'];
                $this->runways = $event['runways'];
                $this->ils = $event['ILS'];
                $this->docsLink = $event['docsLink'];
                $this->remarks = $event['remarks'];
            }
        }
    }
    
    public function updateEvent($infos)
    {
        $string = '';
        if ($_SESSION['mode'] == 'connected')
        {
            global $db;
            
            $nbInfos = sizeof($infos);
            $i = 1;
            foreach ($infos as $info => $value)
            {
                if ($i == $nbInfos) $string .= "`".$info."`='".$value."'";
                else $string .= "`".$info."`='".htmlspecialchars($value)."',";
                $i++;
            }
            $preparedQuery = $db->prepare("UPDATE events SET $string WHERE `eventId`=".$infos['eventId'].";");
            $preparedQuery->execute();
        }
    }
}

class SpecialEvent
{
    public $id;
    public $creatorId;
    public $title;
    public $description;
    public $url;
    public $dateTimeCreation;
    public $dateEvent;
    public $valid = TRUE;
    public $eventsList = Array();
    public $pilotsList = Array();
    
    public function addPilot($callsign, $participation)
    {
        if (isset($callsign) AND isset($participation))
        {
            if ($callsign != NULL AND $participation != NULL)
            {
                global $db;
                
                $preparedQuery = $db->prepare("INSERT INTO specialEvents_pilots VALUES('','$this->id','$callsign','$participation',NOW());");
                $preparedQuery->execute();
                $this->pilotsList = Array();
                $this->selectById($this->id);
            }
        }
    }
    
    public function selectById($id)
    {
        if (isset($id))
        {
            if ($id != NULL)
            {
                global $db;
    
                // We list every special event
                $specialEvents_list = $db->query("SELECT * FROM specialEvents_events WHERE specialEventsId = $id");
                $specialEvent = $specialEvents_list->fetch(PDO::FETCH_ASSOC);
                // And gather all relative information
                $this->id = $specialEvent['specialEventsId'];
                $this->creatorId = $specialEvent['userId'];
                $this->title = $specialEvent['title'];
                $this->description = $specialEvent['description'];
                $this->url = $specialEvent['url'];
                $this->dateTimeCreation = $specialEvent['dateTime'];
                
                /* EVENT LIST */
                // We make a query returning specialEventId and eventId
                $specialEventsEvents_list = $db->query(
                        "SELECT specialEvents_airports.specialEventsId,events.eventId
                        FROM specialEvents_airports,events 
                        WHERE specialEvents_airports.specialeventsId = $id
                        AND specialEvents_airports.eventId = events.eventId
                        AND events.date >= CURDATE()
                        ORDER BY events.date, events.beginTime");
                
                // We initialize the "valid" to TRUE, in case the previous Special
                // event selected is not valid.
                $this->valid = TRUE;
                
                // We initialize an empty array 
                $this->eventsList = Array();
                // Each event in that special event
                foreach ($specialEventsEvents_list as $specialEventsEvent)
                {
                    // We pick information of that particular event
                    $Event = new Event();
                    $Event->selectById($specialEventsEvent['eventId']);
                    // If the event will occur in the future, we take it in account
                    if ($Event->date >= date('Y-m-d'))
                    {
                        $this->eventsList[] = $Event->id;
                        $this->dateEvent = $Event->date;
                    }
                }
                // We test if the array is empty (thus the specialEvent is not valid)
                if (empty($this->eventsList))
                {
                    $this->valid = FALSE;
                }
                
                /* PILOT LIST */
                // We select all pilots relative to this special event
                $specialEventPilots_list = $db->query("SELECT * FROM specialEvents_pilots WHERE specialEventsId = $id");
                // We initialize an empty array
                $this->pilotsList = Array();
                foreach ($specialEventPilots_list as $specialEventsPilot)
                {
                    $this->pilotsList[] = $specialEventsPilot;
                }
            }
        }
    }
    
}

class Flightplan
{
    public $departureAirport;
    public $arrivalAirport;
    public $alternateDestination;
    public $cruiseAltitude;
    public $trueAirspeed;
    public $callsign;
    public $airline;
    public $flightNumber;
    public $category;
    public $aircraftType;
    public $dateDeparture;
    public $dateArrival;
    public $departureTime;
    public $arrivalTime;
    public $waypoints;
    public $soulsOnBoard;
    public $fuelTime;
    public $pilotName;
    public $email;
    public $privateKey;
    public $comments;
    public $status;
    public $history;
    public $lastUpdated;
    public $id;
    public $departureATCpresence = true;
    public $arrivalATCpresence = true;
    public $dataMissing = true;
    
    function create($dateDeparture,$departureAirport,$arrivalAirport,$alternateDestination,$cruiseAltitude,$trueAirspeed,$callsign,$pilotName,$airline,$flightNumber,$category,$aircraftType,$departureTime,$arrivalTime,$waypoints,$soulsOnBoard,$fuelTime,$comments)
    {
        // We check if the information were given
        if (isset($departureAirport) AND isset($arrivalAirport) AND isset($callsign) AND isset($departureTime) AND isset($arrivalTime) AND isset($dateDeparture))
        {
            // We check wether the information is fine
            // In particular, the airport must contain 4 letters
            if (preg_match("#^[a-zA-z]{4}$#",$departureAirport) AND preg_match("#^[a-zA-z]{4}$#",$arrivalAirport) AND $callsign != NULL AND $callsign != 'Callsign' AND $departureTime != NULL AND $arrivalTime != NULL AND $dateDeparture != NULL)
            {
                
                global $db;
                
                // We can generate some alerts
                $alert_departureATC = true;
                $alert_arrivalATC = true;
                
                // We gather the information
                $this->departureAirport = $departureAirport;
                $this->arrivalAirport = $arrivalAirport;
                $this->alternateDestination = ($alternateDestination != NULL ? $alternateDestination : '');
                $this->cruiseAltitude = ($cruiseAltitude != NULL ? $cruiseAltitude : '');
                $this->trueAirspeed = ($trueAirspeed != NULL ? $trueAirspeed : '');
                $this->callsign = $callsign;
                $this->airline = ($airline != NULL ? $airline : '');
                $this->flightNumber = ($flightNumber != NULL ? $flightNumber : '');
                $this->category = ($category != NULL ? $category : '');
                $this->aircraftType = ($aircraftType != NULL ? $aircraftType : '');
                $this->departureTime = $departureTime;
                $this->arrivalTime = $arrivalTime;
                $this->dateDeparture = $dateDeparture;
                // If the arrival time is before departure time, i assume the arrival will be after midnight of the next day
                if ($this->arrivalTime < $this->departureTime) $this->dateArrival = date('Y-m-d',strtotime($this->dateDeparture."+1 days"));
                // Otherwise i assume the arrival date is the same than the departure one
                else $this->dateArrival = $this->dateDeparture;
                $this->waypoints = ($waypoints != NULL ? $waypoints : '');
                $this->soulsOnBoard = ($soulsOnBoard != NULL ? $soulsOnBoard : '');
                $this->fuelTime = ($fuelTime != NULL ? $fuelTime : '');
                $this->pilotName = ($pilotName != NULL ? $pilotName : '');
                $this->comments = ($comments != NULL ? $comments : '');
                $this->status = 'filled';
				
                // If the airport is not controlled, we advise the pilot
                if (isAirportControlled($departureAirport, $this->dateDeparture, $departureTime) == false)
                {
                    $alert_departureATC = false;
                    $this->departureATCpresence = false;
                }
                if (isAirportControlled($arrivalAirport, $this->dateArrival, $arrivalTime) == false)
                {
                    $alert_arrivalATC = false;
                    $this->arrivalATCpresence = false;
                }
                
                // I insert the flightplan into DB
                $preparedQuery = $db->prepare("INSERT INTO flightplans20140113 VALUES('','',:callsign,:airline,:flightNumber,:departureAirport,:arrivalAirport,:alternateDestination,:cruiseAltitude,:trueAirspeed,:dateDeparture,:dateArrival,:departureTime,:arrivalTime,:aircraftType,:soulsOnBoard,:fuelTime,:pilotName,:waypoints,:category,:comments,'','');");
                $preparedQuery->execute(array(
                    ":callsign"             =>  $this->callsign,
                    ":airline"              =>  $this->airline,
                    ":flightNumber"         =>  $this->flightNumber,
                    ":departureAirport"     =>  $this->departureAirport,
                    ":arrivalAirport"       =>  $this->arrivalAirport,
                    ":alternateDestination" =>  $this->alternateDestination,
                    ":cruiseAltitude"       =>  $this->cruiseAltitude,
                    ":trueAirspeed"         =>  $this->trueAirspeed,
                    ":dateDeparture"        =>  $this->dateDeparture,
                    ":dateArrival"          =>  $this->dateArrival,
                    ":departureTime"        =>  $this->departureTime,
                    ":arrivalTime"          =>  $this->arrivalTime,
                    ":aircraftType"         =>  $this->aircraftType,
                    ":soulsOnBoard"         =>  $this->soulsOnBoard,
                    ":fuelTime"             =>  $this->fuelTime,
                    ":pilotName"            =>  $this->pilotName,
                    ":waypoints"            =>  $this->waypoints,
                    ":category"             =>  $this->category,
                    ":comments"             =>  $this->comments,
                ));
                $this->id = $db->lastInsertId();
                // I also insert the default comment entered by the pilot
                $preparedQuery = $db->prepare("INSERT INTO flightplan_comments VALUES('',:id,:callsign,:comments,'".date("Y-m-d H:i:s")."');");
                $preparedQuery->execute(array(":id" => $this->id, ":callsign" => $this->callsign, ":comments" => $this->comments));
                // I also insert the status of the flightplan
                $preparedQuery = $db->prepare("INSERT INTO flightplan_status VALUES('','9999',:id,:status,'".date("Y-m-d H:i:s")."');");
                $preparedQuery->execute(array(":id" => $this->id, ":status" => $this->status));
                
                // We get the ATC user ID
                $dep_ATCiD = $db->query("SELECT userId FROM events WHERE airportICAO='$this->departureAirport' AND date='$this->dateDeparture' AND beginTime<='$this->departureTime' AND endTime>='$this->departureTime' LIMIT 1");
                $dep_ATCiD =$dep_ATCiD->fetch(PDO::FETCH_ASSOC);
                $dep_ATCiD = $dep_ATCiD[0];
                
                $arr_ATCiD = $db->query("SELECT userId FROM events WHERE airportICAO='$this->arrivalAirport' AND date='$this->dateArrival' AND beginTime<='$this->arrivalTime' AND endTime>='$this->arrivalTime' LIMIT 1");
                $arr_ATCiD = $arr_ATCiD->fetch(PDO::FETCH_ASSOC);
                $arr_ATCiD = $arr_ATCiD[0];
                
                // If the user wants to, we can send him the alert
                if (getInfo("notifications", "users", "userId", $dep_ATCiD) == 1)
                {
                    $dep_ATCMail = getInfo('mail', 'users', 'userId', $dep_ATCiD);
                    mail($dep_ATCMail, 'New flightplan : '.$this->callsign, $this->callsign.' wants to take off at '.$this->departureTime.' from your airport '.$this->departureAirport.' -- http://lenny64.free.fr/edit_flightplan.php5?idFlightplan='.$this->id);
                }
                if (getInfo("notifications", "users", "userId", $arr_ATCiD) == 1)
                {
                    $arr_ATCMail = getInfo('mail', 'users', 'userId', $arr_ATCiD);
                    mail($arr_ATCMail, 'New flightplan : '.$this->callsign, $this->callsign.' wants to land at '.$this->arrivalTime.' on your airport '.$this->arrivalAirport.' -- http://lenny64.free.fr/edit_flightplan.php5?idFlightplan='.$this->id);
                }
                // There is no data missing
                $this->dataMissing = false;
                
            }
        }
    }
    
    function create_old($dateDeparture,$departureAirport,$arrivalAirport,$cruiseAltitude,$callsign,$category,$aircraftType,$departureTime,$arrivalTime,$waypoints,$comments)
    {
        // We check if the information were given
        if (isset($departureAirport) AND isset($arrivalAirport) AND isset($callsign) AND isset($departureTime) AND isset($arrivalTime) AND isset($dateDeparture))
        {
            // We check wether the information is fine
            // In particular, the airport must contain 4 letters
            if (preg_match("#^[a-zA-z]{4}$#",$departureAirport) AND preg_match("#^[a-zA-z]{4}$#",$arrivalAirport) AND $callsign != NULL AND $departureTime != NULL AND $arrivalTime != NULL AND $dateDeparture != NULL)
            {
                global $db;
                
                // We can generate some alerts
                $alert_departureATC = true;
                $alert_arrivalATC = true;
                
                // We gather the information
                $this->departureAirport = $departureAirport;
                $this->arrivalAirport = $arrivalAirport;
                $this->cruiseAltitude = $cruiseAltitude;
                $this->callsign = $callsign;
                $this->category = $category;
                $this->aircraftType = $aircraftType;
                $this->departureTime = $departureTime;
                $this->arrivalTime = $arrivalTime;
                $this->dateDeparture = $dateDeparture;
                // If the arrival time is before departure time, i assume the arrival will be after midnight of the next day
                if ($this->arrivalTime < $this->departureTime) $this->dateArrival = date('Y-m-d',strtotime($this->dateDeparture."+1 days"));
                // Otherwise i assume the arrival date is the same than the departure one
                else $this->dateArrival = $this->dateDeparture;
                $this->waypoints = $waypoints;
                $this->comments = $comments;
				
                // If the airport is not controlled, we advise the pilot
                if (isAirportControlled($departureAirport, $this->dateDeparture, $departureTime) == false)
                {
                    $alert_departureATC = false;
                    $this->departureATCpresence = false;
                }
                if (isAirportControlled($arrivalAirport, $this->dateArrival, $arrivalTime) == false)
                {
                    $alert_arrivalATC = false;
                    $this->arrivalATCpresence = false;
                }
                
                // I insert the flightplan into DB
                $preparedQuery = $db->prepare("INSERT INTO flightplans20140113 VALUES('','',:callsign,'',:departureAirport,:arrivalAirport,'',:cruiseAltitude,'',:dateDeparture,:dateArrival,:departureTime,:arrivalTime,:aircraftType,'','','',:waypoints,:category,:comments,'','');");
                $preparedQuery->execute(array(
                    ":callsign"             =>  $this->callsign,
                    ":departureAirport"     =>  $this->departureAirport,
                    ":arrivalAirport"       =>  $this->arrivalAirport,
                    ":cuiseAltitude"        =>  $this->cruiseAltitude,
                    ":dateDeparture"        =>  $this->dateDeparture,
                    ":dateArrival"          =>  $this->dateArrival,
                    ":departureTime"        =>  $this->departureTime,
                    ":arrivalTime"          =>  $this->arrivalTime,
                    ":aircraftType"         =>  $this->aircraftType,
                    ":waypoints"            =>  $this->waypoints,
                    ":category"             =>  $this->category,
                    ":comments"             =>  $this->comments
                ));
                $this->id = $db->lastInsertId();
                                
                // We get the ATC user ID
                $dep_ATCiDQuery = $db->query("SELECT userId FROM events WHERE airportICAO='$this->departureAirport' AND date='$this->dateDeparture' AND beginTime<='$this->departureTime' AND endTime>='$this->departureTime' LIMIT 1");
                $dep_ATCiD = $dep_ATCiDQuery->fetch(PDO::FETCH_ASSOC);
                $dep_ATCiD = $dep_ATCiD[0];
                
                $arr_ATCiDQuery = $db->query("SELECT userId FROM events WHERE airportICAO='$this->arrivalAirport' AND date='$this->dateArrival' AND beginTime<='$this->arrivalTime' AND endTime>='$this->arrivalTime' LIMIT 1");
                $arr_ATCiD = $arr_ATCiDQuery->fetch(PDO::FETCH_ASSOC);
                $arr_ATCiD = $arr_ATCiD[0];
                
                // If the user wants to, we can send him the alert
                if (getInfo("notifications", "users", "userId", $dep_ATCiD) == 1)
                {
                    $dep_ATCMail = getInfo('mail', 'users', 'userId', $dep_ATCiD);
                    mail($dep_ATCMail, 'New flightplan : '.$this->callsign, $this->callsign.' wants to take off at '.$this->departureTime.' from your airport '.$this->departureAirport.' -- http://lenny64.free.fr/edit_flightplan.php5?idFlightplan='.$this->id);
                }
                if (getInfo("notifications", "users", "userId", $arr_ATCiD) == 1)
                {
                    $arr_ATCMail = getInfo('mail', 'users', 'userId', $arr_ATCiD);
                    mail($arr_ATCMail, 'New flightplan : '.$this->callsign, $this->callsign.' wants to land at '.$this->arrivalTime.' on your airport '.$this->arrivalAirport.' -- http://lenny64.free.fr/edit_flightplan.php5?idFlightplan='.$this->id);
                }
                // There is no data missing
                $this->dataMissing = false;
                
            }
        }
    }
    
    // Function to edit a flight plan
    public function editFlightplan()
    {
        global $db;
        
        // If the arrival time is before departure time, i assume the arrival will be after midnight of the next day
        if ($this->arrivalTime < $this->departureTime) $this->dateArrival = date('Y-m-d',strtotime($this->dateDeparture."+1 days"));
        
        $preparedQuery = $db->prepare("UPDATE flightplans20140113 SET
                callsign = :callsign,
                airline = :airline,
                flightnumber = :flightNumber,
                airportICAOFrom = :departureAirport,
                airportICAOTo = :arrivalAirport,
                alternateDestination = :alternateDestination,
                cruiseAltitude = :cruiseAltitude,
                trueAirspeed = :trueSpeed,
                dateDeparture = :dateDeparture,
                dateArrival = :dateArrival,
                departureTime = :departureTime,
                arrivalTime = :arrivalTime,
                aircraft = :aircraftType,
                soulsOnBoard = :soulsOnBoard,
                fuelTime = :fuelTime,
                pilotName = :pilotName,
                waypoints = :waypoints,
                category = :category,
                comments = :comments 
                WHERE flightplanId = :flightplanId;");
        
        $preparedQuery->execute(array(
            ":callsign"             =>  $this->callsign,
            ":airline"              =>  $this->airline,
            ":flightNumber"         =>  $this->flightNumber,
            ":departureAirport"     =>  $this->departureAirport,
            ":arrivalAirport"       =>  $this->arrivalAirport,
            ":alternateDestination" =>  $this->alternateDestination,
            ":cruiseAltitude"       =>  $this->cruiseAltitude,
            ":trueSpeed"            =>  $this->trueAirspeed,
            ":dateDeparture"        =>  $this->dateDeparture,
            ":dateArrival"          =>  $this->dateArrival,
            ":departureTime"        =>  $this->departureTime,
            ":arrivalTime"          =>  $this->arrivalTime,
            ":aircraftType"         =>  $this->aircraftType,
            ":soulsOnBoard"         =>  $this->soulsOnBoard,
            ":fuelTime"             =>  $this->fuelTime,
            ":pilotName"            =>  $this->pilotName,
            ":waypoints"            =>  $this->waypoints,
            ":category"             =>  $this->category,
            ":comments"             =>  $this->comments,
            ":flightplanId"         =>  $this->id
        ));
    }
    
    // Function to select a flight plan with it's ID
    public function selectById($id)
    {
        if (isset($id))
        {
            if ($id != NULL)
            {
                global $db;
                
                $flightplans_list = $db->query("SELECT * FROM flightplans20140113 WHERE flightplanId = $id");
                $flightplan = $flightplans_list->fetch(PDO::FETCH_ASSOC);
                
                $this->id = $flightplan['flightplanId'];
                $this->associatedEvent = $flightplan['eventId'];
                $this->departureAirport = $flightplan['airportICAOFrom'];
                $this->arrivalAirport = $flightplan['airportICAOTo'];
                $this->alternateDestination = $flightplan['alternateDestination'];
                $this->cruiseAltitude = $flightplan['cruiseAltitude'];
                $this->trueAirspeed = $flightplan['trueAirspeed'];
                $this->callsign = $flightplan['callsign'];
                $this->airline = $flightplan['airline'];
                $this->flightNumber = $flightplan['flightNumber'];
                $this->category = $flightplan['category'];
                $this->aircraftType = $flightplan['aircraft'];
                $this->departureTime = $flightplan['departureTime'];
                $this->arrivalTime = $flightplan['arrivalTime'];
                $this->dateDeparture = $flightplan['dateDeparture'];
                $this->dateArrival = $flightplan['dateArrival'];
                $this->waypoints = $flightplan['waypoints'];
                $this->soulsOnBoard = $flightplan['soulsOnBoard'];
                $this->fuelTime = $flightplan['fuelTime'];
                $this->pilotName = $flightplan['pilotName'];
                
                // C O M M E N T S
                // We retrieve all comments relative to that flight plan
                $comments_list = $db->query("SELECT * FROM flightplan_comments WHERE flightplanId = $id");
                // We initialize the comment array
                $this->comments = array();
                // A security in case comments_list is null
                if ($comments_list != NULL)
                {
                    foreach ($comments_list as $comment)
                    {
                        // We select the comment's pseudo and password
                        $pseudo = $comment['pseudo'];
                        $comment = $comment['comment'];
                        // We put it into an array that will be displayed
                        $this->comments[] = array("pseudo" => $pseudo, "comment" => $comment);
                    }
                }
                
                // S T A T U S
                // We retrieve the status if the flightplan
                $status_list = $db->query("SELECT * FROM flightplan_status WHERE flightplanId = $id ORDER BY flightplanStatusId DESC LIMIT 1");
                $status = $status_list->fetch(PDO::FETCH_ASSOC);
                $this->status = $status['status'];
                
                // H I S T O R Y
                // We retrieve the flightplan's history
                $history_list = $db->query("SELECT * FROM flightplan_history WHERE flightplanId = $this->id ORDER BY dateTime DESC");
                // We initialize the last updated dateTime value
                $this->lastUpdated = 0;
                // We initialize the history array
                $this->history = array();
                // A security in case history_list is NULL
                if ($history_list != NULL)
                {
                    foreach ($history_list as $history)
                    {
                        // We select the variable and value
                        $variable = $history['variable'];
                        $value = $history['value'];
                        $date = $history['dateTime'];
                        // If the variable has not been stored into the history array
                        if (!array_key_exists($variable,$this->history))
                        {
                            // We put the variable to the value
                            $this->history[$variable] = array('value' => $value, 'dateTime' => $date);
                            //echo $variable." = ".$value." at ".$date."<br/>";
                        }

                        if ($this->lastUpdated == 0) $this->lastUpdated = $date;
                    }
                }
                
                // E M A I L   A N D   P R I V A T E   K E Y
                // We retrieve the email address
                $email_list = $db->query("SELECT * FROM flightplan_emails WHERE flightplanId = $id ORDER BY flightplanEmailId DESC LIMIT 1");
                $email = $email_list->fetch(PDO::FETCH_ASSOC);
                $this->email = $email['email'];
                $this->privateKey = $email['privateKey'];
                
            }
        }
    }
    
    public function addComment($pseudo,$comment)
    {
        if (isset($pseudo) AND isset($comment))
        {
            if ($pseudo != NULL AND $comment != NULL)
            {
                global $db;
                
                // We insert the comment
                $preparedQuery = $db->prepare("INSERT INTO flightplan_comments VALUES('',:id,:pseudo,:comment,:date);");
                $preparedQuery->execute(array(":id" => $this->id, ":pseudo" => $pseudo, ":comment" => $comment, ":date" => date('Y-m-d H:i:s')));
                echo "Comment added<br/>";
            }
        }
    }
    
    public function changeFlightplanStatus($userId,$flightplanId,$status)
    {
        if (isset($userId) AND isset($flightplanId) AND isset($status))
        {
            if ($userId != NULL AND $flightplanId != NULL AND $status != NULL)
            {
                global $db;
                
                $dateTime = date("Y-m-d H:i:s");
                $this->lastUpdated = $dateTime;
                $this->status = $status;
                $preparedQuery = $db->prepare("INSERT INTO flightplan_status VALUES('',:userId,:flightplanId,:status,:dateTime);");
                $preparedQuery->execute(array(":userId" => $userId, ":flightplanId" => $flightplanId, ":status" => $status, ":dateTime" => $dateTime));
            }
        }
    }
    
    public function changeFlightplanInfo($userId,$flightplanId,$variable,$value)
    {
	if (isset($userId) AND isset($flightplanId) AND isset($variable) AND isset($value))
        {
            if ($userId != NULL AND $flightplanId != NULL AND $variable != NULL)
            {
                global $db;
                
                $dateTime = date("Y-m-d H:i:s");
                $this->lastUpdated = $dateTime;
                $this->history[$variable] = array('value' => $value, 'dateTime' => $dateTime);
                $preparedQuery = $db->prepare("INSERT INTO flightplan_history VALUES('',:flightplanId,:userId,:variable,:value,:dateTime);");
                $preparedQuery->execute(array(":flightplanId" => $flightplanId, ":userId" => $userId, ":variable" => $variable, ":value" => $value, ":dateTime" => $dateTime));
            }
        }
    }
    
    public function createEmail($email)
    {
        if(isset($email))
        {
            if ($email != NULL)
            {
                global $db;
                
                $this->email = $email;
                $this->privateKey = substr(md5($email.$this->id),0,6);
                $preparedQuery = $db->prepare("INSERT INTO flightplan_emails VALUES('', :id, :email, :privateKey, NOW());");
                $preparedQuery->execute(array(":id" => $this->id, ":email" => $this->email, ":privateKey" => $this->privateKey));
                mail($this->email, $this->callsign.' : Your key to modify the flightplan '.$this->id, 'Good day ! To modify your flightplan, this is the key you will need : '.$this->privateKey);
            }
        }
    }
    
}

class Poll
{
    public $id;
    public $userPollId;
    public $dateBegin;
    public $dateEnd;
    public $title;
    public $content;
    public $choices = Array();
    public $okToVote = TRUE;

    public function create()
    {
    }

    public function selectById($id)
    {
        if (isset($id))
        {
            if ($id != NULL)
            {
                global $db;
                $polls_list = $db->query("SELECT * FROM polls_submits WHERE pollId = $id");
                $poll = $polls_list->fetch(PDO::FETCH_ASSOC);

                $this->id = $poll['pollId'];
                $this->userPollId = $poll['userId'];
                $this->dateBegin = $poll['dateBegin'];
                $this->dateEnd = $poll['dateEnd'];
                $this->title = $poll['title'];
                $this->content = $poll['content'];

                // We get every choices for a given poll
                $choices_list = $db->query("SELECT answer FROM polls_answers WHERE pollId = $id");
                // We gather every choice for this poll
                foreach ($choices_list as $choice)
                {
                    $this->choices[] = $choice['answer'];
                }
            }
        }
    }

    public function checkAnswer()
    {
        global $db;
        /*
         * PART TO AVOID VOTING TWICE
         * Now there is only the IP verification
         * Later there should be a cookie verification
         */
        // This is the user IP
        $ip = $_SERVER['REMOTE_ADDR'];
        // I list the IP into the database
        $listPreviousIp = $db->query("SELECT ip FROM polls_results");
        // Now we consider the user can vote
        $this->okToVote = TRUE;

        // For each IP into database
        foreach ($listPreviousIp as $previousIp)
        {
                // We check if the current IP is stored
                if ($previousIp['ip'] == $ip)
                {
                        // If so, we prohibit the user to vote again
                        $this->okToVote = FALSE;
                }
        }

        // Is there any cookie for this poll ?
        if (isset($_COOKIE['lenny64_poll']) AND $_COOKIE['lenny64_poll'] == $this->id)
        {
                // If so, we prohibit the user to vote again
                $this->okToVote = FALSE;
        }

        // Of course, if the user can vote, we allow a new entry into database
        if ($this->okToVote !== FALSE)
        {
                return TRUE;
        }
        // If the person has already voted
        else
        {
                return FALSE;
        }
    }

    public function answer($answer,$ip)
    {
        $this->checkAnswer();

        if ($this->okToVote != TRUE)
        {
                // We print an alert message
                echo "<div class='warning'>You already voted. 
                <br/><br/>
                Note : This feature is in Beta. Please <a href='./contact.php5' style='color: #aaa;'>contact me</a> for :
                <ul>
                        <li>Poll proposals ;</li>
                        <li>Bug reports.</li>
                </ul>
                </div>";
                return FALSE;
        }
        else
        {
                // Insertion of the vote
                $preparedQuery = $db->prepare("INSERT INTO polls_results VALUES('', :id, :answer, '', :ip, :date);");
                $preparedQuery->execute(array(":id" => $this->id, ":answer" => $answer, ":ip" => $ip, ":date" => date('Y-m-d H:i:s')));
                // We display a message
                echo "<div class='warning'>Your vote has been accepted. Thank you.
                <br/><br/>
                Note : This feature is in Beta. Please <a href='./contact.php5' style='color: #aaa;'>contact me</a> for :
                <ul>
                        <li>Poll proposals ;</li>
                        <li>Bug reports.</li>
                </ul>
                </div>";
                return TRUE;
        }
    }
	
}

class Cookie
{
	public $doesExist = false;
	public $name;
	public $value;
	public $expirDate;
	
	public function doesExist()
	{
		if (isset($_COOKIE[$this->name]) AND $_COOKIE[$this->name] != NULL)
		{
			$this->doesExist = true;
			$this->value = htmlspecialchars($_COOKIE[$this->name]);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function create($name_,$value_,$expirDate_)
	{
		$this->name = $name_;
		$this->value = $value_;
		$this->expirDate = $expirDate_;
		
		if ($this->doesExist() === false)
		{
			setcookie($this->name,$this->value,$this->expirDate);
		}
		else
		{
			return false;
		}
	}
}

?>
