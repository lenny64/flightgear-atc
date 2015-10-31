<?php

error_reporting(E_ALL); ini_set("display_errors", 1);

date_default_timezone_set('UTC');

require_once './include/config.php5';

// Let's open the DB
$db = new PDO("mysql:host=".SQL_SERVER.";dbname=".SQL_DB, SQL_LOGIN, SQL_PWD);
// This will be closed in footer.php

include('./include/classes.php5');
include('./include/functions.php5');

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
    $User->connect($User->id);
}

/* COOKIE MANAGEMENT */
if (isset($_POST['createCookie']) AND isset($_POST['cookieValue']))
{
    if ($_POST['createCookie'] != NULL)
    {
        setcookie($_POST['createCookie'],$_POST['cookieValue'], time()+3600*24*36);
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

	<title>Flightgear ATC Events</title>

	<!-- STYLES -->
        <link href='http://fonts.googleapis.com/css?family=Arvo:700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="./style/general.css"/>
	<link rel="stylesheet" type="text/css" href="./style/event.css"/>
	<link rel="stylesheet" type="text/css" href="./style/dashboard.css"/>
	<link rel="stylesheet" type="text/css" href="./style/contact.css"/>
	<link rel="stylesheet" type="text/css" href="./style/poll.css"/>
        <link rel="stylesheet" href="./bootstrap-3.3.5/css/bootstrap.min.css">

	<!-- AUTRES -->
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="shortcut icon" href="http://lenny64.free.fr/img/favicon.ico" />

        <script type="text/javascript" src="./include/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="./include/jquery-ui-1.10.4.custom.min.js"></script>
        <script type="text/javascript" src="./include/jquery.validate.min.js"></script>
        <script src="./bootstrap-3.3.5/js/bootstrap.min.js"></script>
</head>

<body>

    <?php

    // A little tracker
    $db->query("INSERT INTO visits VALUES('','".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."','".$_SERVER['REQUEST_URI']."');");

    ?>
