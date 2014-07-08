<div id="menu">
    <div class="menu_entry"><a href="index.php5">Events</a></div>
    <!--<div class="menu_entry"><a href="school.php5">School</a></div>-->
    <div class="menu_entry"><a href="lfml_event.php5">Elements of phraseology</a></div>
    <div class="menu_entry"><a href="controlled_area.php5">Regularly controlled area</a></div>
    <!--<div class="menu_entry"><a href="flight.php">Flying</a></div>
    <div class="menu_entry"><a href="communication.php">Communicating</a></div>
    <div class="menu_entry"><a href="information.php">Information</a></div>-->
    <div class="menu_entry"><a href="contact.php5">Contact</a></div>
    <?php if ($_SESSION['mode'] == 'connected' AND isset($_SESSION['id']))
    {
        echo '<div class="menu_entry"><a href="dashboard.php5">My dashboard</a></div>';
        echo '<div class="menu_entry"><a href="index.php5?disconnect">Disconnect</a></div>';
    }
    else
        echo '<div class="menu_entry"><a href="dashboard.php5">ATC Dashboard</a></div>';
    ?>
</div>
