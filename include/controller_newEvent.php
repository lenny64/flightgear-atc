<?php

// Those are the fields which are required as inputs
$requiredFields = Array(
    'newSessionAirportName',
    'newSessionAirportICAO',
    'newSessionYear',
    'newSessionMonth',
    'newSessionDay',
    'newSessionBeginHour',
    'newSessionBeginMinutes',
    'newSessionEndHour',
    'newSessionEndMinutes'    );

// We initialize each variable that will be asked
$Mail = '';
$Password = '';
$AirportName = '';
$AirportICAO = '';
$Year = '';
$Month = '';
$Day = '';
$BeginHour = '';
$BeginMinutes = '';
$EndHour = '';
$EndMinutes = '';
$FGCOM = '';
$TeamSpeak = '';
$DocsLink = '';
$Remarks = '';

// We give clearance to create a new Session by default
$permissionToCreate = true;

// When a new session is detected
if (isset($_GET['newSession']))
{
    // I watch whether every field has been filled
    foreach ($requiredFields as $field)
    {
        // If the field is not well filled
        if (!isset($_POST[$field]) OR $_POST[$field] == NULL)
        {
            // We put all the missing information inside an Array, will be read later
            $missingInformations[] = substr($field,10);
            $permissionToCreate = false;
        }

        eval ( '$' . substr($field,10) . ' = "' . htmlspecialchars($_POST[$field]) . '";');
    }

    if (!isset($_SESSION['id']) OR $_SESSION['id'] == NULL OR $_SESSION['mode'] != 'connected')
    {
        $permissionToCreate = false;
    }

    // If there is a missing information ...
    if (isset($missingInformations) AND $missingInformations != NULL)
    {
        // We indicate information is missing
        echo "<div class='alert alert-danger'>Those information are missing :<br/><ul>";
        // And we list each info missing
        foreach ($missingInformations as $missingInformation)
        {
            echo "<li>".$missingInformation."</li>";
        }
        echo "</ul></div>";
    }

    // We copy less important values by default
    $FGCOM = htmlspecialchars($_POST['newSessionFGCOM']);
    $TeamSpeak = htmlspecialchars($_POST['newSessionTeamSpeak']);
    $DocsLink = htmlspecialchars($_POST['newSessionDocsLink']);
    $Remarks = htmlspecialchars($_POST['newSessionRemarks']);

    // Is it okay to create a new session ?
    if ($permissionToCreate == true)
    {
        // We create an user in charge of the session

        // We first check if he's connected
        if (!isset($User->id))
        {
            // If not we actually create the user
            // $User = new User();
            // $User->create($Mail, $Password, $_SERVER['REMOTE_ADDR']);
        }
        // We create the airport (if exists, won't be added again)
        $Airport = new Airport();
        $Airport->create($AirportName, $AirportICAO);
        // We finally create an event
        $Event = new Event();
        $Event->create($Year,$Month, $Day, $BeginHour, $BeginMinutes, $EndHour, $EndMinutes, $AirportICAO, $FGCOM, $TeamSpeak, $DocsLink, $Remarks);
        // Here we want to be sure the event has been created by classes.php
        if ($Event->eventCreated == true)
        {
            echo "<div class='alert alert-info'>The new session has been created at ".$Event->airportICAO."</div>";
            $highlightEvent = $Event->id;
        }
        // If the event could not have been created
        else
        {
            // We see if there is an error (which is very probable)
            if (isset($Event->error))
            {
                // Is the error present ?
                if ($Event->error != NULL)
                {
                    // If so, we show the error
                    echo "<div class='alert alert-danger'>".$Event->error."</div>";
                }
            }
        }
    }
}


?>
