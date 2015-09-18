<?php
/*
<nav class="navbar">
    <div class="container-fluid">
        <img src="./img/flightgear-atc_logo_shadowless_161x51.png" alt="Flightgear ATC events" height="30"/>
        <?php
            echo '<a href="index.php5"><div class="menu_entry">Flightgear ATC Events</div></a>';
            echo '<a href="school.php5"><div class="menu_entry">School</div></a>';
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
</nav>
 * 
 */
?>
<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container" id="navfluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><img src="./img/flightgear-atc_logo_shadowless_161x51.png" alt="Flightgear ATC events" height="25"/></a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li class="active"><a href="./index.php5">Flightgear ATC Events</a></li>
          <li><a href="./school.php5">School</a></li>
          <li><a href="controlled_area.php5">Controlled area</a></li>
          <li><a href="downloads.php5">Downloads</a></li>
          <li><a href="contact.php5">Contact</a></li>
          <li><a href="faq.php5">FAQ</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="./dashboard.php5">ATC Dashboard</a></li>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
</nav>