
<?php

// Controller for an event creation
include './include/controller_newEvent.php';

// Here is the controller to display events
require_once './include/dashboard_eventsClass.php';
// We list all events for this user
$DashboardEvents = new DashboardEventsList();
$DashboardEvents->selectEvents($User->id);

// If the list is not empty we show the one click event entry
if (sizeof($DashboardEvents->dashboardEvents) != 0)
{
  include './include/dashboard_oneClickEvent.php';
}

// If there are no events
if (sizeof($DashboardEvents->dashboardEvents) == 0)
{
  ?>
  <div class='alert alert-info'>Welcome on your dashboard. You can create events directly <a href="./index.php">from the home page</a>.</div>
  <?php
}
// If there are some events
else
{
  foreach ($DashboardEvents->dashboardEvents as $event)
  {
      $DashboardEvents->selectById($event['eventId']);
  ?>
  <div class="card mb-3">
      <div class="card-header">
          <h4><span class="label label-primary"><?php echo $DashboardEvents->airportICAO;?></span></h4>
          <a class="btn btn-info btn-sm" href="./edit_event.php?eventId=<?php echo $DashboardEvents->id;?>"><span class="glyphicon glyphicon glyphicon-pencil"></span> Edit event</a>
      </div>
      <div class="card-body">
          <h5>Date</h5>
          <?php echo $DashboardEvents->date; ?>

          <h5>Time</h5>
          From <?php echo $DashboardEvents->beginTime;?> to <?php echo $DashboardEvents->endTime;?>

          <h5>Other information</h5>
          FGCom : <?php echo $DashboardEvents->fgcom;?> // Teamspeak : <?php echo $DashboardEvents->teamspeak; ?>
          <br/>
          Documents to download : <a href="<?php echo $DashboardEvents->docsLink;?>"><?php echo $DashboardEvents->docsLink;?></a>
          <br/>
          Remarks :
          <br/>
          <?php echo $DashboardEvents->remarks; ?>
      </div>
  </div>
      <?php
  }
}
?>
