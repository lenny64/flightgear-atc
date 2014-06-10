<?php

class User
{
    public $ip;
    public $mail;
    public $password;
    public $id;
    public $nofitications;
    public $hasCookie = false;
    public $userCookieId;
    private $requestNewUser = true;
    
    public function associateCookieToUser()
    {
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
			mysql_query("UPDATE users SET `cookie` = '$this->userCookieId' WHERE `userId` = '$this->id';");
		}
	}
	
	public function searchUserCookie()
	{
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
			$list_cookies = mysql_query("SELECT userId, cookie FROM users");
			
			while ($cookie = mysql_fetch_array($list_cookies))
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
        // We get the list of users
        $users_list = mysql_query("SELECT userId, mail, password FROM users");
        
        // We see
        while ($user = mysql_fetch_array($users_list))
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
                        mysql_query("UPDATE users SET `ip`=$IP WHERE userId = $this->id;");
                    }
                    
                    $_SESSION['id'] = $this->id;
                    $_SESSION['mode'] = 'connected';
                    //$this->associateCookieToUser();
                    
                    // We tell there is no need to create another user
                    $this->requestNewUser = false;
                }
                else
                {
                    echo "<div class='warning'>Password not correct</div>";
                    $_SESSION['mode'] = 'disconnected';
                    
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
            mysql_query("INSERT INTO `users` VALUES('','$Mail','$Password','$IP','0','','');") or die(mysql_error());
            $_SESSION['mode'] = 'connected';
            $_SESSION['id'] = getInfo('userId', 'users', 'mail', $Mail);
            $this->id = $_SESSION['id'];
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
                $users_list = mysql_query("SELECT * FROM users WHERE userId = $id");
                $users = mysql_fetch_array($users_list);
                
                $this->ip = $users['ip'];
                $this->mail = $users['mail'];
                $this->password = $users['password'];
                $this->id = $users['userId'];
                $this->notifications = $users['notifications'];
                $this->parameters = json_decode($users['userParameters'],true);
                
                $_SESSION['id'] = $this->id;
                $_SESSION['mode'] = "connected";
            }
        }
    }
    
    /* Function to change the way an user wants to receive or not
     * notifications about flightplans filled on his airport.
     */
    public function changeNotification($notification)
    {
        mysql_query("UPDATE users SET notifications='$notification' WHERE userId='".$this->id."';");
        $this->notifications = $notification;
    }
    
    /* Function to change other parameters
     */
    public function changeParameters($parameters)
    {
		$jsonUserParameters = json_encode($parameters);
        mysql_query("UPDATE users SET userParameters='$jsonUserParameters' WHERE userId='".$this->id."';");
        $this->parameters = $parameters;
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
        $this->name         =   $AirportName;
        $this->icao         =   $AirportICAO;
        
        // We get the list of airports
        $airports_list = mysql_query("SELECT * FROM airports");
        
        // We see
        while ($airport = mysql_fetch_array($airports_list))
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
        if ($this->requestNewAirport == true) mysql_query("INSERT INTO `airports` VALUES('','$AirportName','$AirportICAO');") or die(mysql_error());
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
        $this->fgcom        =   $FGCOM;
        $this->teamspeak    =   $TeamSpeak;
        $this->docsLink     =   $DocsLink;
        $this->remarks      =   $Remarks;
        
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
        }
        
        // We get the list of events
        $events_list = mysql_query("SELECT * FROM events");
        
        
        // We see
        while ($event = mysql_fetch_array($events_list))
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
            mysql_query("INSERT INTO `events` 
                VALUES('',
                '$this->airportICAO',
                '$this->userId',
                '$this->date',
                '$this->beginTime',
                '$this->endTime',
                '$this->fgcom',
                '$this->teamspeak',
                '$this->transitionLevel',
                '$this->runways',
                '$this->ils',
                '$this->docsLink',
                '$this->remarks',
                '',
                '',
                '',
                '',
                '');") or die(mysql_error());
            $this->eventCreated = true;
            $this->id = mysql_insert_id();
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
                $events_list = mysql_query("SELECT * FROM events WHERE eventId = $id");
                $event = mysql_fetch_array($events_list);
                
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
            $nbInfos = sizeof($infos);
            $i = 1;
            foreach ($infos as $info => $value)
            {
                if ($i == $nbInfos) $string .= "`".$info."`='".mysql_real_escape_string(htmlspecialchars($value))."'";
                else $string .= "`".$info."`='".mysql_real_escape_string(htmlspecialchars($value))."',";
                $i++;
            }
            $query = "UPDATE events SET $string WHERE `eventId`=".$infos['eventId'].";";
            mysql_query($query) or die(mysql_error());
        }
    }
}

class Flightplan
{
    public $associatedEvent;
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
                // We can generate some alerts
                $alert_departureATC = true;
                $alert_arrivalATC = true;
                
                // We gather the information
                $this->departureAirport = mysql_real_escape_string(htmlspecialchars($departureAirport));
                $this->arrivalAirport = mysql_real_escape_string(htmlspecialchars($arrivalAirport));
                $this->alternateDestination = mysql_real_escape_string(htmlspecialchars($alternateDestination));
                $this->cruiseAltitude = mysql_real_escape_string(htmlspecialchars($cruiseAltitude));
                $this->trueAirspeed = mysql_real_escape_string(htmlspecialchars($trueAirspeed));
                $this->callsign = mysql_real_escape_string(htmlspecialchars($callsign));
                $this->airline = mysql_real_escape_string(htmlspecialchars($airline));
                $this->flightNumber = mysql_real_escape_string(htmlspecialchars($flightNumber));
                $this->category = mysql_real_escape_string(htmlspecialchars($category));
                $this->aircraftType = mysql_real_escape_string(htmlspecialchars($aircraftType));
                $this->departureTime = mysql_real_escape_string(htmlspecialchars($departureTime));
                $this->arrivalTime = mysql_real_escape_string(htmlspecialchars($arrivalTime));
                $this->dateDeparture = mysql_real_escape_string(htmlspecialchars($dateDeparture));
                // If the arrival time is before departure time, i assume the arrival will be after midnight of the next day
                if ($this->arrivalTime < $this->departureTime) $this->dateArrival = date('Y-m-d',strtotime($this->dateDeparture."+1 days"));
                // Otherwise i assume the arrival date is the same than the departure one
                else $this->dateArrival = $this->dateDeparture;
                $this->waypoints = mysql_real_escape_string(htmlspecialchars($waypoints));
                $this->soulsOnBoard = mysql_real_escape_string(htmlspecialchars($soulsOnBoard));
                $this->fuelTime = mysql_real_escape_string(htmlspecialchars($fuelTime));
                $this->pilotName = mysql_real_escape_string(htmlspecialchars($pilotName));
                $this->comments = mysql_real_escape_string(htmlspecialchars($comments));
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
                mysql_query("INSERT INTO flightplans20140113 VALUES('','','$this->callsign','$this->airline','$this->flightNumber','$this->departureAirport','$this->arrivalAirport','$this->alternateDestination','$this->cruiseAltitude','$this->trueAirspeed','$this->dateDeparture','$this->dateArrival','$this->departureTime','$this->arrivalTime','$this->aircraftType','$this->soulsOnBoard','$this->fuelTime','$this->pilotName','$this->waypoints','$this->category','$this->comments','','');");
                $this->id = mysql_insert_id();
                // I also insert the default comment entered by the pilot
                mysql_query("INSERT INTO flightplan_comments VALUES('','$this->id','$this->callsign','$this->comments','".date("Y-m-d H:i:s")."');");
                // I also insert the status of the flightplan
                mysql_query("INSERT INTO flightplan_status VALUES('','9999','$this->id','$this->status','".date("Y-m-d H:i:s")."');");
                
                // We get the ATC user ID
                $dep_ATCiD = mysql_query("SELECT userId FROM events WHERE airportICAO='$this->departureAirport' AND date='$this->dateDeparture' AND beginTime<='$this->departureTime' AND endTime>='$this->departureTime' LIMIT 1") or die(mysql_error());
                $dep_ATCiD = mysql_fetch_row($dep_ATCiD);
                $dep_ATCiD = $dep_ATCiD[0];
                
                $arr_ATCiD = mysql_query("SELECT userId FROM events WHERE airportICAO='$this->arrivalAirport' AND date='$this->dateArrival' AND beginTime<='$this->arrivalTime' AND endTime>='$this->arrivalTime' LIMIT 1") or die(mysql_error());
                $arr_ATCiD = mysql_fetch_row($arr_ATCiD);
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
                // We can generate some alerts
                $alert_departureATC = true;
                $alert_arrivalATC = true;
                
                // We gather the information
                $this->departureAirport = mysql_real_escape_string(htmlspecialchars($departureAirport));
                $this->arrivalAirport = mysql_real_escape_string(htmlspecialchars($arrivalAirport));
                $this->cruiseAltitude = mysql_real_escape_string(htmlspecialchars($cruiseAltitude));
                $this->callsign = mysql_real_escape_string(htmlspecialchars($callsign));
                $this->category = mysql_real_escape_string(htmlspecialchars($category));
                $this->aircraftType = mysql_real_escape_string(htmlspecialchars($aircraftType));
                $this->departureTime = mysql_real_escape_string(htmlspecialchars($departureTime));
                $this->arrivalTime = mysql_real_escape_string(htmlspecialchars($arrivalTime));
                $this->dateDeparture = mysql_real_escape_string(htmlspecialchars($dateDeparture));
                // If the arrival time is before departure time, i assume the arrival will be after midnight of the next day
                if ($this->arrivalTime < $this->departureTime) $this->dateArrival = date('Y-m-d',strtotime($this->dateDeparture."+1 days"));
                // Otherwise i assume the arrival date is the same than the departure one
                else $this->dateArrival = $this->dateDeparture;
                $this->waypoints = mysql_real_escape_string(htmlspecialchars($waypoints));
                $this->comments = mysql_real_escape_string(htmlspecialchars($comments));
				
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
                mysql_query("INSERT INTO flightplans20140113 VALUES('','','$this->callsign','','$this->departureAirport','$this->arrivalAirport','','$this->cruiseAltitude','','$this->dateDeparture','$this->dateArrival','$this->departureTime','$this->arrivalTime','$this->aircraftType','','','','$this->waypoints','$this->category','$this->comments','','');") or die(mysql_error());
                $this->id = mysql_insert_id();
                                
                // We get the ATC user ID
                $dep_ATCiD = mysql_query("SELECT userId FROM events WHERE airportICAO='$this->departureAirport' AND date='$this->dateDeparture' AND beginTime<='$this->departureTime' AND endTime>='$this->departureTime' LIMIT 1") or die(mysql_error());
                $dep_ATCiD = mysql_fetch_row($dep_ATCiD);
                $dep_ATCiD = $dep_ATCiD[0];
                
                $arr_ATCiD = mysql_query("SELECT userId FROM events WHERE airportICAO='$this->arrivalAirport' AND date='$this->dateArrival' AND beginTime<='$this->arrivalTime' AND endTime>='$this->arrivalTime' LIMIT 1") or die(mysql_error());
                $arr_ATCiD = mysql_fetch_row($arr_ATCiD);
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
    
    public function selectById($id)
    {
        if (isset($id))
        {
            if ($id != NULL)
            {
                $flightplans_list = mysql_query("SELECT * FROM flightplans20140113 WHERE flightplanId = $id");
                $flightplan = mysql_fetch_array($flightplans_list);
                
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
                $comments_list = mysql_query("SELECT * FROM flightplan_comments WHERE flightplanId = $id");
                // We initialize the comment array
                $this->comments = array();
                while ($comment = mysql_fetch_array($comments_list))
                {
                    // We select the comment's pseudo and password
                    $pseudo = $comment['pseudo'];
                    $comment = $comment['comment'];
                    // We put it into an array that will be displayed
                    $this->comments[] = array("pseudo" => $pseudo, "comment" => $comment);
                }
                
                // S T A T U S
                // We retrieve the status if the flightplan
                $status_list = mysql_query("SELECT * FROM flightplan_status WHERE flightplanId = $id ORDER BY flightplanStatusId DESC LIMIT 1");
                $status = mysql_fetch_assoc($status_list);
                $this->status = $status['status'];
                
                // H I S T O R Y
                // We retrieve the flightplan's history
                $history_list = mysql_query("SELECT * FROM flightplan_history WHERE flightplanId = $this->id ORDER BY dateTime DESC");
                // We initialize the last updated dateTime value
                $this->lastUpdated = 0;
                // We initialize the history array
                $this->history = array();
                while ($history = mysql_fetch_array($history_list))
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
        }
    }
    
    public function addComment($pseudo,$comment)
    {
        if (isset($pseudo) AND isset($comment))
        {
            if ($pseudo != NULL AND $comment != NULL)
            {
                // We insert the comment
                mysql_query("INSERT INTO flightplan_comments VALUES('','$this->id','$pseudo','$comment','".date('Y-m-d H:i:s')."');") or die(mysql_error());
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
				$dateTime = date("Y-m-d H:i:s");
				$this->lastUpdated = $dateTime;
				$this->status = $status;
                mysql_query("INSERT INTO flightplan_status VALUES('','$userId','$flightplanId','$status','$dateTime');") or die(mysql_error());
            }
        }
    }
    
    public function changeFlightplanInfo($userId,$flightplanId,$variable,$value)
    {
		if (isset($userId) AND isset($flightplanId) AND isset($variable) AND isset($value))
        {
            if ($userId != NULL AND $flightplanId != NULL AND $variable != NULL AND $value != NULL)
            {
				$dateTime = date("Y-m-d H:i:s");
				$this->lastUpdated = $dateTime;
				$this->history[$variable] = array('value' => $value, 'dateTime' => $dateTime);
                mysql_query("INSERT INTO flightplan_history VALUES('','$flightplanId','$userId','$variable','$value','$dateTime');") or die(mysql_error());
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
				$polls_list = mysql_query("SELECT * FROM polls_submits WHERE pollId = $id");
				$poll = mysql_fetch_array($polls_list);

				$this->id = $poll['pollId'];
				$this->userPollId = $poll['userId'];
				$this->dateBegin = $poll['dateBegin'];
				$this->dateEnd = $poll['dateEnd'];
				$this->title = $poll['title'];
				$this->content = $poll['content'];
				
				// We get every choices for a given poll
				$choices_list = mysql_query("SELECT answer FROM polls_answers WHERE pollId = $id");
				// We gather every choice for this poll
				while ($choice = mysql_fetch_array($choices_list))
				{
					$this->choices[] = $choice['answer'];
				}
			}
		}
	}
	
	public function checkAnswer()
	{
		/*
		 * PART TO AVOID VOTING TWICE
		 * Now there is only the IP verification
		 * Later there should be a cookie verification
		 */
		// This is the user IP
		$ip = $_SERVER['REMOTE_ADDR'];
		// I list the IP into the database
		$listPreviousIp = mysql_query("SELECT ip FROM polls_results");
		// Now we consider the user can vote
		$this->okToVote = TRUE;
		
		// For each IP into database
		while ($previousIp = mysql_fetch_array($listPreviousIp))
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
			mysql_query("INSERT INTO polls_results VALUES('','$this->id','$answer','','$ip','".date('Y-m-d H:i:s')."');");
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
