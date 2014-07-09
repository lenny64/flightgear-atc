<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// Variable to see if there is a login entered
$err_noLogin = true;

// If the user wants to log in
if (isset($_POST['email']) AND isset($_POST['password']) AND $_POST['email'] != NULL AND $_POST['password'] != NULL)
{
    $err_noLogin = false;
    
    // Are the information correct ?
    if ($_POST['email'] == getInfo('mail', 'users', 'mail', $_POST['email']) AND $_POST['password'] == getInfo('password', 'users', 'mail', $_POST['email']))
    {
        $userAuthenticated = true;
        $User = new User();
        $User->selectById(getInfo('userId', 'users', 'mail', $_POST['email']));
        $User->connect($User->id);
    }
    else
    {
        $userAuthenticated = false;
    }
}
// If there is already a session open
else if ($_SESSION['mode'] == 'connected')
{
    $err_noLogin = false;
    $userAuthenticated = true;
}

// Finally, if there is an error during the login
if ($err_noLogin == true OR $userAuthenticated == false)
{
    if (isset($userAuthenticated) AND $userAuthenticated == false) { echo "<div class='information'>Your password and/or e-mail is not correct</div>"; }
    if ($err_noLogin == true) { echo "<div class='information'>Please enter your e-mail and password</div>"; }
    ?>
    <br/>
    <?php
    // If the user wanted to edit the event
    if ($_GET['eventId'] != NULL) echo '<form action="./edit_event.php5?eventId='.$_GET['eventId'].'" method="post">';
    // Else if the user wanted to just log in
    else echo '<form action="./dashboard.php5" method="post" class="dashboard_connectionForm">'; ?>
        <label>E-mail</label>
        <input type="text" name="email"/>
        <br/>
        <label>Password</label>
        <input type="password" name="password"/>
        <br/>
        <input type="submit" value="Connect" id="dashboard_connectionSubmit"/>
    </form>
    <?php
    include('./include/footer.php5');
    exit;
}
?>
