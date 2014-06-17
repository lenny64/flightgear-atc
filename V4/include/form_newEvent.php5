<?php

// Those are the fields which are required as inputs
$requiredFields = Array(
    'newSessionMail',
    'newSessionPassword',
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
        
        eval ( '$' . substr($field,10) . ' = "' . $_POST[$field] . '";');
    }
    
    // If there is a missing information ...
    if (isset($missingInformations) AND $missingInformations != NULL)
    {
		// We indicate information is missing
		echo "<div class='warning'>Those information are missing :<br/><ul>";
		// And we list each info missing
		foreach ($missingInformations as $missingInformation)
		{
			echo "<li>".$missingInformation."</li>";
		}
		echo "</ul></div>";
	}
			
    // We copy less important values by default
    $FGCOM = mysql_real_escape_string(htmlspecialchars($_POST['newSessionFGCOM']));
    $TeamSpeak = mysql_real_escape_string(htmlspecialchars($_POST['newSessionTeamSpeak']));
    $DocsLink = mysql_real_escape_string(htmlspecialchars($_POST['newSessionDocsLink']));
    $Remarks = mysql_real_escape_string(htmlspecialchars($_POST['newSessionRemarks']));
    
    // Is it okay to create a new session ?
    if ($permissionToCreate == true)
    {
		// We create an user in charge of the session
		
		// We first check if he's connected
        if (!isset($User->id))
        {
			// If not we actually create the user
			$User = new User();
			$User->create($Mail, $Password, $_SERVER['REMOTE_ADDR']);
		}
        // We create the airport (if exists, won't be added again)
        $Airport = new Airport();
        $Airport->create($AirportName, $AirportICAO);
        // We finally create an event
        $Event = new Event();
        $Event->create($Year,$Month, $Day, $BeginHour, $BeginMinutes, $EndHour, $EndMinutes, $AirportICAO, $FGCOM, $TeamSpeak, $DocsLink, $Remarks);
        // Here we want to be sure the event has been created by classes.php5
		if ($Event->eventCreated == true)
		{
			echo "<div class='information'>The new session has been created at ".$Event->airportICAO."</div><br/><br/>";
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
					echo "<div class='warning'>".$Event->error."</div>";
				}
			}
		}
    }
}



// Information to print the new session form
if (isset($_GET['form_newSession']) AND isset($_GET['date']))
{
	if ($_GET['date'] != NULL)
	{
		$getDate = mysql_real_escape_string(htmlspecialchars($_GET['date']));
	}
}

?>

<form action="./index.php5?newSession#calendar_day<?php echo $getDate;?>" method="post" class="form_new_event">
    <?php
    // If we asked the form to open
    if (isset($_GET['form_newSession']) AND isset($_GET['date']))
    {
        if ($_GET['date'] != NULL)
        {
            $getDate = mysql_real_escape_string(htmlspecialchars($_GET['date']));
            $inputDate = true;
            $getYear = date("Y",strtotime($getDate));
            $getMonth = date("m", strtotime($getDate));
            $getDay = date("d", strtotime($getDate));
            
            echo '<div id="div_newSession'.$date.'">';
        }
    }
    // Otherwise, by default we don't show the form
    else
    {
        echo '<div id="div_newSession'.$date.'" style="display: none;">';
    }
    ?>
        <?php
        
        // If the user is already connected, we won't ask him to ident
        if (isset($_SESSION['id']) AND $_SESSION['id'] != NULL AND $_SESSION['mode'] == 'connected')
        {
            ?>
            <input type="hidden" name="<?php echo $requiredFields[0];?>" value="<?php echo getInfo('mail', 'users', 'userId', $_SESSION['id']);?>"/>
            <input type="hidden" name="<?php echo $requiredFields[1];?>" value="<?php echo getInfo('password', 'users', 'userId', $_SESSION['id']);?>"/>
            <div class="information"><b>Good news</b> ! You are connected : <?php echo getInfo('mail', 'users', 'userId', $_SESSION['id']);?>.</div>
            <?php
        }
        
        // Otherwise
        else {
        ?>
        <h2>Identification</h2>
        
        <br/><br/>
        
        <div class="information">First time you create a session ? Remember this password</div>
        <br/>
        <span class="new_event_entry_field">Your email address*</span><br/><input type="text" name="<?php echo $requiredFields[0];?>" value=""/> <span class="input_example">e.g. people@gmail.com</span>
        <br/>
        <span class="new_event_entry_field">Password*</span><br/><input type="password" name="<?php echo $requiredFields[1];?>"/>
        <?php
        }
        ?>
        <br/>
        <h2><img src="./img/scheme_airport.png"/> Location</h2>
        <div>
            <span class="new_event_entry_field">Airport selection : </span>
            <br/>
                <?php
                $airports = mysql_query("SELECT * FROM airports ORDER BY ICAO");
                while ($airport = mysql_fetch_array($airports))
                {
                    $name = $airport['name'];
                    $ICAO = $airport['ICAO'];

                    // We pick the information relevant to the event for the autocompletion
                    // To do that, we only grab the LAST entry (to get updated information) !
                    // If the user is connected, we will look for his information
                    if (isset($_SESSION['id']) AND $_SESSION['id'] != NULL AND $_SESSION['mode'] == 'connected')
                    {
                        $autoCompletionEvent = mysql_query("SELECT * FROM events WHERE airportICAO = '$ICAO' AND remarks != 'openradar' AND userId = ".$_SESSION['id']." ORDER BY eventId DESC LIMIT 1") or die(mysql_error());
                    }
                    // If the user is not connected, we will gather only the last information
                    else
                    {
                        $autoCompletionEvent = mysql_query("SELECT * FROM events WHERE airportICAO = '$ICAO' AND remarks != 'openradar' ORDER BY eventId DESC LIMIT 1") or die(mysql_error());
                    }
                    $autoCompletionEvent = mysql_fetch_assoc($autoCompletionEvent);

                    $beginTime = explode(':',$autoCompletionEvent['beginTime']);
                    $endTime = explode(':',$autoCompletionEvent['endTime']);

                    $fgcom = $autoCompletionEvent['fgcom'];
                    $teamspeak = $autoCompletionEvent['teamspeak'];
                    $docsLink = $autoCompletionEvent['docsLink'];

                    $remarks = str_replace("\n","",$autoCompletionEvent['remarks']);?>
                    <span class="airport_selection_button" onclick="
                            document.getElementById('apt_name').value='<?php echo $name; ?>';
                            document.getElementById('apt_name').style.backgroundColor='#33ee33';
                            document.getElementById('apt_icao').value='<?php echo $ICAO; ?>';
                            document.getElementById('apt_icao').style.backgroundColor='#33ee33';
                            document.getElementById('apt_beginHour').value='<?php echo $beginTime[0];?>';
                            document.getElementById('apt_beginHour').style.backgroundColor='#33ee33';
                            document.getElementById('apt_beginMinutes').value='<?php echo $beginTime[1];?>';
                            document.getElementById('apt_beginMinutes').style.backgroundColor='#33ee33';
                            document.getElementById('apt_endHour').value='<?php echo $endTime[0];?>';
                            document.getElementById('apt_endHour').style.backgroundColor='#33ee33';
                            document.getElementById('apt_endMinutes').value='<?php echo $endTime[1];?>';
                            document.getElementById('apt_endMinutes').style.backgroundColor='#33ee33';
                            document.getElementById('apt_fgcom').value='<?php echo $fgcom; ?>';
                            document.getElementById('apt_fgcom').style.backgroundColor='#33ee33';
                            document.getElementById('apt_teamspeak').value='<?php echo $teamspeak; ?>';
                            document.getElementById('apt_teamspeak').style.backgroundColor='#33ee33';
                            document.getElementById('apt_docslink').value='<?php echo $docsLink; ?>';
                            document.getElementById('apt_docslink').style.backgroundColor='#33ee33';
                            <?php
                            // If the user is connected we will look for his remarks
                            if (isset($_SESSION['id']) AND $_SESSION['id'] != NULL AND $_SESSION['mode'] == 'connected')
                            {    ?>
                            document.getElementById('apt_remarks').value='<?php echo str_replace("\r","",$remarks);?>';
                            document.getElementById('apt_remarks').style.backgroundColor='#33ee33';
                            <?php } // DO NOT FORGET TO CLOSE TAG >>>> ?>">
                    <?php echo $airport['ICAO'];?> 
                    </span>
                <?php }
                ?>
                <br/>
                <span class="airport_selection_button" onclick="
                            document.getElementById('apt_name').value='';
                            document.getElementById('apt_name').style.backgroundColor='#fff';
                            document.getElementById('apt_icao').value='';
                            document.getElementById('apt_icao').style.backgroundColor='#fff';
                            document.getElementById('apt_beginHour').value='';
                            document.getElementById('apt_beginHour').style.backgroundColor='#fff';
                            document.getElementById('apt_beginMinutes').value='';
                            document.getElementById('apt_beginMinutes').style.backgroundColor='#fff';
                            document.getElementById('apt_endHour').value='';
                            document.getElementById('apt_endHour').style.backgroundColor='#fff';
                            document.getElementById('apt_endMinutes').value='';
                            document.getElementById('apt_endMinutes').style.backgroundColor='#fff';
                            document.getElementById('apt_fgcom').value='';
                            document.getElementById('apt_fgcom').style.backgroundColor='#fff';
                            document.getElementById('apt_teamspeak').value='';
                            document.getElementById('apt_teamspeak').style.backgroundColor='#fff';
                            document.getElementById('apt_docslink').value='';
                            document.getElementById('apt_docslink').style.backgroundColor='#fff';
                            document.getElementById('apt_remarks').value='';
                            document.getElementById('apt_remarks').style.backgroundColor='#fff';">
                New airport
                </span>

        </div>
        <br/>
        <span class="new_event_entry_field">Airport name*</span> <input type="text" id="apt_name" name="<?php echo $requiredFields[2];?>"/> <span class="input_example">e.g. Frankfurt/Main</span>
        <span class="new_event_entry_field">ICAO*</span> <input type="text" id="apt_icao" size="5" name="<?php echo $requiredFields[3];?>"/> <span class="input_example">e.g. EDDF</span>
        
        <h2>Date and time</h2>
        <img src="./img/scheme_date.png"/> <span class="new_event_entry_field">Date*</span>
        <input type="text" size="4" name="<?php echo $requiredFields[4];?>" value="<?php if (isset($getYear) and $getYear != NULL) echo $getYear; else { echo "Year"; } ?>"/>
        <select name="<?php echo $requiredFields[5];?>">
            <option value="01" <?php if (isset($getMonth) and $getMonth == "01") echo "selected"; ?> >January</option>
            <option value="02" <?php if (isset($getMonth) and $getMonth == "02") echo "selected"; ?> >February</option>
            <option value="03" <?php if (isset($getMonth) and $getMonth == "03") echo "selected"; ?> >March</option>
            <option value="04" <?php if (isset($getMonth) and $getMonth == "04") echo "selected"; ?> >April</option>
            <option value="05" <?php if (isset($getMonth) and $getMonth == "05") echo "selected"; ?> >May</option>
            <option value="06" <?php if (isset($getMonth) and $getMonth == "06") echo "selected"; ?> >June</option>
            <option value="07" <?php if (isset($getMonth) and $getMonth == "07") echo "selected"; ?> >July</option>
            <option value="08" <?php if (isset($getMonth) and $getMonth == "08") echo "selected"; ?> >August</option>
            <option value="09" <?php if (isset($getMonth) and $getMonth == "09") echo "selected"; ?> >September</option>
            <option value="10" <?php if (isset($getMonth) and $getMonth == "10") echo "selected"; ?> >October</option>
            <option value="11" <?php if (isset($getMonth) and $getMonth == "11") echo "selected"; ?> >November</option>
            <option value="12" <?php if (isset($getMonth) and $getMonth == "12") echo "selected"; ?> >December</option>
        </select>
        <input type="text" size="3" name="<?php echo $requiredFields[6];?>" value="<?php if (isset($getDay) and $getDay != NULL) echo $getDay; else { echo "Days"; } ?>"/> <span class="input_example">Two digits : e.g. 05</span> 
        
        <img src="./img/scheme_time.png"/> <span class="new_event_entry_field">From*</span> 
        <input type="text" size="2" id="apt_beginHour" name="<?php echo $requiredFields[7];?>" onkeyup="if(this.value.length==2) document.getElementById('apt_beginMinutes').focus();"/>
        :
        <input type="text" size="2" id="apt_beginMinutes" name="<?php echo $requiredFields[8];?>" onkeyup="if(this.value.length==2) document.getElementById('apt_endHour').focus();"/>
        UTC <span class="new_event_entry_field">to*</span> 
        <input type="text" size="2" id="apt_endHour" name="<?php echo $requiredFields[9];?>" onkeyup="if(this.value.length==2) document.getElementById('apt_endMinutes').focus();"/>
        :
        <input type="text" size="2" id="apt_endMinutes" name="<?php echo $requiredFields[10];?>"/> UTC
        
        <h2>Communication</h2>
        <span class="new_event_entry_field">FGCom frequency</span> <input type="text" size="4" name="newSessionFGCOM" id="apt_fgcom"/> <span class="input_example">e.g. 120.50</span>
        <br/>
        and/or
        <br/>
        <span class="new_event_entry_field">Mumble information</span> <input type="text" size="15" name="newSessionTeamSpeak" id="apt_teamspeak"/> <span class="input_example">e.g. teamspeak.server.com</span>
        
        <h2>Additional Information</h2>
        <span class="new_event_entry_field">Link to download the airport's charts</span>
        <br/>
        <input type="text" name="newSessionDocsLink" size="30" value="http://" onfocus="this.value='';" id="apt_docslink"/> <span class="input_example">e.g. http://www.vacc-sag.org/airport/EDDF</span>
        <br/>
        <br/>
        <span class="new_event_entry_field">Remarks</span> :
        <br/>
        <textarea name="newSessionRemarks" id="apt_remarks" rows="4" cols="50"></textarea>
        <br/>
        <br/>
        <input type="submit" value="Create session"/>
        
    </div>
</form>
