<?php

//error_reporting(E_ALL); ini_set("display_errors", 1);

date_default_timezone_set('Europe/Paris');

require_once './include/config.php5';

include('./include/classes.php5');
include('./include/functions.php5');

/* COOKIE MANAGEMENT */
// Every time the user accesses a page, we check if the cookie exists
$Lenny64_ID = new Cookie();
// We create it. It will check if the cookie exists in classes.php5
$Lenny64_ID_value = md5(date('YmdHiu'));
$Lenny64_ID->create('lenny64_id',$Lenny64_ID_value,time()+(3600*24*7));
// Do we receive the command to create a cookie ?
if (isset($_POST['cookie_create']) AND isset($_POST['cookie_name']) AND $_POST['cookie_name'] != NULL)
{
	// If so, we create a cookie with the cookie_name and cookie_value parameters.
	$str = "\$Lenny64_".$_POST['cookie_name']." = new Cookie(); \$Lenny64_".$_POST['cookie_name']."->create('lenny64_".$_POST['cookie_name']."','".$_POST['cookie_value']."',time()+(3600*24*7));";
	eval($str);
}
// Optional : unset a cookie
if (isset($_GET['disconnect']))
{
	unset($_COOKIE['lenny64_id']);		setcookie('lenny64_id','',-1);
	//unset($_COOKIE['lenny64_poll']);	setcookie('lenny64_poll','', -1);
}


/* SESSION MANAGEMENT */
session_start();

if ($_SESSION['mode'] != 'connected' OR !isset($_SESSION['mode']) OR isset($_GET['disconnect'])) {
    $_SESSION['mode'] = 'guest';
    unset($_SESSION['id']);
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
</head>

<body>
    
    <div id="body_container">
    
    <img src="./img/banniere6.jpg" alt="Flighgear ATC events"/>
    
    <?php
    
    // Let's open the DB
    mysql_connect(SQL_SERVER, SQL_LOGIN, SQL_PWD);
    mysql_select_db(SQL_DB);
    // This will be closed in footer.php
    
    // A little tracker
    mysql_query("INSERT INTO visits VALUES('','".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."','".$_SERVER['REQUEST_URI']."');");
    
    
    ?>
