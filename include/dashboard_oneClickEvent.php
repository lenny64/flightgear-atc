
<h3>
  <span class="glyphicon glyphicon glyphicon-flash"></span> One-click event
</h3>

<div class="dashboard-oneClick">
    <?php

    // We select the first entry into the dashboardEvents
    $DashboardEvents->selectById($DashboardEvents->dashboardEvents[0]['eventId']);

    for ($i = 0 ; $i < 6 ; $i++)
    {
    ?>

    <div class="col-md-2 col-sm-4 col-xs-6">
        <form role="form" action="./dashboard.php?newSession" method="post">
            <input type="hidden" name="newSessionMail" value="a">
            <input type="hidden" name="newSessionPassword" value="a">
            <input type="hidden" name="newSessionAirportName" value="<?php echo $DashboardEvents->airportName;?>">
            <input type="hidden" name="newSessionAirportICAO" value="<?php echo $DashboardEvents->airportICAO;?>">
            <input type="hidden" name="newSessionYear" value="<?php echo date('Y',strtotime('Today +'.$i.'days'));?>">
            <input type="hidden" name="newSessionMonth" value="<?php echo date('m',strtotime('Today +'.$i.'days'));?>">
            <input type="hidden" name="newSessionDay" value="<?php echo date('d',strtotime('Today +'.$i.'days'));?>">
            <input type="hidden" name="newSessionBeginHour" value="<?php echo date('H',strtotime($DashboardEvents->beginTime));?>">
            <input type="hidden" name="newSessionBeginMinutes" value="<?php echo date('i',strtotime($DashboardEvents->beginTime));?>">
            <input type="hidden" name="newSessionEndHour" value="<?php echo date('H',strtotime($DashboardEvents->endTime));?>">
            <input type="hidden" name="newSessionEndMinutes" value="<?php echo date('i',strtotime($DashboardEvents->endTime));?>">
            <input type="hidden" name="newSessionFGCOM" value="<?php echo $DashboardEvents->fgcom;?>">
            <input type="hidden" name="newSessionTeamSpeak" value="<?php echo $DashboardEvents->teamspeak;?>">
            <input type="hidden" name="newSessionDocsLink" value="<?php echo $DashboardEvents->docsLink;?>">
            <input type="hidden" name="newSessionRemarks" value="<?php echo $DashboardEvents->remarks;?>">
            <strong class="oneClick-date"><?php echo date('D j M',strtotime('Today +'.$i.'days'));?></strong>
            <br/>
            <span class="label label-primary"><?php echo $DashboardEvents->airportICAO;?></span>
            <br/>
            <span class="oneClick-time"><?php echo $DashboardEvents->beginTime." &rarr; ".$DashboardEvents->endTime;?></span>
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
