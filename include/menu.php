<?php

if ($PAGE_NAME == "index.php") {
    $active_index = "active";
}
else if ($PAGE_NAME == "school.php") {
    $active_school = "active";
}
else if ($PAGE_NAME == "controlled_area.php") {
    $active_controlled_area = "active";
}
else if ($PAGE_NAME == "downloads.php") {
    $active_downloads = "active";
}
else if ($PAGE_NAME == "contact.php") {
    $active_contact = "active";
}
else if ($PAGE_NAME == "faq.php") {
    $active_faq = "active";
}
else if ($PAGE_NAME == "api.php") {
    $active_api = "active";
}
else if ($PAGE_NAME == "dashboard.php") {
    $active_dashboard = "active";
}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-1">
  <a class="navbar-brand" href="#"><img src="./img/flightgear-atc_logo_shadowless_161x51.png" alt="Flightgear ATC events" height="25"/></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link <?= $active_index; ?>" href="./index.php">Flightgear ATC Events</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $active_school; ?>" href="./school.php">School</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $active_controlled_area; ?>" href="./controlled_area.php">Controlled area</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $active_downloads; ?>" href="./downloads.php">Downloads</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $active_contact; ?>" href="./contact.php">Contact</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $active_faq; ?>" href="./faq.php">FAQ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $active_api; ?>" href="./api.php">API</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link <?= $active_dashboard; ?>" href="./dashboard.php">ATC Dashboard</a>
        </li>
        <?php if ($_SESSION['mode'] == 'connected') { ?>
        <li class="nav-item">
            <a class="nav-link" href="./dashboard.php?disconnect">Disconnect</a>
        </li>
        <?php } ?>
    </div>
  </div>
</nav>

<!-- <nav class="navbar navbar-inverse navbar-static-top">
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
          <li><a href="./index.php">Flightgear ATC Events</a></li>
          <li><a href="./school.php">School</a></li>
          <li><a href="controlled_area.php">Controlled area</a></li>
          <li><a href="downloads.php">Downloads</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="faq.php">FAQ</a></li>
          <li><a href="api.php">API</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="./dashboard.php">ATC Dashboard</a></li>
          <?php
          if ($_SESSION['mode'] == 'connected') {
            echo '<li><a href="./dashboard.php?disconnect">Disconnect</a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
</nav> -->
