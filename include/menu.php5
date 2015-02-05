<div id="menu">
<img src="./img/flightgear-atc_logo_shadowless_161x51.png" alt="Flightgear ATC events" height="30"/>
<?php
    echo '<a href="index.php5"><div class="menu_entry">Flightgear ATC Events</div></a>';
    echo '<a href="lfml_event.php5"><div class="menu_entry">Phraseology</div></a>';
    echo '<a href="controlled_area.php5"><div class="menu_entry">Controlled area</div></a>';
    echo '<a href="downloads.php5"><div class="menu_entry">Downloads</div></a>';
    echo '<a href="contact.php5"><div class="menu_entry">Contact</div></a>';
    echo '<a href="faq.php5"><div class="menu_entry">FAQ</div></a>';
    if ($_SESSION['mode'] == 'connected' AND isset($_SESSION['id']))
    {
        echo '<a href="dashboard.php5"><div class="menu_entry">My dashboard</div></a>';
    }
    else
        echo '<a href="dashboard.php5"><div class="menu_entry">ATC Dashboard</div></a>';
?>
</div>


<div id="body_container">