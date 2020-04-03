<?php

include './include/controller_newEvent.php';

// We define global variable to prevent "undefined variable" error
global $getDate;

// Information to print the new session form
if (isset($_GET['form_newSession']) AND isset($_GET['date']))
{
    if ($_GET['date'] != NULL)
    {
        $getDate = htmlspecialchars($_GET['date']);
    }
}

?>

<form role="form" action="./new_event.php?date=<?php echo $getDate; ?>&newSession" method="post" class="">
    <?php
    // If we asked the form to open
    if (isset($_GET['date']))
    {
        if ($_GET['date'] != NULL)
        {
            $getDate = htmlspecialchars($_GET['date']);
            $inputDate = true;
            $getYear = date("Y",strtotime($getDate));
            $getMonth = date("m", strtotime($getDate));
            $getDay = date("d", strtotime($getDate));

            echo '<div id="div_newSession'.$getDate.'">';
        }
    }
    // Otherwise, by default we don't show the form
    else
    {
        echo '<div id="div_newSession'.$getDate.'" style="display: none;">';
    }
    ?>
        <?php

        // If the user is not connected
        if (!isset($_SESSION['id']) OR $_SESSION['id'] == NULL OR $_SESSION['mode'] != 'connected') {
        ?>
        <h3>Identification</h3>

        <div class="alert alert-info">
            The ATC Dashboard has been re-designed! Click below if you want to access there.
            <br/><br/>
            <a href="./dashboard.php" class="btn btn-primary">I already have an account</a> <a href="./subscribe.php" class="btn btn-default">I want to create an account</a>
        </div>

        <?php
        // If the user is already connected, we won't ask him to ident
        } else {
        ?>
        <div class="alert alert-success">
            <b>Tip!</b> Quickly create your ATC events using the <a href="./dashboard.php" class="btn btn-success">ATC dashboard</a>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3><img src="./img/scheme_airport.png"/> Location</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label">Airport selection</label>
                            <br/>
                                <?php
                                $airports = $db->query("SELECT * FROM airports ORDER BY ICAO");
                                foreach ($airports as $airport)
                                {
                                    $name = $airport['name'];
                                    $ICAO = $airport['ICAO'];

                                    // We pick the information relevant to the event for the autocompletion
                                    // To do that, we only grab the LAST entry (to get updated information) !
                                    // If the user is connected, we will look for his information
                                    if (isset($_SESSION['id']) AND $_SESSION['id'] != NULL AND $_SESSION['mode'] == 'connected')
                                    {
                                        $autoCompletionEvent_result = $db->query("SELECT * FROM events WHERE airportICAO = '$ICAO' AND remarks != 'openradar' AND userId = ".$_SESSION['id']." ORDER BY eventId DESC LIMIT 1");
                                    }
                                    // If the user is not connected, we will gather only the last information
                                    else
                                    {
                                        $autoCompletionEvent_result = $db->query("SELECT * FROM events WHERE airportICAO = '$ICAO' AND remarks != 'openradar' ORDER BY eventId DESC LIMIT 1");
                                    }
                                    $autoCompletionEvent = $autoCompletionEvent_result->fetch(PDO::FETCH_ASSOC);

                                    if ($autoCompletionEvent != FALSE)
                                    {
                                        $beginTime = explode(':',$autoCompletionEvent['beginTime']);
                                        $endTime = explode(':',$autoCompletionEvent['endTime']);

                                        $fgcom = $autoCompletionEvent['fgcom'];
                                        $teamspeak = $autoCompletionEvent['teamspeak'];
                                        $docsLink = $autoCompletionEvent['docsLink'];

                                        $remarks = str_replace("\n","",$autoCompletionEvent['remarks']);?>
                                        <span class="btn btn-info" onclick="
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
                                }
                                ?>
                                <span class="btn btn-primary" onclick="
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
                        <div class="row">
                            <div class="col form-group">
                                <label class="control-label" for="apt_name">Airport name*</label>
                                <input type="text" class="form-control" id="apt_name" name="<?php echo $requiredFields[0];?>" placeholder="Airport name" required>
                            </div>
                            <div class="col form-group">
                                <label class="control-label" for="apt_icao">Airport ICAO*</label>
                                <input type="text" class="form-control" id="apt_icao" name="<?php echo $requiredFields[1];?>" placeholder="ICAO code" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3><img src="./img/scheme_date.png"/> Date and time</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label" for="newEvent-dateYear">Date*</label>
                            <div class="row">
                                <div class="col">
                                    <select name="<?php echo $requiredFields[2];?>" id="newEvent-dateYear" class="form-control">
                                        <option value="<?php echo date('Y');?>" <?php if(isset($getYear) AND $getYear == date('Y')) echo "selected";?>><?php echo date('Y');?></option>
                                        <option value="<?php echo date('Y',strtotime(date()." + 1 year"));?>" <?php if(isset($getYear) AND $getYear != date('Y')) echo "selected";?>><?php echo date('Y',strtotime(date()." + 1 year"));?></option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select name="<?php echo $requiredFields[3];?>" class="form-control">
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
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="newEvent-dateDay" name="<?php echo $requiredFields[4];?>" value="<?php if (isset($getDay) and $getDay != NULL) echo $getDay; else { echo "Days"; } ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="control-label" for="apt_beginHour"><img src="./img/scheme_time.png"/> From*</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="apt_beginHour" name="<?php echo $requiredFields[5];?>" placeholder="hh" required>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="apt_beginMinutes" name="<?php echo $requiredFields[6];?>" placeholder="mm" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label class="control-label" for="apt_endHour"><img src="./img/scheme_time.png"/> To*</label>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="apt_endHour" name="<?php echo $requiredFields[7];?>" placeholder="hh" required>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="apt_endMinutes" name="<?php echo $requiredFields[8];?>" placeholder="mm" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3><img src="./img/scheme_communication.png"/> Communication</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col form-group">
                                <label class="control-label" for="apt_fgcom">FGCom Frequency</label>
                                <input type="text" class="form-control" id="apt_fgcom" name="newSessionFGCOM" placeholder="FGCom">
                            </div>
                            <div class="col form-group">
                                <label class="control-label" for="apt_teamspeak">Mumble information</label>
                                <input type="text" class="form-control" id="apt_teamspeak" name="newSessionTeamSpeak" placeholder="Mumble">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Additional Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="col-xs-12 form-group">
                            <label class="control-label" for="apt_docslink">Link to download the airport's charts</label>
                            <div class="">
                                <input type="text" class="form-control" id="apt_docslink" name="newSessionDocsLink" value="http://">
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="control-label" for="apt_remarks">Remarks</label>
                            <div class="">
                                <textarea class="form-control" id="apt_remarks" name="newSessionRemarks"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>










        <button type="submit" class="btn btn-primary" value="Create session">Create session</button>
        <?php
      } ?>
    </div>
</form>
