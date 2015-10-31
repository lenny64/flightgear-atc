
<?php


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
    ?>
    <div class="container">
    <?php
    if (isset($userAuthenticated) AND $userAuthenticated == false) { echo "<div class='alert alert-info'>Your password and/or e-mail is not correct</div>"; }
    if ($err_noLogin == true) { echo "<div class='alert alert-info'>Please enter your e-mail and password</div>"; }
    ?>
    <br/>
    <?php
    // If the user wanted to edit the event
    if ($_GET['eventId'] != NULL) echo '<form role="form" action="./edit_event.php5?eventId='.$_GET['eventId'].'" method="post">';
    // Else if the user wanted to just log in
    else echo '<form class="dashboard_connectionForm" role="form" action="./dashboard.php5" method="post">'; ?>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input class="form-control" type="text" name="email" id="email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" type="password" name="password" id="password">
        </div>
        <button type="submit" class="btn btn-primary">Connect</button>
    </form>
    </div>
    <br/>
    <br/>
    <?php
    include('./include/footer.php5');
    exit;
}
?>
