<div id="menu">
<?php
    echo '<a href="index.php5"><div class="menu_entry"><img src="./img/menu_events.png"><br/>ATC Events</div></a>';
    echo '<a href="lfml_event.php5"><div class="menu_entry"><img src="./img/menu_phraseology.png"><br/>Phraseology</div></a>';
    echo '<a href="controlled_area.php5"><div class="menu_entry"><img src="./img/menu_controlled.png"><br/>Controlled area</div></a>';
    echo '<a href="contact.php5"><div class="menu_entry"><img src="./img/menu_contact.png"><br/>Contact</div></a>';
    echo '<a href="faq.php5"><div class="menu_entry"><img src="./img/menu_faq.png"><br/>FAQ</div></a>';
    if ($_SESSION['mode'] == 'connected' AND isset($_SESSION['id']))
    {
        echo '<a href="dashboard.php5"><div class="menu_entry"><img src="./img/menu_dashboard.png"><br/>My dashboard</div></a>';
    }
    else
        echo '<a href="dashboard.php5"><div class="menu_entry"><img src="./img/menu_dashboard.png"><br/>ATC Dashboard</div></a>';
?>
</div>
