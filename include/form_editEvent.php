

    <?php

    if (isset($_GET['eventId']) AND $_GET['eventId'] != NULL)
    {
        if (isset($_POST['airportICAO']) AND isset($_POST['date']) AND isset($_POST['beginTime']) AND isset($_POST['endTime']))
        {
            if ($_POST['airportICAO'] != NULL AND $_POST['date'] != NULL AND $_POST['beginTime'] != NULL AND $_POST['endTime'] != NULL)
            {
                $Event->id = $_GET['eventId'];
                $Event->airportICAO = $_POST['airportICAO'];
                $Event->userId = $_POST['userId'];
                $Event->date = $_POST['date'];
                $Event->beginTime = $_POST['beginTime'];
                $Event->endTime = $_POST['endTime'];
                $Event->fgcom = $_POST['fgcom'];
                $Event->teamspeak = $_POST['teamspeak'];
                $Event->transitionLevel = $_POST['transitionLevel'];
                $Event->runways = $_POST['runways'];
                $Event->ils = $_POST['ils'];
                $Event->docsLink = $_POST['docsLink'];
                $Event->remarks = $_POST['remarks'];
                if ($Event->updateEvent() === true)
                {
                    echo "The event has been successfully edited ! <a href='./edit_event.php?eventId=$Event->id'>Refresh the page</a>";
                }
                else
                {
                    echo "Sorry there was an error, please try again.";
                }
            }
        }
    }

    ?>

    <form class="" role="form" action="./edit_event.php?eventId=<?php echo $Event->id;?>" method="post">
        <input type="hidden" name="userId" value="<?php echo $User->id;?>"/>
        <input type="hidden" name="eventId" value="<?php echo $Event->id;?>"/>
        <!-- AIRPORT -->
        <h3>Airport</h3>
        <div class="col-sm-6 form-group">
            <label class="control-label" for="editEvent-airportName">Name</label>
            <input type="text" class="form-control" id="editEvent-airportName" value="<?php echo getInfo('globalAirportName','airports_global','globalAirportICAO',$Event->airportICAO);?>"/>
        </div>
        <div class="col-sm-6 form-group">
            <label class="control-label" for="editEvent-airportICAO">ICAO</label>
            <input type="text" class="form-control" id="editEvent-airportICAO" name="airportICAO" value="<?php echo $Event->airportICAO;?>" size="4"/>
        </div>

        <!-- TIME -->
        <h3>Time</h3>
        <div class="col-md-4">
            <label class="control-label" for="editEvent-date">Date</label>
            <input type="text" class="form-control" id="editEvent-date" name="date" value="<?php echo $Event->date;?>"/>
        </div>
        <div class="col-md-4">
            <label class="control-label" for="editEvent-beginTime">Begin time</label>
            <input type="text" class="form-control" id="editEvent-beginTime" name="beginTime" value="<?php echo $Event->beginTime;?>"/>
        </div>
        <div class="col-md-4">
            <label class="control-label" for="editEvent-endTime">End time</label>
            <input type="text" class="form-control" id="editEvent-endTime" name="endTime" value="<?php echo $Event->endTime;?>"/>
        </div>

        <!-- COMMUNICATION -->
        <h3>Communication</h3>
        <div class="col-md-6">
            <label class="control-label" for="editEvent-fgcom">FGcom</label>
            <input type="text" class="form-control" id="editEvent-fgcom" name="fgcom" value="<?php echo $Event->fgcom;?>"/>
        </div>
        <div class="col-md-6">
            <label class="control-label" for="editEvent-teamspeak">Teamspeak</label>
            <input type="text" class="form-control" id="editEvent-teamspeak" name="teamspeak" value="<?php echo $Event->teamspeak;?>"/>
        </div>

        <!-- FLYING INFORMATION -->
        <h3>Useful information</h3>
        <div class="col-md-3">
            <label class="control-label" for="editEvent-transitionLevel">Transition level</label>
            <input type="text" class="form-control" id="editEvent-transitionLevel" name="transitionLevel" value="<?php echo $Event->transitionLevel;?>"/>
        </div>
        <div class="col-md-3">
            <label class="control-label" for="editEvent-runways">Runways</label>
            <input type="text" class="form-control" id="editEvent-runways" name="runways" value="<?php echo $Event->runways;?>"/>
        </div>
        <div class="col-md-3">
            <label class="control-label" for="editEvent-ils">ILS</label>
            <input type="text" class="form-control" id="editEvent-ils" name="ils" value="<?php echo $Event->ils;?>"/>
        </div>
        <div class="col-md-3">
            <label class="control-label" for="editEvent-docsLink">Documents to download</label>
            <input type="text" class="form-control" id="editEvent-docsLink" name="docsLink" value="<?php echo $Event->docsLink;?>"/>
        </div>
        <div class="col-md-12">
        <label class="control-label" for="editEvent-remarks">Remarks</label>
        <textarea name="remarks" id="editEvent-remarks" class="form-control" rows="5"><?php echo $Event->remarks;?></textarea>
        </div>
        <div class="col-md-12">
            <br/>
            <button type="submit" class="btn btn-primary">Edit</button>
        </div>

    </form>
