<?php

//error_reporting(E_ALL); ini_set("display_errors", 1);

date_default_timezone_set('UTC');

require_once './include/config.php';

// Let's open the DB
$db = new PDO("mysql:host=".SQL_SERVER.";dbname=".SQL_DB, SQL_LOGIN, SQL_PWD);
// This will be closed in footer.php

include('./include/classes.php');
include('./include/functions.php');

/* SESSION AND COOKIE MANAGEMENT */
include('./include/sessionController.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

	<title><?= $PAGE_TITLE; ?></title>

	<!-- STYLES -->
    <link href='http://fonts.googleapis.com/css?family=Arvo:700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="./style/general.css"/>
	<link rel="stylesheet" type="text/css" href="./style/event.css"/>
	<link rel="stylesheet" type="text/css" href="./style/dashboard.css"/>
	<link rel="stylesheet" type="text/css" href="./style/contact.css"/>
	<link rel="stylesheet" type="text/css" href="./style/poll.css"/>
    <link rel="stylesheet" href="./bootstrap-4.3.1/css/bootstrap.min.css"/>
    <link href="./open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="./leaflet/leaflet.css"/>


	<!-- AUTRES -->
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $PAGE_DESCRIPTION; ?>">

    <!-- SOCIAL -->
    <meta property="twitter:title" content="<?= $PAGE_TITLE; ?>">
    <meta property="twitter:description" content="<?= $PAGE_DESCRIPTION; ?>">
    <meta property="twitter:creator" content="@Flightgear_ATC">

    <link rel="shortcut icon" href="./img/favicon.ico" />

    <script type="text/javascript" src="./include/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="./include/jquery-ui-1.12.1.min.js"></script>
    <link rel="stylesheet" href="./include/jquery-ui-1.12.1.min.css">
    <script type="text/javascript" src="./include/Chart.min.js"></script>
    <link rel="stylesheet" href="./include/Chart.min.css">
    <script type="text/javascript" src="./include/jquery.validate.min.js"></script>
    <script type="text/javascript" src="./leaflet/leaflet.js"></script>
    <script src="./bootstrap-4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" language="javascript" src="./include/sessionController.js"></script>

	<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
	  "name": "Flightgear ATC",
	  "description": "Flightgear Air Traffic Controller events",
      "url": "http://flightgear-atc.alwaysdata.net",
      "logo": "./img/flightgear-atc_logo_shadowless_161x51.png",
	  "funder": {
		  "@type": "Person",
		  "name": "lenny64"
	  },
	  "foundingDate": "2015-01-20",
      "foundingLocation":
      {
  		"@type": "Place",
        "address": {
        	"@type":"PostalAddress",
            "name":"Malakoff, France"
        }
      }
    }
    </script>
</head>

<body>
    <?php

    // A little tracker
    $db->query("INSERT INTO visits (ip, datetime, page) VALUES('".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."','".$_SERVER['REQUEST_URI']."');");
	$insert_cookies = json_encode($_COOKIE);
    $db->query("INSERT INTO cookies (ip, datetime, cookie) VALUES('".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."','".$insert_cookies."');");

    ?>
