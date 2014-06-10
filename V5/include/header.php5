<?php

//error_reporting(E_ALL); ini_set("display_errors", 1);

date_default_timezone_set('UTC');

require_once './include/config.php5';

include('./include/classes.php5');
include('./include/functions.php5');


// Let's open the DB
mysql_connect(SQL_SERVER, SQL_LOGIN, SQL_PWD);
mysql_select_db(SQL_DB);
// This will be closed in footer.php

/* SESSION MANAGEMENT */
session_start();

if ($_SESSION['mode'] != 'connected' OR !isset($_SESSION['mode']) OR isset($_GET['disconnect'])) {
    $_SESSION['mode'] = 'guest';
    unset($_SESSION['id']);
}
elseif (isset($_SESSION['mode']) AND $_SESSION['mode'] == 'connected' AND isset($_SESSION['id']) AND $_SESSION['id'] != NULL)
{
	$User = new User();
	$User->selectById($_SESSION['id']);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<html>
    
<head>

	<title>Flightgear ATC Sessions -- lenny64</title>

	<!-- STYLES -->
	<link rel="stylesheet" type="text/css" href="./style/general.css"/>
	<link rel="stylesheet" type="text/css" href="./style/event.css"/>
	<link rel="stylesheet" type="text/css" href="./style/dashboard.css"/>
	<link rel="stylesheet" type="text/css" href="./style/contact.css"/>
	<link rel="stylesheet" type="text/css" href="./style/poll.css"/>

	<!-- AUTRES -->
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
        
    <link rel="shortcut icon" href="http://lenny64.free.fr/img/favicon.ico" />
    
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="./include/jquery-ui-1.10.4.custom.min.js"></script>
    <script type="text/javascript" src="./include/OpenLayers.js"></script>
</head>

<body>
    
    
<?php include('./include/menu.php5'); ?>
    
    <div id="body_container">
    
    <img id="banner" src="./img/banniere8.png" alt="Flighgear ATC events"/>
    
    <?php
        
    // A little tracker
    mysql_query("INSERT INTO visits VALUES('','".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."','".$_SERVER['REQUEST_URI']."');");
    
    ?>
