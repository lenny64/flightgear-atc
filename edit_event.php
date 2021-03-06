<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>
<?php include('./include/restriction.php'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="container">

    <a href="./dashboard.php" class="btn btn-default">« Back to the dashboard</a>
    <h2>Edit an event</h2>

<?php

$eventCorrect = false;

// If the event is correct
if (isset($_GET['eventId']) AND $_GET['eventId'] != NULL AND $userAuthenticated == true)
{
    $eventCorrect = true;
    // We get the eventId
    $eventId = $_GET['eventId'];
    $Event = new Event();
    // We pick the event we want
    $Event->selectById($eventId);

    if ($Event->userId != $User->id)
    {
        $userAuthenticated = false;
    }
}

// IF ALL CONDITIONS ARE HERE TO ALLOW EDITING
if ($eventCorrect == true AND $userAuthenticated == true)
{
    include './include/form_editEvent.php';
}
?>

</div>

<?php include('./include/footer.php'); ?>
