<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<h2>Add a comment to a flightplan</h2>

<?php

// If there is a new comment for that flightplan
if (isset($_POST['flightplanId']) AND isset($_POST['pseudo']) AND isset($_POST['comment']))
{
    if ($_POST['flightplanId'] != NULL AND $_POST['pseudo'] != NULL AND $_POST['comment'] != NULL)
    {
        // We select the flightplan
        $Flightplan = new Flightplan();
        $flightplanId = mysql_real_escape_string(htmlspecialchars($_POST['flightplanId']));
        $Flightplan->selectById($flightplanId);
        
        $pseudo = mysql_real_escape_string(htmlspecialchars($_POST['pseudo']));
        $comment = mysql_real_escape_string(htmlspecialchars($_POST['comment']));
        
        $Flightplan->addComment($pseudo, $comment);
    }
}

// If there is a flightplan to edit (request from show_flightplans.php5)
if (isset($_GET['idFlightplan']) AND $_GET['idFlightplan'] != NULL)
{
    // We select the flightplan
    $Flightplan = new Flightplan();
    $flightplanId = mysql_real_escape_string(htmlspecialchars($_GET['idFlightplan']));
    $Flightplan->selectById($flightplanId);
    
    ?>
<div class="event_flightplan" style="display:block;">
    
    Callsign : <b><?php echo $Flightplan->callsign; ?></b><br/>
    Aircraft : <b><?php echo $Flightplan->aircraftType; ?></b><br/>
    Category of flight : <b><?php echo $Flightplan->category; ?></b><br/>
    Departure : <b><?php echo $Flightplan->departureAirport; ?></b><br/>
    Departure time : <b><?php echo $Flightplan->departureTime; ?></b><br/>
    Destination : <b><?php echo $Flightplan->arrivalAirport; ?></b><br/>
    Arrival time : <b><?php echo $Flightplan->arrivalTime; ?></b><br/>
    Cruise level : <b><?php echo $Flightplan->cruiseAltitude; ?></b><br/>
    Waypoints : <b><?php echo $Flightplan->waypoints; ?></b><br/>
    Comments : 
    <br/>
    <?php
    
    foreach ($Flightplan->comments as $comments)
    {
		$pseudo = $comments['pseudo'];
		$comment = $comments['comment'];
		echo "<b>$pseudo</b> $comment<br/>";
	}
    
    ?>
    
</div>

<form action="./edit_flightplan.php5?idFlightplan=<?php echo $Flightplan->id; ?>" method="post">
    <input type="hidden" name="flightplanId" value="<?php echo $Flightplan->id; ?>"/>
    <input type="text" name="pseudo" value="Your name"/>
    <br/>
    <textarea name="comment">Comment</textarea>
    <br/>
    <input type="submit" value="Submit"/>
</form>

<?php

}

?>

<br/>
<br/>
<?php include('./include/footer.php5'); ?>
