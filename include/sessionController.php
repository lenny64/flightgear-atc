<?php

if (isset($_COOKIE['cookieConsentAnswer'])) {
    if ($_COOKIE['cookieConsentAnswer'] == "1") {
        session_start();
    }
}
// if (!isset($_COOKIE['cookieConsentAsk'])) {
//     setcookie('cookieConsentAsk',1,time()+3600*24*365);
// }
// if (!isset($_COOKIE['cookieConsentAnswer'])) {
//     setcookie('cookieConsentAnswer',0,time()+3600*24*365);
// }

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
