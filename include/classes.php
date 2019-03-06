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
    public $parameters;
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
            $preparedQuery->execute(array(":userCookieId" => purgeInputs($this->userCookieId), ":userId" => purgeInputs($this->id)));
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

    public function create($submittedMail,$submittedPassword,$IP)
    {
        global $db;

        $Mail = purgeInputs($submittedMail);
        $Password = purgeInputs($submittedPassword);

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
                        $preparedQuery->execute(array(":ip" => purgeInputs($IP), ":userId" => purgeInputs($this->id)));
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
            $preparedQuery = $db->prepare("INSERT INTO `users` (mail, password, ip, notifications, cookie, userParameters) VALUES(:Mail, :Password, :IP, '0', '', '');");
            $preparedQuery->execute(array(":Mail" => purgeInputs($Mail), ":Password" => purgeInputs($Password), ":IP" => purgeInputs($IP)));
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
        $preparedQuery->execute(array(":notification" => purgeInputs($notification), ":userId" => purgeInputs($this->id)));
        $this->notifications = $notification;
    }

    /* Function to change other parameters
     */
    public function changeParameters($parameters)
    {
        global $db;
        if ($this->parameters != null)
        {
            foreach ($this->parameters as $parameter => $value)
            {
                $checkedParameters[$parameter] = $value;
            }
        }
        foreach ($parameters as $parameter => $value)
        {
            $checkedParameters[$parameter] = purgeInputs($value);
        }
        $jsonUserParameters = json_encode($checkedParameters);
        $preparedQuery = $db->prepare("UPDATE users SET userParameters=:jsonUserParameters WHERE userId=:userId;");
        $preparedQuery->execute(array(":jsonUserParameters" => $jsonUserParameters, ":userId" => purgeInputs($this->id)));
        $this->parameters = $parameters;
    }

    /* Function to add/change the name of the ATC
     * it would appear when a session is shown on the main page
     * /!\ Requires the "users_names" table
     */
    public function changeName($submittedName)
    {
        global $db;
        $name = purgeInputs($submittedName);
        $preparedQuery = $db->prepare("INSERT INTO users_names (userId, userName) VALUES (:userId, :name)");
        $preparedQuery->execute(array(":userId" => $this->id, ":name" => $name));
        $this->name = $name;
    }

    // Function to check wether an email/password pair is correct
    public function checkUserLogin($email,$password)
    {
        global $db;

        // We treat the inputs to avoid any issue
        $inputEmail = purgeInputs($email);
        $inputPassword = purgeInputs($password);

        // We first check if the email exists
        $userInfoList = $db->query("SELECT * FROM users WHERE mail = '$inputEmail'");
        //$userInfo = $userInfoList->fetch(PDO::FETCH_ASSOC);
        $userInfos = $userInfoList->fetchAll(PDO::FETCH_ASSOC);

        $wrong_login = true;

        foreach ($userInfos as $userInfo)
        {
          // If the email exists
          if (isset($userInfo) AND sizeof($userInfo) != 0)
          {
            // We check if the password == the db_password
            $db_password = $userInfo['password'];
            if ($db_password == $inputPassword OR $inputPassword == md5($db_password))
            {
              $wrong_login = false;
              $this->selectById($userInfo['userId']);
            }
          }
        }

        return $wrong_login;
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

        $this->name         =   purgeInputs($AirportName);
        $this->icao         =   purgeInputs($AirportICAO);

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
            $preparedQuery = $db->prepare("INSERT INTO `airports` (name, ICAO) VALUES(:AirportName, :AirportICAO);");
            $preparedQuery->execute(array(":AirportName" => $AirportName, ":AirportICAO" => $AirportICAO));
        }
    }
}


class Event
{
    public $airportICAO;
    public $airportName;
    public $airportCity;
    public $airportCountry;
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
        if (isset($AirportICAO) AND $AirportICAO != NULL)
        {
            $this->airportICAO = strtoupper($AirportICAO);
        }
        // Otherwise we do not create an event
        else
        {
            $this->requestNewEvent = false;
        }

        // We check if there is a date called
        if (isset($Year) AND isset($Month) AND isset($Day) AND $Year != NULL AND $Month != NULL AND $Day != NULL)
        {
            $this->date = date('Y-m-d',strtotime($Year.'-'.$Month.'-'.$Day));

            // We transform each Hour+Minutes into Time format
            $this->beginTime = date('H:i:s',  strtotime($BeginHour.':'.$BeginMinutes));
            $this->endTime = date('H:i:s',  strtotime($EndHour.':'.$EndMinutes));

            // Security to avoid "0 hours" events OR if the egin time occurs after the end time
            if ($this->beginTime >= $this->endTime AND $this->endTime != "00:00:00")
            {
                $this->requestNewEvent = false;
                $this->error = "Please check your date and times";
            }

        }
        // Otherwise we do not create an event
        else            $this->requestNewEvent = false;

        // It is possible the userId has already been defined
        // and does not requires a session open (eg the API).
        if (!isset($this->userId) OR $this->userId == NULL)
        {
          $this->userId       =   $_SESSION['id'];
        }

        // If the user is not connected (password not correct)
        if ($_SESSION['mode'] != 'connected')
        {
          /*
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
            */
            $this->requestNewEvent = false;

            if (isset($this->userId) AND $this->userId != NULL)
            {
              $this->requestNewEvent = true;
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
            $statement = $db->prepare("INSERT INTO events (airportICAO, userId, date, beginTime, endTime, fgcom, teamspeak, transitionLevel, runways, ILS, docsLink, remarks, contactRange, chartAero, chartGround, chartParking, airportLogo)
                VALUES(
                :airportICAO,
                :userId,
                :date,
                :beginTime,
                :endTime,
                :fgcom,
                :teamspeak,
                '',
                '',
                '',
                :docsLink,
                :remarks,
                '',
                '',
                '',
                '',
                '');");

            $statement->execute(array(
                ':airportICAO'      =>  purgeInputs($this->airportICAO),
                ':userId'           =>  purgeInputs($this->userId),
                ':date'             =>  purgeInputs($this->date),
                ':beginTime'        =>  purgeInputs($this->beginTime),
                ':endTime'          =>  purgeInputs($this->endTime),
                ':fgcom'            =>  purgeInputs($this->fgcom),
                ':teamspeak'        =>  purgeInputs($this->teamspeak),
                ':docsLink'         =>  purgeInputs($this->docsLink),
                ':remarks'          =>  purgeInputs($this->remarks)));

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

                $events_list = $db->query("
                    SELECT
                        events.* ,
                        airports_global.globalAirportName AS airportName,
                        airports_global.globalAirportCity AS airportCity,
                        airports_global.globalAirportCountry AS airportCountry
                    FROM events
                    LEFT JOIN airports_global
                    ON events.airportICAO = airports_global.globalAirportICAO
                    WHERE events.eventId = $id
                    LIMIT 0, 1");
                $event = $events_list->fetch(PDO::FETCH_ASSOC);

                $this->id = $event['eventId'];
                $this->airportICAO = strtoupper($event['airportICAO']);
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

                $this->airportName = $event['airportName'];
                $this->airportCity = $event['airportCity'];
                $this->airportCountry = $event['airportCountry'];
            }
        }
    }

    public function updateEvent()
    {
        if ($_SESSION['mode'] == 'connected')
        {
            global $db;

            $this->id = purgeInputs($this->id);
            $this->airportICAO = purgeInputs($this->airportICAO);
            $this->date = purgeInputs($this->date);
            $this->beginTime = purgeInputs($this->beginTime);
            $this->endTime = purgeInputs($this->endTime);
            $this->fgcom = purgeInputs($this->fgcom);
            $this->teamspeak = purgeInputs($this->teamspeak);
            $this->transitionLevel = purgeInputs($this->transitionLevel);
            $this->runways = purgeInputs($this->runways);
            $this->ils = purgeInputs($this->ils);
            $this->docsLink = purgeInputs($this->docsLink);
            $this->remarks = purgeInputs($this->remarks);

            $query = "UPDATE `events` SET
                `airportICAO` = :airportICAO,
                `date` = :date,
                `beginTime` = :beginTime,
                `endTime` = :endTime,
                `fgcom` = :fgcom,
                `teamspeak` = :teamspeak,
                `transitionLevel` = :transitionLevel,
                `runways` = :runways,
                `ILS` = :ils,
                `docsLink` = :docsLink,
                `remarks` = :remarks
                WHERE `eventId` = :eventId ;";

            $preparedQuery = $db->prepare($query);

            $preparedQuery->bindValue(":airportICAO",$this->airportICAO);
            $preparedQuery->bindValue(":date",$this->date);
            $preparedQuery->bindValue(":beginTime",$this->beginTime);
            $preparedQuery->bindValue(":endTime",$this->endTime);
            $preparedQuery->bindValue(":fgcom",$this->fgcom);
            $preparedQuery->bindValue(":teamspeak",$this->teamspeak);
            $preparedQuery->bindValue(":transitionLevel",$this->transitionLevel);
            $preparedQuery->bindValue(":runways",$this->runways);
            $preparedQuery->bindValue(":ils",$this->ils);
            $preparedQuery->bindValue(":docsLink",$this->docsLink);
            $preparedQuery->bindValue(":remarks",$this->remarks);
            $preparedQuery->bindValue(":eventId",$this->id);

            if ($preparedQuery->execute())
            {
                return true;
            }
            else
            {
                return false;
            }

        }
    }

    public function getATCSessions($beginDate,$limitDate)
    {
        // Return the Ids' of events between beginDate and endDate
        global $db;

        $events = Array();

        $eventsList = $db->query("SELECT eventId FROM events WHERE date >= '$beginDate' AND date <= '$limitDate'");

        if ($eventsList != NULL)
        {
            foreach ($eventsList as $event)
            {
                $events[] = $event['eventId'];
            }
        }

        return $events;
    }
}

class SpecialEvent
{
    public $id;
    public $creatorId;
    public $title;
    public $description;
    public $url;
    public $dateBegin;
    public $dateEnd;
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

                $preparedQuery = $db->prepare("INSERT INTO specialEvents_pilots (specialEventsId, callsign, participation, dateTime) VALUES(:id, :callsign, :participation, NOW());");

                $preparedQuery->bindValue(':id',purgeInputs($this->id));
                $preparedQuery->bindValue(':callsign',purgeInputs($callsign));
                $preparedQuery->bindValue(':participation',purgeInputs($participation));

                $preparedQuery->execute();
                $this->pilotsList = Array();
                $this->selectById($this->id);
            }
        }
    }

    public function addEventToSpecialEvent($eventId, $userId)
    {
        if (isset($eventId) AND isset($userId))
        {
            if ($eventId != NULL AND $userId != NULL)
            {
                global $db;

                // We check if the event is already listed
                if (array_search($eventId, $this->eventsList) === false)
                {
                    $preparedQuery = $db->prepare("INSERT INTO specialEvents_airports (specialEventsId, eventId, userId, confirmed) VALUES(:id, :eventId, :userId, '1');");

                    $preparedQuery->bindValue(':id',purgeInputs($this->id));
                    $preparedQuery->bindValue(':callsign',purgeInputs($eventId));
                    $preparedQuery->bindValue(':participation',purgeInputs($userId));

                    $preparedQuery->execute();
                }
            }
        }
    }

    public function removeEventFromSpecialEvent($eventId)
    {
        if (isset($eventId))
        {
            if ($eventId != NULL)
            {
                global $db;

                $preparedQuery = $db->prepare("DELETE FROM specialEvents_airports WHERE eventId = $eventId;");
                $preparedQuery->execute();
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
                $this->dateBegin = $specialEvent['dateBegin'];
                $this->dateEnd = $specialEvent['dateEnd'];

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
    public $error = Array();

    function checkInformation()
    {
        // We check if all the information was given
        if (!isset($this->departureAirport) OR !isset($this->arrivalAirport) OR !isset($this->callsign) OR !isset($this->departureTime) OR !isset($this->arrivalTime) OR !isset($this->dateDeparture))
        {
            // In this case we have data missing
            $this->error[] = 'data is missing';
        }
        else
        {
            // We check wether the information is fine
            // In particular, the airport must contain 4 letters
            // Wrong airport
            if (preg_match("#^[a-zA-z]{4}$#",$this->departureAirport) == FALSE)
            {
                $this->error[] = 'invalid departure airport';
            }
            // Wrong airport
            if (preg_match("#^[a-zA-z]{4}$#",$this->arrivalAirport) == FALSE)
            {
                $this->error[] = 'invalid arrival airport';
            }
            // Wrong callsign
            if ($this->callsign == NULL OR $this->callsign == 'Callsign')
            {
                $this->error[] = 'invalid callsign';
            }
            // Wrong departureTime
            if ($this->departureTime == NULL OR preg_match("#^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]((:[0-5][0-9])?)$#", $this->departureTime) == FALSE)
            {
                $this->error[] = 'invalid departure time '.$this->departureTime;
            }
            // Wrong arrivalTime
            if ($this->arrivalTime == NULL OR preg_match("#^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]((:[0-5][0-9])?)$#", $this->arrivalTime) == FALSE)
            {
                $this->error[] = 'invalid arrival time '.$this->arrivalTime;
            }
            // Managing the date/times
            if ($this->departureTime == $this->arrivalTime)
            {
                $this->error[] = "arrival time can't be the same than departure time";
            }
            if ($this->dateDeparture == NULL)
            {
                $this->error[] = 'invalid departure date';
            }
            if (isset($this->dateArrivalManual) AND $this->dateArrivalManual != NULL)
            {
                $completeDepartureTime = date("Y-m-d H:i:s",strtotime($this->dateDeparture." ".$this->departureTime));
                $completeArrivalTime = date("Y-m-d H:i:s",strtotime($this->dateArrivalManual." ".$this->arrivalTime));
                if ($completeArrivalTime > date("Y-m-d H:i:s",strtotime($completeDepartureTime." + 24 hours")))
                {
                    $this->error[] = 'a flightplan cannot be more than 24 hours';
                }
            }
        }
        return $this->error;
    }

    function create($dateDeparture,$dateArrival,$departureAirport,$arrivalAirport,$alternateDestination,$cruiseAltitude,$trueAirspeed,$callsign,$pilotName,$airline,$flightNumber,$category,$aircraftType,$departureTime,$arrivalTime,$waypoints,$soulsOnBoard,$fuelTime,$comments)
    {

        // We gather the information
        $this->departureAirport = $departureAirport;
        $this->arrivalAirport = $arrivalAirport;
        $this->alternateDestination = ($alternateDestination != NULL ? $alternateDestination : '');
        $this->cruiseAltitude = ($cruiseAltitude != NULL ? $cruiseAltitude : '');
        $this->trueAirspeed = ($trueAirspeed != NULL ? $trueAirspeed : '');
        $this->callsign = $callsign;
        $this->airline = ($airline != NULL ? $airline : '');
        $this->flightNumber = ($flightNumber != NULL ? $flightNumber : '');
        $this->category = ($category != NULL ? $category : 'IFR');
        $this->aircraftType = ($aircraftType != NULL ? $aircraftType : '');
        $this->departureTime = $departureTime;
        $this->arrivalTime = $arrivalTime;
        $this->dateDeparture = $dateDeparture;
        // If the arrival time is before departure time, i assume the arrival will be after midnight of the next day
        if ($this->arrivalTime < $this->departureTime) $this->dateArrival = date('Y-m-d',strtotime($this->dateDeparture."+1 days"));
        // Otherwise i assume the arrival date is the same than the departure one
        else $this->dateArrival = $this->dateDeparture;

        // Specially added for ATC-pie
        $this->dateArrivalManual = $dateArrival;

        $this->waypoints = ($waypoints != NULL ? $waypoints : '');
        $this->soulsOnBoard = ($soulsOnBoard != NULL ? $soulsOnBoard : '');
        $this->fuelTime = ($fuelTime != NULL ? $fuelTime : '');
        $this->pilotName = ($pilotName != NULL ? $pilotName : '');
        $this->comments = ($comments != NULL ? $comments : '');
        $this->status = 'filed';

        // We check if all information given is fine
        $this->checkInformation();

        global $db;

        // We check if there is another flightplan with the same information
        $similarFlightplanList = $db->query("SELECT flightplanId FROM flightplans20140113 WHERE dateDeparture='".$this->dateDeparture."' AND callsign='".$this->callsign."' AND departureTime='".$this->departureTime."' AND airportICAOFrom='".$this->departureAirport."' AND arrivalTime='".$this->arrivalTime."' AND airportICAOTo='".$this->arrivalAirport."'");
        $similarFlightplans = Array();
        if (isset($similarFlightplanList) AND $similarFlightplanList != NULL)
        {
            foreach ($similarFlightplanList as $similarFlightplan)
            {
                $similarFlightplans[] = $similarFlightplanList->fetch(PDO::FETCH_ASSOC);
            }
        }
        if (sizeof($similarFlightplans) > 0)
        {
            $this->error[] = "a similar flight plan has already been filed";
        }

        // If there are no error
        if (sizeof($this->error) == 0)
        {

            // We can generate some alerts
            $alert_departureATC = true;
            $alert_arrivalATC = true;

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
            $preparedQuery = $db->prepare("INSERT INTO flightplans20140113 (eventId, callsign, airline, flightNumber, airportICAOFrom, airportICAOTo, alternateDestination, cruiseAltitude, `trueAirspeed`, dateDeparture, dateArrival, departureTime, arrivalTime, aircraft, soulsOnBoard, fuelTime, pilotName, waypoints, category, comments, ATCId, ATCNotes)
                                            VALUES ('0', :callsign, :airline, :flightNumber, :departureAirport, :arrivalAirport, :alternateDestination, :cruiseAltitude, :trueAirspeed, :dateDeparture, :dateArrival, :departureTime, :arrivalTime, :aircraftType, :soulsOnBoard, :fuelTime, :pilotName, :waypoints, :category, :comments, '', '');");
            $preparedQuery->execute(array(
                ":callsign"             => purgeInputs($this->callsign),
                ":airline"              =>  purgeInputs($this->airline),
                ":flightNumber"         =>  purgeInputs($this->flightNumber),
                ":departureAirport"     =>  purgeInputs($this->departureAirport),
                ":arrivalAirport"       =>  purgeInputs($this->arrivalAirport),
                ":alternateDestination" =>  purgeInputs($this->alternateDestination),
                ":cruiseAltitude"       =>  purgeInputs($this->cruiseAltitude),
                ":trueAirspeed"         =>  purgeInputs($this->trueAirspeed),
                ":dateDeparture"        =>  purgeInputs($this->dateDeparture),
                ":dateArrival"          =>  purgeInputs($this->dateArrival),
                ":departureTime"        =>  purgeInputs($this->departureTime),
                ":arrivalTime"          =>  purgeInputs($this->arrivalTime),
                ":aircraftType"         =>  purgeInputs($this->aircraftType),
                ":soulsOnBoard"         =>  purgeInputs($this->soulsOnBoard),
                ":fuelTime"             =>  purgeInputs($this->fuelTime),
                ":pilotName"            =>  purgeInputs($this->pilotName),
                ":waypoints"            =>  purgeInputs($this->waypoints),
                ":category"             =>  purgeInputs($this->category),
                ":comments"             =>  purgeInputs($this->comments),
            ));
            $this->id = $db->lastInsertId();
            // I also insert the default comment entered by the pilot
            $preparedQuery = $db->prepare("INSERT INTO flightplan_comments (flightplanId, pseudo, comment, dateTime) VALUES(:id, :callsign, :comments, '".date("Y-m-d H:i:s")."');");
            $preparedQuery->execute(array(":id" => purgeInputs($this->id), ":callsign" => purgeInputs($this->callsign), ":comments" => purgeInputs($this->comments)));
            // I also insert the status of the flightplan
            $preparedQuery = $db->prepare("INSERT INTO flightplan_status (userId, flightplanId, status, dateTime) VALUES('9999', :id, :status, '".date("Y-m-d H:i:s")."');");
            $preparedQuery->execute(array(":id" => purgeInputs($this->id), ":status" => purgeInputs($this->status)));

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
                mail($dep_ATCMail, 'New flightplan : '.$this->callsign, $this->callsign.' wants to take off at '.$this->departureTime.' from your airport '.$this->departureAirport.' -- http://flightgear-atc.alwaysdata.com/edit_flightplan.php?idFlightplan='.$this->id);
            }
            if (getInfo("notifications", "users", "userId", $arr_ATCiD) == 1)
            {
                $arr_ATCMail = getInfo('mail', 'users', 'userId', $arr_ATCiD);
                mail($arr_ATCMail, 'New flightplan : '.$this->callsign, $this->callsign.' wants to land at '.$this->arrivalTime.' on your airport '.$this->arrivalAirport.' -- http://flightgear-atc.alwaysdata.com/edit_flightplan.php?idFlightplan='.$this->id);
            }
            // There is no data missing
            $this->dataMissing = false;
        }
    }

    // Function to edit a flight plan
    public function editFlightplan()
    {
        global $db;

        // We check the information
        $this->checkInformation();

        // If no error were detected
        if (sizeof($this->error) == 0)
        {
            if ($this->dateArrival < $this->dateDeparture) $this->dateArrival = $this->dateDeparture;
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
                    `trueAirspeed` = :trueSpeed,
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
                ":callsign"             =>  purgeInputs($this->callsign),
                ":airline"              =>  purgeInputs($this->airline),
                ":flightNumber"         =>  purgeInputs($this->flightNumber),
                ":departureAirport"     =>  purgeInputs($this->departureAirport),
                ":arrivalAirport"       =>  purgeInputs($this->arrivalAirport),
                ":alternateDestination" =>  purgeInputs($this->alternateDestination),
                ":cruiseAltitude"       =>  purgeInputs($this->cruiseAltitude),
                ":trueSpeed"            =>  purgeInputs($this->trueAirspeed),
                ":dateDeparture"        =>  purgeInputs($this->dateDeparture),
                ":dateArrival"          =>  purgeInputs($this->dateArrival),
                ":departureTime"        =>  purgeInputs($this->departureTime),
                ":arrivalTime"          =>  purgeInputs($this->arrivalTime),
                ":aircraftType"         =>  purgeInputs($this->aircraftType),
                ":soulsOnBoard"         =>  purgeInputs($this->soulsOnBoard),
                ":fuelTime"             =>  purgeInputs($this->fuelTime),
                ":pilotName"            =>  purgeInputs($this->pilotName),
                ":waypoints"            =>  purgeInputs($this->waypoints),
                ":category"             =>  purgeInputs($this->category),
                ":comments"             =>  purgeInputs($this->comments),
                ":flightplanId"         =>  purgeInputs($this->id)
            ));
        }
    }

    public function getFlightplans()
    {
        global $db;

        // Callsign
        if (isset($this->callsign) AND $this->callsign != NULL)
        {
            $queryCallsign = "FP.callsign = '$this->callsign'";
        }
        else
        {
            $queryCallsign = "FP.callsign LIKE '%'";
        }
        // Date
        if (isset($this->dateDeparture) AND $this->dateDeparture != NULL)
        {
            $queryDate = "(FP.dateDeparture = '$this->dateDeparture' OR FP.dateArrival = '$this->dateArrival')";
        }
        else
        {
            $queryDate = "FP.dateDeparture LIKE '%'";
        }
        // Airport
        if (isset($this->departureAirport) AND $this->departureAirport != NULL)
        {
            $queryICAO = "(FP.airportICAOFrom = '$this->departureAirport' OR FP.airportICAOTo = '$this->departureAirport')";
        }
        else
        {
            $queryICAO = "(FP.airportICAOFrom LIKE '%' OR FP.airportICAOTo LIKE '%')";
        }
        // Status
        if (isset($this->status) AND $this->status != NULL)
        {
            //$queryStatus = "HAVING FPStatus.status = '".$this->status."'";
            $queryStatus = "AND FPStatus.status = '".$this->status."'";
        }
        else
        {
            $queryStatus = "";
        }
        $queryLimit = "LIMIT 0,30";

        // $query = "SELECT FP.flightplanId
        //             FROM (
        //                 SELECT * FROM flightplan_status
        //                 ORDER BY flightplan_status.dateTime DESC) AS FPStatus
        //                     JOIN (
        //                     SELECT * FROM flightplans20140113) AS FP
        //                         ON FP.flightplanId = FPStatus.flightplanId
        //             WHERE $queryCallsign AND $queryDate AND $queryICAO
        //             $queryStatus;";
        // $query = "SELECT FP.flightplanId,FPStatus.*
        //             FROM (
        //                 SELECT * FROM flightplan_status
        //                 ORDER BY flightplan_status.dateTime DESC) AS FPStatus
        //                     JOIN (
        //                     SELECT * FROM flightplans20140113) AS FP
        //                         ON FP.flightplanId = FPStatus.flightplanId
        //             WHERE $queryCallsign AND $queryDate AND $queryICAO
        //             $queryLimit
        //             $queryStatus;";
        $query = "SELECT fpstatus.flightplanId
                    FROM flightplan_status fpstatus
                    JOIN flightplans20140113 FP ON fpstatus.flightplanId = FP.flightplanId
                    WHERE $queryCallsign AND $queryDate AND $queryICAO
                    GROUP BY fpstatus.flightplanId
                    ORDER BY FP.departureTime ASC
                    $queryLimit;";
        $queryPrepare = $db->prepare($query);
        $queryPrepare->execute();
        $flightplans = $queryPrepare->fetchAll();

        return $flightplans;
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

                $pseudo = purgeInputs($pseudo);
                $comment = purgeInputs($comment);

                // We insert the comment
                $preparedQuery = $db->prepare("INSERT INTO flightplan_comments (flightplanId, pseudo, comment, dateTime) VALUES(:id, :pseudo, :comment, :date);");
                $preparedQuery->execute(array(":id" => purgeInputs($this->id), ":pseudo" => $pseudo, ":comment" => $comment, ":date" => date('Y-m-d H:i:s')));
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
                $this->lastUpdated = purgeInputs($dateTime);
                $this->status = purgeInputs($status);
                $preparedQuery = $db->prepare("INSERT INTO flightplan_status (userId, flightplanId, status, dateTime) VALUES(:userId, :flightplanId, :status, :dateTime);");
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
                $this->lastUpdated = purgeInputs($dateTime);
                $this->history[$variable] = array('value' => purgeInputs($value), 'dateTime' => purgeInputs($dateTime));
                $preparedQuery = $db->prepare("INSERT INTO flightplan_history (flightplanId, userId, variable, value, dateTime) VALUES(:flightplanId, :userId, :variable, :value, :dateTime);");
                $preparedQuery->execute(array(":flightplanId" => purgeInputs($flightplanId), ":userId" => purgeInputs($userId), ":variable" => purgeInputs($variable), ":value" => purgeInputs($value), ":dateTime" => $dateTime));
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
                $preparedQuery = $db->prepare("INSERT INTO flightplan_emails (flightplanId, email, privateKey, dateTime) VALUES(:id, :email, :privateKey, NOW());");
                $preparedQuery->execute(array(":id" => purgeInputs($this->id), ":email" => purgeInputs($this->email), ":privateKey" => purgeInputs($this->privateKey)));
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
                Note : This feature is in Beta. Please <a href='./contact.php' style='color: #aaa;'>contact me</a> for :
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
                $preparedQuery = $db->prepare("INSERT INTO polls_results (pollId, result, cookieId, ip, dateResult) VALUES(:id, :answer, '', :ip, :date);");
                $preparedQuery->execute(array(":id" => purgeInputs($this->id), ":answer" => purgeInputs($answer), ":ip" => purgeInputs($ip), ":date" => date('Y-m-d H:i:s')));
                // We display a message
                echo "<div class='warning'>Your vote has been accepted. Thank you.
                <br/><br/>
                Note : This feature is in Beta. Please <a href='./contact.php' style='color: #aaa;'>contact me</a> for :
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

class APIReponse
{
    public $APIversion;
    public $query;
    public $reponse = Array();

    function __construct()
    {
        // TODO
    }
}

class Depeche
{
    public $depecheId;
    public $title;
    public $content;
    public $abstractImg;
    public $type;
    public $importance;
    public $maxOccurences;
    public $occurences; // For a selected depeche
    public $dateValidated; // For a selected depeche
    public $limitDateValidity; // For a selected depeche
    public $validFrom;
    public $validTo;
    public $minNbControlledAirports;
    public $maxNbControlledAirports;
    public $controlledAirports; // To know which airports are controlled
    public $conditions;
    private $depecheList = Array();
    public $validatedDepechesList = Array();

    public function selectDepecheById($id)
    {
        global $db;

        $listDepeches = $db->query("SELECT * FROM `depecheList`
                LEFT JOIN depecheValidation ON depecheList.depecheId = depecheValidation.depecheIdValidated
                WHERE depecheList.depecheId = $id
                ORDER BY depecheValidation.depecheValidationId DESC LIMIT 0,1");

        if ($listDepeches != NULL)
        {
            $depeche = $listDepeches->fetch(PDO::FETCH_ASSOC);

            $this->depecheId = $depeche['depecheId'];
            $this->title = $depeche['title'];
            $this->content = $depeche['content'];
            $this->abstractImg = $depeche['abstractImg'];
            $this->type = $depeche['type'];
            $this->importance = $depeche['importance'];
            $this->maxOccurences = $depeche['maxOccurences'];
            $this->occurences = $depeche['occurences'];
            $this->dateValidated = $depeche['dateValidated'];
            $this->limitDateValidity = $depeche['limitDateValidity'];
            $this->validFrom = $depeche['validFrom'];
            $this->validTo = $depeche['validTo'];
            $this->minNbControlledAirports = $depeche['minNbControlledAirports'];
            $this->maxNbControlledAirports = $depeche['maxNbControlledAirports'];
            $this->controlledAirports = explode(',',$depeche['controlledAirports']);
            $this->conditions = $depeche['conditions'];
        }
    }

    public function listAvailableDepeches()
    {
        global $db;

        $listSqlDepeches = $db->query("SELECT * FROM depecheList WHERE
                (validFrom >= DATE(NOW()) OR validFrom IS NULL)
                AND (validTo >= DATE(NOW()) OR validTo IS NULL)");

        $this->depecheList = Array();

        if ($listSqlDepeches != NULL)
        {
            foreach ($listSqlDepeches as $depeche)
            {
                $this->depecheList[] = $depeche['depecheId'];
            }
        }

        return $this->depecheList;
    }

    public function validateDepeche()
    {
        global $db;

        // We first list available depeches
        $this->listAvailableDepeches();

        // We initialize an array with potential depeches
        $potentialDepeche = Array();

        // If there are no depeches, we can't do anything ...
        if (empty($this->depecheList))
        {
        }


        // If there is at least one depeche
        else
        {
            // Get number of controlled airports
            $ATCSessions = new Event();
            $controlledAirports = $ATCSessions->getATCSessions(date('Y-m-d'), date('Y-m-d'));
            $nbControlledAirports = sizeof($controlledAirports);

            // We list all depeches available
            // !!! WARNING THIS IS THE ENGINE OF DEPECHE GENERATION !!!
            foreach ($this->depecheList as $depeche)
            {
                $this->selectDepecheById($depeche);

                /*
                // STEP 1 : number of controlled airports
                if ($nbControlledAirports >= $this->minNbControlledAirports AND $nbControlledAirports <= $this->maxNbControlledAirports)
                {
                    // STEP 2 : occurences < maxOccurences
                    // The occurence can be > maxOccurences if the validation date is prior to 1 week
                    if ($this->occurences <= $this->maxOccurences OR ($this->occurences > $this->maxOccurences AND $this->dateValidated <= date('Y-m-d',strtotime(date('Y-m-d')." - 7 days"))))
                    {
                        // We put this data into the potentialDepeche array
                        $potentialDepeche['depecheId'][$this->depecheId] = $this->depecheId;
                        $potentialDepeche['importance'][$this->depecheId] = $this->importance;
                        $potentialDepeche['conditions'][$this->depecheId] = $this->conditions;
                        $potentialDepeche['occurences'][$this->depecheId] = $this->occurences;
                        $potentialDepeche['maxOccurences'][$this->depecheId] = $this->maxOccurences;
                        $potentialDepeche['occurenceRatio'][$this->depecheId] = $this->occurences/$this->maxOccurences;

                        $ponderationOccurences = 5;
                        $ponderationImportance = 7;
                        $score[$this->depecheId] = ((1/($this->occurences/$this->maxOccurences+1)*$ponderationOccurences) + (($this->importance/10)*$ponderationImportance))/($ponderationImportance+$ponderationOccurences);

                        $potentialDepeche['score'][$this->depecheId] = $score[$this->depecheId];
                    }
                }
                 */

                /**************************************
                 * ********** NEW SCORE DEBUG *********/

                // We put this data into the potentialDepeche array
                $debugDepeche['depecheId'][$this->depecheId] = $this->depecheId;
                $debugDepeche['importance'][$this->depecheId] = $this->importance;
                $debugDepeche['conditions'][$this->depecheId] = $this->conditions;
                $debugDepeche['occurences'][$this->depecheId] = $this->occurences;
                $debugDepeche['maxOccurences'][$this->depecheId] = $this->maxOccurences;
                $debugDepeche['occurenceRatio'][$this->depecheId] = $this->occurences/$this->maxOccurences;
                // Defining the timelapse
                $lastValidationDateQuery = $db->query("SELECT `dateValidated` FROM depecheValidation WHERE depecheIdValidated = $this->depecheId ORDER BY dateValidated DESC LIMIT 0,1");
                $lastValidationDate = $lastValidationDateQuery->fetchAll(PDO::FETCH_ASSOC);
                if (empty($lastValidationDate))
                {
                    $timelapse = 1;
                }
                else
                {
                    foreach ($lastValidationDate as $validationDate)
                    {
                        $dateValidated = new DateTime($validationDate['dateValidated']);
                        $dateToday = new DateTime(date("Y-m-d H:i:s"));
                        $interval = $dateValidated->diff($dateToday);
                        $timelapse = $interval->d;
                    }
                }

                $ponderationImportance = 7;
                $ponderationOccurences = 5;

                $importance = $this->importance/10;

                $factor1 = 1-1/(1+($timelapse-$this->occurences+1)/(($this->occurences+1)*10));
                $factor2 = (($factor1*$ponderationOccurences)+($importance*$ponderationImportance))/($ponderationImportance+$ponderationOccurences);
                $score2[$this->depecheId] = round(abs($factor2),3);

                // STEP 1 : number of controlled airports
                if ($nbControlledAirports >= $this->minNbControlledAirports AND $nbControlledAirports <= $this->maxNbControlledAirports)
                {
                    $potentialDepeche['score'][$this->depecheId] = $score2[$this->depecheId];
                }

                $array_for_debug[$this->depecheId] = Array('timelapse' => $timelapse, 'occurences' => $this->occurences, 'factor1' => $factor1, 'factor2' => $factor2, 'score2' => $score2[$this->depecheId]);

            }

            /*echo "<pre>";
            print_r($array_for_debug);
            echo "</pre>";*/

            // FOR DEBUG
            if ($score2 != NULL)
            {
                $insertScoreQuery = $db->prepare("INSERT INTO depecheStats (date, stats) VALUES(:date, :score);");
                $insertScoreQuery->execute(array(":date"=>date('Y-m-d'),":score"=>var_export($score2,true)));
            }

            // If there are some potential depeches
            if (!empty($potentialDepeche))
            {
                // We initialize the validated depeche and maxImportance variables
                $validatedDepeche = 0;
                $minScore = 0;

                ksort($potentialDepeche['score']);
                foreach ($potentialDepeche['score'] as $depecheId => $value)
                {
                    if ($value > $minScore)
                    {
                        $validatedDepeche = $depecheId;
                        $minScore = $value;
                    }
                }

                // We select the validated depeche
                $this->selectDepecheById($validatedDepeche);

                // We increment the occurence
                $this->occurences = $this->occurences + 1;

                // Date validated = limitDateValidity = today
                //  (this can change !)
                $this->dateValidated = date('Y-m-d H:i:s');
                $this->limitDateValidity = date('Y-m-d',strtotime(date('Y-m-d')."+ 1 day"));

                // Controlled airports
                $this->controlledAirports = $controlledAirports;


                // We insert the validated depeche into the table depecheValidation
                $preparedQuery = $db->prepare("INSERT INTO depecheValidation (depecheIdValidated, dateValidated, limitDateValidity, controlledAirports, occurences) VALUES(:depecheId, :dateValidated, :limitDateValidity, :controlledAirports, :occurences);");

                $preparedQuery->bindValue(':depecheId',$this->depecheId);
                $preparedQuery->bindValue(':dateValidated',$this->dateValidated);
                $preparedQuery->bindValue(':limitDateValidity',$this->limitDateValidity);
                // Implode is mandatory as it is an array
                $preparedQuery->bindValue(':controlledAirports',implode(',',$this->controlledAirports));
                $preparedQuery->bindValue(':occurences',$this->occurences);
                $preparedQuery->execute();

            }
        }
    }

    public function listValidatedDepeche($day = NULL)
    {
        global $db;

        // If no day has been specified we select for today
        if ($day == NULL)
        {
            $listSqlValidatedDepeche = $db->query("SELECT * FROM depecheValidation WHERE DATE(dateValidated) = '".date("Y-m-d")."' LIMIT 0,1");
        }
        // Otherwise we select for the date that has been specified
        else
        {
            $listSqlValidatedDepeche = $db->query("SELECT * FROM depecheValidation WHERE DATE(dateValidated) = DATE($day) LIMIT 0,1");
        }

        // Are there some results ?
        if ($listSqlValidatedDepeche != NULL)
        {
            $this->validatedDepechesList = $listSqlValidatedDepeche->fetch(PDO::FETCH_ASSOC);
            // We select the depeche that is validated
            $this->selectDepecheById($this->validatedDepechesList['depecheIdValidated']);
        }

    }

    public function listAllAvailableDepeches()
    {
        global $db;

        $listAllAvailableDepeches = $db->query("SELECT depecheId FROM depecheList ORDER BY validFrom DESC");

        // We initialize the array
        $this->depecheList = Array();

        if ($listAllAvailableDepeches != NULL)
        {
            // We select all the depecheId into the array
            foreach ($listAllAvailableDepeches as $availableDepeche)
            {
                $this->depecheList[] = $availableDepeche['depecheId'];
            }
        }
    }

    public function displayDepeche($content)
    {
        $controlledAirports = Array();

        if ($this->controlledAirports != NULL)
        {
            foreach ($this->controlledAirports as $eventId)
            {
                $controlledAirports[] = getInfo('airportICAO','events','eventId',$eventId);
                $beginTime[] = getInfo('beginTime','events','eventId',$eventId);
                $endTime[] = getInfo('endTime','events','eventId',$eventId);
            }
        }

        // Replaces AIRPORT with the single controlled airport
        if (sizeof($controlledAirports) == 1)
        {
            $content = str_replace("AIRPORT",$controlledAirports[0],$content);
            $content = str_replace("BEGIN_TIME",$beginTime[0],$content);
            $content = str_replace("END_TIME",$endTime[0],$content);
        }

        // More than one airport controlled
        else if (sizeof($controlledAirports) > 1)
        {
            $content = str_replace("DEPARTURE_AIRPORT",$controlledAirports[0],$content);
            $content = str_replace("ARRIVAL_AIRPORT",$controlledAirports[1],$content);
            $content = str_replace("BEGIN_TIME",$beginTime[0],$content);
            $content = str_replace("END_TIME",$endTime[0],$content);
        }

        return $content;
    }
}

?>
