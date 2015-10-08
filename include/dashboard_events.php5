
<?php

// Controller for an event creation
include './include/controller_newEvent.php5';

// Here is the controller to display events
require_once './include/dashboard_eventsClass.php5';
// We list all events for this user
$DashboardEvents = new DashboardEventsList();
$DashboardEvents->selectEvents($User->id);
?>

<h3><span class="glyphicon glyphicon glyphicon-flash"></span> One-click event</h3>
<div class="alert alert-success">New! You can now create an event with just one click!</div>

<div class="dashboard-oneClick">
    <?php

    // We select the first entry into the dashboardEvents
    $LastEvent = new DashboardEventsList();
    $lastEvent = $LastEvent->getEventInfo($DashboardEvents->dashboardEvents[0][0]);

    for ($i = 0 ; $i < 6 ; $i++)
    {
    ?>

    <div class="col-md-2 col-sm-4 col-xs-6">
        <form role="form" action="./dashboard.php5?newSession" method="post">
            <input type="hidden" name="newSessionMail" value="a">
            <input type="hidden" name="newSessionPassword" value="a">
            <input type="hidden" name="newSessionAirportName" value="<?php echo $lastEvent->airportICAO;?>">
            <input type="hidden" name="newSessionAirportICAO" value="<?php echo $lastEvent->airportICAO;?>">
            <input type="hidden" name="newSessionYear" value="<?php echo date('Y',strtotime('Today +'.$i.'days'));?>">
            <input type="hidden" name="newSessionMonth" value="<?php echo date('m',strtotime('Today +'.$i.'days'));?>">
            <input type="hidden" name="newSessionDay" value="<?php echo date('d',strtotime('Today +'.$i.'days'));?>">
            <input type="hidden" name="newSessionBeginHour" value="<?php echo date('H',strtotime($lastEvent->beginTime));?>">
            <input type="hidden" name="newSessionBeginMinutes" value="<?php echo date('i',strtotime($lastEvent->beginTime));?>">
            <input type="hidden" name="newSessionEndHour" value="<?php echo date('H',strtotime($lastEvent->endTime));?>">
            <input type="hidden" name="newSessionEndMinutes" value="<?php echo date('i',strtotime($lastEvent->endTime));?>">
            <input type="hidden" name="newSessionFGCOM" value="<?php echo $lastEvent->fgcom;?>">
            <input type="hidden" name="newSessionTeamSpeak" value="<?php echo $lastEvent->teamspeak;?>">
            <input type="hidden" name="newSessionDocsLink" value="<?php echo $lastEvent->docsLink;?>">
            <input type="hidden" name="newSessionRemarks" value="<?php echo $lastEvent->remarks;?>">
            <strong class="oneClick-date"><?php echo date('D j M',strtotime('Today +'.$i.'days'));?></strong>
            <br/>
            <span class="label label-primary"><?php echo $lastEvent->airportICAO;?></span>
            <br/>
            <span class="oneClick-time"><?php echo $lastEvent->beginTime." &rarr; ".$lastEvent->endTime;?></span>
            <br/>
            <button type="submit" class="btn btn-xs btn-default">Create</button>
        </form>
    </div>

    <?php
    }

    ?>
    <small>
        The airport location and event times are based on your last entry.
        <br/>
        You are free to edit the event after you created it by using the <span class="btn btn-default btn-sm"><span class="glyphicon glyphicon glyphicon-pencil"></span> Edit event</span> button below.
    </small>
</div>

<h3><span class="glyphicon glyphicon glyphicon-th-list"></span> Events</h3>

<?php
foreach ($DashboardEvents->dashboardEvents as $event)
{
    $Event = $DashboardEvents->getEventInfo($event['eventId']);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><span class="label label-primary"><?php echo $Event->airportICAO;?></span></h4> 
        <a class="btn btn-default btn-sm" href="./edit_event.php5?eventId=<?php echo $Event->id;?>"><span class="glyphicon glyphicon glyphicon-pencil"></span> Edit event</a>
    </div>
    <div class="panel-body">
        <h5>Date</h5>
        <?php echo $Event->date; ?>

        <h5>Time</h5>
        From <?php echo $Event->beginTime;?> to <?php echo $Event->endTime;?>

        <h5>Other information</h5>
        FGCom : <?php echo $Event->fgcom;?> // Teamspeak : <?php echo $Event->teamspeak; ?>
        <br/>
        Documents to download : <a href="<?php echo $Event->docsLink;?>"><?php echo $Event->docsLink;?></a>
        <br/>
        Remarks :
        <br/>
        <?php echo $Event->remarks; ?>
    </div>
</div>
    <?php
}
?>
